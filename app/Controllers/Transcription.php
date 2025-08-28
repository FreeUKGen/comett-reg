<?php namespace App\Controllers;

use App\Models\Transcription_Model;
use App\Models\Syndicate_Model;
use App\Models\Allocation_Model;
use App\Models\Allocation_Images_Model;
use App\Models\Identity_Model;
use App\Models\Identity_Last_Indexes_Model;
use App\Models\Transcription_Detail_Def_Model;
use App\Models\Def_Fields_Model;
use App\Models\Def_Image_Model;
use App\Models\Detail_Data_Model;
use App\Models\Detail_Comments_Model;
use App\Models\Transcription_Comments_Model;
use App\Models\Transcription_CSV_File_Model;
use App\Models\Data_Group_Model;
use App\Models\Document_Sources_Model;

class Transcription extends BaseController
{
	function __construct() 
	{
        helper('common');
        helper('report');
        helper('transcribe');
    }

	public function create_BMD_step1($start_message)
	{				
		// initialise method
		$session = session();
		$syndicate_model = new Syndicate_Model();
		$allocation_model = new Allocation_Model();
		$transcription_model = new Transcription_Model();
		$identity_last_indexes_model = new Identity_Last_Indexes_Model();
		
		// get transcriptions for this project in syndicate, allocation, transcription order
		$transcriptions =	$transcription_model
							->where('BMD_identity_index', $session->BMD_identity_index)
							->where('project_index', $session->current_project[0]['project_index'])
							->findAll();
		// were any found?
		if ( ! $transcriptions )
			{
				$transcriptions[0]['BMD_next_page'] = 0;
				$transcriptions[0]['BMD_file_name'] = "none";
				$transcriptions[0]['BMD_scan_name'] = "none";
			}

		// set values
		switch ($start_message) 
			{
				case 0:
					// initialise values
					// get identity last indexes
					$last_indexes = $identity_last_indexes_model
									->where('project_index', $session->current_project[0]['project_index'])
									->where('identity_index', $session->BMD_identity_index)
									->orderby('change_date', 'DESC')
									->find();
					// found?
					if ( $last_indexes )
						{
							// set last allocation index
							$session->set('last_allocation', $last_indexes[0]['allocation_index']);
							
							// set next page number this allocation
							$last_transcription =	$transcription_model
													->where('project_index', $last_indexes[0]['project_index'])
													->where('BMD_identity_index', $last_indexes[0]['identity_index'])
													->where('BMD_header_index', $last_indexes[0]['transcription_index'])
													->find();
							// found
							if ( $last_transcription )
								{
									$session->set('scan_page', $last_transcription[0]['BMD_next_page']);
								}
							else
								{
									$session->set('scan_page', 0);
								}					
						}
					else
						{
							// set allocation to none found
							$session->set('last_allocation', '999999');
							
							// set last page to zero
							$session->set('scan_page', 0);
						}
						
					$session->status = '0';
					$session->set('scan_page_suffix', '');
					$session->set('comment_text', '');
					$session->set('autocreate', 'Y');
					$session->set('scan_name', '');
					$session->set('make_current', 'Y');
					$session->set('reopen', 'Y');
					$session->set('view', 1);
					// message defaults
					$session->set('message_1', 'Start a new '.$session->current_project[0]['project_name'].' Transcription by selecting the Allocation it is attached to. Your last transcription was '.$transcriptions[0]['BMD_file_name'].', '.$transcriptions[0]['BMD_scan_name']);
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('field_name', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Start a new '.$session->current_project[0]['project_name'].' Transcription by selecting the Allocation it is attached to. Your last transcription was '.$transcriptions[0]['BMD_file_name'].', '.$transcriptions[0]['BMD_scan_name']);
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('view', 1);
					break;
				default:
			}
	
		echo view('templates/header');
		switch ($session->view) 
			{
				case 1:
					echo view('linBMD2/create_BMD_step1');
					break;
				default:
					break;
			}
		echo view('templates/footer');
	}
	
	public function create_BMD_step2()
	{
		// initialise method
		$session = session();
		$syndicate_model = new Syndicate_Model();
		$identity_model = new Identity_Model();
		$identity_last_indexes_model = new Identity_Last_Indexes_Model();
		$allocation_model = new Allocation_Model();
		$transcription_model = new Transcription_Model();
		$transcription_detail_def_model = new Transcription_Detail_Def_Model();
		$def_fields_model = new Def_Fields_Model();
		$detail_data_model = new Detail_Data_Model();
		$transcription_comments_model = new Transcription_Comments_Model();
		$def_image_model = new Def_Image_Model();
		
		// get inputs
		$session->set('allocation', $this->request->getPost('allocation'));
		$session->set('scan_page', $this->request->getPost('scan_page'));
		$session->set('scan_page_suffix', $this->request->getPost('scan_page_suffix'));
		$session->set('comment_text', $this->request->getPost('comment_text'));
		$session->set('autocreate', $this->request->getPost('autocreate'));
		$session->set('scan_name', $this->request->getPost('scan_name'));
		$session->set('make_current', $this->request->getPost('make_current'));
		
		// user wants to create a transcription
		// get allocation														
		$session->current_allocation = $allocation_model->where('BMD_allocation_index', $session->allocation)
														->where('project_index', $session->current_project[0]['project_index'])
														->find();
		if ( ! $session->current_allocation )
			{
				$session->set('message_2', 'You must select an allocation from the dropdown list.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('field_name', 'allocation');
				return redirect()->to( base_url('transcription/create_BMD_step1/1') );
			}
			
		// allocation selected?
		if ( $session->current_allocation[0]['BMD_allocation_index'] == '999999' )
			{
				$session->set('message_2', 'You must select an allocation from the dropdown list.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('field_name', 'allocation');
				return redirect()->to( base_url('transcription/create_BMD_step1/1') );
			}													
		
		// set $session->allocation_name so as to not have to reselect it on other errors
		$session->set('allocation_name', $session->current_allocation[0]['BMD_allocation_name']);
		$session->set('last_allocation', $session->current_allocation[0]['BMD_allocation_index']);
		
		// do tests
		// is start page numeric?
		if ( ! is_numeric($session->scan_page) )
			{
				$session->set('message_2', 'Scan page number must be numeric.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('field_name', 'scan_page');
				return redirect()->to( base_url('header/create_BMD_step1/1') );
			}
		
		// is scan page in allocation range?
		$result = filter_var	(
								$session->scan_page, 
								FILTER_VALIDATE_INT, 
								array	(
										'options' => array	(
															'min_range' => $session->current_allocation[0]['BMD_start_page'], 
															'max_range' => $session->current_allocation[0]['BMD_end_page']
															)
										)
								);
		if ( ! $result )
			{
				$session->set('message_2', 'Scan page number is not in the allocation page range => '.$session->current_allocation[0]['BMD_start_page']. ' to '.$session->current_allocation[0]['BMD_end_page'].'. Is your page number correct? Have you finished transcribing all Transcriptions in this allocation?');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('field_name', 'scan_page');
				return redirect()->to( base_url('transcription/create_BMD_step1/1') );
			}
		
		// comment entered
		if ( strlen($session->comment_text) > 100 )
			{
				$session->set('message_2', 'Please limit your comment text to 100 characters max.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('field_name', 'comment_text');
				return redirect()->to( base_url('transcription/create_BMD_step1/1') );
			}
			
		// autocreate scan name
		if ( $session->autocreate == 'Y' AND $session->scan_name != '' )
			{
				$session->set('message_2', 'If auto create scan name is Yes, you must leave the scan name blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('field_name', 'scan_name');
				return redirect()->to( base_url('transcription/create_BMD_step1/1') );
			}
		if ( $session->autocreate == 'N' AND $session->scan_name == '' )
			{
				$session->set('message_2', 'If auto create scan name is No, you must enter a scan name.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('field_name', 'scan_name');
				return redirect()->to( base_url('transcription/create_BMD_step1/1') );
			}
			
		// test scan name for file extension
		if ( $session->autocreate == 'N' AND $session->scan_name != '' )
			{
				$exploded_scan_name = explode('.', $session->scan_name);
				if ( ! isset($exploded_scan_name[1]) )
					{
						$session->set('message_2', 'Please enter the scan name with its file extension, eg .jpg');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'scan_name');
						return redirect()->to( base_url('transcription/create_BMD_step1/1') );
					}
			}
			
		// test suffix for alpha
		if ( $session->scan_page_suffix != '' )
			{
				// test for alpha
				if ( ! ctype_alpha($session->scan_page_suffix) )
					{
						$session->set('message_2', 'It looks like your scan page suffix is incorrect.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'scan_page_suffix');
						return redirect()->to( base_url('transcription/create_BMD_step1/1') );
					}
			}
			
		// ok data input checks complete
		
		// Create the scan name if autocreate = yes
		// format 1938B1-F-0337.jpg or 1988B-D-0425.jpg or 1994B-A-001.jpg for births after 1993 (yes really!)
		if ( $session->autocreate == 'Y' )
			{
				// construct part1
				$session->set('scan_name',	$session->current_allocation[0]['BMD_year'].$session->current_allocation[0]['BMD_type']);
				
				// add quarter number if quarter based
				$exploded_scan_path = explode('/', $session->current_allocation[0]['BMD_reference']);
				$quarter_number = array_search($exploded_scan_path[3], $session->quarters_short_long);
				if ( $quarter_number ) 
					{
						// quarter was found
						$session->scan_name = $session->scan_name.$quarter_number;											
					}
					
				// construct the page number, gosh this is a pigs ear!
				$this->construct_scan_page_number('create');
				
				// construct part 2
				$session->set('scan_name', 	$session->scan_name.'-'.$session->current_allocation[0]['BMD_letter'].'-'.$session->scan_page_number.$session->scan_page_suffix.'.'.$session->current_allocation[0]['BMD_scan_type']);
			}
			
		// does this scan name already exist on a header?
		$session->set('transcription', $transcription_model	
			->where('BMD_scan_name', $session->scan_name)
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('project_index', $session->current_project[0]['project_index'])
			->findAll());	
		
		// found?
		if ( $session->transcription )
			{
				// is this header closed?
				if ( $session->transcription[0]['BMD_header_status'] == 1 )
					{
						// exist and closed
						$session->set('message_2', 'The scan '.$session->scan_name.' has already been transcribed by you and is closed. Perhaps you want to reopen it?');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('view', 2);
						return redirect()->to( base_url('transcription/create_BMD_step1/1') );
					}
				else
					{
						// exists and open
						$session->set('message_2', 'The scan '.$session->scan_name.' already exists on the transcription '.$session->transcription[0]['BMD_file_name'].' which is in your open list of transcriptions.');
						$session->set('message_class_2', 'alert alert-warning');
						return redirect()->to( base_url('transcribe/transcribe_step1/2') );
					}
			}
		
		// does the scan exist on the project image server?
		$curl_url =	$session->freeukgen_source_values['image_server']
					.$session->current_allocation[0]['BMD_reference']
					.$session->scan_name;
		
		// set up the curl
		$ch = curl_init($curl_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $session->identity_userid.':'.$session->identity_password);
		
		// do the curl
		$curl_result = curl_exec($ch);
		curl_close($ch);	
	
		// anything found
		if ( $curl_result == '' )
			{
				// problem so send error message
				$session->set('message_2', 'A technical problem occurred. Send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Failed to fetch scan in Transcription::create_BMD_step2 => '.$curl_url);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/create_BMD_step1/1') );
			}
			
		// load returned data to array
		$lines = preg_split("/\r\n|\n|\r/", $curl_result);
					
		// now test to see if a valid scan was found
		foreach($lines as $line)
			{
				if ( strpos($line, "404 Not Found") !== false )
					{
						$session->set('message_2', 'I cannot find the scan you requested on the image server. Please check your entries. To correct you might want to delete the Allocation and recreate it. Check in particular any suffix you entered and the page number. '.$curl_url.' => Does not exist.');
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url('transcription/create_BMD_step1/1') );
					}
			}
		
		if ( curl_exec($ch) === false )
			{
				// problem so send error message
				$session->set('message_2', 'A technical problem occurred. Send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Header::create_BMD_step2, around line 202 => '.$curl_url.' => '.curl_error($ch));
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('header/create_BMD_step1/1') );
			}
		
		// construct BMD file name with 4 digit page number
		
		// First, is scan page 4 digits long? = page number
		$scan_page_len = strlen($session->scan_page);
		// if not, make it four
		if ( $scan_page_len != 4 )
			{
				$session->scan_page = str_pad($session->scan_page, 4, "0", STR_PAD_LEFT);
			}
			
		// second - construct the file name
		// if multi-letter, make it first one.
		$letter = '';
		if ( strlen($session->current_allocation[0]['BMD_letter']) > 1 )
			{
				$letter = $session->current_allocation[0]['BMD_letter'][0];
			}
		else
			{
				// use letter from allocation
				$letter = $session->current_allocation[0]['BMD_letter'];
			}
		$name_elements = explode('-', $session->scan_name);
		$session->set('BMD_file_name', 	$name_elements[0].$letter.$session->scan_page.$session->scan_page_suffix);
		
		// does the file already exist in project
		$session->set('transcription', $transcription_model	
			->where('BMD_file_name', $session->BMD_file_name)
			->where('project_index', $session->current_project[0]['project_index'])
			->findAll());
				
		// found?
		if ( $session->transcription )
			{
				// is the header attached to current identity
				if ( $session->BMD_identity_index != $session->transcription[0]['BMD_identity_index'] )
					{
						$session->set('message_2', 'This transcription => '.$session->BMD_file_name.', already exists on '.$session->current_project[0]['project_name'].' but is being processed by a different transcriber.');
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url('transcription/create_BMD_step1/1') );
					}	
				// is this header closed?
				if ( $session->transcription[0]['BMD_header_status'] == 1 )
					{
						// exist and closed
						$session->set('message_2', 'The transcription '.$session->BMD_file_name.' has already been processed and is closed. Do you wish to reopen it?');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('view', 2);
						return redirect()->to( base_url('transcription/create_BMD_step1/1') );
					}
				else
					{
						// exists and open
						$session->set('message_2', 'The transcription '.$session->BMD_file_name.' already exists on transcription '.$session->transcription[0]['BMD_file_name'].' which is in your open list of transcriptions.');
						$session->set('message_class_2', 'alert alert-warning');
						return redirect()->to( base_url('transcription/create_BMD_step1/1') );
					}
			}
		
		// does this file name already exist on the project? uses common helper
		BMD_file_exists_on_project($session->BMD_file_name);
		if ( $session->BMD_file_exists_on_project == '1' )
			{
				$session->set('message_2', 'An upload with this name already exists in the '.$session->current_project[0]['project_name'].'. Verify your input data => '.$session->BMD_file_name.' or download the file to change it.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/create_BMD_step1/1') );
			}
		
		// get this image set
		$def_image = $def_image_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('syndicate_index', $session->current_allocation[0]['BMD_syndicate_index'])
			->where('data_entry_format', $session->current_allocation[0]['data_entry_format'])
			->where('scan_format', $session->current_allocation[0]['scan_format'])
			->find();

		// initialse image fields depending if found or not
		if ( $def_image )
			{			
				// found, so use initial image set
				$image_x = $def_image[0]['image_x'];
				$image_y = $def_image[0]['image_y'];
				$image_rotate = $def_image[0]['image_rotate'];
				$image_scroll_step = $def_image[0]['image_scroll_step'];
				$panzoom_x = $def_image[0]['panzoom_x'];
				$panzoom_y = $def_image[0]['panzoom_y'];
				$panzoom_z = $def_image[0]['panzoom_z'];
				$sharpen = $def_image[0]['sharpen'];
				$zoom_lock = 'Y';
				$calib_x = $def_image[0]['calib_x'];
				$calib_y = $def_image[0]['calib_y'];
			}
		else
			{
				// not found, set common defaults
				$image_x = 0;
				$image_rotate = 0;
				$panzoom_x = 1;
				$panzoom_y = 1;
				$panzoom_z = 1;
				$sharpen = 2;
				$calib_x = 1920;
				$calib_y = 1080;
				
				// set scan_format specific values
				switch ($session->current_allocation[0]['scan_format'])
					{
						case 'handwritten':
							$image_y = 60;
							$image_scroll_step = 50;
							break;
						case 'typed':
							$image_y = 45;
							$image_scroll_step = 40;
							break;
						case 'printed':
							$image_y = 45;
							$image_scroll_step = 40;
							break;
						default:
							$image_y = 45;
							$image_scroll_step = 40;
							break;
					}
			}
			
		// last indexes removed - see code snippets
					
		// set last action
		switch ( $session->current_project[0]['project_index'] )
			{
				case 1:
					$last_action = 'BMD file created';
					break;
				case 2:
					$last_action = 'CSV file created';
					break;
				case 3:
					$last_action = 'CSV file created';
					break;
			}
		// now, create the transcription in the database	
		$data =	[
					'project_index' => $session->current_project[0]['project_index'],
					'BMD_identity_index' => $session->BMD_identity_index,
					'BMD_allocation_index' => $session->allocation,
					'BMD_syndicate_index' => $session->current_allocation[0]['BMD_syndicate_index'],
					'current_data_entry_format' => $session->current_allocation[0]['data_entry_format'],
					'BMD_file_name' => $session->BMD_file_name,
					'BMD_scan_name' => $session->scan_name,
					'BMD_start_date' => $session->current_date,
					'BMD_end_date' => '',
					'BMD_submit_date' => '',
					'BMD_submit_status' => '',
					'BMD_submit_fail_message' => '',
					'BMD_current_page' => $session->scan_page,
					'BMD_current_page_suffix' => $session->scan_page_suffix,
					'BMD_next_page' => $session->scan_page + 1,
					'BMD_records' => 0,
					'BMD_last_action' => $last_action,
					'BMD_header_status' => '0',
					'BMD_image_x' => $image_x,
					'BMD_image_y' => $image_y,
					'BMD_image_rotate' => $image_rotate,
					'BMD_image_scroll_step' => $image_scroll_step,
					'BMD_panzoom_x' => $panzoom_x,
					'BMD_panzoom_y' => $panzoom_y,
					'BMD_panzoom_z' => $panzoom_z,
					'BMD_sharpen' => $sharpen,
					'BMD_font_family' => $session->data_entry_font,
					'zoom_lock' => $zoom_lock,
					'header_x' => $calib_x,
					'header_y' => $calib_y,
					'source_code' => 'BS',
				];
		// get the index of this transcription on insert
		$id = $transcription_model->insert($data);
		
		// if comment entered, add comment line entry
		if ( $session->comment_text != '' )
			{
				// load data
				$data =	[
							'transcription_index' => $id,
							'project_index' => $session->current_project[0]['project_index'],
							'identity_index' => $session->BMD_identity_index,
							'comment_sequence' => 10,
							'comment_text' => $session->comment_text,
							'source_text' => ' ',
						];
				
				// insert record
				$transcription_comments_model->insert($data);
			}
		
		// update identity with the last allocation and next page
		$data =	[
					'last_allocation' => $session->allocation,
					'last_transcription' => $id,
					'last_page_in_last_transcription' => $session->scan_page + 1,
				];
		$identity_model->update($session->BMD_identity_index, $data);
		
		// reload identity
		$session->current_identity = $identity_model	
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('project_index', $session->current_project[0]['project_index'])
			->find();
		
		// load the transcription data entry format table
		// create the detail def for this transcription
		// get any existing record
		$transcription_detail_def = $transcription_detail_def_model 
			->where('project_index', $session->current_project[0]['project_index'])
			->where('identity_index', $session->BMD_identity_index)
			->where('transcription_index', $id)
			->find();
									
		// well, since I am creating a new transcription, it shouldn't exist! If it does there's something wrong...
		// delete it if it exists
		if ( $transcription_detail_def )
			{
				$transcription_detail_def_model
					->where('project_index', $session->current_project[0]['project_index'])
					->where('identity_index', $session->BMD_identity_index)
					->where('transcription_index', $id)
					->delete();
			}
			
		// now create transcription detail def
		// 1) from the last transcription if found
		// 2) from the defaults set by the coordinator
		
		// set create from last detail def found flag
		$create_from_last = 0;
				
		// try to create detail def from identity last indexes
		// get last indexes
		$last_indexes = $identity_last_indexes_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('identity_index', $session->BMD_identity_index)
			->where('data_entry_format', $session->current_allocation[0]['data_entry_format'])
			->find();
			
		// found?
		if ( $last_indexes )
			{
				// detail def for last transcription, the data entry format found so use it to create detail def this transcription
				$last_detail_def = $transcription_detail_def_model 
					->where('project_index', $session->current_project[0]['project_index'])
					->where('identity_index', $session->BMD_identity_index)
					->where('transcription_index', $last_indexes[0]['transcription_index'])
					->where('data_entry_format', $session->current_allocation[0]['data_entry_format'])
					->where('scan_format', $session->current_allocation[0]['scan_format'])
					->find();
					
				// found?
				if ( $last_detail_def )
					{
						// set last detail def found flag
						$create_from_last = 1;
								
						// read details defs and insert to detail defs table
						foreach ( $last_detail_def as $ldf )
							{
								$transcription_detail_def_model	
									->set(['project_index' => $session->current_project[0]['project_index']])
									->set(['transcription_index' => $id])
									->set(['identity_index' => $session->BMD_identity_index])
									->set(['data_entry_format' => $ldf['data_entry_format']])
									->set(['scan_format' => $ldf['scan_format']])
									->set(['field_order' => $ldf['field_order']])
									->set(['field_name' => $ldf['field_name']])
									->set(['column_name' => $ldf['column_name']])
									->set(['column_width' => $ldf['column_width']])
									->set(['font_size' => $ldf['font_size']])
									->set(['font_weight' => $ldf['font_weight']])
									->set(['field_align' => $ldf['field_align']])
									->set(['pad_left' => $ldf['pad_left']])
									->set(['html_name' => $ldf['html_name']])
									->set(['html_id' => $ldf['html_id']])
									->set(['field_type' => $ldf['field_type']])
									->set(['blank_OK' => $ldf['blank_OK']])
									->set(['date_format' => $ldf['date_format']])
									->set(['volume_quarterformat' => $ldf['volume_quarterformat']])
									->set(['volume_roman' => $ldf['volume_roman']])
									->set(['table_fieldname' => $ldf['table_fieldname']])
									->set(['capitalise' => $ldf['capitalise']])
									->set(['dup_fieldname' => $ldf['dup_fieldname']])
									->set(['dup_fromfieldname' => $ldf['dup_fromfieldname']])
									->set(['special_test' => $ldf['special_test']])
									->set(['virtual_keyboard' => $ldf['virtual_keyboard']])
									->set(['js_event' => $ldf['js_event']])
									->set(['js_function' => $ldf['js_function']])
									->set(['auto_full_stop' => $ldf['auto_full_stop']])
									->set(['auto_copy' => $ldf['auto_copy']])
									->set(['auto_focus' => $ldf['auto_focus']])
									->set(['colour' => $ldf['colour']])
									->set(['field_popup_help' => $ldf['field_popup_help']])
									->insert();
							}
						
						// set message			
						$session->set('message_2',  'Your new transcription has been been created. The data entry format has been created from the last transription that you used. Start transcribing!');
					}
			}
				
			// no last detail defs found?
			if ( $create_from_last == 0 )
				{
					// no existing detail defs found so create from standard defs	
					// get the standard def
					$session->standard_def = $def_fields_model	
						->where('project_index', $session->current_project[0]['project_index'])
						->where('syndicate_index', $session->current_allocation[0]['BMD_syndicate_index'])
						->where('data_entry_format', $session->current_allocation[0]['data_entry_format'])
						->where('scan_format', $session->current_allocation[0]['scan_format'])
						->orderby('field_order','ASC')
						->find();
						
						// initialise data array
						$data = array();
			
						// read the standard def entries and create the data array for insert to transcription detail def records
						foreach ( $session->standard_def as $record )
							{
								// read through $record as it is an array
								foreach ( $record as $field_name => $field_value )
									{
										// create the insert record field by field
										$data[$field_name] = $field_value;
									}
								
								// remove record index to avoid duplicate primary indexes
								unset($data['field_index']);
								
								// set the additional fields not in standard def
								$data['transcription_index'] = $id;
								$data['identity_index'] = $session->BMD_identity_index;
								
								// insert the record
								$transcription_detail_def_model->insert($data);					
							}
							
						// set message
						$session->set('message_2', 'Your new transcription has been been created. The data entry format has been created from the standard data entry definition. Start transcribing!');
				}
				
		// return
		$session->set('message_class_2', 'alert alert-success');
		$session->set('reference_extension_control', '0');
		return redirect()->to( base_url('transcribe/transcribe_step1/0') );
	}
	
	public function reopen_BMD_step1($start_message)
	{
		// initialise method
		$session = session();
		
		switch ($start_message) 
			{
				case 0:
					// initialise values
					$session->set('BMD_file', '');
					$session->set('BMD_reopen_confirm', 'N');
					// message defaults
					$session->set('message_1', 'Enter the name of the Transcription you wish to reopen and confirm.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Enter the name of the Transcription you wish to reopen and confirm.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
		
		// show views
		echo view('templates/header');
		echo view('linBMD2/header_BMD_reopen');
		echo view('templates/footer');	
	}
		
	public function reopen_BMD_step2()
	{
		// initialise method
		$session = session();
		$transcription_model = new Transcription_Model();
		$transcription_detail_def_model = new Transcription_Detail_Def_Model();
		$allocation_model = new Allocation_Model();
		$detail_data_model = new Detail_Data_Model();
		$transcription_CSV_file_model = new Transcription_CSV_File_Model();
		$transcription_comments_model = new Transcription_Comments_Model();
		
		// get user input
		$session->set('BMD_file', $this->request->getPost('BMD_file'));
		$session->set('BMD_reopen_confirm', $this->request->getPost('BMD_reopen_confirm'));
		
		// did user confirm?
		if ($session->BMD_reopen_confirm == 'N') 
			{
					$session->set('message_2', 'You did not confirm to reopen this transcription '.$session->BMD_file.'. It is still closed.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('transcription/reopen_BMD_step1/1') );
			}
		
		// user confirmed
		// is the BMD file name blank?
		if ($session->BMD_file == '')
			{
				$session->set('message_2', 'Transcription name cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/reopen_BMD_step1/1') );
			}
		
		// does file exist in database?
		$reopen_transcription = $transcription_model	
			->where('BMD_file_name', $session->BMD_file)
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('project_index', $session->current_project[0]['project_index'])
			->findAll();		
		if ( ! $reopen_transcription )
			{
				$session->set('message_2', 'The Transcription, '.$session->BMD_file.', name you entered does not exist in your transcription database so it cannot be re-opened. If you have uploaded the transcription to your project, you can download it to FreeComETT and continue transcribing. See download option in the Create Transcription menu');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/reopen_BMD_step1/1') );
			}	
		
		// is it open?
		if ( $reopen_transcription[0]['BMD_header_status'] == '0' )
			{
				$session->set('message_2', 'The Transcription, '.$session->BMD_file.', is already open. Select it from your ACTIVE transcriptions or Close it and then Reopen it to update your FreeComETT data if you have uploaded it to your project.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/reopen_BMD_step1/1') );
			}
			
		// does BMD file already exist on FreeBMD? : method in common_helper
		BMD_file_exists_on_project($session->BMD_file);
		
		// if it exists get it and recreate FreeComETT data; otherwise just update the header as being open
		if ( $session->BMD_file_exists_on_project == '1')
			{	
				// Get csv data
				$this->fetch_csv_file($reopen_transcription[0]['BMD_file_name']);
				
				// OK, something has been DLed but is it a valid CVS format?	
				// load CSV file to an array for easy processing.
				$CSV_lines = preg_split("/\r\n|\n|\r/", $session->csv_string);
				
				// check this is a proper CSV file
				$CSV_line_array = explode(',', $CSV_lines[0]);
				if ( $CSV_line_array[0] != '+INFO' )
					{
						$session->set('message_2', 'FreeComETT has been able to download the requested Transcription file data but it dpesn\'t appear to be a valid FreeUKGen CSV file. Error in Transcription::reopen_BMD_step2. Send email to '.$session->linbmd2_email);
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url('transcription/reopen_BMD_step1/1') );
					}
			
				// OK now I have a recognised CSV file
													
				// get allocation
				$session->current_allocation = $allocation_model
					->where('BMD_allocation_index',  $reopen_transcription[0]['BMD_allocation_index'])
					->where('BMD_identity_index', $session->BMD_identity_index)
					->find();
				
				// load current data dictionary
				load_current_data_dictionary();
				
				// now continue by project
				switch ( $session->current_project[0]['project_name'] )
					{
						case 'FreeBMD':
							$this->freebmd_loadCSV($CSV_lines, $reopen_transcription);
							break;
						case 'FreeCEN':
							$this->freecen_loadCSV($CSV_lines, $reopen_transcription);
							break;
						case 'FreeREG':
							$this->freereg_loadCSV($CSV_lines, $reopen_transcription);
							break;
						default:
							break;
					}
			}

		// get all detail records and count them
		$count_detail = count($detail_data_model	
			->where('BMD_header_index', $reopen_transcription[0]['BMD_header_index'])
			->find());		
		
		// all ok, so reopen the header
		$data =	[
					'BMD_header_status' => '0',
					'BMD_end_date' => '',
					'BMD_submit_date' => '',
					'BMD_submit_status' => '',
					'BMD_submit_message' => '',
					'BMD_records' => $count_detail,
				];

		// update last action and set return message
		if ( $session->BMD_file_exists_on_project == '1')
			{
				$data['BMD_last_action'] = 'Reopen transcription WITH data update';
				$session->set('message_2', 'Transcription '.$session->BMD_file.' has been re-opened and transcription data has been updated from project. You can select it in your ACTIVE transcription list.');
			}
		else
			{
				$data['BMD_last_action'] = 'Reopen transcription NO data update';
				$session->set('message_2', 'Transcription '.$session->BMD_file.' has been re-opened. Data has NOT been updated from project. You can select it in your ACTIVE transcription list.');
			}
			
		// reopen header
		$transcription_model
			->update($reopen_transcription[0]['BMD_header_index'], $data);		
			
		// return to transcription home page
		$session->status = '0';
		$session->set('message_class_2', 'alert alert-info');
		return redirect()->to( base_url('transcribe/transcribe_step1/1') );
	}
	
	public function download_bmd_file()
	{
		// initialise method
		$session = session();
		$header_model = new Header_Model();
		
		// check file was entered
		if ( $session->fetch_bmd == '' ) 
			{
				$session->set('message_2', 'You confirmed to download a transcription, but you did not enter a Transcription name!');
				$session->set('message_class_2', 'alert alert-danger');
				return;
			}
			
		// was this file created by FreeComETT and by this user
		$session->set('header', $header_model	->where('BMD_file_name', $session->fetch_bmd)
												->where('BMD_identity_index', $session->BMD_identity_index)
												->findAll());
		// found?
		if ( $session->header )
			{
				// if found, is it open?
				if ( $session->header[0]['BMD_header_status'] != 1 )
					{
						$session->set('message_2', 'This file was created by you and is in your ACTIVE Transcription list. You do not need to download it!');
						$session->set('message_class_2', 'alert alert-danger');
						return;
					}
				else
					{
						$session->set('message_2', 'This file was created by you and is in your CLOSED Transcription list. You should reopen it to change it.');
						$session->set('message_class_2', 'alert alert-danger');
						return;
					}
			}
			
		// does the file exist on project
		BMD_file_exists_on_project($session->fetch_bmd);
		if ( $session->BMD_file_exists_on_project == '0' )
			{
				$session->set('message_2', 'Transcription does not exist on '.$session->current_project[0]['project_name'].'. Verify your input.');
				$session->set('message_class_2', 'alert alert-danger');
				return;
			}
			
		// all OK
		
		// delete existing csv file
		// create the CSV file path
		$CSV_file = getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_userid.'/CSV_Files/'.$session->current_transcription[0]['BMD_file_name'].'.BMD';
		// test CSV file exists, delete it if so
		if ( file_exists($CSV_file) === true )
			{
				unlink($CSV_file);
			}
		
		// Download BMD file
		fetch_csv_file();
			
		// done
		$session->set('message_2', 'Transcription downloaded successfully.');
		$session->set('message_class_2', 'alert alert-success');
		return;
	}
	
	public function fetch_csv_file($file_to_fetch)
	{
		// initialise method
		$session = session();

		// create the curl parameters
		$encoding = 'utf8';
		
		// set up the fields to pass
		$postfields =	array(
								"__bmd_0" => "Download",
								"__bmd_1" => $session->identity_userid,
								"__bmd_2" => $session->identity_password,
								"encoding" => $encoding,
								"downloaddo_".$file_to_fetch => "Download",
							);
		// set up the curl. curl_url has been set depending on environment,
		$ch = curl_init($session->curl_url);			
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$curl_result = curl_exec($ch);		
		
		// anything found
		if ( $curl_result == '' )
			{
				// problem so send error message
				$session->set('message_2', 'A technical problem occurred. Send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Transcription::fetch_csv_file, => '.$curl_url.' => '.curl_error($ch));
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('header/create_BMD_step1/1') );
			}
		
		curl_close($ch);
		
		// move data to session for further processing
		$session->csv_string = $curl_result;
	}
	
	public function freebmd_loadCSV($CSV_lines, $reopen_transcription)
	{
		// initialise method
		$session = session();
		$detail_data_model = new Detail_Data_model();
		$detail_comments_model = new Detail_Comments_Model();
		$transcription_CSV_file_model = new Transcription_CSV_File_Model();
		$transcription_comments_model = new Transcription_Comments_Model();
		$session->CSV_count = 0;
		
		// now, the truth is in the downloaded CSV data and the detail database lines must be updated accordingly.
		// the CSV data can contain comments which follow the data line and must be added to the detail comments in the database
		// if I delete all detail data and comments from database and reload from the downloaded CSV data, I will lose the link between the detail line and the scan image so I have to rebuild this data too. This works for 1 column scan but not for multile columns.
		// so I need to preserve the line to scan links.
		// Here's what I am doing...
		// read the CSV data
		// find the line in the DB using the CSV data
		// if found there have been no changes to the line so mark the line in the DB as reopened
		// if not found insert the line
		// those DB lines not marked as reopened, mean that they no longer exist in the CSV data, so they can be deleted.
		// there should be no parked lines except if the first line is not found
		// for comments, remove all comments and re-insert them
		
		// update all details for this transcription to show reopen not processed
		$detail_data_model	
			->where('BMD_header_index', $reopen_transcription[0]['BMD_header_index'])
			->set(['reopen_flag' => 'N'])
			->update();
			
		// fetch current detail
		$current_detail = $detail_data_model
			->where('BMD_header_index', $reopen_transcription[0]['BMD_header_index'])
			->find();
							
		// delete all comments
		$detail_comments_model	
			->where('BMD_header_index', $reopen_transcription[0]['BMD_header_index'])
			->delete();
										
		// find the CSV line with the first page flag in order to determine where detail data starts ( depends on project )	
		foreach ( $CSV_lines as $key => $CSV_line )
			{
				if ( explode(',', $CSV_line)[0] == '+PAGE' )
					{
						$CSV_data_start_key = $key + 1;
						break;
					}
			}	
		
		// find number of elements in CSV lines, subtracting 1 to not read the last +PAGE line
		$CSV_data_end_key = count($CSV_lines) - 1;
				
		// loop through CSV data until end of data from the current key set above
		$CSV_line_further_processing = array();
		$last_line_sequence_reopened = 0;
		for ( $i = $CSV_data_start_key; $i < $CSV_data_end_key; $i++ )
			{			
				// initialise inner loop
				$line_processed = 0;
				$comment_flag = 0;
						
				// Initialise comment data array
				$comment_data['project_index'] = $session->current_project[0]['project_index'];
				$comment_data['BMD_identity_index'] = $session->BMD_identity_index;
				$comment_data['BMD_header_index'] = $reopen_transcription[0]['BMD_header_index'];
				
				// test for a comment or +BREAK etc ATTENTION - the order of these case statements is important
				switch (true)
					{
						case strpos($CSV_lines[$i], '+BREAK') !== FALSE:
							// set comment flag
							$comment_flag = 1;
							// continue to build comment data array
							$comment_data['BMD_comment_span'] = 0;
							$comment_data['BMD_comment_text'] = '';
							$comment_data['BMD_comment_type'] = 'B';
							break;
						case strpos($CSV_lines[$i], '+PAGE') !== FALSE:
							// set comment flag
							$comment_flag = 1;
							// build data array
							$comment_data['BMD_comment_span'] = 0;
							$comment_data['BMD_comment_text'] = '';
							$comment_data['BMD_comment_type'] = 'P';
							break;
						case strpos($CSV_lines[$i], '#COMMENT') !== FALSE:
							// set comment flag
							$comment_flag = 1;
							// parse the comment line
							$comment_array1 = explode('(', $CSV_lines[$i]);
							$comment_array2 = explode(')', $comment_array1[1]);
							// build comment data
							$comment_data['BMD_comment_span'] = $comment_array2[0];
							$comment_data['BMD_comment_text'] = trim($comment_array2[1]);
							$comment_data['BMD_comment_text'] = trim($comment_data['BMD_comment_text'], '"');
							$comment_data['BMD_comment_type'] = 'C';
							break;
						case strpos($CSV_lines[$i], '#THEORY,REF') !== FALSE:
							// set comment flag
							$comment_flag = 1;
							// parse the comment line
							$comment_array1 = explode(',', $CSV_lines[$i]);
							// build comment data
							$comment_data['BMD_comment_span'] = 0;
							$comment_data['BMD_comment_text'] = trim($comment_array1[2], '"');
							$comment_data['BMD_comment_type'] = 'R';
							break;
						case strpos($CSV_lines[$i], '#THEORY') !== FALSE:
							// set comment flag
							$comment_flag = 1;
							// parse the comment line
							$comment_array1 = explode('(', $CSV_lines[$i]);
							$comment_array2 = explode(')', $comment_array1[1]);
							// build comment data
							$comment_data['BMD_comment_span'] = $comment_array2[0];
							$comment_data['BMD_comment_text'] = trim($comment_array2[1]);
							$comment_data['BMD_comment_text'] = trim($comment_data['BMD_comment_text'], '"');
							$comment_data['BMD_comment_type'] = 'T';
							break;
						case strpos($CSV_lines[$i], '#') !== FALSE:
							// set comment flag
							$comment_flag = 1;
							// parse comment
							$comment_array1 = explode('#', $CSV_lines[$i]);
							$trimmed_data = trim($comment_array1[1]);
							$trimmed_data = trim($trimmed_data, '"');				
							// build comment data
							$comment_data['BMD_comment_type'] = 'N';
							$comment_data['BMD_comment_span'] = 0;
							$comment_data['BMD_comment_text'] = $trimmed_data;
							break;
					}
					
				// process comment but not if first data line
				if ( $comment_flag == 1 AND $i != $CSV_data_start_key )
					{
						// complete comment data array
						$comment_data['BMD_line_sequence'] = $last_line_sequence_reopened;
						$comment_data['BMD_line_index'] = $last_detail_index;
						
						// insert data to DB
						$detail_comments_model->insert($comment_data);
						
						// line processed
						$line_processed = 1;
					}
							
				// have I processed the line - if not it is a normal line
				if ( $line_processed == 0 )
					{
						// I have a normal data line
						// explode the line to get the individual fields
						$CSV_line_array = explode(',', $CSV_lines[$i]);
				
						// get data entry format and add to data array by table field name
						$index = 0;
						$detail_data = array();
						foreach ( $session->current_transcription_def_fields as $field )
							{
								// trim data for spaces
								$trimmed_data = trim($CSV_line_array[$index]);
								// trim data for "
								$trimmed_data = trim($trimmed_data, '"');
								// add to data array
								$detail_data[$field['table_fieldname']] = $trimmed_data;
								// increment index
								$index = $index + 1;
							}
						
						// get the line from the DB
						$DB_line = $detail_data_model
							->where('project_index', $session->current_project[0]['project_index'])
							->where('BMD_identity_index', $session->BMD_identity_index)
							->where('BMD_header_index', $reopen_transcription[0]['BMD_header_index'])
							->like($detail_data)
							->find();
						if ( ! $DB_line )
							{
								// if the first line in the CSV is not found in the DB, the last fields will not be updated. 
								// So create defaults based on the first current detail line and create sensible defaults
								if ( $i == $CSV_data_start_key )
									{
										$last_line_sequence_reopened = 0;
										$last_panzoom_x = $current_detail[0]['BMD_line_panzoom_x'];
										$last_panzoom_y = $current_detail[0]['BMD_line_panzoom_y'] + $reopen_transcription[0]['BMD_image_scroll_step'];
										$last_panzoom_z = $current_detail[0]['BMD_line_panzoom_z'];
										$last_sharpen = $current_detail[0]['BMD_line_sharpen'];
										$last_rotate = $current_detail[0]['BMD_line_image_rotate'];
									}	
										
								// If the DB line not found it means that the line is in the CSV data but not in the DB, ie it needs to be added to the DB
								// complete the data array for insert; the CSV data line fields are already set
								$detail_data['project_index'] = $session->current_project[0]['project_index'];
								$detail_data['BMD_identity_index'] = $reopen_transcription[0]['BMD_identity_index'];
								$detail_data['BMD_header_index'] = $reopen_transcription[0]['BMD_header_index'];
								$detail_data['BMD_identity_index'] = $reopen_transcription[0]['BMD_identity_index'];
								$detail_data['BMD_line_sequence'] = $last_line_sequence_reopened + 2;
								$detail_data['BMD_line_panzoom_x'] = $last_panzoom_x;
								$detail_data['BMD_line_panzoom_y'] = $last_panzoom_y - $reopen_transcription[0]['BMD_image_scroll_step'];
								$detail_data['BMD_line_panzoom_z'] = $last_panzoom_z;
								$detail_data['BMD_line_sharpen'] = $last_sharpen;
								$detail_data['BMD_line_image_rotate'] = $last_rotate;
								$detail_data['reopen_flag'] = 'Y';
								$detail_data['line_verified'] = 'NO';
								$detail_data['BMD_status'] = '0';								
								$last_detail_index = $detail_data_model	
									->insert($detail_data);
							}
						else
							{
								// if it was found in the DB, update it to say that it has been processed by reopen
								$detail_data_model	
									->where('BMD_index', $DB_line[0]['BMD_index'])
									->set(['reopen_flag' => 'Y'])
									->set(['line_verified' => 'YES'])
									->set(['BMD_status' => '0'])
									->update();
																		
								// store details for possible insert after this line
								$last_line_sequence_reopened = $DB_line[0]['BMD_line_sequence'];
								$last_panzoom_x	= $DB_line[0]['BMD_line_panzoom_x'];
								$last_panzoom_y	= $DB_line[0]['BMD_line_panzoom_y'];
								$last_panzoom_z	= $DB_line[0]['BMD_line_panzoom_z'];
								$last_sharpen = $DB_line[0]['BMD_line_sharpen'];
								$last_rotate = $DB_line[0]['BMD_line_image_rotate'];
								$last_detail_index = $DB_line[0]['BMD_index'];
							}
					}	
				// get next line
			}
					
		// now delete all non processed DB lines
		$detail_data_model	
			->where('BMD_header_index', $reopen_transcription[0]['BMD_header_index'])
			->where('reopen_flag', 'N')
			->delete();
		
		// Manage header comments
		// get the third csv line, it will contain the header comments if any
		$header_comment	= explode(',', $CSV_lines[2]);
		// delete the header comment if any
		$current_header_comment = $transcription_comments_model
			->where('transcription_index', $reopen_transcription[0]['BMD_header_index'])
			->delete();
		// add header comment record if any
		if ( $header_comment[1] != '' )
			{
				// chop text to 65000 chars max
				$comment_chopped = substr($header_comment[1], 0, 65000);
				// insert
				$transcription_comments_model
					->set(['transcription_index' => $reopen_transcription[0]['BMD_header_index']])
					->set(['project_index' => $session->current_project[0]['project_index']])
					->set(['identity_index' => $session->BMD_identity_index])
					->set(['comment_sequence' => 10])
					->set(['comment_text' => $comment_chopped])
					->set(['source_text' => ' '])
					->insert();
			}
	}
	
	public function download_transcription_step1($start_message)
	{
		// initialise method
		$session = session();
		
		switch ($start_message) 
			{
				case 0:
					// initialise values
					$session->set('BMD_file', '');
					$session->set('scan_page_suffix', '');
					$session->set('BMD_download_confirm', 'N');
					// message defaults
					$session->set('message_1', 'Enter the name of the Transcription you wish to DOWNLOAD and confirm.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('allocation_name', 'Please select an allocation to attach the downloaded transcription to.');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Enter the name of the Transcription you wish to DOWNLOAD and confirm.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
		
		// show views
		echo view('templates/header');
		echo view('linBMD2/transcription_BMD_download');
		echo view('templates/footer');
		
	}
	
	public function download_transcription_step2()
	{
		// initialise method
		$session = session();
		$transcription_model = new Transcription_model();
		$allocation_model = new Allocation_Model();
		
		// get user input
		$session->set('allocation', $this->request->getPost('allocation'));
		$session->set('BMD_file', $this->request->getPost('BMD_file'));
		$session->set('scan_page_suffix', $this->request->getPost('scan_page_suffix'));
		$session->set('BMD_download_confirm', $this->request->getPost('BMD_download_confirm'));
		
		// Did user enter a file name?
		if ($session->BMD_file == '')
			{
				$session->set('message_2', 'Transcription name cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/download_transcription_step1/1') );
			}
			
		// is it correct format
		$file_array = explode('.', $session->BMD_file);
		if ( count($file_array) > 1 )
			{
				$session->set('message_2', 'Please do not enter the file extension, eg .BMD.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/download_transcription_step1/1') );
			}
			
		// OK - file name was entered correctly
		
		// does file exist in database? If it already exists it cannot be downloaded
		$download_transcription = 	$transcription_model	
									->where('BMD_file_name', $session->BMD_file)
									->where('BMD_identity_index', $session->BMD_identity_index)
									->where('project_index', $session->current_project[0]['project_index'])
									->findAll();		
		
		// were any found?
		if ( $download_transcription )
			{
				$session->set('message_2', 'The transcription, '.$session->BMD_file.', name you entered already exists in your FreeComETT transcription database so it cannot be downloaded. If it is not in your open transcription list on the Transcription Home Page you may want to re-open it.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/download_transcription_step1/1') );
			}
			
		// OK - file does not exist in database
		
		// get allocation														
		$session->current_allocation = $allocation_model->where('BMD_allocation_index', $session->allocation)
														->where('project_index', $session->current_project[0]['project_index'])
														->find();
		if ( ! $session->current_allocation )
			{
				$session->set('message_2', 'You must select an allocation from the dropdown list OR create a new one to attach the downloaded transcription to.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('field_name', 'allocation');
				return redirect()->to( base_url('transcription/download_transcription_step1/1') );
			}
			
		// OK - allocation was selected	
			
		// did user confirm?
		if ($session->BMD_download_confirm == 'N') 
			{
				$session->set('message_2', 'You did not confirm to download this transcription '.$session->BMD_file.'.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/download_transcription_step1/1') );
			}
		
		// OK - user confirmed
		
		// OK - data entry tests passed
		
		// OK - Start integrity checks
		
		// Does the CSV file already exist in FreeComETT?
		$CSV_file = getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_userid.'/CSV_Files/'.$session->BMD_file.'.BMD';
		if ( file_exists($CSV_file) === true )
			{
				$session->set('message_2', 'A CSV/BMD file named, '.$session->BMD_file.', already exists in your CSV/BMD file store in FreeComETT which implies that you have already downloaded the file and created a transcription for it. Please check your open transcripton files in the Transcription Home Page or reopen the transcription if you have closed it.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/download_transcription_step1/1') );
			}
			
		// OK - file does not exist in FreeComETT CSV file store
		
		// does BMD file already exist on FreeBMD? : method in common_helper
		BMD_file_exists_on_project($session->BMD_file);
		if ( $session->BMD_file_exists_on_project == '0')
			{
				$session->set('message_2', 'The BMD file, '.$session->BMD_file.', is not available on the '.$session->current_project[0]['project_name'].' project. I cannot continue because there is nothing to download.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/download_transcription_step1/1') );
			}
			
