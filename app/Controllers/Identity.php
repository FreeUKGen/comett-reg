<?php namespace App\Controllers;

use App\Models\Identity_Model;
use App\Models\Submitters_Model;				
use App\Models\Parameter_Model;
use App\Models\Detail_Data_Model;
use App\Models\Detail_Comments_Model;
use App\Models\Transcription_Detail_Def_Model;
use App\Models\Transcription_Model;
use App\Models\Allocation_Model;
use App\Models\Syndicate_Model;
use App\Models\Messaging_Model;
use App\Models\Roles_Model;
use App\Models\Projects_Model;
use App\Models\Signins_Model;
use App\Models\Status_Codes_Model;
use App\Models\Allocation_Images_Model;

class Identity extends BaseController
{
	function __construct() 
	{
        helper('common');
        helper('transcription');
    }
	
	public function signin_step1($start_message)
		{
			// initialise
			$session = session();
			$messaging_model = new Messaging_Model();
			$parameter_model = new Parameter_Model();

			// initialise message
			$session->set('message_1', 'Welcome, please sign in.');
			$session->set('message_class_1', 'alert alert-primary');
			
			// is javascript enabled?
			if ( $session->javascript == 'disabled' )
				{
					return redirect()->to( base_url('home/no_javascript') );
				}
			
			if ( $start_message == 0 )
				{					
					// initialise
					if ( $session->session_expired == 1 )
						{
							$session->set('message_2', 'Your session has expired - Time out. Please sign in again to continue.');
							$session->set('message_class_2', 'alert alert-danger');
							$session->set('session_expired', 0);
						}
					else
						{
							$session->set('message_2', '');
							$session->set('message_class_2', '');
							
							// get today date
							$today = date("Y-m-d");
							// get message to show
							$session->current_message =	$messaging_model
								->where('project_index', $session->current_project['project_index'])
								->where('from_date <=', $today)
								->where('to_date >=', $today)
								->find();
							// set show message if any found
							if ( $session->current_message )
								{
									$session->show_message = 'show';
								}
							else
								{
									$session->show_message = '';
								}
								
							// get version from parameters
							$parameter = $parameter_model->where('Parameter_key', 'version')->findAll();
							$session->set('version', $parameter[0]['Parameter_value']);
							
							// load linbmd2 email
							$parameter = $parameter_model->where('Parameter_key', 'linbmd2_email')->findAll();
							$session->set('linbmd2_email', $parameter[0]['Parameter_value']);
						}
				}
			
			// show view
			//DS NEW
			echo view('templates/header-no-nav');
			echo view('linBMD2/new_signin');

			//echo view('templates/header');
			//echo view('linBMD2/signin');
		}
	
	public function signin_step2()
	{
		// initialise method
		$session = session();
		$identity_model = new Identity_Model();
		$syndicate_model = new Syndicate_Model();
		$submitters_model = new Submitters_Model();
		$parameter_model = new Parameter_Model();
		$session->signon_success = 0;
		
		// what OS is this?
		$agent = $this->request->getUserAgent();
		$currentPlatform = $agent->getPlatform();
		
		// build / update FreeComETT syndicate DB ; manage_syndicate_DB() in common_helper
		// frequency depends on parameter in Projects table, and can be different per project
		// calculate number of signons / syndicate refresh frequency ; get remainder
		$update = $session->current_project['signons_to_project'] % $session->current_project['syndicate_refresh'];
		if ( $update == 0 )
			{
				manage_syndicate_DB();
			}
			
		// get input and set session fields
		$session->set('identity_userid', $this->request->getPost('identity'));
		$session->set('identity_password', $this->request->getPost('password'));
		//DS Nov 25 - actual_x, actual_y not needed, but DB issue if session variables not set
//		$session->actual_x = $this->request->getPost('actual_x');
//		$session->actual_y = $this->request->getPost('actual_y');
		$session->actual_x = rand(1,1000);
		$session->actual_y = rand(1,1000);
	
		//@TODO DS temporary
		log_message('info', 'User:' . $this->request->getPost('identity'));
		log_message('info', 'Pwd:' . $this->request->getPost('password'));
		// log_message('info', 'FSV:' . print_r($session->freeukgen_source_values, true));
		
		// do I have a hash key from Freeukgen sources
		//@TODO DS disable this for now
		if ($session->freeukgen_source_values['hash'] == 'error' )
			{
				// this should never happen but, if it does, send error
				$session->set('message_2', 'A technical problem occurred. Cannot determine hash key. Send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Failed to retrieve hash key in Identity::signin_step2');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('identity/signin_step1/1') );
			}
				
		// hash the entered password
		$UserPassword_hash = hash_hmac('md5', $session->identity_password, $session->freeukgen_source_values['hash'], true);
		$UserPassword_base64 = rtrim(base64_encode($UserPassword_hash), '=');
		
