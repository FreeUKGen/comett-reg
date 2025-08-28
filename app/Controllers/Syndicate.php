<?php namespace App\Controllers;

use App\Models\SyndicateTable_Model;
use App\Models\Syndicate_Model;
use App\Models\Allocation_Model;
use App\Models\Transcription_Model;
use App\Models\Roles_Model;
use App\Models\Transcription_Cycle_Model;
use App\Models\Identity_Model;
use App\Models\Parameter_Model;
use App\Models\Detail_Data_Model;

class Syndicate extends BaseController
{	
	function __construct() 
	{
        helper('common');
    }
	
	public function manage_syndicates($start_message)
	{		
		// initialise method
		$session = session();
		$project_syndicate_model = new SyndicateTable_Model();
		$freecomett_syndicate_model = new Syndicate_Model();
		$parameter_model = new Parameter_Model();
		
		// if masquerade flag on, restore saved coordinator details
		if ( $session->masquerade == 1 )
			{
				$session->identity_userid = $session->coordinator_identity_userid;
				$session->identity_password = $session->coordinator_identity_password;
				$session->identity_emailid = $session->coordinator_identity_emailid;
				$session->BMD_identity_index = $session->coordinator_BMD_identity_index;
				$session->realname = $session->coordinator_realname;
				$session->data_entry_font = $session->coordinator_data_entry_font;
				$session->environment_user = $session->coordinator_environment_user;
				$session->role = $session->coordinator_role;
				
				$session->masquerade = 0;
			}
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Syndicates for, Coordinator => '.$session->identity_userid.' => '.$session->realname);
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					
					switch ($session->result) 
						{
							case 'no_credit':
								$session->set('message_2', 'Syndicate was updated to NOT INCLUDE Credit Line in Transcription CSV Header when uploading to '.$session->current_project[0]['project_name']);
								$session->set('message_class_2', 'alert alert-success');
								break;
							case 'credit':
								$session->set('message_2', 'Syndicate was updated to INCLUDE Credit Line in Transcription CSV Header when uploading to '.$session->current_project[0]['project_name']);
								$session->set('message_class_2', 'alert alert-success');
								break;
							case 'live':
								$session->set('message_2', 'New transcribers are assigned to LIVE environment on '.$session->current_project[0]['project_name']);
								$session->set('message_class_2', 'alert alert-success');
								break;
							case 'test':
								$session->set('message_2', 'New transcribers are assigned to TEST environment on '.$session->current_project[0]['project_name']);
								$session->set('message_class_2', 'alert alert-success');
								break;
							case 'after':
								$session->set('message_2', 'New transcribers are assigned to classic verification of transcription data.');
								$session->set('message_class_2', 'alert alert-success');
								break;
							case 'onthefly':
								$session->set('message_2', 'New transcribers are assigned to line by line verification of transcription data.');
								$session->set('message_class_2', 'alert alert-success');
								break;
						}
					
					// get all syndicates in syndicate name sequence - depends on project - create temp array for display info
					$temp_synd = array();
					
					// need to join SyndicateMembers because the coordinator flag is in this table.
					switch ($session->current_project[0]['project_name']) 
						{
							case 'FreeBMD':
								// set user to coordinator userid if real user is DBADMIN level to allow testing
								if ( $session->current_identity[0]['role_index'] <= 1 )
									{
										$parameter = $parameter_model->where('Parameter_key', 'freebmd_pretendtobecoord')->find();
										$coord = $parameter[0]['Parameter_value'];
										$user = $coord;
									}
								else
									{
										$user = $session->identity_userid;
									}
									
								// get all syndicates with user as coordinator
								$db1 = \Config\Database::connect($session->syndicate_DB);
								$sql =	"	
											SELECT * 
											FROM SyndicateTable 
											JOIN SyndicateMembers
											ON SyndicateTable.SyndicateID = SyndicateMembers.SyndicateID
											WHERE SyndicateMembers.UserID = '".$user."' AND SyndicateMembers.CoOrdinator = 'Y'
											ORDER BY SyndicateTable.SyndicateName
										";
								$query = $db1->query($sql);
								$session->project_syndicates = $query->getResultArray();
									
								// any found?
								if (  ! $session->project_syndicates )
									{
										$session->set('message_2',  'You are not responsible for any syndicates.');
										$session->set('message_class_2', 'alert alert-danger');
										return redirect()->to( base_url('syndicate/manage_syndicates/1') );
									}

								// get specific FreeComETT syndicate data
								foreach ( $session->project_syndicates as $key => $project_syndicate )
									{
										// get FreeComETT syndicate
										$freecomett_syndicate = $freecomett_syndicate_model
											->where('project_index', $session->current_project[0]['project_index'])
											->where('BMD_syndicate_index', $project_syndicate['SyndicateID'])
											->find();
											
										// load temp array - phase 1
										$temp_synd[$key]['syndicate_id'] = $project_syndicate['SyndicateID'];
										$temp_synd[$key]['syndicate_name'] = $project_syndicate['SyndicateName'];
										$temp_synd[$key]['syndicate_email'] = $project_syndicate['SyndicateEmail'];
										$temp_synd[$key]['recruiting'] = $project_syndicate['Recruiting'];
											
										// found
										if ( $freecomett_syndicate )
											{
												// load temp array - phase 2
												$temp_synd[$key]['BMD_syndicate_credit'] = $freecomett_syndicate[0]['BMD_syndicate_credit'];
												$temp_synd[$key]['status'] = $freecomett_syndicate[0]['status'];
												$temp_synd[$key]['new_user_environment'] = $freecomett_syndicate[0]['new_user_environment'];
												$temp_synd[$key]['verify_mode'] = $freecomett_syndicate[0]['verify_mode'];
											}
										else
											{
												// load temp array - phase 2
												$temp_synd[$key]['BMD_syndicate_credit'] = 'N';
												$temp_synd[$key]['status'] = '1';
												$temp_synd[$key]['new_user_environment'] = 'TEST';
												$temp_synd[$key]['verify_mode'] = 'onthefly';
											}
									}
								break;
							case 'FreeREG':
								// set user to coordinator userid if real user is DBADMIN level to allow testing
								if ( $session->current_identity[0]['role_index'] <= 1 )
									{
										$parameter = $parameter_model->where('Parameter_key', 'freereg_pretendtobecoord')->find();
										$coord = $parameter[0]['Parameter_value'];
										$user = $coord;
									}
								else
									{
										$user = $session->identity_userid;
									}
									
								// get all syndicates with user as coordinator
								// define mongodb - see common helper
								define_environment(3);
								$mongodb = define_mongodb();
								$collection_syndicates = $mongodb['database']->selectCollection('syndicates');

								// get the syndicates for this coordinator
								$session->project_syndicates = $collection_syndicates->find
									(
										[
											'syndicate_coordinator' => $user
										]
									)->toArray();
																		
								// any found?
								if (  ! $session->project_syndicates )
									{
										$session->set('message_2',  'You are not responsible for any syndicates.');
										$session->set('message_class_2', 'alert alert-danger');
										return redirect()->to( base_url('syndicate/manage_syndicates/1') );
									}

								// get specific FreeComETT syndicate data
								foreach ( $session->project_syndicates as $key => $project_syndicate )
									{
										// get FreeComETT syndicate
										$id = $project_syndicate['_id']->__toString();
										$freecomett_syndicate = $freecomett_syndicate_model
											->where('project_index', $session->current_project[0]['project_index'])
											->where('BMD_syndicate_index', $id)
											->find();
											
										// load temp array
										$recruiting = '1';
										if ( ! $project_syndicate['accepting_transcribers'] )
											{
												$recruiting = '0';
											}
											
										// load temp array - phase 1
										$temp_synd[$key]['syndicate_id'] = $id;
										$temp_synd[$key]['syndicate_name'] = $project_syndicate['syndicate_code'];
										$temp_synd[$key]['recruiting'] = $recruiting;
											
										// found
										if ( $freecomett_syndicate )
											{
												// load temp array - phase 2
												$temp_synd[$key]['syndicate_email'] = $freecomett_syndicate[0]['BMD_syndicate_email'];
												$temp_synd[$key]['BMD_syndicate_credit'] = $freecomett_syndicate[0]['BMD_syndicate_credit'];
												$temp_synd[$key]['status'] = $freecomett_syndicate[0]['status'];
												$temp_synd[$key]['new_user_environment'] = $freecomett_syndicate[0]['new_user_environment'];
												$temp_synd[$key]['verify_mode'] = $freecomett_syndicate[0]['verify_mode'];
											}
										else
											{
												$temp_synd[$key]['syndicate_email'] = '';
												$temp_synd[$key]['BMD_syndicate_credit'] = 'N';
												$temp_synd[$key]['status'] = '1';
												$temp_synd[$key]['new_user_environment'] = 'TEST';
												$temp_synd[$key]['verify_mode'] = 'onthefly';
											}
									}
								break;
								
							case 'FreeCEN':
								break;
						}
						
					// create session variable	
					$session->temp_synd = $temp_synd;	
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage your Syndicates.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
			
		// show syndicates
		echo view('templates/header');
		echo view('linBMD2/manage_syndicates');
		echo view('linBMD2/sortTableNew');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// i'm using this method for two purposes, coming from syndicates and coming from syndicate users
		// initialise method
		$session = session();
		$syndicate_model = new Syndicate_Model();
		$identity_model = new Identity_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		$transcription_model = new Transcription_Model();
		$detail_data_model = new Detail_Data_Model();
		$session->result = '';
		
		// get inputs
		if ( $this->request->getPost('BMD_syndicate_index') !== null )
			{
				// get syndicate
				$BMD_syndicate_index = $this->request->getPost('BMD_syndicate_index');
				$selected_syndicate = $syndicate_model
					->where('BMD_syndicate_index',  $BMD_syndicate_index)
					->where('project_index',  $session->current_project[0]['project_index'])
					->find();
				if ( ! $selected_syndicate )
					{
						$session->set('message_2', 'Invalid syndicate, please select again.');
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url('syndicate/manage_syndicates/2') );
					}
			}
			
