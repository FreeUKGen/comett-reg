<?php namespace App\Controllers;

use App\Models\Districts_Model;
use App\Models\Parameter_Model;
use App\Models\Volumes_Model;
use App\Models\Firstname_Model;
use App\Models\Surname_Model;
use App\Models\Detail_Data_Model;
use App\Models\Header_Model;
use App\Models\Header_Table_Details_Model;
use App\Models\Allocation_Model;
use App\Models\Allocation_Images_Model;
use App\Models\Table_Details_Model;
use App\Models\Def_Fields_Model;
use App\Models\Def_Image_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Identity_Model;
use App\Models\Submitters_Model;
use App\Models\Detail_Comments_Model;
use App\Models\Transcription_Model;
use App\Models\Transcription_Comments_Model;
use App\Models\Transcription_Detail_Def_Model;
use App\Models\Transcription_CSV_File_Model;
use App\Models\Transcription_Current_Layout_Model;
use App\Models\Identity_Last_Indexes_Model;
use App\Models\Roles_Model;
use App\Models\Syndicate_Model;
use App\Models\Freeukgen_Sources_Model;

class Database extends BaseController
{
	function __construct() 
	{
        helper('common');
        helper('backup');
        helper('remote');
    }
	
	public function database_step1($start_message)
	{
		// initialise
		$session = session();
		
		switch ($start_message) 
			{
				case 0:
					// message defaults
					$session->set('message_1', 'Choose the Database action you wish to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Choose the Database action you wish to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
		
		// show views
		echo view('templates/header');
		echo view('linBMD2/database_menu');
		echo view('templates/footer');
	}
	
	public function coord_step1($start_message)
	{
		// initialise
		$session = session();
		
		switch ($start_message) 
			{
				case 0:
					// message defaults
					$session->set('message_1', 'Choose the COORDINATOR action you wish to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Choose the COORDINATOR action you wish to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
		
		// show views
		echo view('templates/header');
		echo view('linBMD2/coord_menu');
		echo view('templates/footer');
	}
			
	public function tester_step1($start_message)
	{
		// initialise
		$session = session();
		
		switch ($start_message) 
			{
				case 0:
					// message defaults
					$session->set('message_1', 'Choose the TESTER action you wish to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Choose the TESTER action you wish to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
		
		// show views
		echo view('templates/header');
		echo view('linBMD2/tester_menu');
		echo view('templates/footer');
	}
	
	public function manage_districts_step1($start_message)
	{
		// initialise
		$session = session();
		
		switch ($start_message) 
			{
				case 0:
					// message defaults
					$session->set('message_1', 'Choose the TESTER action you wish to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Choose the TESTER action you wish to perform.' );
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
		
		// show views
		echo view('templates/header');
		echo view('linBMD2/manage_districts');
		echo view('linBMD2/sortTableNew');
		echo view('templates/footer');
	}
	
	public function update_def_fields()
	{
		// initialise
		$session = session();
		$def_fields_model = new Def_Fields_Model();
		
		// read through standard defs
		foreach ( $session->current_transcription_def_fields as $key => $fields )
			{			
				// update standard defs table
				$def_fields_model	->where('project_index', $session->current_project[0]['project_index'])
									->where('data_entry_format', $session->current_transcription_def_fields[$key]['data_entry_format'])
									->where('scan_format', $session->current_transcription_def_fields[$key]['scan_format'])
									->where('field_name', $session->current_transcription_def_fields[$key]['field_name'])
									->set(['column_width' => $fields['column_width']])
									->set(['font_size' => $fields['font_size']])
									->set(['font_weight' => $fields['font_weight']])
									->set(['field_align' => $fields['field_align']])
									->set(['pad_left' => $fields['pad_left']])
									->set(['capitalise' => $fields['capitalise']])
									->set(['volume_roman' => $fields['volume_roman']])
									->update();
			}
		
		// reload standard defs
		$session->standard_def =	$def_fields_model	
									->where('project_index', $session->current_project[0]['project_index'])
									->where('syndicate_index', $session->current_transcription[0]['BMD_syndicate_index'])
									->where('data_entry_format', $session->current_allocation[0]['data_entry_format'])
									->where('scan_format', $session->current_allocation[0]['scan_format'])
									->orderby('field_order','ASC')
									->find();
									
		// go to data entry
		return redirect()->to( base_url(base_url($session->controller.'/transcribe_'.$session->controller.'_step1/0')) );	
	}
	
	public function update_image_values()
	{
		// initialise
		$session = session();
		$def_image_model = new Def_Image_Model();

		// does this image set exist
		$image =	$def_image_model
					->where('project_index', $session->current_project[0]['project_index'])
					->where('syndicate_index', $session->current_transcription[0]['BMD_syndicate_index'])
					->where('data_entry_format', $session->current_transcription_def_fields[0]['data_entry_format'])
					->where('scan_format', $session->current_transcription_def_fields[0]['scan_format'])
					->find();
	
		// was a record found?
		if ( $image )
			{
				// found so update
				$def_image_model	->where('project_index', $session->current_project[0]['project_index'])
									->where('data_entry_format', $session->current_transcription_def_fields[0]['data_entry_format'])
									->where('scan_format', $session->current_transcription_def_fields[0]['scan_format'])
									->set(['image_x' => $session->current_transcription[0]['BMD_image_x']])
									->set(['image_y' => $session->current_transcription[0]['BMD_image_y']])
									->set(['image_rotate' => 0])
									->set(['image_scroll_step' => $session->current_transcription[0]['BMD_image_scroll_step']])
									->set(['panzoom_x' => $session->current_transcription[0]['BMD_panzoom_x']])
									->set(['panzoom_y' => $session->current_transcription[0]['BMD_panzoom_y']])
									->set(['panzoom_z' => $session->current_transcription[0]['BMD_panzoom_z']])
									->set(['sharpen' => $session->current_transcription[0]['BMD_sharpen']])
									->update();
			}
		else
			{
				// not found so insert
				$def_image_model	->set(['project_index' => $session->current_project[0]['project_index']])
									->set(['data_entry_format' => $session->current_transcription_def_fields[0]['data_entry_format']])
									->set(['scan_format' => $session->current_transcription_def_fields[0]['scan_format']])
									->set(['image_x' => $session->current_transcription[0]['BMD_image_x']])
									->set(['image_y' => $session->current_transcription[0]['BMD_image_y']])
									->set(['image_rotate' => 0])
									->set(['image_scroll_step' => $session->current_transcription[0]['BMD_image_scroll_step']])
									->set(['panzoom_x' => $session->current_transcription[0]['BMD_panzoom_x']])
									->set(['panzoom_y' => $session->current_transcription[0]['BMD_panzoom_y']])
									->set(['panzoom_z' => $session->current_transcription[0]['BMD_panzoom_z']])
									->set(['sharpen' => $session->current_transcription[0]['BMD_sharpen']])
									->insert();
			}
		
		// go round again
		return redirect()->to( base_url($session->controller.'/transcribe_'.$session->controller.'_step1/0') );
	}
	
	public function firstnames()
	{
		// initialise
		$session = session();
		$firstname_model = new Firstname_Model();
		// get firstnames
		$session->set('names', $firstname_model->select('Firstname AS name')
																			->select('Firstname_popularity AS popularity')
																			->orderby('popularity', 'DESC')
																			->findAll());
		// show views
		$session->set('message_1', 'First names listed in descending order by popularity');
		$session->set('message_class_1', 'alert alert-primary');
		echo view('templates/header');
		echo view('linBMD2/show_names');
		echo view('linBMD2/transcribe_script');
		echo view('templates/footer');
	}
	
	public function surnames()
	{
		// initialise
		$session = session();
		$surname_model = new Surname_Model();
		// get surnames
		$session->set('names', $surname_model->select('Surname AS name')
																			->select('Surname_popularity AS popularity')
																			->orderby('popularity', 'DESC')
																			->findAll());
		// show views
		$session->set('message_1', 'Family names listed in descending order by popularity');
		$session->set('message_class_1', 'alert alert-primary');
		echo view('templates/header');
		echo view('linBMD2/show_names');
		echo view('linBMD2/transcribe_script');
		echo view('templates/footer');
	}
	
	public function database_backup()
	{
		// initialise
		$session = session();
		// do the backup
		database_backup();
		
		$session->set('message_2', 'The FreeComETT database has been backed up to your web user folder.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('housekeeping/index/2') );
	}
	
	public function merge_names()
	{		
		// initialise method
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		
		// get all details
		$detail_data = $detail_data_model	
			->findAll();
			
		// read data
		foreach ($detail_data as $detail_line) 
			{
				// merge second and third name to first name
				$detail_line['BMD_firstname'] = $detail_line['BMD_firstname'].' '.$detail_line['BMD_secondname'].' '.$detail_line['BMD_thirdname'];
				
				// update record
				$data =	[
							'BMD_firstname' => $detail_line['BMD_firstname'],
							'BMD_secondname' => '',
							'BMD_thirdname' => '',
						];
				$detail_data_model->update($detail_line['BMD_index'], $data);
			}
			
		// all done
		$session->set('message_2', 'Second and third names have been merged to first name.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('housekeeping/index/2') );	
	}
	
	public function create_header_data_entry_dimensions()
	{		
		// initialise method
		$session = session();
		$table_details_model = new Table_Details_Model();
		$header_table_details_model = new Header_Table_Details_Model();
		$header_model = new Header_Model();
		$allocation_model = new Allocation_Model();
		
		// read headers
		$headers = $header_model ->findall();
		
		foreach ($headers as $header)
			{
				// do data entry table dimensions axist already for this header
				$dimensions = $header_table_details_model
					->where('BMD_header_index', $header['BMD_header_index'])
					->find();
				
				// found?
				if ( ! $dimensions )
					{
						// get allocation
						$allocation = $allocation_model
							->where('BMD_allocation_index', $header['BMD_allocation_index'])
							->find();
						
						// allocation found?
						if ( $allocation )
							{
								// create the data entry table details
								// set format 
								// format change year depends on scan type = controller
								// default format = post
								$format = 'post';
								switch ($allocation[0]['BMD_type'])
									{
										case 'B':
											$controller = 'births';
											if ( $allocation[0]['BMD_year'] < 1993 )
												{
													$format = 'prior';
												}
											break;
										case 'D':
										$controller = 'deaths';
											if ( $allocation[0]['BMD_year'] < 1993 )
												{
													$format = 'prior';
												}
											break;
										case 'M':
										$controller = 'marriages';
											if ( $allocation[0]['BMD_year'] < 1994 )
												{
													$format = 'prior';
												}
											break;
										default:
											break;
									}
					
									// get the records
									$table_details = $table_details_model	
											->where('BMD_controller', $controller)
											->where('BMD_table_attr', 'body')
											->where('BMD_format', $format)
											->orderby('BMD_order','ASC')
											->find();
									// loop through table element by element and write the header specific table details
									foreach ($table_details as $td) 
										{ 
											// write to header table details
											$data =	[
													'BMD_header_index' => $header['BMD_header_index'],
													'BMD_table_details_index' => $td['BMD_index'],
													'BMD_header_span' => $td['BMD_span'],
													'BMD_header_align' => $td['BMD_align'],
													'BMD_header_pad_left' => $td['BMD_pad_left'],
													];
											$header_table_details_model->insert($data);
										}
							}
					}
			}
			
		// all done
		$session->set('message_2', 'Header data entry table dimensions have been created for all existing headers.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('housekeeping/index/2') );	
	}
	
	public function delete_user_data_step1($start_message)
	{		
		// initialise method
		$session = session();
		$identity_model = new Identity_Model();
		$roles_model = new Roles_Model();

		if ( $start_message == 0 )
			{
				$session->set('message_1', 'Delete ALL FreeComETT data for a transcriber (Allocations, Transcriptions, Scans, Uploaded CSVs etc). This does NOT delete the transcriber identity in FreeComETT and it does NOT delete the transcriber from the project in FreeGenealogy.');
				$session->set('message_class_1', 'alert alert-primary');
				$session->set('message_2', '');
				$session->set('message_class_2', '');
				$session->caller = $session->_ci_previous_url;
			}
			
		// get all identities this project
		$delete_ids = $identity_model
			->where('project_index', $session->current_project[0]['project_index'])
			->orderby('BMD_user')
			->findAll();

		// any found?
		if ( ! $delete_ids )
			{
				$session->set('message_2', 'No Identities found in this project => '.$session->current_project[0]['project_name']);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/delete_user_data_step1/1') );
			}
			
		// set session array
		$session->delete_ids = $delete_ids;
		
		// show view
		echo view('templates/header');
		echo view('linBMD2/identity_delete_step1');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');
	}
	
	public function delete_user_data_step2($start_message)
	{		
		// initialise method
		$session = session();
		
		// get input
		if ( $start_message == 0 )
			{
				$session->identity_index = $_POST['identity_index'];
				$session->identity_user = $_POST['identity_user'];
			}
		
		// show view
		echo view('templates/header');
		echo view('linBMD2/identity_delete_step2');
		echo view('templates/footer');
	}
	
	public function delete_user_data_step3()
	{		
		// initialise method
		$session = session();
		$model = new Identity_Model();

		// is the password valid for current user
		if ( $session->identity_password != $_POST['identity_pw'] )
			{
				$session->set('message_2', 'You requested to delete data for, '.$session->identity_user.', in project, '.$session->current_project[0]['project_name'].', but the confirmation password you entered is not correct for your administrator identity.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/delete_user_data_step2/1') );
			}
			
		// ok, delete confirmed	
		// delete data in directory tree
		$folders = array('Scans', 'CSV_Files', 'Backups');
		foreach ( $folders as $folder )
			{				
				// set path
				$path = getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_user.'/'.$folder.'/';
				
				// get files
				$files = glob($path.'*');
				foreach( $files as $file ) 
					{
						if( is_file($file) )
							{
								// delete files
								unlink($file);
							}
					}
					
				// delete folder
				if ( is_dir($path) )
					{
						rmdir($path);
					}
			}
		
		// delete user folder
		$path = getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_user.'/';
		// delete any files in this directory
		$files = glob($path.'*');
		foreach( $files as $file ) 
			{
				if( is_file($file) )
					{
						// delete files
						unlink($file);
					}
			}
		// now delete the directory
		if ( is_dir($path) )
			{
				rmdir($path);
			}
			
		// delete table entries for this project/user in all tables
		$models = array('Transcription_Detail_Def_Model', 'Transcription_Model', 'Transcription_Comments_Model', 'Detail_Data_Model', 'Detail_Comments_Model', 'Allocation_Model', 'Allocation_Images_Model', 'Transcription_CSV_File_Model', 'Transcription_Current_Layout_Model', );
		foreach ( $models as $model_name )
			{
				switch ($model_name) 
					{
						case 'Transcription_Detail_Def_Model':
							$model = new Transcription_Detail_Def_Model();
							$identity_field = 'identity_index';
							break;
						case 'Transcription_Comments_Model':
							$model = new Transcription_Comments_Model();
							$identity_field = 'identity_index';
							break;
						case 'Transcription_Model':
							$model = new Transcription_Model();
							$identity_field = 'BMD_identity_index';
							break;
						case 'Detail_Data_Model':
							$model = new Detail_Data_Model();
							$identity_field = 'BMD_identity_index';
							break;
						case 'Detail_Comments_Model':
							$model = new Detail_Comments_Model();
							$identity_field = 'BMD_identity_index';
							break;
						case 'Allocation_Model':
							$model = new Allocation_Model();
							$identity_field = 'BMD_identity_index';
							break;
						case 'Allocation_Images_Model':
							$model = new Allocation_Images_Model();
							$identity_field = 'identity_index';
							break;
						case 'Transcription_CSV_File_Model':
							$model = new Transcription_CSV_File_Model();
							$identity_field = 'identity_index';
							break;
						case 'Transcription_Current_Layout_Model':
							$model = new Transcription_Current_Layout_Model();
							$identity_field = 'identity_index';
							break;
					}

				$project_field = 'project_index';

				$table = $model
					->where($project_field, $session->current_project[0]['project_index'])
					->where($identity_field, $session->identity_index)
					->delete();
			}

		$session->set('message_2', 'All FreeComETT transcription data for, '.$session->identity_user.', in project, '.$session->current_project[0]['project_name'].', has been DELETED.');
		$session->set('message_class_2', 'alert alert-success');
		
		// return to caller
		if (str_contains($session->caller, 'syndicate')) 
			{
				return redirect()->to( base_url('database/coord_step1/1') );
			}
		else
			{
				return redirect()->to( base_url('database/database_step1/1') );
			}
	}
	
	public function add_syndicate_to_def_image_table()
	{
		// Def_images has been changed to include the syndicate. Need to create records for each syndicate using the NULL syndicate entries as base
		// initialise
		$session = session();
		$syndicate_model = new Syndicate_Model();
		$def_image_model = new Def_Image_Model();
		
		// get syndicates
		$syndicates = $syndicate_model
			->where('project_index', $session->current_project[0]['project_index'])
			->find();
		
		// read the syndicates one by one
		foreach ( $syndicates as $syndicate )
			{
				// read all def_image records with syndicate NULL
				$def_images = $def_image_model
					->where('project_index', $session->current_project[0]['project_index'])
					->where('syndicate_index', NULL )
					->find();
					
				// read def_images
				foreach ( $def_images as $def_image )
					{
						// does a record exist with this syndicate, data entry format and scan format
						$def_image_exists = $def_image_model
							->where('project_index', $session->current_project[0]['project_index'])
							->where('syndicate_index', $syndicate['BMD_syndicate_index'])
							->where('data_entry_format', $def_image['data_entry_format'])
							->where('scan_format', $def_image['scan_format'])
							->find();
							
						// found?
						if ( ! $def_image_exists )
							{
								// if not add it with the current syndicate
								$def_image_model
									->set(['project_index' => $session->current_project[0]['project_index']])
									->set(['syndicate_index' => $syndicate['BMD_syndicate_index']])
									->set(['data_entry_format' => $def_image['data_entry_format']])
									->set(['scan_format' => $def_image['scan_format']])
									->set(['image_x' => $def_image['image_x']])
									->set(['image_y' => $def_image['image_y']])
									->set(['image_rotate' => $def_image['image_rotate']])
									->set(['image_scroll_step' => $def_image['image_scroll_step']])
									->set(['panzoom_x' => $def_image['panzoom_x']])
									->set(['panzoom_y' => $def_image['panzoom_y']])
									->set(['panzoom_z' => $def_image['panzoom_z']])
									->set(['sharpen' => $def_image['sharpen']])
									->insert();
							}
					}
			}
			
		// send complete message and redirect
		$session->set('message_2', 'Def Image records have been added to Def Images Table for all syndicates in your project '.$session->current_project[0]['project_name'].'.' );
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('database/database_step1/1') );
	}
	
	public function add_syndicate_to_def_fields_table()
	{
		// Def_fields has been changed to include the syndicate. Need to create records for each syndicate using the NULL syndicate entries as base
		// initialise
		$session = session();
		$syndicate_model = new Syndicate_Model();
		$def_fields_model = new Def_Fields_Model();
		
		// get syndicates from project server
		switch ( $session->current_project[0]['project_name']) 
			{
				case 'FreeBMD':
					// get syndicates from server
					$db = \Config\Database::connect($session->syndicate_DB);
					$sql =	"
								SELECT * 
								FROM SyndicateTable 
								WHERE SyndicateTable.SyndicateShortDesc NOT LIKE 'This syndicate is no longer active having completed its agreed allocations.'
								ORDER BY SyndicateTable.SyndicateID
							";
					$query = $db->query($sql);
					$project_syndicates = $query->getResultArray();
					break;
				case 'FreeREG':
					break;
				case 'FreeCEN':
					break;
			}
		
		// read the syndicates one by one
		foreach ( $project_syndicates as $syndicate )
			{
				// read all def_image records with syndicate NULL
				$def_fields = $def_fields_model
					->where('project_index', $session->current_project[0]['project_index'])
					->where('syndicate_index', NULL )
					->find();
					
				// read def_images
				foreach ( $def_fields as $def_field )
					{
						// does a record exist with this syndicate, data entry format and scan format
						$def_field_exists = $def_fields_model
							->where('project_index', $session->current_project[0]['project_index'])
							->where('syndicate_index', $syndicate['SyndicateID'])
							->where('data_entry_format', $def_field['data_entry_format'])
							->where('scan_format', $def_field['scan_format'])
							->where('field_order', $def_field['field_order'])
							->where('field_name', $def_field['field_name'])
							->find();
							
						// found?
						if ( ! $def_field_exists )
							{
								// if not add it with the current syndicate
								$def_fields_model
									->set(['project_index' => $session->current_project[0]['project_index']])
									->set(['syndicate_index' => $syndicate['SyndicateID']])
									->set(['data_entry_format' => $def_field['data_entry_format']])
									->set(['scan_format' => $def_field['scan_format']])
									->set(['field_order' => $def_field['field_order']])
									->set(['field_name' => $def_field['field_name']])
									->set(['column_name' => $def_field['column_name']])
									->set(['column_width' => $def_field['column_width']])
									->set(['font_size' => $def_field['font_size']])
									->set(['font_weight' => $def_field['font_weight']])
									->set(['field_align' => $def_field['field_align']])
									->set(['pad_left' => $def_field['pad_left']])
									->set(['html_name' => $def_field['html_name']])
									->set(['html_id' => $def_field['html_id']])
									->set(['field_type' => $def_field['field_type']])
									->set(['blank_OK' => $def_field['blank_OK']])
									->set(['date_format' => $def_field['date_format']])
									->set(['volume_quarterformat' => $def_field['volume_quarterformat']])
									->set(['volume_roman' => $def_field['volume_roman']])
									->set(['table_fieldname' => $def_field['table_fieldname']])
									->set(['capitalise' => $def_field['capitalise']])
									->set(['dup_fieldname' => $def_field['dup_fieldname']])
									->set(['dup_fromfieldname' => $def_field['dup_fromfieldname']])
									->set(['special_test' => $def_field['special_test']])
									->set(['virtual_keyboard' => $def_field['virtual_keyboard']])
									->set(['input_first_line' => $def_field['input_first_line']])
									->set(['js_event' => $def_field['js_event']])
									->set(['js_function' => $def_field['js_function']])
									->set(['auto_full_stop' => $def_field['auto_full_stop']])
									->set(['auto_copy' => $def_field['auto_copy']])
									->set(['auto_focus' => $def_field['auto_focus']])
									->set(['colour' => $def_field['colour']])
									->set(['field_format' => $def_field['field_format']])
									->insert();
							}
					}
			}
			
		// send complete message and redirect
		$session->set('message_2', 'Def Field records have been added to Def Fields Table for all syndicates in your project '.$session->current_project[0]['project_name'].'.' );
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('database/database_step1/1') );
	}
	
	public function set_coord_role()
	{
		// initialise
		$session = session();
		$identity_model = new Identity_Model();
		
		// get FreeComETT identities
		$identities = $identity_model
			->where('project_index', $session->current_project[0]['project_index'])
			->find();
			
		// read identities
		foreach ( $identities as $identity )
			{
				// get syndicate member from server
				// define mongodb - see common helper
				// define whether we are looking for the test or live server access
				define_environment(2);
				$mongodb = define_mongodb();
				// define userid_details collection (need curly brackets because of _ in collection name)
				$collection_userid = $mongodb['database']->selectCollection('userid_details');
				// get the userid_details record for this transcriber
				$project_member = $collection_userid->find
					(
						[
							'userid' => $identity['BMD_user']
						]
					)->toArray();
				
				// found?
				if ( $project_member )
					{
						// coordinator?
						if ( $project_member[0]['person_role'] == 'county_coordinator' )
							{
								// set data for identity update
								$identity_model
									->where('project_index', $session->current_project[0]['project_index'])
									->where('BMD_identity_index', $identity['BMD_identity_index'])
									->where('role_index !=', 1)
									->set(['role_index' => 2])
									->update();
							}
					}
			}
		
		// set return
		$session->set('message_2', 'Coordinator role has been set for all existing coordinators.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('database/database_step1/1') );
	}
	
	public function fix_calibration_step1($start_message, $transcription_index)
	{
		// initialise method
		$session = session();
		$transcription_model = new Transcription_Model();
		$transcription_detail_def_model = new Transcription_Detail_Def_Model();
		$detail_data_model = new Detail_Data_Model();
		$allocation_model = new Allocation_Model();
		$session->fix_calib_index = $transcription_index;
	
		// set messages
		switch ($start_message) 
			{
				case 0:
					// get the transcription
					$session->fix_calib_transcription = $transcription_model
						->where('BMD_header_index', $session->fix_calib_index)
						->where('project_index',  $session->current_project[0]['project_index'])
						->find();
					// get transcription detail def
					$session->fix_calib_def_fields = $transcription_detail_def_model
						->where('transcription_index', $session->fix_calib_index)
						->where('project_index', $session->current_project[0]['project_index'])
						->orderby('field_order','ASC')
						->findAll();
					// get transcription details
					$session->fix_calib_details = $detail_data_model
						->where('BMD_header_index', $session->fix_calib_index)
						->where('project_index',  $session->current_project[0]['project_index'])
						->orderby('BMD_line_sequence','ASC')
						->findAll();
					$last_line_key = array_key_last($session->fix_calib_details);
					$session->fix_calib_last_lineno = $session->fix_calib_details[$last_line_key]['BMD_line_sequence'];
					$first_line_key = array_key_first($session->fix_calib_details);
					$session->fix_calib_first_lineno = $session->fix_calib_details[$first_line_key]['BMD_line_sequence'];
					// get allocation
					$session->fix_calib_allocation = $allocation_model	
						->where('BMD_allocation_index',  $session->fix_calib_transcription[0]['BMD_allocation_index'])
						->where('project_index', $session->current_project[0]['project_index'])
						->find();
		
					// setup the image
					// set image parameters
					$session->set('panzoom_x', 1);
					$session->set('panzoom_y', 1);
					$session->set('panzoom_z', $session->fix_calib_transcription[0]['BMD_panzoom_z']);
					$session->image_y = 350;
					
					// set creds
					$user = rawurlencode($session->identity_userid);
					$mdp = rawurlencode($session->identity_password);
					
					// set servertype and URL
					$server_split = explode('//', $session->freeukgen_source_values['image_server']);
							
					// initialse image			
					$url = 	$image_split[0]
							.'//'
							.$user
							.':'
							.$mdp
							.'@'
							.$image_split[1]
							.$session->fix_calib_allocation[0]['BMD_reference']
							.$session->fix_calib_transcription[0]['BMD_scan_name'];						
					$session->url = $url;
								
					// get image info to get mime type
					$imageInfo = getimagesize($url);
				
					// get mime type
					$session->mime_type = $imageInfo['mime'];
					
					// encode to base 64
					$session->fileEncode = base64_encode(file_get_contents($url));
					
					// message defaults
					$session->cols = 0;
					$session->set('message_1', 'Start by telling me how many columns are in the scan for this Transcription => '.$session->fix_calib_transcription[0]['BMD_file_name']);
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Start by telling me how many columns are in the scan for this Transcription => '.$session->fix_calib_transcription[0]['BMD_file_name']);
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}

		// request fix details
		// show views
		echo view('templates/header');
		echo view('linBMD2/fix_calibration_cols');
		echo view('linBMD2/transcribe_panzoom');
		echo view('templates/footer');		
	}
	
	public function fix_calibration_step2($start_message)
	{
		// initialise method
		$session = session();
		
		// get number of cols
		$session->cols = $this->request->getPost('columns');

		// test input
		if ( $session->cols < 2 OR $session->cols > 6 )
			{
				$session->set('message_2', 'Number of columns must be at least 2 and no more than 6.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/fix_calibration_step1/1/'.$session->fix_calib_index) );
			}
			
		// set messages
		switch ($start_message) 
			{
				case 0:
					// Initialise columns array - 6 max
					$columns = array();
					for( $i = 0; $i < $session->cols; $i++ ) 
						{
							$columns[$i]['column'] = $i + 1;
							if ( $i == 0 )
								{
									$columns[$i]['first_line'] = $session->fix_calib_first_lineno;
								}
							else
								{
									$columns[$i]['first_line'] = 0;
								}
							if ( $i == $session->cols - 1 )
								{
									$columns[$i]['last_line'] = $session->fix_calib_last_lineno;
								}
							else
								{
									$columns[$i]['last_line'] = 0;
								}
							$columns[$i]['panzoom_x'] = 0;
							$columns[$i]['panzoom_y'] = 0;
						}
					$session->columns = $columns;
					// message defaults
					$session->set('message_1', 'Fix the calibration for this Transcription => '.$session->fix_calib_transcription[0]['BMD_file_name']. ' <= by providing the data required in the table below.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Fix the calibration for this Transcription => '.$session->fix_calib_transcription[0]['BMD_file_name']. ' <= by providing the data required in the table below.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}

		// request fix details
		// show views
		echo view('templates/header');
		echo view('linBMD2/fix_calibration');
		echo view('templates/footer');		
	}
	
	public function fix_calibration_step3($start_message)
	{
		// initialise method
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		$transcription_model = new Transcription_Model();
		
		// get input
		$columns_input = array();
		$columns_input = json_decode($this->request->getPost('columns'), true);

		// read columns
		foreach ($columns_input as $column )
			{
				$this_column_first_line = 0;
				foreach ( $session->fix_calib_details as $line )
					{
						if ( $line['BMD_line_sequence'] >= $column['fl'] AND $line['BMD_line_sequence'] <= $column['ll'] )
							{
								if ( $this_column_first_line == 0 )
									{
										$new_py = $column['py'];
										$this_column_first_line = 1;
									}
								else
									{
										$new_py = $new_py - $session->fix_calib_transcription[0]['BMD_image_scroll_step'];
									}
								$detail_data_model
									->set(['BMD_line_panzoom_x' => $column['px']])
									->set(['BMD_line_panzoom_y' => $new_py])
									->where('BMD_index', $line['BMD_index'])
									->update();
							}
					}
			}
		
		// reload details
		$session->fix_calib_details = $detail_data_model
			->where('BMD_header_index', $session->fix_calib_index)
			->where('project_index',  $session->current_project[0]['project_index'])
			->orderby('BMD_line_sequence','ASC')
			->findAll();
		$last_line_key = array_key_last($session->fix_calib_details);
		$session->fix_calib_last_px = $session->fix_calib_details[$last_line_key]['BMD_line_panzoom_x'];
		$transcription_model
			->set(['BMD_panzoom_x' => $session->fix_calib_details[$last_line_key]['BMD_line_panzoom_x']])
			->set(['BMD_panzoom_y' => $session->fix_calib_details[$last_line_key]['BMD_line_panzoom_y']])
			->where('BMD_header_index', $session->fix_calib_details[$last_line_key]['BMD_header_index'])
			->update();
							
		// return
		$session->set('message_2', 'Fixed detail line image coordinates for => '.$session->fix_calib_transcription[0]['BMD_file_name']);
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('syndicate/show_all_transcriptions_step1/'.$session->saved_syndicate_index) );
	}
	
	public function freereg_build_datadictionary()
	{
		// this method builds the data dictionary for FreeREG. 
		// It can only be build once.
		// Once it has been built by this routine it cannot be auto built again and must be manually maintained.
		
		// initialise method
		$session = session();
		$sources_model = new Freeukgen_Sources_Model();
		$def_fields_model = new Def_Fields_Model();
		$parameter_model = new Parameter_Model();
		$detail_data_model = new Detail_Data_Model();
		
		// am I in FreeREG
		if ( $session->current_project[0]['project_name'] != 'FreeREG' )
			{
				$session->set('message_2', 'You MUST be in FreeREG for this option.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1'));
			}
			
		// has the routine been run already?
		//if ( $parameter_model->where('Parameter_key', 'freereg_build')->find()[0]['Parameter_value'] == 'DONE' )
			//{
				//$session->set('message_2', 'You have already run the Data Dictionary build. You cannot run it twice.');
				//$session->set('message_class_2', 'alert alert-danger');
				//return redirect()->to( base_url('database/database_step1/1'));
			//}

		// data dictionary definitions are in $session->freeukgen_source_values which is loaded at signin in Project controller
		// read field order for each type from Freeukgen_sources
		$field_orders =	$sources_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('source_purpose', 'dd_field_order')
			->findAll();
			
		// process fields or each field order record
		foreach ( $field_orders as $field_order )
			{
				// get field orders
				$field_defs = $session->freeukgen_source_values[$field_order['source_key']];
				
				// create array of field names, trim to remove spaces and transform to lower case
				$field_defs = explode('+', $field_defs);
				$field_defs_clean = array();
				foreach ( $field_defs as $key => $field_def )
					{
						$field_defs_clean[$key] = strtolower(trim($field_def));
					}
			
				// for each field def get field names
				$order = 0;
				foreach ( $field_defs_clean as $clean_def )
					{
						// get fields for this definition
						$fields = $session->freeukgen_source_values[$clean_def];

						// process fields and add to def_fields DB to create the data_dictionary
						foreach ( $fields as $field )
							{
								// transform to htmlid
								$htmlid = str_replace('_', '', $field);
								// get this field from DB
								$exists = $def_fields_model
									->where('project_index', $session->current_project[0]['project_index'])
									->where('syndicate_index', NULL)
									->where('data_entry_format', $field_order['source_def'])
									->where('html_id', $htmlid)
									->findall();
								
								// found?
								if ( ! $exists )
									{
										// field is new
										// construct names
										$fname = str_replace('_', ' ', $field);
										$fname = ucwords($fname);
										$cname = str_replace(' ', '', $fname);
										$hname = str_replace('_', '', $field);
										// construct field type
										$ftype = NULL;
										if ( str_contains($hname, 'name' )) $ftype = 'name';
										if ( str_contains($hname, 'date' )) $ftype = 'date';
										if ( str_contains($hname, 'place' )) $ftype = 'place';
										if ( str_contains($hname, 'title' )) $ftype = 'title';
										if ( str_contains($hname, 'county' )) $ftype = 'county';
										if ( str_contains($hname, 'sex' )) $ftype = 'sex';
										if ( str_contains($hname, 'notes' )) $ftype = 'notes';
										if ( str_contains($hname, 'occupation' )) $ftype = 'occupation';
										if ( str_contains($hname, 'parish' )) $ftype = 'parish';
										if ( str_contains($hname, 'age' )) $ftype = 'age';
										// construct attributes
										$cwidth = 100;
										$cheight = 20;
										if ( $ftype == 'notes' )
											{
												$cwidth = 400;
												$cheight = 40;
											}													
										// insert record
										$def_fields_model
											->set(['project_index' => $session->current_project[0]['project_index']])
											->set(['syndicate_index' => NULL])
											->set(['data_entry_format' => $field_order['source_def']])
											->set(['scan_format' => 'FreeREG'])
											->set(['field_order' => $order])
											->set(['field_name' => $fname])
											->set(['column_name' => $cname])
											->set(['html_name' => $hname])
											->set(['html_id' => $hname])
											->set(['field_type' => $ftype])
											->set(['table_fieldname' => $field])
											->set(['capitalise' => 'First'])
											->set(['dup_fieldname' => 'dup_'.$hname])
											->set(['dup_fieldname' => 'dup_'.$hname])
											->set(['column_width' => $cwidth])
											->set(['column_height' => $cheight])
											->set(['blank_OK' => 'Y'])
											->insert();	
									}
							}
					}
			}
			
		// now make sure that the table detail_data has all the data fields.
		$forge = \Config\Database::forge();
		$db = db_connect();
		$fields_added = array();
		$def_columns = $def_fields_model
			->where('project_index', $session->current_project[0]['project_index'])
			->findall();
		$detail_columns = $db->getFieldNames('detail_data');
		foreach ( $def_columns as $def_column )
			{
				if ( ! in_array($def_column['table_fieldname'], $fields_added) )
					{
						// does this field already exist?
						if ( ! in_array($def_column['table_fieldname'], $detail_columns) )
							{
								// add it to the table if NOT
								if ( $def_column['field_type'] == 'notes' )
									{
										$column =	[
														$def_column['table_fieldname'] => ['type' => 'TEXT', 'null' => true,],
													];
									}
								else
									{
										$column =	[
														$def_column['table_fieldname'] => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true,],
													];
									}
								$forge->addColumn('detail_data', $column);
								// add it to added fields array to avoid duplicate fields
								$fields_added[] = $def_column['table_fieldname'];
							}
					}
			}
			
		// the detail_data model has been modified to load the detail fields when the model is initialised.		
		
		// update global data dictionary done
		$parameter_model
			->set(['Parameter_value' => 'DONE'])
			->where('Parameter_key', 'freereg_build')
			->update();
		
		// return to data dictionary management
		if ( count($fields_added) == 0 )
			{
				$session->set('message_2', 'No new fields found for FreeREG data dictionary. Please review the data dictionary.');
			}
		else
			{
				$session->set('message_2', 'The following new fields found for FreeREG data dictionary. Please review and correct the data dictionary, for these fields: ');
				foreach ( $fields_added as $field_added )
					{
						$session->message_2 = $session->message_2.$field_added.', ';
					}
			}
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('data_dictionary/manage_data_dictionary/2'));
	}
	
	public function list_all_churches()
	{
		// this method produces a list of all churches in CSV format
		// chapman_code, place_name, church_name, church_code
		
		// initialise method
		$session = session();
		define_environment(3);
		$mongodb = define_mongodb();
		$csv_string = '';
		$fields = [0 => '$chapman_code', 1 => '$place_name', 2 => '$church_name', 3 => '$church_code'];
		
		// am I in FreeREG
		if ( $session->current_project[0]['project_name'] != 'FreeREG' )
			{
				$session->set('message_2', 'You MUST be in FreeREG for this option.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1'));
			}
		
		// read all churches
		// get churches
		$churches = $mongodb['database']->selectCollection('churches')->find
			(
				[],
				['projection' => ['church_name' => 1, 'church_code' => 1, 'place_id' => 1]]
			)->toArray();		
		// read churches
		foreach ( $churches as $church )
			{
				$church_name = $church['church_name'];
				$church_code = $church['church_code'];
				// get place name
				$place = $mongodb['database']->selectCollection('places')->find
				(
					['_id' => $church['place_id']],
					['projection' => ['chapman_code' => 1, 'place_name' => 1]]
				)->toArray();
				// data found
				if ( $place )
					{
						$chapman_code = $place[0]['chapman_code'];
						$place_name = $place[0]['place_name'];
					}
				else
					{
						$chapman_code = '198';
						$place_name = 'No Place';
					}
				// add line to csv file
				$csv_string = $csv_string.'"'.$chapman_code.'","'.$place_name.'","'.$church_name.'","'.$church_code.'"'."\r\n";
			}
		
		// create CSV file
		$tmpf = getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_userid.'/CSV_Files/current_churches.csv';
		// delete it if it exists
		if ( file_exists($tmpf) ) unlink($tmpf);
		// open, load and close it
		$fh = fopen($tmpf, 'w+');
		fwrite($fh, $csv_string);
		fclose($fh);
		
		// return
		$session->set('message_2', 'Current churches listed to CSV file.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('database/database_step1/1'));
	}
}