		$session->UserPassword_base64 = $UserPassword_base64;
	
		// validate depending on project
		switch ($session->current_project['project_name']) 
			{
				case 'FreeBMD':
					// find identity entered by user
					$db = \Config\Database::connect($session->project_DB);
					$sql = 	"
								SELECT * 
								FROM Submitters 
								WHERE UserID = '".$session->identity_userid."'
							";
					$query = $db->query($sql);
					$session->submitter = $query->getResultArray();	
			
					// was it found?
					if ( ! $session->submitter )
						{
							$session->set('message_2', 'The identity you entered is not defined for '.$session->current_project['project_name'].'. Please ensure that you have entered your Identity correctly.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('identity/signin_step1/1') );
						}

					// are hashes same?
					if ( $session->submitter[0]['Password'] != $UserPassword_base64 )
						{
							$session->set('message_2', 'The password you entered is not valid for your identity '.$this->request->getPost('identity').'. Please ensure that you have entered your Identity and password correctly.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('identity/signin_step1/1') );
						}

					// is user active? except for me.
					if ( $session->submitter[0]['NotActive'] == 1 AND $this->request->getPost('identity') != 'dreamstogo' )
						{
							$get_date = getdate($session->submitter[0]['NotActiveDate']);
							$not_active_date = $get_date['mday'].'-'.$get_date['month'].'-'.$get_date['year'];
							$session->set('message_2', 'Your '.$session->current_project['project_name'].' account has been suspended on '.$not_active_date.' for this reason, '.$session->submitter[0]['NotActiveReason'].'. Please contact your coordinator.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('identity/signin_step1/1') );
						}
						
					// has the user completed registration ?
					if ( $session->submitter[0]['NewlyEntered'] == 1 )
						{
							$session->set('message_2', 'You don\'t appear to have completed your registration with FreeBMD by responding to the confirmation email sent to you. Please complete your registration to continue to use FreeBMD.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('identity/signin_step1/1') );
						}
					
					// get the syndicate(s) this user is attached to
					$session->syndicateID = '';
					$db1 = \Config\Database::connect($session->syndicate_DB);
					$sql =	"	
								SELECT * 
								FROM SyndicateMembers 
								JOIN SyndicateTable
								ON SyndicateTable.SyndicateID = SyndicateMembers.SyndicateID
								WHERE SyndicateMembers.UserID = '".$session->identity_userid."'
								ORDER BY SyndicateTable.SyndicateName
							";
					$query = $db1->query($sql);
					$session->project_user_syndicates = $query->getResultArray();
				
					// do syndicate checks				
					// any found?
					if ( ! $session->project_user_syndicates )
						{
							$session->set('message_2', 'You do not appear to be a member of any syndicates. Please contact your coordinator.');
							$session->set('message_class_2', '');
							return redirect()->to( base_url('identity/signin_step1/1') );
						}
						
					// multiple syndicates?
					if ( count($session->project_user_syndicates) > 1 )
						{
							// this method will set the $session->current_syndicate if there are mutiple syndicates
							// and return to signon_step_3
							return redirect()->to( base_url('identity/signin_select_syndicate') );
						}
					else
						{
							// set current syndicate if only one syndicate
							$session->current_syndicate = $syndicate_model
								->where('project_index', $session->current_project['project_index'])
								->where('BMD_syndicate_index', $session->project_user_syndicates[0]['SyndicateID'])
								->find();
						}		
					break;
					
				case 'FreeREG':
					// $session->project_DB is defined on Projects.php and comes from the projects table
					// define mongodb - see common helper
					// define whether we are looking for the test or live server access
					define_environment(2);
					$mongodb = define_mongodb();
				
					// define userid_details collection (need curly brackets because of _ in collection name)
					$collection_userid = $mongodb['database']->selectCollection('userid_details');

					// define syndicate collection
					$collection_syndicates = $mongodb['database']->selectCollection('syndicates');
		
					// get the userid_details record for this transcriber
					$session->submitter = $collection_userid->find
						(
							[
								'userid' => $session->identity_userid
							]
						)->toArray();

// log_message('info', 'DSa: submitter:' . print_r($collection_userid, true));
// log_message('info', 'DSb: submitter:' . print_r($session->submitter, true));

					// was it found?
					if ( ! $session->submitter )
						{
							$session->set('message_2', 'The identity you entered is not defined for '.$session->current_project['project_name'].' on the '.$session->environment.' server. Please ensure that you have entered your Identity correctly. If you are sure, please contact your co-ordinator to make sure that your userid is set up on this server.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('identity/signin_step1/1') );
						}
			
					// are hashes same?


					 if (!$this->validPassword($session))
						{
							log_message('info', 'Invalid password for ' . $this->request->getPost('identity'));
							$session->set('message_2', 'The password you entered is not valid for your identity '.$this->request->getPost('identity').'. Please ensure that you have entered your Identity and password correctly.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('identity/signin_step1/1') );
						}
					else
						log_message('info', 'User authenticated');
						
					// is user active? except for me.
					if ( $session->submitter[0]['active'] == false AND $this->request->getPost('identity') != 'freeregdev' )
						{
							$session->set('message_2', 'Your '.$session->current_project['project_name'].' account is not active
							. Please contact your coordinator.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('identity/signin_step1/1') );
						}
												
					// get the syndicate(s) this user is attached to
					$session->project_user_syndicates = $collection_syndicates->find
						(
							[
								'syndicate_code' => $session->submitter[0]['syndicate']
							]
						)->toArray();
				
					// do syndicate checks				
					// any found?
					if ( ! $session->project_user_syndicates )
						{
							$session->set('message_2', 'You do not appear to be a member of any syndicates. Please contact your coordinator.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('identity/signin_step1/1') );
						}
					
					// multiple syndicates?
					// transcribers can only be in one syndicate in FreeREG
					
					// set current syndicate if only one syndicate
					$id = $session->project_user_syndicates[0]['_id']->__toString();
					$session->current_syndicate = $syndicate_model
						->where('project_index', $session->current_project['project_index'])
						->where('BMD_syndicate_index', $id)
						->find();

					// none found?
					if ( ! $session->current_syndicate )
						{
							// refresh FreeComETT syndicates - maybe new ones have been created
							manage_syndicate_DB();
							// try again
							$session->current_syndicate = $syndicate_model
								->where('project_index', $session->current_project['project_index'])
								->where('BMD_syndicate_index', $id)
								->find();
							// none found ?
							if ( ! $session->current_syndicate )
								{
									// send error
									$session->set('message_2', 'You do not appear to be a member of any syndicates. Please contact your coordinator.');
									$session->set('message_class_2', 'alert alert-danger');
									return redirect()->to( base_url('identity/signin_step1/1') );
								}
						}
					break;
					
				case 'FreeCEN':
					break;
			}	
				
		return redirect()->to( base_url('identity/signin_step3') );
	}


     // @TODO DS temporary hack so i can do work
	private function validPassword($session)
	{
		$ident = $this->request->getPost('identity');
		$passwd = $this->request->getPost('password');
	    // if ($ident == 'test' && $passwd == 'ds122') return true;
		
	    if ($session->submitter[0]['password'] == $session->UserPassword_base64)
			return true;
	    return false;
	}

	private function imageServer() :string
	{
		return getenv('app.imageServer') ?? $session->freeukgen_source_values['image_server'];
	}

	public function signin_step3()
	{
		// initialise method
		$session = session();
		$identity_model = new Identity_Model();
		$syndicate_model = new Syndicate_Model();
		$allocation_model = new Allocation_Model();
		$submitters_model = new Submitters_Model();
		$parameter_model = new Parameter_Model();
		$projects_model = new Projects_Model();
		$signins_model = new Signins_Model();
		$status_codes_model = new Status_Codes_Model();
		$allocation_images_model = new Allocation_Images_Model();
		
		// set flags
		$session->signon_success = 1;
		$new_user = 0;
		$session->masquerade = 0;
		
		// update number of signons this syndicate in order to know when to next update syndicates table
		$signons = $session->current_project['signons_to_project'] + 1;
		$projects_model
			->where('project_index', $session->current_project['project_index'])
			->set(['signons_to_project' => $signons])
			->update();

		// get user identity in FreeComETT by using the UserID
		$session->current_identity = $identity_model
			->where('project_index', $session->current_project['project_index'])
			->where('BMD_user', $session->identity_userid)
			->find();
										
		// found?
		if ( ! $session->current_identity )
			{					
				// No?, so add it
				// set user defaults for this session, this syndicate
				// is this user a syndicate leader? default is no = ordinary transcriber
				$user_role = 4;
				switch ($session->current_project['project_name']) 
					{
						case 'FreeBMD':	
							// is the person signing on a coordinator
							if ( $session->project_user_syndicates[0]['CoOrdinator'] == 'Y' )
								{
									$user_role = '2';
								}				
							break;
						
						case 'FreeREG':
							// is the person signing on a coordinator
							if ( $session->identity_userid == $session->project_user_syndicates[0]['syndicate_coordinator'] )
								{
									$user_role = '2';
								}
							break;
							
						case 'FreeCEN':
							break;
					}
				
				// user environment and verify flag
				$user_env = 'LIVE';
				$user_ver = 'onthefly';
				if ( $session->current_syndicate )
					{
						$user_env = $session->current_syndicate[0]['new_user_environment'];
						$user_ver = $session->current_syndicate[0]['verify_mode'];
					}
				
				// add record - most fields are provided by DB default definitions.
				$identity_model
					->set(['project_index' => $session->current_project['project_index']])
					->set(['BMD_user' => $session->identity_userid])
					->set(['environment' => $user_env])
					->set(['verify_mode' => $user_ver])
					->set(['role_index' => $user_role])
					->insert();
		
				// create user folder and subfolders if they don't exist
				$userPath = getenv('app.userDir');
				$userPath .= '/' . $session->identity_userid; 
				if (!is_dir($userPath)) { 
					if (mkdir($userPath)) {
						mkdir($userPath . '/Backups');
						mkdir($userPath . '/Scans');
						mkdir($userPath . '/CSV_Files');
					}
					else {
						log_message('error', "Could not setup user directories");
						exit(2);
					}
					// set_new user_flag
					$new_user = 1;
				}				
			}
		else
			{
				// if found verify directories exist
				// create user folder and subfolders if they don't exist
				$userDir = getenv('app.userDir');
				if (!$userDir) {
					log_message('error', '.env does not have app.userDir setting');
					exit(1);
				}

				$userDir = $userDir . '/' . $session->identity_userid;
				if (!is_dir($userDir))
					mkdir($userDir, 0777, true);
				if (!is_dir($userDir . '/Backups'))
					mkdir($userDir . '/Backups');
				if (!is_dir($userDir . '/CSV_Files'))
					mkdir($userDir . '/CSV_Files');
				if (!is_dir($userDir . '/Scans'))
					mkdir($userDir . '/Scans');
			}
		
		// signon is validated - WOW! At last!
		
		// get the identity
		$session->current_identity = $identity_model
			->where('project_index', $session->current_project['project_index'])
			->where('BMD_user', $session->identity_userid)
			->find();
			
		// set identity session parms depending on project
		switch ($session->current_project['project_name']) 
			{
				case 'FreeBMD':	
					// set session variables
					$session->identity_emailid = $session->submitter[0]['EmailID'];
					$session->realname = $session->submitter[0]['GivenName'].' '.$session->submitter[0]['Surname'];
					$session->total_records = $session->submitter[0]['TotalEntries'];
					$session->BMD_identity_index = $session->current_identity[0]['BMD_identity_index'];
					break;
					
				case 'FreeREG':
					// set session variables
					$session->identity_emailid = $session->submitter[0]['email_address'];
					$session->realname = $session->submitter[0]['person_forename'].' '.$session->submitter[0]['person_surname'];
					$session->total_records = $session->submitter[0]['number_of_records'];
					$session->BMD_identity_index = $session->current_identity[0]['BMD_identity_index'];
					
					// define mongodb - see common helper
					define_environment(3);
					$mongodb = define_mongodb();
					
					// load FreeREG assignments == FreeComETT allocations
					// there may be new, unchanged, removed assignments
					
					// define collections required
					$collection_assignments = $mongodb['database']->selectCollection('assignments');
					$collection_sources = $mongodb['database']->selectCollection('sources');
					$collection_image_server_images = $mongodb['database']->selectCollection('image_server_images');
					$collection_image_server_groups = $mongodb['database']->selectCollection('image_server_groups'); 
					$collection_registers = $mongodb['database']->selectCollection('registers');
					$collection_churches = $mongodb['database']->selectCollection('churches');
					$collection_places = $mongodb['database']->selectCollection('places');
					
					// here is the table logic for getting all info to create the allocation in FreeComETT
					// assignments 'source_id' => sources 'register_id' => register 'church_id' => churches 'place_id' => places
					
					// get the assignments for this transcriber
					$current_assignments = $collection_assignments->find
						(
							[
								'userid_detail_id' => $session->submitter[0]['_id'],
								'source_id' => ['$exists' => true],
								'freecomett' => ['$exists' => false]
							]
						)->toArray();
					
					// any found?					
					if ( $current_assignments )
						{
							// read the assignments
							foreach ( $current_assignments as $assignment )
								{
									// does this assignment exist in freecomett allocations table?
									$id = $assignment['_id']->__toString();
									$allocation = $allocation_model
										->where('project_index', $session->current_project['project_index'])
										->where('REG_assignment_id', $id)
										->find();
								
									// not found? add it
									if ( ! $allocation )
										{
											// doesn't exist so get all the data required as per logic above.
											// source
											$assignment_source = $collection_sources->find(['_id' => $assignment['source_id']])->toArray();
											if ( ! $assignment_source )
												{
													$session->set('message_2', 'Assignment source cannot be found for this assignment => '.$assignment['_id'].'. Please contact your coordinator.');
													$session->set('message_class_2', 'alert alert-danger');
													return redirect()->to( base_url('identity/signin_step1/1') );
												}
											// register
											$assignment_register = $collection_registers->find(['_id' => $assignment_source[0]['register_id']])->toArray();
											if ( ! $assignment_register )
												{
													$session->set('message_2', 'Assignment register cannot be found for this assignment => '.$assignment['_id'].'. Please contact your coordinator.');
													$session->set('message_class_2', 'alert alert-danger');
													return redirect()->to( base_url('identity/signin_step1/1') );
												}
											// church
											$assignment_church = $collection_churches->find(['_id' => $assignment_register[0]['church_id']])->toArray();
											if ( ! $assignment_church )
												{
													$session->set('message_2', 'Assignment church cannot be found for this assignment => '.$assignment['_id'].'. Please contact your coordinator.');
													$session->set('message_class_2', 'alert alert-danger');
													return redirect()->to( base_url('identity/signin_step1/1') );
												}
											// place
											$assignment_place = $collection_places->find(['_id' => $assignment_church[0]['place_id']])->toArray();
											if ( ! $assignment_place )
												{
													$session->set('message_2', 'Assignment place cannot be found for this assignment => '.$assignment['_id'].'. Please contact your coordinator.');
													$session->set('message_class_2', 'alert alert-danger');
													return redirect()->to( base_url('identity/signin_step1/1') );
												}								
											// get the image server images for this assignment, sorted by image file name
											$assignment_images = $collection_image_server_images->find([ 'assignment_id' => $assignment['_id'] ],[ 'sort' => ['image_file_name' => 1] ])->toArray();
											if ( ! $assignment_images )
												{
													$session->set('message_2', 'Images records cannot be found for this assignment => '.$assignment['_id'].'. Please contact your coordinator.');
													$session->set('message_class_2', 'alert alert-danger');
													return redirect()->to( base_url('identity/signin_step1/1') );
												}
											// get the image server group record for these images
											// use the image group id from the first image record
											$assignment_group = $collection_image_server_groups->find(['_id' => $assignment_images[0]['image_server_group_id']])->toArray();
											if ( ! $assignment_group )
												{
													$session->set('message_2', 'Image group cannot be found for this assignment => '.$assignment['_id'].'. Please contact your coordinator.');
													$session->set('message_class_2', 'alert alert-danger');
													return redirect()->to( base_url('identity/signin_step1/1') );
												}
									
											// build allocation data
											// start page and end page
											$start_page = 1;
											$end_page = count($assignment_images);
												
											// add it to the freecomett allocations table
											$allocation_model
												->set(['project_index' => $session->current_project['project_index']])
												->set(['BMD_identity_index' => $session->current_identity[0]['BMD_identity_index']])
												->set(['BMD_syndicate_index' => $session->current_syndicate[0]['BMD_syndicate_index']])
												->set(['BMD_allocation_name' => $assignment_group[0]['group_name']])
												->set(['BMD_reference' => $assignment_source[0]['folder_name']])
												->set(['BMD_start_date' => date('d-M-Y', strtotime($assignment['assign_date']))]) 
												->set(['BMD_end_date' => ''])
												->set(['BMD_start_page' => $start_page])
												->set(['BMD_last_uploaded' => null])
												->set(['BMD_end_page' => $end_page])
												->set(['BMD_year' => $assignment_group[0]['start_date']])
												->set(['BMD_quarter' => 0])
												->set(['BMD_letter' => ''])
												->set(['BMD_type' => 'C']) // C = Composite, ie assignment could contain any event type.
												->set(['BMD_scan_type' => 'jpg'])
												->set(['BMD_last_action' => 'Create '.$session->current_project['allocation_text']])
												->set(['BMD_status' => 'Open'])
												->set(['BMD_sequence' => 'SEQUENCED'])
												->set(['data_entry_format' => 'composite'])
												->set(['scan_format' => 'FreeREG'])
												->set(['REG_assignment_id' => $id])
												->set(['REG_county_group' => $assignment_place[0]['country']])
												->set(['REG_county' => $assignment_place[0]['county']])
												->set(['REG_chapman_code' => $assignment_place[0]['chapman_code']])
												->set(['REG_place' => $assignment_place[0]['place_name']])
												->set(['REG_church_name' => $assignment_church[0]['church_name']])
												->set(['REG_church_code' => $assignment_church[0]['church_code']])
												->set(['REG_register_type' => $assignment_register[0]['register_type']])
												->set(['REG_image_folder_name' => $assignment_source[0]['folder_name']])
												->set(['source_code' => 'FS'])
												->insert();
											
											// get the insert key
											$allocation_index = $allocation_model->getInsertID();
										
											// load allocation record
											$session->current_allocation = $allocation_model
												->where('project_index', $session->current_project['project_index'])
												->where('BMD_allocation_index', $allocation_index)
												->find();
												
											// since this assignment was created, I need to create the assignment image records
											// these are used later on when creating the transcription
											// read the images
											$allocation_index = $allocation_model->getInsertID();
											$image_count = 0;
											foreach ( $assignment_images as $assignment_image )
												{
													$image_count = $image_count + 1;
													$image_params = array
														(
															'chapman_code' => $assignment_place[0]['chapman_code'],
															'folder_name' => $assignment_source[0]['folder_name'],
															'image_file_name' => $assignment_image['image_file_name'],
														);

													$image_url = $session->freeukgen_source_values['image_server']
																.'manage_freereg_images/'
																.'view?'
																.http_build_query($image_params);
													
													$allocation_images_model
														->set(['project_index' => $session->current_project['project_index']])
														->set(['allocation_index' => $allocation_index])
														->set(['transcription_index' => NULL])
														->set(['identity_index' => $session->current_identity[0]['BMD_identity_index']])
														->set(['image_id' => $assignment_image['_id']])
														->set(['original_image_file_name' => $assignment_image['image_file_name']])
														->set(['image_file_name' => $assignment_image['image_file_name']])
														->set(['image_url' => $image_url])
														->set(['image_status' => $assignment_image['status']])
														->set(['trans_start_date' => NULL])
														->set(['trans_complete_date' => NULL])	
														->insert();
												}
												
											// asignment has been created
											
											// set document comment
											$session->document_comment = '';
											$session->document_source = '';
											
											// 20241023 - FreeREG FreeComETT steering group decided to automatically create the transcription for an assignment rather than asking the user to do so.
											// call create transcription in Transcription Helper and then return here.
											FreeREG_create_transcription_package($session->current_allocation[0]);
										}
									else
										{
											// update it
											$allocation_model
												->where('project_index', $session->current_project['project_index'])
												->where('BMD_allocation_index', $allocation[0]['BMD_allocation_index'])
												->set(['BMD_syndicate_index' => $session->current_syndicate[0]['BMD_syndicate_index']])
												->update();
										}	
								}
							
							// get all allocations created from backend and test to see if they are still assignments.
							$allocations = $allocation_model
								->where('BMD_identity_index', $session->BMD_identity_index)
								->where('project_index', $session->current_project['project_index'])
								->where('source_code', 'FS')
								->findAll();
								
							// any found
							if ( $allocations )
								{
									// read allocations
									foreach ( $allocations as $allocation )
										{
											// what is the status of this allocation?
											$assignment_group = $collection_image_server_groups->find
												(
													[
														'group_name' => $allocation['BMD_allocation_name']
													]
												)->toArray();
											// not found ?
											if ( ! $assignment_group )
												{
													// update allocation as closed
													$allocation_model
														->set(['BMD_status' => 'Closed'])
														->where('BMD_allocation_index', $allocation['BMD_allocation_index'])
														->update();
												}
											// found
											else
												{
													// get status code
													if ( isset($assignment_group[0]['summary']['status'][0]) ) 
														{
															$status = $assignment_group[0]['summary']['status'][0];
														}
													else
														{
															$status = 'bt';
														}
													$allocation_status = $status_codes_model
														->where('project_index', $session->current_project['project_index'])
														->where('status_project_code', $status)
														->find();											
													if ( $allocation_status )
														{
															// update allocation status code
															$allocation_model
																->set(['BMD_status' => $allocation_status[0]['status_freecomett_code']])
																->where('BMD_allocation_index', $allocation['BMD_allocation_index'])
																->update();
														}
												}				
										}
								}
						}			
					break;
				case 'FreeCEN':
					break;
			}
	
		// set others
		$session->data_entry_font = $session->current_identity[0]['default_dataentryfont'];
		$session->environment_user = $session->current_identity[0]['environment'];
		$session->role = $session->current_identity[0]['role_index'];
		$session->syndicate_name = $session->current_syndicate[0]['BMD_syndicate_name'];
		$session->syndicate_id = $session->current_syndicate[0]['BMD_syndicate_index'];
		
		// add record to signins this user
		$signins_model
			->set(['identity_index' => $session->BMD_identity_index])
			->set(['identity_role' => $session->role])
			->set(['syndicate_index' => $session->current_syndicate[0]['BMD_syndicate_index']])
			->set(['signin_x' => $session->actual_x])
			->set(['signin_y' => $session->actual_y])
			->insert();
		
		// is there an update in progress?
		$update_in_progress = $parameter_model->where('Parameter_key', 'updateinprogress')->find();
		// if update in progress and user role is not DBADMIN stop
		if ( $update_in_progress[0]['Parameter_value'] == 'YES' AND $session->current_identity[0]['role_index'] != 1 )
			{
				// update in progress message
				return redirect()->to( base_url('home/update_in_progress') );
			}	
										
		// Can I reach the image server and get an image?			
		
		// do I have an image server access from Freeukgen sources
		$server = $this->imageServer();
		if (!$server || ($server == 'error'))
		{
			// this should never happen but, if it does, send error
			$session->set('message_2', 'A technical problem occurred. Cannot determine image server. Send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Failed to retrieve image server URL in Identity::signin_step3');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('identity/signin_step1/1') );
		}
				
		// setup curl by trying to DL an image - any image will do
		switch ($session->current_project['project_name']) 
			{
				case 'FreeBMD':
				
					$curl_url =	$session->freeukgen_source_values['image_server']
								.'GUS/1870/Marriages/December/ANC-05/'
								.'1870M4-M-0185.jpg';

					$ch = curl_init($curl_url);
					curl_setopt($ch, CURLOPT_USERPWD, "$session->identity_userid:$session->identity_password");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
											
					// do the curl
					$curl_result = curl_exec($ch);
					curl_close($ch);
				
					// anything found
					if ( $curl_result == '' )
						{
							// problem so send error message
							$session->set('message_2', 'A technical problem occurred. Is your browser blocking access to images? Send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Failed to reach Image Server in Identity::signin_step3');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('identity/signin_step1/1') );
						}
				
					// load returned data to array
					$lines = preg_split("/\r\n|\n|\r/", $curl_result);
					
					// now test to see if a valid page was found
					foreach($lines as $line)
						{
							if ( strpos($line, "404 Not Found") !== false )
								{
									$session->set('message_2', 'A technical problem occurred. Please send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Malformed URL in Identity::signin_step3 for FreeBMD');
									$session->set('message_class_2', 'alert alert-danger');
									return redirect()->to( base_url('identity/signin_step1/1') );
								}
						}
					break;
					
				case 'FreeREG':			
					$server = $this->imageServer();
					if (!$server || $server == 'error')
					{
						// this should never happen but, if it does, send error
						$session->set('message_2', 'A technical problem occurred. Cannot determine image server access. Send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Failed to retrieve image server access in Identity::signin_step3');
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url('identity/signin_step1/1') );
					}
					
					// set up the cURL
					$curl_params = array
						(
							'chapman_code' => 'GLS',
							'folder_name' => 'Mickleton PR',
							'image_file_name' => 'Mickleton-BU-1590-1640_025.jpg',
							'userid' => $session->identity_userid,
							'id' => '65eb34b9f493fda1d6ee24d3',
							'image_server_access' => $session->freeukgen_source_values['image_server_access'],
						);

					$curl_url = $session->freeukgen_source_values['image_server']
								.'manage_freereg_images/'
								.'view?'
								.http_build_query($curl_params);

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $curl_url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					
					// and execute it
					$curl_response = curl_exec($ch);
					curl_close($ch);
					
					// test the reponse
					if ( str_contains($curl_response, 'Errors')) 
						{
							$session->set('message_2', 'A technical problem occurred. Please send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Malformed URL in Identity::signin_step3 for FreeREG');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('identity/signin_step1/1') );
						}
					break;
					
				case 'FreeCEN':	
					break;
			}
		
			
		// OK I can access image server
		
		// A word about environment
		// The environment tells FreeComETT whether to use TEST or LIVE servers.
		// It can be set 
		// - at application level in the parameters table
		// - at project level in the projects table
		// - at user level in the identity table. A new user is always added as TEST until his coordinator moves him to LIVE.
		// Thus this is a hierachy
		// Global first, then project, then user
		// method is in common_helper
		define_environment(3);
		
		// set curl_url for upload
		switch ($session->environment) 
			{
				case 'LIVE':
					$session->set('curl_url', $session->current_project['project_autouploadurllive']);
					break;
				case 'TEST':
					$session->set('curl_url', $session->current_project['project_autouploadurltest']);
					break;
				default:
					$session->set('curl_url', $session->current_project['project_autouploadurltest']);
					break;
			}
		
		// set open or closed transcription flag - in this case open
		$session->status = '0';
		// set open or closed allocation flag - in this case open
		$session->allocation_status = 'Open';

		// load global variables - function is in common helper
		load_variables();
		
		// redirect
		if ( $new_user == 1 )
			{
				// show help if new user
				$session->set('message_2', 'Hello '.$session->realname.', welcome to FreeComETT! This is your first time here, so please start by reading the help. It will help you! If in doubt, choose the first option.');
				$session->set('message_class_2', 'alert alert-info');
								
				// send email to coordinator if new user
				return redirect()->to(base_url('email/send_email/new_user') );
				
				// redirection to help happens in the email function
			}
		else
			{				
				// show transcriptions
				$session->set('message_2', '');
				$session->set('message_class_2', '');
				return redirect()->to( base_url('transcribe/transcribe_step1/0') );
			}
	}
	
	public function admin_user_step1($start_message)
	{		
		// initialise method
		$session = session();
		$roles_model = new Roles_Model();
		
		// set values
		switch ($start_message) 
			{
				case 0:
					// initialise values
					$session->set('admin_user', '');
					// message defaults
					$session->set('message_1', 'Change FreeComETT user role for FreeComETT user.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					// load rights
					$session->available_roles =	$roles_model
													->where('role_precedence >=', $session->current_identity[0]['role_index'])
													->orderby('role_precedence')
													->findAll();
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Change FreeComETT user role for FreeComETT user.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}	
	
		echo view('templates/header');
		echo view('linBMD2/admin_user');
		echo view('templates/footer');
	}
	
	public function admin_user_step2()
	{		
		// initialise method
		$session = session();
		$model = new Identity_Model();
		
		// get user data
		$session->set('identity', $this->request->getPost('identity'));
		$session->set('role_index', $this->request->getPost('role'));
		
		// find identity entered by user
		$identity = $model
					->where('project_index', $session->current_project['project_index'])
					->where('BMD_user', $session->identity)
					->find();
		// was it found?
		if ( ! $identity )
			{
				$session->set('message_2', 'This Identity you entered is not registered in the current project.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('identity/admin_user_step1/1') );
			}
			
		$data = [
					'role_index' => $session->role_index,
				];
		$model->update($identity[0]['BMD_identity_index'], $data);
				
		// go back
		$session->set('message_2', 'The user role has been changed.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('identity/admin_user_step1/1') );
	}
	
	public function change_details_step2($start_message)
	{
		// initialise method
		$session = session();
		$identity_model = new Identity_Model();
		
		// get identity record
		$session->current_identity = $identity_model
			->where('BMD_user', $session->identity_userid)
			->find();
			
		// set verify mode text
		switch ($session->current_identity[0]['verify_mode']) 
			{
				case 'after':
					$session->verify_mode_text = 'Verify after Transcription is complete using the Verify Module';
					break;
				case 'onthefly':
					$session->verify_mode_text = 'Verify line-by-line in the Transcription Module';
					break;
				default:
					$session->verify_mode_text = 'No Verify Mode specified';
					break;
			}
		
		if ( $start_message == 0 )
			{				
				$session->set('message_1', 'Change your Identity details for '.$session->current_project['project_name'].'.');
				$session->set('message_class_1', 'alert alert-primary');
				$session->set('message_2', '');
				$session->set('message_class_2', '');
			}
		
		// set inputs
		$session->set('identity', $session->identity_userid);			

		// show view
		$session->details_step = 3;
		echo view('templates/header');
		echo view('linBMD2/change_identity_step2');
		echo view('templates/footer');
	}	
	
	public function change_details_step3()
	{
		// initialise method
		$session = session();
		$identity_model = new Identity_Model();
		
		// get user data
		$session->set('verify_mode', $this->request->getPost('verify_mode'));
			
		// All good so update to database
		$data = [
					'verify_mode' => $session->verify_mode,
				];
		$identity_model->update($session->current_identity[0]['BMD_identity_index'], $data);
		
		// reload current identity and user
		$session->current_identity = 	$identity_model
										->where('project_index', $session->current_project['project_index'])
										->where('BMD_identity_index', $session->BMD_identity_index)
										->findAll();
		
		// go back to transcribe home
		$session->set('message_2', 'Your Identity has been changed on FreeComETT for this project '.$session->current_project['project_name'].'.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('transcribe/transcribe_step1/2') );
	}
	
	public function signin_select_syndicate()
	{
		// initialise method
		$session = session();
		
		// get syndicate
		$session->set('message_1', 'Please select the syndicate you want to work with the this FreeComETT session.');
		$session->set('message_class_1', 'alert alert-primary');
		$session->set('message_2', '');
		$session->set('message_class_2', '');

		echo view('templates/header');
		echo view('linBMD2/signin_select_syndicate');
	}
	
	public function signin_get_syndicate()
	{
		// initialise method
		$session = session();
		$syndicate_model = new Syndicate_Model();
		
		// set current syndicate
		$session->current_syndicate = $syndicate_model
			->where('project_index', $session->current_project['project_index'])
			->where('BMD_syndicate_index', $this->request->getPost('syndicate'))
			->find();
		
		// continue processing
		return redirect()->to( base_url('identity/signin_step3') );
	}
}