		// OK - transcription exists, so it can be downloaded
		
		// OK integrity test passed
		
		// create and open file in append mode
		$fp = fopen($CSV_file, 'a');
		if ( $fp === false )
			{
				$session->set('message_2', 'Oups! Cannot create CSV file in Transcribe/download_transcription_step2. Send an email to '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/download_transcription_step1/1') );
			}
		fclose($fp);

		// OK - transcription CSV file insubstantiated
		
		// try to download the file
		$this->fetch_csv_file($session->BMD_file);
		
		// OK - file downloaded
		
		// load DLed file to array
		$CSV_lines = file($CSV_file);
			
		// OK - now I have a recognised CSV file
		
		// I know the project I am in and I know that the DLed file is from that project
		// now continue to create the database from the CSV by project
		switch ( $session->current_project[0]['project_name'] )
			{
				case 'FreeBMD':
					$this->freebmd_createDB($CSV_lines);
					break;
				case 'FreeCEN':
					$this->freecen_createDB($CSV_lines);
					break;
				case 'FreeREG':
					$this->freereg_createDB($CSV_lines);
					break;
				default:
					break;
			}
			
		// OK - DB created
		
		// remove downloaded BMD
		if ( file_exists($CSV_file) === true )
			{
				unlink($CSV_file);
			}
		
		// return to transcription home page
		$session->status = '0';
		$session->set('message_2', 'Transcription '.$session->BMD_file.' was downloaded and added to FreeComETT. You can select it in the list below.');
		$session->set('message_class_2', 'alert alert-info');
		return redirect()->to( base_url('transcribe/transcribe_step1/0') );
	}
	