		if ( $this->request->getPost('BMD_identity_index') !== null )
			{
				// get identity
				$BMD_identity_index = $this->request->getPost('BMD_identity_index');
				$selected_identity = $identity_model
					->where('BMD_identity_index', $BMD_identity_index)
					->where('project_index',  $session->current_project[0]['project_index'])
					->find();
				if ( ! $selected_identity )
					{
						$session->set('message_2', 'Invalid identity, please select again.');
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url('syndicate/manage_syndicates/2') );
					}
			}
			
		if ( $this->request->getPost('BMD_header_index') !== null )
			{
				// get transcription
				$BMD_header_index = $this->request->getPost('BMD_header_index');
				$selected_transcription = $transcription_model
					->where('BMD_header_index', $BMD_header_index)
					->where('project_index',  $session->current_project[0]['project_index'])
					->find();
				if ( ! $selected_transcription )
					{
						$session->set('message_2', 'Invalid transcription, please select again.');
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url('syndicate/show_all_transcriptions_step1/'.$session->saved_syndicate_index) );
					}
				// get details
				$selected_details = $detail_data_model
					->where('BMD_header_index', $BMD_header_index)
					->where('project_index',  $session->current_project[0]['project_index'])
					->find();
				if ( ! $selected_details )
					{
						$session->set('message_2', 'Since no lines have been transcribed for the selected Transcription, '.$selected_transcription[0]['BMD_file_name'].', there is no calibration to fix.');
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url('syndicate/show_all_transcriptions_step1/'.$session->saved_syndicate_index) );
					}
			}
			
		// get the cycle code
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		$session->set('BMD_cycle_text', $transcription_cycle_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'SYNDC')
			->find());
		
		// has user selected to look at user allocations from the All allocations menu?
		if ( $session->BMD_cycle_code == 'SYUSA' )
			{
				// if so set the cycle code accordingly
				$session->BMD_cycle_code = 'SYALU';
			}
			
		// has user selected to look at user transcriptions from the All transcriptions menu?
		if ( $session->BMD_cycle_code == 'SYUST' )
			{
				// if so set the cycle code accordingly
				$session->BMD_cycle_code = 'SYTRU';
			}
										
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NONES': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('syndicate/manage_syndicates/2') );
					break;
				case 'SYUSN': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('syndicate/manage_syndicates/2') );
					break;
				case 'SYNOU': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('syndicate/manage_syndicates/2') );
					break;
				case 'UPDCR': // toogle header credit line
					switch ($selected_syndicate[0]['BMD_syndicate_credit'])
						{
							case 'Y':
								$data =	[
											'BMD_syndicate_credit' => 'N',
										];
								$session->result = 'no_credit';
								break;
							case 'N':
								$data =	[
											'BMD_syndicate_credit' => 'Y',
										];
								$session->result = 'credit';
								break;
						}
					$syndicate_model->update($BMD_syndicate_index, $data);
					return redirect()->to( base_url('syndicate/manage_syndicates/0') );
					break;
				case 'SYNEN': // toogle new transcriber environment
					switch ($selected_syndicate[0]['new_user_environment'])
						{
							case 'TEST':
								$data =	[
											'new_user_environment' => 'LIVE',
										];
								$session->result = 'live';
								break;
							case 'LIVE':
								$data =	[
											'new_user_environment' => 'TEST',
										];
								$session->result = 'test';
								break;
						}
					$syndicate_model->update($BMD_syndicate_index, $data);
					return redirect()->to( base_url('syndicate/manage_syndicates/0') );
					break;
				case 'SYNVM': // toogle verify mode
					switch ($selected_syndicate[0]['verify_mode'])
						{
							case 'after':
								$data =	[
											'verify_mode' => 'onthefly',
										];
								$session->result = 'onthefly';
								break;
							case 'onthefly':
								$data =	[
											'verify_mode' => 'after',
										];
								$session->result = 'after';
								break;
						}
					$syndicate_model->update($BMD_syndicate_index, $data);
					return redirect()->to( base_url('syndicate/manage_syndicates/0') );
					break;
				case 'SYNUS': // Manage FreeComETT syndicate users
					$session->saved_syndicate_index = $BMD_syndicate_index;
					return redirect()->to( base_url('syndicate/manage_users_step1/'.$BMD_syndicate_index) );
					break;
				case 'SYENU': // toogle identity environment
					switch ($selected_identity[0]['environment'])
						{
							case 'TEST':
								$data =	[
											'environment' => 'LIVE',
										];
								break;
							case 'LIVE':
								$data =	[
											'environment' => 'TEST',
										];
								break;
						}
					$identity_model->update($BMD_identity_index, $data);
					return redirect()->to( base_url('syndicate/manage_users_step1/'.$session->saved_syndicate_index) );
					break;
				case 'SYVEU': // toogle identity verify mode
					switch ($selected_identity[0]['verify_mode'])
						{
							case 'after':
								$data =	[
											'verify_mode' => 'onthefly',
										];
								break;
							case 'onthefly':
								$data =	[
											'verify_mode' => 'after',
										];
								break;
						}
					$identity_model->update($BMD_identity_index, $data);
					return redirect()->to( base_url('syndicate/manage_users_step1/'.$session->saved_syndicate_index) );
					break;
				case 'SYNAL': // show all allocations this syndicate
					$session->saved_syndicate_index = $BMD_syndicate_index;
					return redirect()->to( base_url('syndicate/show_all_allocations_step1/'.$BMD_syndicate_index) );
					break;
				case 'SYNTR': // show all transcriptions this syndicate
					$session->saved_syndicate_index = $BMD_syndicate_index;
					return redirect()->to( base_url('syndicate/show_all_transcriptions_step1/'.$BMD_syndicate_index) );
					break;
				case 'SYCAL': // show all calibration this syndicate
					$session->saved_syndicate_index = $BMD_syndicate_index;
					return redirect()->to( base_url('transcribe/calibrate_reference_step0/0') );
					break;
				case 'SYALU': // show allocations for this selected user
					// save the coordinator details masquerade not on
					if ( $session->masquerade == 0 )
						{
							$session->coordinator_identity_userid = $session->identity_userid;
							$session->coordinator_identity_password = $session->identity_password;
							$session->coordinator_identity_emailid = $session->identity_emailid;
							$session->coordinator_BMD_identity_index = $session->BMD_identity_index;
							$session->coordinator_realname = $session->realname;
							$session->coordinator_data_entry_font = $session->data_entry_font;
							$session->coordinator_environment_user = $session->environment_user;
							$session->coordinator_role = $session->role;
							
							$session->masquerade = 1;
						}
					// make selected identity this session identity
					$session->BMD_identity_index = $selected_identity[0]['BMD_identity_index'];
					$session->identity_userid = $selected_identity[0]['BMD_user'];
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					return redirect()->to( base_url('allocation/manage_allocations/0'));
					break;
				case 'SYTRU': // show transcriptions for this selected user
					// save the coordinator details masquerade not on
					if ( $session->masquerade == 0 )
						{
							$session->coordinator_identity_userid = $session->identity_userid;
							$session->coordinator_identity_password = $session->identity_password;
							$session->coordinator_identity_emailid = $session->identity_emailid;
							$session->coordinator_BMD_identity_index = $session->BMD_identity_index;
							$session->coordinator_realname = $session->realname;
							$session->coordinator_data_entry_font = $session->data_entry_font;
							$session->coordinator_environment_user = $session->environment_user;
							$session->coordinator_role = $session->role;
							
							$session->masquerade = 1;
						}
					// make selected identity this session identity
					$session->BMD_identity_index = $selected_identity[0]['BMD_identity_index'];
					$session->identity_userid = $selected_identity[0]['BMD_user'];
					// message
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					return redirect()->to( base_url('transcribe/transcribe_step1/0'));
					break;
				case 'SYDEL': // delete user data
					$session->role_index = $selected_identity[0]['role_index'];
					$session->identity_index = $selected_identity[0]['BMD_identity_index'];
					$session->identity_user = $selected_identity[0]['BMD_user'];
					$session->caller = $session->_ci_previous_url;
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					return redirect()->to( base_url('database/delete_user_data_step2/1'));
					break;
				case 'SYFIX': // Fix calibration THIS transcription
					return redirect()->to( base_url('database/fix_calibration_step1/0/'.$BMD_header_index));
					break;
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised. Report to '.$session->linbmd2_email);
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('syndicate/manage_syndicates/0') );			
	}
	
	public function manage_users_step1($BMD_syndicate_index)
	{
		// initialise method
		$session = session();
		$identity_model = new Identity_Model();
		$syndicate_model = new Syndicate_Model();
		$allocation_model = new Allocation_Model();
		$transcription_model = new Transcription_Model();
		$roles_model = new Roles_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		$session->result = '';

		// get syndicate data
		$selected_syndicate = $syndicate_model
			->where('project_index',  $session->current_project[0]['project_index'])
			->where('BMD_syndicate_index', $BMD_syndicate_index)
			->find();
		
		// get all users attached to this syndicate - depends on project
		switch ($session->current_project[0]['project_name']) 
			{
				case 'FreeBMD':
					$db1 = \Config\Database::connect($session->syndicate_DB);
					$sql =	"	
								SELECT * 
								FROM SyndicateMembers
								WHERE SyndicateMembers.SyndicateID = '".$BMD_syndicate_index."'
								ORDER BY LOWER(SyndicateMembers.UserID)
							";
					$query = $db1->query($sql);
					$transcribers_in_syndicate = $query->getResultArray();
					break;
				case 'FreeREG':
					// define mongodb - see common helper
					define_environment(3);
					$mongodb = define_mongodb();
					$collection_userid = $mongodb['database']->selectCollection('userid_details');
					// get syndicate details from freecomett
					$syndicate = $syndicate_model
						->where('BMD_syndicate_index', $BMD_syndicate_index)
						->find();
					// get the userid_details record for this transcriber
					$transcribers_in_syndicate = $collection_userid->find
						(
							[
								'syndicate' => $syndicate[0]['BMD_syndicate_name']
							]
						)->toArray();
					break;
				case 'FreeCEN':
					break;
			}
																			
		// count number of transcribers
		$total_transcribers_in_syndicate = count($transcribers_in_syndicate);
		
		// read transcribers in syndicate to see if they are in FreeComETT
		$freecomett_transcribers = array();
		foreach ( $transcribers_in_syndicate as $transcriber )
			{
				// set user ID - depends on project
				switch ($session->current_project[0]['project_name']) 
					{
						case 'FreeBMD':
							$userid = $transcriber['UserID'];
							break;
						case 'FreeREG':
							$userid = $transcriber['userid'];
							break;
						case 'FreeCEN':
							break;
					}
					
				// get the freecomett identity
				$freecomett_identity = $identity_model
					->where('project_index',  $session->current_project[0]['project_index'])
					->where('BMD_User', $userid)
					->find();

				// found?
				if ( $freecomett_identity )
					{
						$freecomett_transcribers[$userid] = $freecomett_identity[0];
					}
			}
									
		// any found?
		$total_freecomett_transcribers = count($freecomett_transcribers); 
		if (  $total_freecomett_transcribers == 0 )
			{
				$session->set('message_2',  'There are no FreeComETT users attached to this syndicate.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('syndicate/manage_syndicates/1') );
			}

		// complete the data array
		foreach ( $freecomett_transcribers as $key => $transcriber )
			{
				// get the last allocation name
				$last_allocation = $allocation_model
					->where('project_index',  $session->current_project[0]['project_index'])
					->where('BMD_allocation_index',  $transcriber['last_allocation'])
					->find();
				// found
				if ( $last_allocation )
					{
						$freecomett_transcribers[$key]['last_allocation_name'] = $last_allocation[0]['BMD_allocation_name'];
					}
				else
					{
						$freecomett_transcribers[$key]['last_allocation_name'] = 'Transcriber Not Active';
					}
			
				// get the last transcription name
				$last_transcription = $transcription_model
					->where('project_index',  $session->current_project[0]['project_index'])
					->where('BMD_header_index',  $transcriber['last_transcription'])
					->find();
				// found
				if ( $last_transcription )
					{
						$freecomett_transcribers[$key]['last_transcription_name'] = $last_transcription[0]['BMD_file_name'];
					}
				else
					{
						$freecomett_transcribers[$key]['last_transcription_name'] = 'None';
					}
					
				// get the role name
				$role = $roles_model
					->where('role_index',  $transcriber['role_index'])
					->find();
				// found
				if ( $role )
					{
						$freecomett_transcribers[$key]['role_name'] = $role[0]['role_name'];
					}
				else
					{
						$freecomett_transcribers[$key]['role_name'] = 'None';
					}
			}
			
		// set session array
		$session->freecomett_transcribers = $freecomett_transcribers;

		// set message
		$session->set('message_2', $selected_syndicate[0]['BMD_syndicate_name'].' => Total Transcribers = '.$total_transcribers_in_syndicate.', of which Total FreeComETT transcribers = '.$total_freecomett_transcribers.'.');
		$session->set('message_class_2', 'alert alert-success');
		
		// show results
		echo view('templates/header');
		echo view('linBMD2/manage_syndicate_users');
		echo view('linBMD2/sortTableNew');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');
	}
	
	public function stop_masquerading()
	{
		// initialise method
		$session = session();
		
		// if masquerade flag on, 
		if ( $session->masquerade == 1 )
			{
				// restore saved coordinator details
				$session->identity_userid = $session->coordinator_identity_userid;
				$session->identity_password = $session->coordinator_identity_password;
				$session->identity_emailid = $session->coordinator_identity_emailid;
				$session->BMD_identity_index = $session->coordinator_BMD_identity_index;
				$session->realname = $session->coordinator_realname;
				$session->data_entry_font = $session->coordinator_data_entry_font;
				$session->environment_user = $session->coordinator_environment_user;
				$session->role = $session->coordinator_role;
				
				// turn masquerade off
				$session->masquerade = 0;
				
				// restore current user array
				
			}
			
		// return to trans home page
		return redirect()->to( base_url('transcribe/transcribe_step1/0') );
	}
	
	public function show_all_allocations_step1($syndicate_index)
	{
		// initialise method
		$session = session();
		$identity_model = new Identity_Model();
		$syndicate_model = new Syndicate_Model();
		$allocation_model = new Allocation_Model();
		
		// get syndicate
		$syndicate = $syndicate_model
			->where('project_index',  $session->current_project[0]['project_index'])
			->where('BMD_syndicate_index', $syndicate_index)
			->find();
			
		// found?
		if ( ! $syndicate )
			{
				$session->set('message_2',  'Your Syndicate cannot be found. Send a message to '.$session->linbmd2_email.' Error in Syndicate::show_all_allocations_step1');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('syndicate/manage_syndicates/1') );
			}
		
		// get all allocations for this syndicate
		$session->all_records = $allocation_model
			->join('identity', 'allocation.BMD_identity_index = identity.BMD_identity_index')
			->where('allocation.project_index',  $session->current_project[0]['project_index'])
			->where('BMD_syndicate_index', $syndicate_index)
			->orderby('identity.BMD_user', 'ASC')
			->find();

		// found?
		if ( ! $session->all_records )
			{
				$session->set('message_2',  'No Allocations found in this Syndicate.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('syndicate/manage_syndicates/1') );
			}
			
		// set message
		$session->set('message_1', 'Show ALL Allocations for Syndicate => '.$syndicate[0]['BMD_syndicate_name']);
		$session->set('message_class_1', 'alert alert-primary');
		$session->set('message_2', '');
		$session->set('message_class_2', '');		
			
		// show syndicates
		echo view('templates/header');
		echo view('linBMD2/manage_syndicate_allocations');
		echo view('linBMD2/sortTableNew');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');							
	}
	
	public function show_all_transcriptions_step1($syndicate_index)
	{
		// initialise method
		$session = session();
		$identity_model = new Identity_Model();
		$syndicate_model = new Syndicate_Model();
		$transcription_model = new Transcription_Model();
		
		// get syndicate
		$syndicate = $syndicate_model
			->where('project_index',  $session->current_project[0]['project_index'])
			->where('BMD_syndicate_index', $syndicate_index)
			->find();
			
		// found?
		if ( ! $syndicate )
			{
				$session->set('message_2',  'Your Syndicate cannot be found. Send a message to '.$session->linbmd2_email.' Error in '.__METHOD__);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('syndicate/manage_syndicates/1') );
			}
		
		// get all transcriptions for this syndicate
		$session->all_records = $transcription_model
			->join('identity', 'transcription.BMD_identity_index = identity.BMD_identity_index')
			->where('transcription.project_index',  $session->current_project[0]['project_index'])
			->where('BMD_syndicate_index', $syndicate_index)
			->orderby('identity.BMD_user', 'ASC')
			->find();

		// found?
		if ( ! $session->all_records )
			{
				$session->set('message_2',  'No Transcriptions found in this Syndicate.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('syndicate/manage_syndicates/1') );
			}
			
		// set message
		$session->set('message_1', 'Show ALL Transcriptions for Syndicate => '.$syndicate[0]['BMD_syndicate_name']);
		$session->set('message_class_1', 'alert alert-primary');
		//$session->set('message_2', '');
		//$session->set('message_class_2', '');		
			
		// show syndicates
		echo view('templates/header');
		echo view('linBMD2/manage_syndicate_transcriptions');
		echo view('linBMD2/sortTableNew');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');							
	}
}