	public function freebmd_createDB($CSV_lines)
	{
		// initialise method
		$session = session();
		$transcription_model = new Transcription_Model();
		$detail_data_model = new Detail_Data_model();
		$detail_comments_model = new Detail_Comments_Model();
		$def_image_model = new Def_Image_Model();
		$transcription_detail_def_model = new Transcription_Detail_Def_Model();
		$def_fields_model = new Def_Fields_Model();
		
		// Step 1 - create the transcription header
		
		// get the header lines into an array and trim
		$head0 = explode(',', $CSV_lines[0]);
		foreach ( $head0 as $key => $element )
			{
				$element = trim($element, '"');
				$element = trim($element);
				$head0[$key] = $element;
			}
		$head1 = explode(',', $CSV_lines[1]);
		foreach ( $head1 as $key => $element )
			{
				$element = trim($element, '"');
				$element = trim($element);
				$head1[$key] = $element;
			}
		$head2 = explode(',', $CSV_lines[2]);
		foreach ( $head2 as $key => $element )
			{
				$element = trim($element, '"');
				$element = trim($element);
				$head2[$key] = $element;
			}
		$head3 = explode(',', $CSV_lines[3]);
		foreach ( $head3 as $key => $element )
			{
				$element = trim($element, '"');
				$element = trim($element);
				$head3[$key] = $element;
			}
		
		// first create scan name
		// year
		$scan_name = $head3[1];
		// type
		switch ( $head0[4] )
			{
				case 'BIRTHS':
					$scan_name = $scan_name.'B';
					break;
				case 'MARRIAGES':
					$scan_name = $scan_name.'M';
					break;
				case 'DEATHS':
					$scan_name = $scan_name.'D';
					break;
				default:
					break;
			}
		// quarter		
		if ( $head3[2] != '' )
			{
				$quarter = strtoupper($head3[2]);
				$quarter = array_search($quarter, $session->quarters);
				$scan_name = $scan_name.$quarter;
			}
		// -
		$scan_name = $scan_name.'-';
		// letter
		$scan_name = $scan_name.$head1[9];
		// -
		$scan_name = $scan_name.'-';
		// page depends on year and type
		$letter_len = strlen($head1[9]);
		$file_len = strlen($session->BMD_file);
		$letter_start = strpos($session->BMD_file, $head1[9]);
		$session->scan_page = substr($session->BMD_file, $letter_start + $letter_len);
		$next_page = $session->scan_page + 1;
		$this->construct_scan_page_number('download');
		$scan_name = $scan_name.$session->scan_page_number;
		// add suffix if there is one
		if ( $session->scan_page_suffix != '' )
			{
				$scan_name = $scan_name.$session->scan_page_suffix;
			}
		// extension
		$scan_name = $scan_name.'.jpg';
			
		// OK - scan name created
		
		// does the scan exist on the project?
		$curl_url = $session->freeukgen_source_values['image_server'].$session->current_allocation[0]['BMD_reference'].$scan_name;
		$ch = curl_init($session->curl_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_USERPWD, $session->identity_userid.':'.$session->identity_password);	
		if ( curl_exec($ch) === false )
			{
				// problem so send error message
				$session->set('message_2', 'A technical problem occurred. Send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Header::create_BMD_step2, around line 202 => '.$curl_url.' => '.curl_error($ch));
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/download_transcription_step1/1') );
			}
		if ( curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200 )
			{
				curl_close($ch);
				$session->set('message_2', 'The scan '.$scan_name.' does not exist on the '.$session->current_project[0]['project_name']. 'server => '.$session->freeukgen_source_values['image_server'].'/'.$session->current_allocation[0]['BMD_reference'] );
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/download_transcription_step1/1') );
			} 	
		
		// OK - scan exists
		
		// get the initial image defs
		$def_image =	$def_image_model
						->where('project_index', $session->current_project[0]['project_index'])
						->where('syndicate_index', $session->current_allocation[0]['BMD_syndicate_index'])
						->where('data_entry_format', $session->current_allocation[0]['data_entry_format'])
						->where('scan_format', $session->current_allocation[0]['scan_format'])
						->find();
		// initialse image fields depending if found or not
		if ( $def_image )
			{
				// found, so use initial image set
				$image_x = $def_image[0]['image_x'];
				$image_y = $def_image[0]['image_y'];
				$image_rotate = $def_image[0]['image_rotate'];
				$image_scroll_step = $def_image[0]['image_scroll_step'];
				$panzoom_x = $def_image[0]['panzoom_x'];
				$panzoom_y = $def_image[0]['panzoom_y'];
				$panzoom_z = $def_image[0]['panzoom_z'];
				$sharpen = $def_image[0]['sharpen'];
			}
		else
			{
				// not found, set common defaults
				$image_x = 0;
				$image_rotate = 0;
				$panzoom_x = 396;
				$panzoom_y = 140;
				$panzoom_z = 1.81;
				$sharpen = 2;
				// set scan_format specific values
				switch ($session->current_allocation[0]['scan_format'])
					{
						case 'handwritten':
							$image_y = 60;
							$image_scroll_step = 50;
							break;
						case 'typed':
							$image_y = 45;
							$image_scroll_step = 40;
							break;
						case 'printed':
							$image_y = 45;
							$image_scroll_step = 40;
							break;
						default:
							$image_y = 45;
							$image_scroll_step = 40;
							break;
					}
			}
		
		// set last action
		switch ( $session->current_project[0]['project_index'] )
			{
				case 1:
					$last_action = 'BMD file created';
					break;
				case 2:
					$last_action = 'CSV file created';
					break;
				case 3:
					$last_action = 'CSV file created';
					break;
			}	
		// now, create the transcription in the database
		$data =	[
					'project_index' => $session->current_project[0]['project_index'],
					'BMD_identity_index' => $session->BMD_identity_index,
					'BMD_allocation_index' => $session->current_allocation[0]['BMD_allocation_index'],
					'BMD_syndicate_index' => $session->current_allocation[0]['BMD_syndicate_index'],
					'BMD_file_name' => $session->BMD_file,
					'BMD_scan_name' => $scan_name,
					'BMD_start_date' => $session->current_date,
					'BMD_end_date' => '',
					'BMD_submit_date' => '',
					'BMD_submit_status' => '',
					'BMD_submit_fail_message' => '',
					'BMD_current_page' => $session->scan_page_number,
					'BMD_current_page_suffix' => $session->scan_page_suffix,
					'BMD_next_page' => $next_page,
					'BMD_records' => 0,
					'BMD_last_action' => $last_action,
					'BMD_header_status' => '0',
					'BMD_image_x' => $image_x,
					'BMD_image_y' => $image_y,
					'BMD_image_rotate' => $image_rotate,
					'BMD_image_scroll_step' => $image_scroll_step,
					'BMD_panzoom_x' => $panzoom_x,
					'BMD_panzoom_y' => $panzoom_y,
					'BMD_panzoom_z' => $panzoom_z,
					'BMD_sharpen' => $sharpen,
					'BMD_font_family' => $session->data_entry_font,
				];
		// get the index of this transcription
		$id = $transcription_model->insert($data);
		
		// OK - transcription created in DB
		
		// load the transcription data entry format table
		// create the def for this transcription but only if the transcription_detail_def doesn't exist
		// get the record
		$transcription_detail_def = $transcription_detail_def_model 
									->where('project_index', $session->current_project[0]['project_index'])
									->where('identity_index', $session->BMD_identity_index)
									->where('transcription_index', $id)
									->find();
		// not found? so create it
		if ( ! $transcription_detail_def )
			{		
				// get the standard def
				$session->set('standard_def',	$def_fields_model	
												->where('project_index', $session->current_project[0]['project_index'])
												->where('syndicate_index', $session->current_transcription[0]['BMD_syndicate_index'])
												->where('data_entry_format', $session->current_allocation[0]['data_entry_format'])
												->where('scan_format', $session->current_allocation[0]['scan_format'])
												->orderby('field_order','ASC')
												->find());
						
				// initialise data array
				$data = array();
			
				// read the standard def entries and create the data array for insert to transcription detail def records
				foreach ( $session->standard_def as $record )
					{
						// read through $record as it is an array
						foreach ( $record as $field_name => $field_value )
							{
								// create the insert record field by field
								$data[$field_name] = $field_value;
							}
						
						// remove record index to avoid duplicate primary indexes
						unset($data['field_index']);
						
						// set the additional fields not in standard def
						$data['transcription_index'] = $id;
						$data['identity_index'] = $session->BMD_identity_index;
						
						// insert the record
						$transcription_detail_def_model->insert($data);						
					}
			}

		// OK - transcription created
		
		// load current data dictionary
		load_current_data_dictionary();

		// find the CSV line with the first page flag in order to determine where detail data starts ( depends on project )	
		foreach ( $CSV_lines as $key => $CSV_line )
			{
				if ( explode(',', $CSV_line)[0] == '+PAGE' )
					{
						$session->CSV_data_start = $key + 1;
						$session->CSV_data_end = end($CSV_lines);
						reset($CSV_lines);
						break;
					}
			}
			
		// OK, I now have the start of the data lines in the CSV file.
		
		// so, delete all detail data and comments in the DB for this transcription
		$detail_data_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('BMD_header_index', $id)
			->delete();
		
		$detail_comments_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('BMD_header_index', $id)
			->delete();
			
		//set CSV array pointer to start key
		while(key($CSV_lines) != $session->CSV_data_start) next($CSV_lines);
		
		// set standard fields for add/update
		$data =	[
					'project_index' => $session->current_project[0]['project_index'],
					'BMD_identity_index' => $session->BMD_identity_index,
					'BMD_header_index' => $id,
					'BMD_status' => '0',
					'BMD_line_panzoom_x' => $panzoom_x,
					'BMD_line_panzoom_y' => $panzoom_y,
					'BMD_line_panzoom_z' => $panzoom_z,
					'BMD_line_sharpen' => $sharpen,
					'BMD_line_image_rotate' => '0',
				];
				
		// read the CSV data
		$line_sequence = 0;
		$count = 0;
		while(current($CSV_lines) != $session->CSV_data_end) 
			{				
				// line processed
				$line_processed = 0;
						
				// Initialise comment data
				$comment_data['project_index'] = $session->current_project[0]['project_index'];
				$comment_data['BMD_identity_index'] = $session->BMD_identity_index;
				$comment_data['BMD_header_index'] = $id;
				$comment_data['BMD_status'] = '0';
				
				// test for a comment or +BREAK
				switch (true)
					{
						case strpos(current($CSV_lines), '+BREAK') !== FALSE:
							// build data array
							$comment_data['BMD_line_sequence'] = $line_sequence;
							$comment_data['BMD_line_index'] = $last_detail_index;
							$comment_data['BMD_comment_span'] = 0;
							$comment_data['BMD_comment_text'] = '';
							$comment_data['BMD_comment_type'] = 'B';
							// insert data
							$detail_comments_model->insert($comment_data);
							// line processed
							$line_processed = 1;
							break;
						case strpos(current($CSV_lines), '#COMMENT') !== FALSE:
							// parse the comment line
							$comment_array1 = explode('(', current($CSV_lines));
							$comment_array2 = explode(')', $comment_array1[1]);
							// build comment data
							$comment_data['BMD_line_sequence'] = $line_sequence;
							$comment_data['BMD_line_index'] = $last_detail_index;
							$comment_data['BMD_comment_span'] = $comment_array2[0];
							$comment_data['BMD_comment_text'] = trim($comment_array2[1]);
							$comment_data['BMD_comment_text'] = trim($comment_data['BMD_comment_text'], '"');
							$comment_data['BMD_comment_type'] = 'C';
							// insert data
							$detail_comments_model->insert($comment_data);
							// line processed
							$line_processed = 1;	
							break;
						case strpos(current($CSV_lines), '#THEORY') !== FALSE:
							// parse the comment line
							$comment_array1 = explode('(', current($CSV_lines));
							$comment_array2 = explode(')', $comment_array1[1]);
							// build comment data
							$comment_data['BMD_line_sequence'] = $line_sequence;
							$comment_data['BMD_line_index'] = $last_detail_index;
							$comment_data['BMD_comment_span'] = $comment_array2[0];
							$comment_data['BMD_comment_text'] = trim($comment_array2[1]);
							$comment_data['BMD_comment_text'] = trim($comment_data['BMD_comment_text'], '"');
							$comment_data['BMD_comment_type'] = 'T';
							// insert data
							$detail_comments_model->insert($comment_data);
							// line processed
							$line_processed = 1;
							break;
						case strpos(current($CSV_lines), '#') !== FALSE:
							// parse comment
							$comment_array1 = explode('#', current($CSV_lines));
							$trimmed_data = trim($comment_array1[1]);
							$trimmed_data = trim($trimmed_data, '"');				
							// build comment data
							$comment_data['BMD_line_sequence'] = $line_sequence;
							$comment_data['BMD_line_index'] = $last_detail_index;
							$comment_data['BMD_comment_type'] = 'N';
							$comment_data['BMD_comment_span'] = 0;
							$comment_data['BMD_comment_text'] = $trimmed_data;
							// insert data
							$detail_comments_model->insert($comment_data);
							// line processed
							$line_processed = 1;
							break;
					}
				
				// have I processed the line
				if ( $line_processed == 0 )
					{
						// I have a normal data line
						// explode the line to get the individual fields
						$CSV_line_array = explode(',', current($CSV_lines));
				
						// increment line sequence
						$line_sequence = $line_sequence + 10;
						$data['BMD_line_sequence'] = $line_sequence;
						$data['BMD_line_panzoom_y'] = $data['BMD_line_panzoom_y'] - $image_scroll_step;
				
						// get data entry format and add to data array by table field name
						$index = 0;
						foreach ( $session->current_transcription_def_fields as $field )
							{
								// trim data
								$trimmed_data = trim($CSV_line_array[$index]);
								// trim data for "
								$trimmed_data = trim($trimmed_data, '"');
								// add to data array
								$data[$field['table_fieldname']] = $trimmed_data;
								// increment index
								$index = $index + 1;
							}
						
						// insert record
						$last_detail_index = $detail_data_model->insert($data);
						
						// increment $count
						$count = $count + 1;
					}	
					
				// get the next CSV line
				next($CSV_lines);	
			}
			
		// update number of records on transcription
		$data =	[
					'BMD_records' => $count,
				];
		$transcription_model->update($id, $data);
	}
	
	public function delete($transcription_index)
	{
		// initialise method
		$session = session();
		$transcription_model = new Transcription_Model();
		$detail_data_model = new Detail_Data_Model;
		$detail_comments_model = new Detail_Comments_Model;
		$transcription_detail_def_model = new Transcription_Detail_Def_Model();
		$identity_model = new Identity_Model();
		$transcription_comments_model = new Transcription_Comments_Model();
		$allocation_images_model = new Allocation_Images_Model();
		
		// a deleted transcription can always be rebuilt by downloading its CSV file from the project servers.
		
		// get the transcription record
		$delete_transcription = $transcription_model	
			->where('BMD_header_index', $transcription_index)
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('project_index', $session->current_project[0]['project_index'])
			->findAll();		
		
		// was it found?
		if ( ! $delete_transcription )
			{
				$session->set('message_2', 'The transcription, '.$session->BMD_file.', name you entered does not exist in your transcription database so it cannot be deleted');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcribe/transcribe_step1/1') );
			}
			
		// delete scan
		$file = getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_userid.'/Scans/'.$delete_transcription[0]['BMD_scan_name'];
				if( is_file($file) )
					{
						unlink($file);
					}
					
		// OK - scan deleted
		
		// delete CSV file
		switch ( $session->current_project[0]['project_name'] )
			{
				case 'FreeBMD':
					$ext = '.BMD';
					break;
				case 'FreeREG':
					$ext = '.CSV';
					break;
				case 'FreeCEN':
					break;
			}
		$file = getcwd().'/Users/'.$session->current_project[0]['project_name'].'/'.$session->identity_userid.'/CSV_Files/'.$delete_transcription[0]['BMD_file_name'].$ext;
				if( is_file($file) )
					{
						unlink($file);
					}
					
		// OK - CSV file deleted
		
		// read detail data and reset reporting data
		$detail_data = $detail_data_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('BMD_header_index', $transcription_index)
			->findAll();
		if ( $detail_data )
			{
				foreach ( $detail_data as $detail_line )
					{
						load_report_data($detail_line, 'sub');
					}
			}
			
		// OK - report data removed
		
		// delete all detail data and detail comments in the DB for this transcription
		$detail_data_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('BMD_header_index', $transcription_index)
			->delete();
		
		$detail_comments_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('BMD_header_index', $transcription_index)
			->delete();
			
		// OK - detail data and comments deleted
		
		// delete transcription details defs 
		$transcription_detail_def_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('identity_index', $session->BMD_identity_index)
			->where('transcription_index', $transcription_index)
			->delete();
		
		// OK details def deleted
			
		// delete any transcripton comments
		$transcription_comments_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('identity_index', $session->BMD_identity_index)
			->where('transcription_index', $transcription_index)
			->delete();
			
		// OK transcription comments deleted
		
		// delete the transcription
		$transcription_model
			->delete($transcription_index);
		
		// OK - transcription deleted
		
		// update allocation images to remove transcription for images attached to it
		$allocation_images_model
			->where('transcription_index', $transcription_index)
			->set(['transcription_index' => NULL ])
			->update();
			
		// OK - images updated
		
		
		// ALL DONE
		$session->set('message_2', 'Transcription, '.$delete_transcription[0]['BMD_file_name'].', has been deleted.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('transcribe/transcribe_step1/0') );
	}
	
	public function comments_step1($start_message)
	{				
		// initialise method
		$session = session();
		$transcription_comments_model = new Transcription_Comments_Model();
		$document_sources_model = new Document_Sources_Model();
		
		switch ($start_message) 
			{
				case 0:
					// message defaults
					$session->set('message_1', 'Add / Change / Remove comments/source for this transcription => '.$session->current_transcription[0]['BMD_file_name']);
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					// get any header comments/source.
					$session->comment_text = '';
					$session->source_text = '';
					$session->comment_text_array =	$transcription_comments_model
						->where('project_index', $session->current_project[0]['project_index'])
						->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
						->where('comment_sequence', 10)
						->find();
					// any found ?
					if ( $session->comment_text_array )
						{
							$session->comment_text = $session->comment_text_array[0]['comment_text'];
							$session->source_text = $session->comment_text_array[0]['source_text'];
						}
					// get document sources
					$session->document_sources = $document_sources_model
						->orderby('document_source')
						->findAll();
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Add / Change / Remove comments/source for this transcription => '.$session->current_transcription[0]['BMD_file_name']);
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
	
		// show views																
		echo view('templates/header');
		echo view('linBMD2/transcription_comments');
		echo view('templates/footer');	
	}
	
	public function comments_step2()
	{				
		// initialise method
		$session = session();
		$transcription_comments_model = new Transcription_Comments_Model();
		
		// get input
		$session->comment_text = $this->request->getPost('comment_text');
		$session->source_text = $this->request->getPost('source_text');
		
		// test length of comment_text
		if ( strlen($session->comment_text) > 65000 )
			{
				$session->set('message_2', 'Please limit your comment text to 65000 characters max.');
				$session->message_error = 'error';
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/comments_step1/1') );
			}
		// test length of source_text
		if ( $session->source_text == 'SL' )
			{
				$session->set('message_2', 'Please select a Document Source.');
				$session->message_error = 'error';
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('transcription/comments_step1/1') );
			}
			
		// delete sequence 10 for any transcription comments
		$transcription_comments_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
			->where('comment_sequence', 10)
			->delete();
		// now add it again with the updated comments
		$data =	[
					'transcription_index' => $session->current_transcription[0]['BMD_header_index'],
					'project_index' => $session->current_project[0]['project_index'],
					'identity_index' => $session->BMD_identity_index,
					'comment_sequence' => 10,
					'comment_text' => $session->comment_text,
					'source_text' => $session->source_text,
				];
		$transcription_comments_model->insert($data);
		
		// all done
		return redirect()->to( base_url('transcribe/transcribe_step1/0') );
	}
	
	public function construct_scan_page_number($action)
	{
		// initialise method
		$session = session();
		
		switch ($session->current_allocation[0]['BMD_type'])
					{
						case "B":
							switch ($action)
								{
									case 'create':
										switch ($session->current_allocation[0]['BMD_year'])
											{
												case 1994:
													$session->scan_page_number = str_pad($session->scan_page, 3, "0", STR_PAD_LEFT);
													break;
												case 1995:
													$session->scan_page_number = str_pad($session->scan_page, 3, "0", STR_PAD_LEFT);
													break;
												case 1996:
													$session->scan_page_number = str_pad($session->scan_page, 3, "0", STR_PAD_LEFT);
													break;
												case 1997:
													switch ($session->current_allocation[0]['BMD_letter'])
														{
															case "A":
																$session->scan_page_number = str_pad($session->scan_page, 3, "0", STR_PAD_LEFT);
																break;
															default:
																$session->scan_page_number = str_pad($session->scan_page, 4, "0", STR_PAD_LEFT);
																break;
														}
													break;
												default:
													$session->scan_page_number = str_pad($session->scan_page, 4, "0", STR_PAD_LEFT);
													break;
											}
										break;
									case 'download':
										switch ($session->current_allocation[0]['BMD_year'])
											{
												case 1994:
													$session->scan_page_number = substr($session->scan_page, -3);
													break;
												case 1995:
													$session->scan_page_number = substr($session->scan_page, -3);
													break;
												case 1996:
													$session->scan_page_number = substr($session->scan_page, -3);
													break;
												case 1997:
													switch ($session->current_allocation[0]['BMD_letter'])
														{
															case "A":
																$session->scan_page_number = substr($session->scan_page, -3);
																break;
															default:
																$session->scan_page_number = substr($session->scan_page, -4);
																break;
														}
													break;
												default:
													$session->scan_page_number = substr($session->scan_page, -4);
													break;
											}
										break;
									break;
								}
						case "M":
							switch ($action)
								{
									case 'create':
										switch ($session->current_allocation[0]['BMD_year'])
											{
												case 1994:
													$session->scan_page_number = str_pad($session->scan_page, 3, "0", STR_PAD_LEFT);
													break;
												case 1995:
													$session->scan_page_number = str_pad($session->scan_page, 3, "0", STR_PAD_LEFT);					
													break;
												case 1996:
													$session->scan_page_number = str_pad($session->scan_page, 3, "0", STR_PAD_LEFT);
													break;
												default:
													$session->scan_page_number = str_pad($session->scan_page, 4, "0", STR_PAD_LEFT);
													break;
											}
										break;
									case 'download':
										switch ($session->current_allocation[0]['BMD_year'])
											{
												case 1994:
													$session->scan_page_number = substr($session->scan_page, -3);
													break;
												case 1995:
													$session->scan_page_number = substr($session->scan_page, -3);
													break;
												case 1996:
													$session->scan_page_number = substr($session->scan_page, -3);
													break;
												default:
													$session->scan_page_number = substr($session->scan_page, -4);
													break;
											}
										break;
								}
							break;
						case "D":
							switch ($action)
								{
									case 'create':
										switch ($session->current_allocation[0]['BMD_year'])
											{
												case 1994:
													$session->scan_page_number = str_pad($session->scan_page, 3, "0", STR_PAD_LEFT);
													break;
												case 1995:
													$session->scan_page_number = str_pad($session->scan_page, 3, "0", STR_PAD_LEFT);					
													break;
												case 1996:
													$session->scan_page_number = str_pad($session->scan_page, 3, "0", STR_PAD_LEFT);
													break;
												default:
													$session->scan_page_number = str_pad($session->scan_page, 4, "0", STR_PAD_LEFT);
													break;
											}
										break;
									case 'download':
										switch ($session->current_allocation[0]['BMD_year'])
											{
												case 1994:
													$session->scan_page_number = substr($session->scan_page, -3);
													break;
												case 1995:
													$session->scan_page_number = substr($session->scan_page, -3);
													break;
												case 1996:
													$session->scan_page_number = substr($session->scan_page, -3);
													break;
												default:
													$session->scan_page_number = substr($session->scan_page, -4);
													break;
											}
										break;
								}
									
							break;
					}		
	}
	
public function FreeREG_action_image($start_message, $action)
	{				
		// initialise method
		$session = session();
		$allocation_images_model = new Allocation_Images_Model();
		$transcription_model = new Transcription_Model();
		
		// update trans complete date on current image if next
		if ( $action == 'next' )
			{
				$allocation_images_model
					->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
					->where('image_file_name', $session->current_image_file_name)
					->set(['trans_complete_date' => date("Y-m-d H:i:s")])	
					->update();
			}
			
		// load all images
		$TP_current_images = $allocation_images_model
			->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
			->orderby('original_image_file_name')
			->find();
		$count_images = count($TP_current_images);
			
		// find the index of the current image
		$TP_image_key = array_search($session->current_image_file_name, array_column($TP_current_images, 'image_file_name'));
		
		// set image index
		switch ( $action )
			{
				case 'next':
					$TP_image_key = $TP_image_key + 1;
					if ( $TP_image_key == $count_images )
						{
							$session->set('message_2', 'You are already transcribing the last image in this Transcription Package.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('transcribe/transcribe_step1/0') );
						}
					break;
				case 'prev':
					$TP_image_key = $TP_image_key - 1;
					if ( $TP_image_key < 0 )
						{
							$session->set('message_2', 'You are already transcribing the first image in this Transcription Package.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('transcribe/transcribe_step1/0') );
						}
					break;
			}
		
		// update image on transcription
		$transcription_model
			->where('BMD_header_index', $TP_current_images[$TP_image_key]['transcription_index'])
			->set(['BMD_scan_name' => $TP_current_images[$TP_image_key]['image_file_name']])
			->update();
		
		// update start date on image record
		$allocation_images_model
			->where('image_index', $TP_current_images[$TP_image_key]['image_index'])
			->set(['trans_start_date' => date("Y-m-d H:i:s")])
			->update();
		
		// reload current transcription
		$session->current_transcription = $transcription_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_header_index',  $TP_current_images[$TP_image_key]['transcription_index'])
			->find();
			
		// set current image file name 
		$session->current_image_file_name = $session->current_transcription[0]['BMD_scan_name']; //276
			
		// now transcribe next/prev image
		$session->FreeREG_image_flag = 1;
		$session->BMD_cycle_code = NULL;
		return redirect()->to( base_url('transcribe/transcribe_next_action') );	
	}
	
public function FreeREG_get_data_entry_format()
	{
		// initialise method
		$session = session();
		
		// set message
		$session->set('message_2', 'Select the intial event type by clicking on one of the event icons.');
		$session->set('message_class_2', 'alert alert-danger');
		
		// set counts to zero for each event type since this is the first time the user has started this transcription
		foreach ( $session->event_types as $event_type )
			{
				$counts[$event_type['type_name_lower']] = 0;
			}
		$session->counts = $counts;
		
		// similar for the layouts
		$layout_dropdown = array();
		$layout_dropdown[0] = 'Select Event Type';
		$session->layout_dropdown = $layout_dropdown;
		
		// show screen		
		echo view('templates/header');	
		echo view('linBMD2/transcribe_details_enter_title');
		echo view('linBMD2/transcribe_details_enter_form');
		if ( $session->image_source[0]['source_images'] == 'yes' )
			{
				echo view('linBMD2/transcribe_details_enter_image');
				echo view('linBMD2/transcribe_panzoom');
			}
		echo view('templates/footer');
	}
	
public function update_data_entry_format()
	{
		// initialise method
		$session = session();
		$transcription_model = new Transcription_Model();
				
		// update current transcription with data entry format
		$transcription_model
			->set(['current_data_entry_format' => $_POST['data_entry_format']])
			->where('BMD_header_index', $session->current_transcription[0]['BMD_header_index'])
			->update();
			
		// reload current transcription
		$session->current_transcription = $transcription_model
			->where('BMD_header_index',  $session->current_transcription[0]['BMD_header_index'])
			->find();
		
		// return to next action
		$session->BMD_cycle_code = '';
		$session->FreeREG_image_flag = 1;	
		return redirect()->to( base_url('transcribe/transcribe_next_action') );
	}
}
