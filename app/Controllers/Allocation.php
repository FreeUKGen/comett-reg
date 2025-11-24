<?php namespace App\Controllers;

use App\Models\Allocation_Model;
use App\Models\Allocation_Images_Model;
use App\Models\Allocation_Image_Sources_Model;
use App\Models\Syndicate_Model;
use App\Models\Identity_Model;
use App\Models\Parameter_Model;
use App\Models\Transcription_Cycle_Model;
use App\Models\Def_Ranges_Model; //Def = Data entry format
use App\Models\Project_Types_Model;
use App\Models\Transcription_Model;
use App\Models\Register_Type_Model;
use App\Models\Document_Sources_Model;
use App\Models\Transcription_Comments_Model;
use App\Models\Transcription_Detail_Def_Model;
use App\Models\Transcription_CSV_File_Model;
use App\Models\Detail_Data_Model;
use App\Models\Detail_Comments_Model;
use App\Models\Def_Fields_Model;
use MongoDB\BSON\Regex;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use \Datetime;
use App\Libraries\BackgroundProcess;

class Allocation extends BaseController
{
	function __construct() 
	{
        helper('common');
        helper('transcription');
    }
	
	public function index()
	{
		// initialise method
		$session = session();
	}


	public function create_allocation_step1($start_message)
	{		
		// initialise method
		$session = session();		
		
		switch ($start_message) 
			{
				case 0:
					// load variables from common_helper.
					load_variables();
					// input values defaults for first time
					$session->set('name', '');
					$session->set('autocreate', 'Y');
					$session->set('type', $session->project_types[0]['type_code']);
					$session->set('year', '');
					$session->set('start_page', '');
					$session->set('end_page', '');
					$session->set('make_current', 'Y');
					$session->set('reference_extension', '');
					if ( $session->current_identity[0]['last_syndicate'] == null )
						{
							$session->set('last_syndicate', '9999');
						}
					else
						{
							$session->set('last_syndicate', $session->current_identity[0]['last_syndicate']);
						}
					$session->set('field_name', '');
					// message defaults
					$session->set('message_1', 'Please enter the data required to create your allocation.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Please enter the data required to create your allocation.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
			
		echo view('templates/header');
		if ( $session->reference_extension_control == 0 )
			{
				echo view('linBMD2/create_allocation_step1');
			}
		else
			{
				echo view('linBMD2/create_allocation_reference');
			}
		echo view('templates/footer');
	}
	
	public function create_allocation_step2()
	{
		// initialise method
		$session = session();
		$identity_model = new Identity_Model();
		$syndicate_model = new Syndicate_Model();
		$allocation_model = new Allocation_Model();
		$parameter_model = new Parameter_Model();
		$def_ranges_model = new Def_Ranges_Model();
		$project_types_model = new Project_Types_Model();
		
		// get url and user password for use in curl - there's a lot of curl!
		// depends on masquerading or not.
		if ( $session->masquerade == 1 )
			{
				$user = $session->coordinator_identity_userid;
				$password = $session->coordinator_identity_password;
			}
		else
			{
				$user = $session->identity_userid;
				$password = $session->identity_password;
			}
		
		// load input values to array
		if ( $session->reference_extension_control == 0 )
			{
				$session->set('name', $this->request->getPost('name'));
				$session->set('autocreate', $this->request->getPost('autocreate'));
				$session->set('type', $this->request->getPost('type'));
				$session->set('letter', $this->request->getPost('letter'));
				$session->set('year', $this->request->getPost('year'));
				$session->set('quarter', $this->request->getPost('quarter'));
				$session->set('start_page', $this->request->getPost('start_page'));
				$session->set('end_page', $this->request->getPost('end_page'));
				$session->set('scan_format', $this->request->getPost('scan_format'));
				$session->set('make_current', $this->request->getPost('make_current'));
				$session->set('reference_extension', $this->request->getPost('reference_extension'));
			}
		else
			{
				$session->set('reference_extension', $this->request->getPost('reference_extension'));
			}						
		
		// do tests but only if reference_extension_control is 0. If it is = 1, we have already validated this stuff
		if ( $session->reference_extension_control == '0' )
			{
				// set last syndicate so as to not have to reselect it on other errors
				$session->set('last_syndicate', $session->syndicate_id);
				
				// test year numeric
				if ( ! is_numeric($session->year) )
					{
						$session->set('message_2', 'Allocation year must be numeric.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'year');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
					
				// test year not before records start year
				if ( $session->year < 1837 )
					{
						$session->set('message_2', 'Allocation year cannot be before 1837.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'year');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
					
				// test quarter has been selected
				if ( $session->quarter == 0 )
					{
						$session->set('message_2', 'Allocation quarter must be selected.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'quarter');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
					
				// create quarter name for later testing to determine reference path
				$session->quarter_name = $session->quarters_short_long[$session->quarter].'/';
			
				// test year not before records start year and quarter
				if ( $session->year == 1837 AND $session->quarter < 3 )
					{
						$session->set('message_2', 'Allocation year is 1837 so allocation quarter cannot be < September.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'year');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
					
				// test type has been selected
				if ( $session->type == 'S' )
					{
						$session->set('message_2', 'Allocation type must be selected.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'type');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
					
				// removed letter checks here. Saved in snippets
				
				// test start page for numeric
				if ( ! is_numeric($session->start_page) )
					{
						$session->set('message_2', 'Start page must be numeric.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'start_page');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
					
				// test end page for numeric
				if (  ! is_numeric($session->end_page) )
					{
						$session->set('message_2', 'End page must be numeric.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'end_page');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
				
				// test end page is not less than start page
				if ( $session->end_page < $session->start_page )
					{
						$session->set('message_2', 'End page cannot be less than start page.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'end_page');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
		
				// test scan format
				if ( $session->scan_format == 'select' )
					{
						$session->set('message_2', 'Scan fomat must be selected.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'scan_format');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
						
				// test allocation name and autocreate
				if ( $session->autocreate == 'N' AND $session->name == '' )
					{
						$session->set('message_2', 'If auto create name is No, you must enter a name yourself.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'name');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
				if ( $session->autocreate == 'Y' AND $session->name != '' )
					{
						$session->set('message_2', 'If auto create name is Yes, you must leave the allocation name blank.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('field_name', 'name');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
					
				// determine data entry format for allocation by project, type, year and quarter
				// get ranges
				$def_ranges = $def_ranges_model	
					->where('project_index', $session->current_project['project_index'])
					->where('type',  $session->type)
					->find();
				
				// any found?
				if ( ! $def_ranges )
					{
						$session->set('message_2', 'The data entry format for this allocation cannot be determined. Are you sure that your entries are correct? Type? Year? Quarter? Scan Format? If you are sure contact the FreeComETT adminstrator on '.$session->linbmd2_email.'  to report this issue (include the allocation details that you are trying to use) => No data entry range found for year, quarter and scan format in Allocation::create_allocation_step2.');
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
					
				// read though data entry ranges until good range found
				$range_found = 0;
				$yearquarter = $session->year.$session->quarter;
				foreach($def_ranges as $def_range)
					{
						$result = filter_var	(
												$yearquarter, 
												FILTER_VALIDATE_INT, 
												array	(
														'options' => array	(
																			'min_range' => $def_range['from_year'].$def_range['from_quarter'], 
																			'max_range' => $def_range['to_year'].$def_range['to_quarter'],
																			)
														)
												);
						if ( $result )
							{
								$range_found = 1;
								$session->def_format = $def_range['data_entry_format'];
								break;
							}
					}
					
				// was the def found
				if ( $range_found == 0 ) 
					{
						$session->set('message_2', 'The data entry format for this allocation cannot be determined. Are you sure that your entries are correct? Type? Year? Quarter? Scan Format? If you are sure contact the FreeComETT adminstrator on '.$session->linbmd2_email.'  to report this issue (include the allocation details that you are trying to use) => No data entry range found for year, quarter and scan format in Allocation::create_allocation_step2.');
						$session->set('message_class_2', 'alert alert-warning');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}				
			}
		else
			{
				// if here extention reference control is = 1, so check that the user chose something
				if ( $session->reference_extension == 0 )
					{
						// nothing was chosen
						$session->set('message_2', 'Please choose a reference extension from the dropdown list');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('reference_extension_control', '1');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
			}
			
		// test if an allocation already exists which has the same pages as the being created
		$allocations =	$allocation_model
			->where('project_index', $session->current_project['project_index'])
			->where('BMD_syndicate_index', $session->syndicate_id)
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('BMD_year', $session->year)
			->where('BMD_quarter', $session->quarter)
			->where('BMD_letter', $session->letter)
			->where('BMD_type', $session->type)
			->findAll();
		// found ? read each one to see if new pages are covered
		if ( $allocations )
			{
				foreach ( $allocations as $all )
					{
						// is new same as existing?
						if ( $session->start_page == $all['BMD_start_page'] AND $session->end_page == $all['BMD_end_page'] )
							{
								$session->set('message_2', 'You already have an allocation in this syndicate for the same year, quarter, and type exactly matching your new start and end pages');
								$session->set('message_class_2', 'alert alert-danger');
								return redirect()->to( base_url('allocation/create_allocation_step1/1') );
							}
							
						// is new in an existing?
						if ( $session->start_page > $all['BMD_start_page'] AND $session->end_page < $all['BMD_end_page'] )
							{
								$session->set('message_2', 'You already have an allocation in this syndicate for the same year, quarter, and type which includes your new start and end pages');
								$session->set('message_class_2', 'alert alert-danger');
								return redirect()->to( base_url('allocation/create_allocation_step1/1') );
							}
							
						// does existing partial cover new - start page?
						if ( $session->start_page >= $all['BMD_start_page'] AND $session->start_page <= $all['BMD_end_page'] )
							{
								$session->set('message_2', 'You already have an allocation in this syndicate for the same year, quarter, and type which partially covers your new start and end pages');
								$session->set('message_class_2', 'alert alert-danger');
								return redirect()->to( base_url('allocation/create_allocation_step1/1') );
							}
							
						// does existing partial cover new end page?
						if ( $session->end_page <= $all['BMD_end_page'] AND $session->end_page >= $all['BMD_start_page'] )
							{
								$session->set('message_2', 'You already have an allocation in this syndicate for the same year, quarter, and type which partially covers your new start and end pages');
								$session->set('message_class_2', 'alert alert-danger');
								return redirect()->to( base_url('allocation/create_allocation_step1/1') );
							}	
					}
			}
			
		// all good
		
		// remove no checks flag at end of data if there is one
		if ( substr($session->letter, -1) == '#' ) 
			{
				$session->letter = substr($session->letter, 0, -1);
			}
		
		// get current project type
		$session->current_project_type = $project_types_model
			->where('type_code', $session->type)
			->find();

		// get curl stuff but only if reference_extension_control is 0. If it is = 1, we have already validated this stuff
		// the idea here is that a scan matching the allocation parameters can be found. Scans won't be downloaded until a transcription in the allocation is created
		if ( $session->reference_extension_control == '0' )
			{
				// this must depend on the project - need more info
				// kickstart the scan path
				$session->set('scan_path', 'GUS/'.$session->year.'/'.$session->current_project_type[0]['type_name_lower'].'/');
			}
		else
			{
				// do letter test only if length of entered letter = 1 ie a single letter was entered, which can be a range, eg A-C
				if ( strlen($session->letter) == 1 )
					{
						// test that scan letter is in the letter range if this reference extension is a letter range
						$letters = array();
						$letters = explode('-', $session->reference_extension_array[$session->reference_extension]); // explode the extension
						if ( isset($letters[1]) )
						{
							$letters[1] = substr($letters[1], 0, -1); // remove last character = remove the /
							// letter range?
							if ( array_search($letters[0], $session->alphabet) !== false AND  array_search($letters[1], $session->alphabet) !== false )
								{
									// if so is the scan letter in the range?
									$letter_found = 0;
									foreach  ( range($letters[0], $letters[1]) as $letter )
										{
											if ( $letter == $session->letter )
												{
													$letter_found = 1;
												}
										}
									// was it found
									if ( $letter_found == 0 )
										{
											// oops wrong letter range
											$session->set('message_2', 'Please choose the correct range for the allocation letter you entered => '.$session->letter);
											$session->set('message_class_2', 'alert alert-danger');
											$session->set('reference_extension_control', '1');
											$session->set('field_name', 'letter');
											return redirect()->to( base_url('allocation/create_allocation_step1/1') );
										}
								}
						}
					}
				// add user selection to scan path
				$session->set('scan_path', $session->scan_path.$session->reference_extension_array[$session->reference_extension]);
			}
			
		// now search through the scan path until a scan is found
		// image url does not depend on environment
		$session->set('scan_found', 0);
		while ( $session->scan_found == 0 )
			{
				// setup curl
				$curl_url = $session->freeukgen_source_values['image_server'].$session->scan_path;
				$ch = curl_init($curl_url);
				curl_setopt($ch, CURLOPT_USERPWD, "$user:$password");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				
				// debug options
				//curl_setopt($ch, CURLOPT_VERBOSE, true);
				//curl_setopt($ch, CURLOPT_STDERR, fopen(getcwd()."/curl.log", 'a+'));
								
				// do the curl
				$curl_result = curl_exec($ch);
				
				// anything found
				if ( $curl_result == '' )
					{
						// problem so send error message
						$session->set('message_2', 'A technical problem occurred. Send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Failed to fetch references in Allocation::create_allocation_step2 => '.$curl_url);
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('reference_extension_control', '0');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
				
				curl_close($ch);
							
				// load returned data to array
				$lines = preg_split("/\r\n|\n|\r/", $curl_result);
							
				// now test to see if a valid page was found
				foreach($lines as $line)
					{
						if ( strpos($line, "404 Not Found") !== false )
							{
								$session->set('message_2', 'A technical problem occurred. Please send an email to '.$session->linbmd2_email.' describing what you were doing when the error occurred => Malformed URL in Allocation::create_allocation_step2, , around line 198 => '.$curl_url);
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('reference_extension_control', '0');
								return redirect()->to( base_url('allocation/create_allocation_step1/1') );
							}
					}
					
				// get all unique hrefs
				$search = "<li><a href='";
				$hrefs = array();
				foreach($lines as $line)
					{
						if ( strpos($line, $search) !== false )
							{
								// get the href
								$href = get_string_between($line, "<li><a href='", "'>");
								// I have a href; check its not already in the array, store if not
								if ( array_search($href, $hrefs) === false )
									{
										$hrefs[] = $href;
									}
							}
					}
					
				// does the quarter requested by the user exist in hrefs? if so avoid requesting the quarter again by removing unrequired hrefs.
				$result = array_search($session->quarter_name, $hrefs);
				if ( $result !== false )
					{
						// the quarter was found so use it
						$hrefs = array();
						$hrefs[] = $session->quarter_name;
					}
					
				// does hrefs contain scans? if so break the while loop. a scan starts with the year and the type (B, M, D)
				$search = $session->year.$session->type;
				foreach ( $hrefs as $key => $value )
					{
						if ( strpos($value, $search) !== false )
							{
								$session->set('scan_found', 1);
								$session->set('reference_extension_control', '0');
								break 2;
							}
					}
					
				// so, if here, no scans were detected, continue building the scan path
				// if hrefs is empty, there is a problem, report it back to the user.
				if ( count($hrefs) == 0 )
					{
						$session->set('message_2', 'Path to scans cannot be identified, Please review your Allocation entries. => Malformed URL in Allocation::create_allocation_step2 => '.$curl_url);
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('reference_extension_control', '0');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
					
				// if hrefs contains more than one entry ask user to choose which one
				if ( count($hrefs) > 1 )
					{
						array_unshift($hrefs, "Please select the source for your scans");
						$session->set('message_2', 'There are multiple sources for the scans for this allocation. Please choose the correct one. If quarters, no scans where found for the quarter you entered, '.$session->quarter_name.'.');
						$session->set('message_class_2', 'alert alert-warning');
						$session->set('reference_extension_array', $hrefs);
						$session->set('reference_extension_control', '1');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
					
				// hrefs contains only one entry and it is not a scan, add it to the path and loop
				// save scan path to session 
				$session->set('scan_path', $session->scan_path.$hrefs[0]);
			}	// end loop

		// scans were found so scan path is known. hrefs contains all the scan names. 
		// Now test that the page range is consistent with the letter
		$valid_hrefs = array();
		foreach ( $hrefs as $key => $value )
			{
				// explode the scan name on . to test the file extension
				$exploded_scan_name = explode('.', $value);
				
				// very browsers can display tif or tiff files so exclude them
				if ( $exploded_scan_name[1] == 'tif' OR $exploded_scan_name[1] == 'tiff' )
					{
						$session->set('message_2', 'You have selected a scan source which contains .tif file images. Very few browsers can display tif images. Please selected a source that contains jpg images (usually ANC-nn).');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('reference_extension_control', '0');
						return redirect()->to( base_url('allocation/create_allocation_step1/1') );
					}
				
				// now explode scan name on - to get the letter and page.
				$exploded_scan_name = explode('-', $exploded_scan_name[0]);
				// element 1 contains the letter, element 2 contains the page.
				// test that I have the letter or letter range I am looking for 
				if ( count($exploded_scan_name) == 3 )
					{
						// single character letter
						if ( $exploded_scan_name[1] == $session->letter )
							{
								// I have found the start of the letter range
								// $valid_hrefs contains the full range by page of all scans for this letter
								$valid_hrefs[] = $exploded_scan_name[2];
							}
					}
				else
					{
						// composite character letter
						if ( $exploded_scan_name[1].'-'.$exploded_scan_name[2] == $session->letter )
							{
								// I have found the start of the letter range
								// $valid_hrefs contains the full range by page of all scans for this letter
								$valid_hrefs[] = $exploded_scan_name[3];
							}
					}
			}

		// test will fail if $valid_hrefs is empty
		if ( empty($valid_hrefs) )
			{
				$session->set('message_2', 'Cannot check page range is OK for the letter you entered as no images have been found. Check the letter or letter range is correct => '.$session->letter.'.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('reference_extension_control', '0');
				return redirect()->to( base_url('allocation/create_allocation_step1/1') );
			}
		
		
		// test start and end hrefs values for numerics; if not make them numeric
		$start_test = $valid_hrefs[0];
		if ( ! is_numeric($valid_hrefs[0]) )
			{
				$start_test = '0000';
			}
		$end_test = end($valid_hrefs);
		if ( ! is_numeric(end($valid_hrefs)) )
			{
				$end_test = '9999';
			}
			
		// Now I can test if the start page and end page are in the scans for this letter range
		if ( $session->start_page < $start_test OR $session->end_page > $end_test )
			{
				$session->set('message_2', 'The page range is not valid for the scan letter you entered. The scan page range for this letter, '.$session->letter.', using scan path, '.$session->scan_path.', starts at '.$valid_hrefs[0].' and ends at '.end($valid_hrefs).'. Please review your Allocation entries.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('reference_extension_control', '0');
				return redirect()->to( base_url('allocation/create_allocation_step1/1') );
			}
	
		// is the start page in the valid hrefs
		$found_flag = 0;
		$session->start_page = str_pad($session->start_page, 4, "0", STR_PAD_LEFT);
		foreach ( $valid_hrefs as $href )
			{
				if ( $href == $session->start_page )
					{
						$found_flag = 1;
					}
			}
		if ( $found_flag == 0 )
			{
				$session->set('message_2', 'The start page is not in the list of scans found on the image server which means that a scan does not exist for the start page you entered. Please review your Allocation entries.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('reference_extension_control', '0');
				return redirect()->to( base_url('allocation/create_allocation_step1/1') );
			}
		
		// is the end page in the valid hrefs
		$found_flag = 0;
		$session->end_page = str_pad($session->end_page, 4, "0", STR_PAD_LEFT);	
		foreach ( $valid_hrefs as $href )
			{
				if ( $href == $session->end_page )
					{
						$found_flag = 1;
					}
			}
		if ( $found_flag == 0 )
			{
				$session->set('message_2', 'The end page is not in the list of scans found on the image server which means that a scan does not exist for the end page you entered. Please review your Allocation entries.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('reference_extension_control', '0');
				return redirect()->to( base_url('allocation/create_allocation_step1/1') );
			}		

		// get scan type eg jpg
		foreach ( $hrefs as $key => $value )
			{
				if ( strpos($value, '.') !== false )
					{
						$scan_type = substr($value, strpos($value, '.')+1);
						break;
					}
			}
		// explode the scan path
		$exploded_scan_path = array();
		$exploded_scan_path = explode('/', $session->scan_path);
		
		// Create the name if autocreate = yes
		if ( $session->autocreate == 'Y' )
			{
				// create the name depending if a quarter was found
				if ( array_search($exploded_scan_path[3], $session->quarters_short_long) !== false ) 
					{
						// quarter was found
						$session->set('name', $session->year.' '.$exploded_scan_path[3].' '.$session->current_project_type[0]['type_name_lower'].', '.$session->letter.' surnames, pages '.$session->start_page.' to '.$session->end_page.', using scan format '.$session->scan_format);
					}
				else
					{
						// quarter was not found
						$session->set('name', $session->year.' '.$session->current_project_type[0]['type_name_lower'].', '.$session->letter.' surnames, pages '.$session->start_page.' to '.$session->end_page.', using scan format '.$session->scan_format);
					}
			}
		
		// create quarter if year based
		if ( array_search($exploded_scan_path[3], $session->quarters_short_long) === false ) 
			{
				// quarter was not  found = year based, so set quarter = 4
				$session->set('quarter', '4');
			}
			
		// add allocation to table
		// create the data for the insert
		$data =	[
					'project_index' => $session->current_project['project_index'],
					'BMD_identity_index' => $session->BMD_identity_index,
					'BMD_syndicate_index' => $session->syndicate_id,
					'BMD_allocation_name' => $session->name,
					'BMD_reference' => $session->scan_path,
					'BMD_start_date' => $session->current_date,
					'BMD_end_date' => '',
					'BMD_start_page' => $session->start_page,
					'BMD_end_page' => $session->end_page,
					'BMD_year' => $session->year,
					'BMD_quarter' => $session->quarter,
					'BMD_letter' => $session->letter,
					'BMD_type' => $session->type,
					'BMD_scan_type' => $scan_type,
					'BMD_last_action' => 'Create Allocation',
					'BMD_status' => 'Open',
					'BMD_sequence' => 'SEQUENCED',
					'data_entry_format' => $session->def_format,
					'scan_format' => $session->scan_format,
					'source_code' => 'BS',
				];
		$id = $allocation_model->insert($data);
		
		// update identity with last syndicate and last allocation
		$data =	[
					'last_syndicate' => $session->syndicate_id,
					'last_allocation' => $id,
				];
		$identity_model->update($session->BMD_identity_index, $data);
		// reload identity
		$session->current_identity = $identity_model	
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('project_index', $session->current_project['project_index'])
			->find();
		// reload allocation
		load_variables();
			
		// return
		$session->set('scan_name', '');
		$session->set('message_2',  'Your new Allocation has been been created. Go to Create a new Transcription to start using it to create a Transcription.');
		$session->set('message_class_2', 'alert alert-success');
		$session->set('reference_extension_control', '0');
		return redirect()->to( base_url('transcribe/transcribe_step1/1') );
	}
	
	public function manage_allocations($start_message)
	{
		// initialise method
		$session = session();
		$allocation_model = new Allocation_Model();
		
		// set messages
		$session->alloc_sort_by = 'allocation.Change_date';
		$session->alloc_sort_order = '';
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', $session->current_project['allocation_text'].' Home Page');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					// sort
					if ( ! isset($session->alloc_sort_by) )
						{
							$session->alloc_sort_by = 'allocation.Change_date';
							$session->alloc_sort_order = 'DESC';
							$session->alloc_sort_name = 'Last change date/time';
						}
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage '.$session->current_project['allocation_text'].'.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// get all allocations
		if (0 == $start_message) {
			$session->allocations = $allocation_model
			->where('allocation.BMD_identity_index', $session->BMD_identity_index)
			->where('allocation.project_index', $session->current_project['project_index'])
			->where('allocation.BMD_syndicate_index', $session->syndicate_id)
			->where('allocation.BMD_status', $session->allocation_status)
			->join('syndicate', 'allocation.BMD_syndicate_index = syndicate.BMD_syndicate_index')
			->join('register_type', 'allocation.REG_register_type = register_type.register_code')
			->join('allocation_image_sources', 'allocation.source_code = allocation_image_sources.source_code')
			->orderBy($session->alloc_sort_by, $session->alloc_sort_order)
			->findAll();
		} 
		else {
			$session->allocations = $allocation_model
			->where('allocation.BMD_identity_index', $session->BMD_identity_index)
			->where('allocation.project_index', $session->current_project['project_index'])
			->where('allocation.BMD_syndicate_index', $session->syndicate_id)
			->where('allocation.BMD_status', $session->allocation_status)
			->join('syndicate', 'allocation.BMD_syndicate_index = syndicate.BMD_syndicate_index')
			->join('register_type', 'allocation.REG_register_type = register_type.register_code')
			->join('allocation_image_sources', 'allocation.source_code = allocation_image_sources.source_code')
			->findAll();
		}

		// show allocations
		echo view('templates/header');
		echo view('linBMD2/manage_allocations');
		echo view('linBMD2/sortTableNew');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$allocation_model = new Allocation_Model();
		$allocation_images_model = new Allocation_Images_Model();
		$allocation_image_sources_model = new Allocation_Image_Sources_Model();
		$syndicate_model = new Syndicate_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		$transcription_model = new Transcription_Model();
		
		// get inputs
		$BMD_allocation_index = $this->request->getPost('allocation_index');
		$session->set('BMD_cycle_code', $this->request->getPost('alloc_next_action'));
		
		$session->set('BMD_cycle_text', $transcription_cycle_model	
			->where('project_index', $session->current_project['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'ALLOC')
			->find());
		
		// get allocation 
		$session->current_allocation = $allocation_model	
			->where('BMD_allocation_index',  $BMD_allocation_index)
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('project_index', $session->current_project['project_index'])
			->find();
		// should never happen but ...
		if ( ! $session->current_allocation )
			{
				$session->set('message_2', 'Invalid '.$session->current_project['allocation_text'].'. Please contact '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('allocation/manage_allocations/2') );
			}
			
		// get syndicate 
		$session->current_syndicate = $syndicate_model		
			->where('BMD_syndicate_index',  $session->current_allocation[0]['BMD_syndicate_index'])
			->where('project_index', $session->current_project['project_index'])
			->find();
		// should never happen but...
		if ( ! $session->current_syndicate )
			{
				$session->set('message_2', 'Invalid syndicate. Please contact '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('allocation/manage_allocations/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NONEA': // nothing was selected
					$session->set('message_2', 'Select an action');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('allocation/manage_allocations/2') );
					break;
				
				case 'CHGEA': // List images
					// only for FreeREG
					if ( $session->current_project['project_index'] != 2 )
						{
							$session->set('message_2', 'Edit Assignment option is only available to FreeREG transcribers.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('allocation/manage_allocations/2') );
						}
					
					// only for FreeComETT created assignments
					// get source records
					$source = $allocation_image_sources_model
						->where('project_index', $session->current_project['project_index'])
						->where('source_code', $session->current_allocation[0]['source_code'])
						->find();
					if ( ! $source OR $source[0]['source_manual'] != 'Y' )
						{
							$session->set('message_2', 'Edit Assignment option is only available for Assignments created within FreeComETT. If you are trying to edit an Assignment created in FreeREG, please contact you co-ordinator.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('allocation/manage_allocations/2') );
						}
						
					// set last action
					$allocation_model
						->set(['BMD_last_action' => $session->BMD_cycle_text[0]['BMD_cycle_name']])
						->where('BMD_allocation_index', $BMD_allocation_index)
						->update();
							
					// change assignment
					return redirect()->to(base_url('allocation/change_assignment_step1/0') );
					break;
					
				case 'LISTI': // List images
					// only for FreeREG
					if ( $session->current_project['project_index'] != 2 )
						{
							$session->set('message_2', 'List images option is only available to FreeREG transcribers.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('allocation/manage_allocations/2') );
						}
					
					// set last action
					$allocation_model
						->set(['BMD_last_action' => $session->BMD_cycle_text[0]['BMD_cycle_name']])
						->where('BMD_allocation_index', $BMD_allocation_index)
						->update();
							
					// list images
					return redirect()->to(base_url('allocation/list_images') );
					break;
					
				case 'CLOSA': // close 
					// action depends on project
					switch ($session->current_project['project_index']) 
						{
							case 1: // FreeBMD
								$data =	[
											'BMD_status' => 'Closed',
											'BMD_end_date' => $session->current_date,
											'BMD_last_action' => $session->BMD_cycle_text[0]['BMD_cycle_name'],
										];
								$allocation_model->update($BMD_allocation_index, $data);
								$session->set('message_2', 'The '.$session->current_project['allocation_text'].' you selected was closed successfully.');
								$session->set('message_class_2', 'alert alert-success');
								return redirect()->to( base_url('allocation/manage_allocations/2') );
								break;
							case 2: // FreeREG
								return redirect()->to( base_url('allocation/close_freereg_assignment_step1/0/'.$BMD_allocation_index) );
								break;
						}
					break;
				case 'REOPA': // reopen
					$data =	[
										'BMD_status' => 'Open',
										'BMD_last_action' => $session->BMD_cycle_text[0]['BMD_cycle_name'],
									];
					$allocation_model->update($BMD_allocation_index, $data);
					$session->set('message_2', 'The '.$session->current_project['allocation_text'].' you selected was re-opened successfully.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('allocation/manage_allocations/2') );
					break;
				case 'SNDEM': //Send email
					// only if allocation is closed
					if ( $session->current_allocation[0]['BMD_status'] == 'Closed' )
						{
							$data =	[
										'BMD_last_action' => $session->BMD_cycle_text[0]['BMD_cycle_name'],
									];
							$allocation_model->update($BMD_allocation_index, $data);
							
							// send email
							return redirect()->to(base_url('email/send_email/allocation') );
						}
					else
						{
							$session->set('message_2', 'Cannot send email to request new '.$session->current_project['allocation_text'].' as the current '.$session->current_project['allocation_text'].' is not closed.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('allocation/manage_allocations/2') );
						}
					break;
				case 'DELEA': // delete allocation
					// only if no transcriptions exist against this allocation
					// get transcriptions for this allocation

					$transcriptions = $transcription_model->where('BMD_allocation_index',  $BMD_allocation_index)
														->where('BMD_identity_index', $session->BMD_identity_index)
														->where('project_index', $session->current_project['project_index'])
														->find();
					// if any found cannot delete
					if ( $transcriptions )
						{
							$session->set('message_2', 'Cannot delete this '.$session->current_project['allocation_text'].' because Transcriptions exist against it.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('allocation/manage_allocations/2') );
						}
					else
						{
							// delete it
							$allocation_model->delete($BMD_allocation_index);
							$session->set('message_2', $session->current_project['allocation_text'].', '.$session->current_allocation[0]['BMD_allocation_name'].', has been deleted.');
							$session->set('message_class_2', 'alert alert-success');
							return redirect()->to( base_url('allocation/manage_allocations/2') );
						}
					break;
			}
		// no action found - Oops should never happen
		$session->set('message_2', 'No action performed. Selected action not recognised. Report to '.$session->linbmd2_email);
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('allocation/manage_allocations/2') );			
	}
	
	public function sort($by)
	{
		// initialise method
		$session = session();
		
		// set sort by
		switch ($by) 
			{
				case 1:
					$session->alloc_sort_by = 'syndicate.BMD_syndicate_name';
					$session->alloc_sort_order = 'ASC';
					$session->alloc_sort_name = 'Syndicate Name';
					break;
				case 2:
					$session->alloc_sort_by = 'allocation.BMD_allocation_name';
					$session->alloc_sort_order = 'ASC';
					$session->alloc_sort_name = 'Allocation Name';
					break;
				case 3:
					$session->alloc_sort_by = 'allocation.BMD_start_date';
					$session->alloc_sort_order = 'DESC';
					$session->alloc_sort_name = 'Start Date';
					break;
				case 4:
					$session->alloc_sort_by = 'allocation.BMD_end_date';
					$session->alloc_sort_order = 'ASC';
					$session->alloc_sort_name = 'End Date';
					break;
				case 5:
					$session->alloc_sort_by = 'allocation.BMD_last_uploaded';
					$session->alloc_sort_order = 'ASC';
					$session->alloc_sort_name = 'Last Page Uploaded';
					break;
				case 6:
					$session->alloc_sort_by = 'allocation.BMD_status';
					$session->alloc_sort_order = 'ASC';
					$session->alloc_sort_name = 'Status';
					break;
				case 7:
					$session->alloc_sort_by = 'allocation.BMD_last_action';
					$session->alloc_sort_order = 'ASC';
					$session->alloc_sort_name = 'Last Action Performed';
					break;
				case 8:
					$session->alloc_sort_by = 'allocation.Change_date';
					$session->alloc_sort_order = 'ASC';
					$session->alloc_sort_name = 'Last Change Date';
					break;
				default:
					$session->alloc_sort_by = 'allocation.Change_date';
					$session->alloc_sort_order = 'DESC';
					$session->alloc_sort_name = 'Last Change Date/Time';
			}
				
		return redirect()->to( base_url('allocation/manage_allocations/1') );
	}
	
	public function toogle_allocations()
	{
		// initialise
		$session = session();
		
		// change status
		if ( $session->allocation_status == 'Open' )
			{
				$session->allocation_status = 'Closed';
			}
		else
			{
				$session->allocation_status = 'Open';
			}
			
		// redirect to manage allocations
		return redirect()->to( base_url('allocation/manage_allocations/0') );
	}
	
	public function list_images()
	{
		// initialise
		$session = session();
		$session->operation = 'list_images';

		$allocation_model = new Allocation_Model();
		$allocation_images_model = new Allocation_Images_Model();
		
		// get images
		$session->allocation_images = $allocation_images_model
			->where('allocation_index', $session->current_allocation[0]['BMD_allocation_index'])
			->orderby('original_image_file_name')
			->findAll();

		// show images
		echo view('templates/header');
		echo view('linBMD2/new_list_images');
		echo view('linBMD2/sortTableNew');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');
	}


	public function new_list_images()
	{
		// initialise
		$session = session();
		$session->operation = 'list_images';

		$allocation_model = new Allocation_Model();
		$allocation_images_model = new Allocation_Images_Model();

		$allocator = [];
		$allocator[] = ['BMD_allocation_name' => "WK ImageTest Allocation"];
		$session->current_allocation = $allocator;
		//$session->current_allocation[0]['BMD_allocation_name'] = "WK ImageTest Allocation";

		// test mode - use .env setting if it exists
		$allocation_index = (int)getenv('test.allocation_index');
		if (!$allocation_index) {
			$allocation_index = $session->current_allocation[0]['BMD_allocation_index'];
		}

		// get images
		$session->allocation_images = $allocation_images_model
			->where('allocation_index', $allocation_index)
			->orderby('original_image_file_name')
			->findAll();

		// show images
		echo view('templates/header');
		echo view('linBMD2/new_list_images');
		echo view('linBMD2/sortTableNew');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');
	}


	public function new_create_assignment()
	{
load_variables();

		// initialise method
		$session = session();
		$allocation_image_sources_model = new Allocation_Image_Sources_Model();
		$project_types_model = new Project_Types_Model();
		$register_type_model = new Register_Type_Model();
		$document_sources_model = new Document_Sources_Model();

				// load MongoDB
				// define mongodb - see common helper
				define_environment(3);
				$mongodb = define_mongodb();

//		foreach ($mongodb['database']->listCollections() as $collectionInfo) {
//			log_message('info', 'COL::' . print_r($collectionInfo, true));
//		}
		// log_message('info', 'COL1:' . $mongodb['database']->getCollection('userid_details'));
		$users = $mongodb['database']->getCollection('userid_details');
		$allUsers = $users->find();
		log_message('info', 'Users:' . print_r($allUsers, true));


				// Images Sources - comes from FreeComETT DB
				$session->allocation_image_sources = $allocation_image_sources_model
					->where('project_index', $session->current_project['project_index'])
					->where('source_manual', 'Y')
					->orderby('source_order')
					->findAll();
				// all found?
				if ( !$session->allocation_image_sources )
				{
					$session->set('message_2', 'Cannot create assignment. Image sources cannot be loaded. Report to '.$session->linbmd2_email);
					$session->set('message_class_2', 'alert alert-error');
					return redirect()->to( base_url('allocation/manage_allocations/2') );
				}

				// create county groups
				$county_groups = array();
				foreach ( $session->freeukgen_source_values as $key => $source_value )
				{
					if ( str_contains($key, 'counties') )
					{
						$group = explode('_', $key);
						$county_groups[] = $group[1];
					}
				}
				$session->county_groups = $county_groups;
				log_message('info', 'groups:' . print_r($session->county_groups, true));

				// get register types
				$session->register_types = $register_type_model
					->where('project_index', $session->current_project['project_index'])
					->where('register_active', 'yes')
					->orderby('register_order')
					->findAll();

				// get document sources
				$session->document_sources = $document_sources_model
					->orderby('document_source')
					->findAll();

				// set assignment mode
				$session->assignment_mode = 'create';

				// initialise current allocation array in order to keep javascript happy!
				$current_allocation = array();
				$current_allocation[0] = array();
				$session->current_allocation = $current_allocation;

		$session->set('message_1', 'Please enter the data required to create your assignment.');
		$session->set('message_class_1', 'alert alert-primary');

		// show views
		echo view('templates/header');
		echo view('linBMD2/new_create_assignment'); // same view used for both create and change
		echo view('templates/footer');
	}
	
	public function create_assignment_step1($start_message)
	{		
		// initialise method
		$session = session();
		$allocation_image_sources_model = new Allocation_Image_Sources_Model();
		$project_types_model = new Project_Types_Model();
		$register_type_model = new Register_Type_Model();
		$document_sources_model = new Document_Sources_Model();		
		
		switch ($start_message) 
			{
				case 0:
					// load variables from common_helper.php
					load_variables();
					
					// load MongoDB
					// define mongodb - see common helper
					define_environment(3);
					$mongodb = define_mongodb();

					// Images Sources - comes from FreeComETT DB
					$session->allocation_image_sources = $allocation_image_sources_model
						->where('project_index', $session->current_project['project_index'])
						->where('source_manual', 'Y')
						->orderby('source_order')
						->findAll();
					// all found?
					if ( !$session->allocation_image_sources )
						{
							$session->set('message_2', 'Cannot create assignment. Image sources cannot be loaded. Report to '.$session->linbmd2_email);
							$session->set('message_class_2', 'alert alert-error');
							return redirect()->to( base_url('allocation/manage_allocations/2') );
						}
						
					// create county groups
					$county_groups = array();
					foreach ( $session->freeukgen_source_values as $key => $source_value )
						{
							if ( str_contains($key, 'counties') )
								{
									$group = explode('_', $key);
									$county_groups[] = $group[1];
								}
						}
					$session->county_groups = $county_groups;
log_message('info', 'groups:' . print_r($session->county_groups, true));
				
					// get register types
					$session->register_types = $register_type_model
						->where('project_index', $session->current_project['project_index'])
						->where('register_active', 'yes')
						->orderby('register_order')
						->findAll();
						
					// get document sources
					$session->document_sources = $document_sources_model
						->orderby('document_source')
						->findAll();
						
					// set assignment mode
					$session->assignment_mode = 'create';
					
					// initialise current allocation array in order to keep javascript happy!
					$current_allocation = array();
					$current_allocation[0] = array();
					$session->current_allocation = $current_allocation;
				
					// message defaults
					$session->set('message_1', '');
					$session->set('message_class_1', '');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Please enter the data required to create your assignment.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
		
		// show views			
		echo view('templates/header');
		echo view('linBMD2/manage_assignment_step1'); // same view used for both create and change
		echo view('templates/footer');
	}
	
	public function create_assignment_step2($start_message)
	{				
		// initialise method
		$session = session();
		$allocation_image_sources_model = new Allocation_Image_Sources_Model();
		$project_types_model = new Project_Types_Model();
		$allocation_model = new Allocation_Model();
		$register_type_model = new Register_Type_Model();
		$allocation_images_model = new Allocation_Images_Model();
		define_environment(3);
		$mongodb = define_mongodb();
		
		// payload received?
		if ( ! $_POST )
			{
				// no data received
				echo 'ERROR => No $_POST data received. Report to '.$session->linbmd2_email;
				return;
			}
			
		// load general input values
		$ass_name = $_POST['ass_name'];
		$county_group = $_POST['county_group'];
		$county = $_POST['county'];
		$chapman_code = $_POST['chapman_code'];
		$place = $_POST['place'];
		$church = $_POST['church'];
		$church_code = $_POST['church_code'];
		$church_code = strtoupper($church_code);
		$source_code = $_POST['source'];
		$register_code = $_POST['register'];
		$session->document_source = $_POST['doc_source'];
		$session->document_comment = $_POST['doc_comment'];
		
		// add it to the freecomett allocations table
		$allocation_model
			->set(['project_index' => $session->current_project['project_index']])
			->set(['BMD_identity_index' => $session->current_identity[0]['BMD_identity_index']])
			->set(['BMD_syndicate_index' => $session->current_syndicate[0]['BMD_syndicate_index']])
			->set(['BMD_allocation_name' => $ass_name])
			->set(['BMD_reference' => ''])
			->set(['BMD_start_date' => date('d-M-Y')])
			->set(['BMD_end_date' => ''])
			->set(['BMD_start_page' => 1])
			->set(['BMD_last_uploaded' => null])
			->set(['BMD_end_page' => null])
			->set(['BMD_year' => date('Y')])
			->set(['BMD_quarter' => 0])
			->set(['BMD_letter' => ''])
			->set(['BMD_type' => 'C']) // C = Composite, ie assignment could contain any event type.
			->set(['BMD_scan_type' => 'jpg'])
			->set(['BMD_last_action' => 'Create '.$session->current_project['allocation_text']])
			->set(['BMD_status' => 'Open'])
			->set(['BMD_sequence' => 'SEQUENCED'])
			->set(['data_entry_format' => 'composite'])
			->set(['scan_format' => 'FreeREG'])
			->set(['REG_assignment_id' => null])
			->set(['REG_county_group' => $county_group])
			->set(['REG_county' => $county])
			->set(['REG_chapman_code' => $chapman_code])
			->set(['REG_place' => $place])
			->set(['REG_church_name' => $church])
			->set(['REG_church_code' => $church_code])
			->set(['REG_register_type' => $register_code])
			->set(['REG_image_folder_name' => null])
			->set(['source_code' => $source_code])
			->insert();

		// get the insert key
		$allocation_index = $allocation_model->getInsertID();
					
		// load allocation record
		$session->current_allocation = $allocation_model
			->where('project_index', $session->current_project['project_index'])
			->where('BMD_allocation_index', $allocation_index)
			->find();
			
		// insert the assignment to the back end
		$now = new DateTime();
		$milli = (int) $now->format('Uv');
		$insertResult = $mongodb['database']->selectCollection('assignments')->insertOne
			(
				[ 	
					'userid_detail_id' => $session->submitter[0]['_id'],
					'instructions' => 'Inserted by FreeComETT',
					'assign_date' => new UTCDateTime($milli),
					'syndicate_id' => new ObjectId($session->current_syndicate[0]['BMD_syndicate_index']),
					'freecomett' => '1',
				]
			);
			
		// initialse files count
		$image_in = 0;
		$image_out = 0;
		$image_no_err = 0;
			
		// get fields depending on image source
		switch ( $session->current_allocation[0]['source_code'] )
			{
				case 'HC':
					// hard copy no images required, so create the TP
					break;		
				case 'LP':	
					// $_FILES contains 6 arrays; name of files, full_path, type, tmp_name, error, size 
					if ( isset($_FILES['images']['name']) )
						{
							// read errors
							foreach ( $_FILES['images']['error'] as $key => $error )
								{
									// increment total count
									$image_in = $image_in + 1;
									// process file only if no error
									if ( $error == 0 )
										{
											// increment image count
											$image_no_err = $image_no_err + 1;
											// create file name
											$image_name = $allocation_index.'_'.$image_no_err.'_'.$_FILES['images']['name'][$key];
											$image_name_noext = pathinfo($image_name, PATHINFO_FILENAME);
											//$image_path = getcwd().'/Users/'.$session->current_project['project_name'].'/'.$session->identity_userid.'/Scans/';
											$user_path = getenv('app.userDir');
											$image_path = $user_path .'/' . $session->identity_userid.'/Scans/';
											$image_url = $image_path.$image_name;
											// Upload file
											if ( move_uploaded_file($_FILES['images']['tmp_name'][$key],$image_url) )
												{
													// increment images uploaded
													$image_out = $image_out + 1;
													// make image extension lower case
													$image_extension = pathinfo($image_url, PATHINFO_EXTENSION);
													$image_extension = strtolower($image_extension);
													// do I have a pdf file
													switch ( $image_extension )
														{
															case 'pdf':
																// extract images from pdf
																$command = '/usr/local/bin/pdfimages -j '.$image_url.' '.$image_path.$image_name_noext;
																exec($command, $output, $retval);
																// remove pdf file as no longer needed
																unlink($image_url);
																break;
															default:
																break;
														}
												}
										}
								}
								
							// now get the names of the images just created for this allocation in order to write image record
							$uploaded_images = glob($image_path.$allocation_index.'*');
							// read matched files
							foreach ( $uploaded_images as $img )
								{
									// get file name
									$img_name = pathinfo($img, PATHINFO_BASENAME);
									$ori_name = explode('_', $img_name)[2];
									// write image record
									$allocation_images_model
										->set(['project_index' => $session->current_project['project_index']])
										->set(['allocation_index' => $allocation_index])
										->set(['transcription_index' => NULL])
										->set(['identity_index' => $session->current_identity[0]['BMD_identity_index']])
										->set(['image_id' => NULL])
										->set(['original_image_file_name' => $ori_name])
										->set(['image_file_name' => $img_name])
										->set(['image_url' => $img])
										->set(['image_status' => 'bt'])
										->set(['trans_start_date' => NULL])
										->set(['trans_complete_date' => NULL])	
										->insert();
								}
						}
					break;
				case 'FT':
					break;
			}
			
		// create the transcription Package - see transcription_helper
		FreeREG_create_transcription_package($session->current_allocation[0]);
			
		// echo the response
		echo 'SUCCESS - Assignment and Transcription Package created with '.$image_no_err.' images. Start Transcribing!';	
	}
	
	public function change_assignment_step2($start_message)
	{				
		// initialise method
		$session = session();
		$allocation_image_sources_model = new Allocation_Image_Sources_Model();
		$project_types_model = new Project_Types_Model();
		$allocation_model = new Allocation_Model();
		$register_type_model = new Register_Type_Model();
		$allocation_images_model = new Allocation_Images_Model();
		$transcription_model = new Transcription_Model();
		$transcription_comments_model = new Transcription_Comments_Model();
		define_environment(3);
		$mongodb = define_mongodb();
		
		// payload received?
		if ( ! $_POST )
			{
				// no data received
				echo 'ERROR => No $_POST data received. Report to '.$session->linbmd2_email;
				return;
			}
			
		// load general input values
		$ass_name = $_POST['ass_name'];
		$county_group = $_POST['county_group'];
		$county = $_POST['county'];
		$chapman_code = $_POST['chapman_code'];
		$place = $_POST['place'];
		$church = $_POST['church'];
		$church_code = $_POST['church_code'];
		$church_code = strtoupper($church_code);
		$source_code = $_POST['source'];
		$register_code = $_POST['register'];
		$doc_source = $_POST['doc_source'];
		$doc_comment = $_POST['doc_comment'];
		
		// update it to the freecomett allocations table
		$allocation_model
			->where('BMD_allocation_index', $session->current_allocation[0]['BMD_allocation_index'])
			->set(['BMD_allocation_name' => $ass_name])
			->set(['REG_county_group' => $county_group])
			->set(['REG_county' => $county])
			->set(['REG_chapman_code' => $chapman_code])
			->set(['REG_place' => $place])
			->set(['REG_church_name' => $church])
			->set(['REG_church_code' => $church_code])
			->set(['REG_register_type' => $register_code])
			->set(['REG_image_folder_name' => NULL])
			->set(['source_code' => $source_code])
			->update();
					
		// load allocation record
		$session->current_allocation = $allocation_model
			->where('BMD_allocation_index', $session->current_allocation[0]['BMD_allocation_index'])
			->find();
			
		// initialse files count
		$image_in = 0;
		$image_out = 0;
		$image_no_err = 0;
			
		// get fields depending on image source
		switch ( $session->current_allocation[0]['source_code'] )
			{
				case 'HC':
					// hard copy no images required
					// a user could create an assignment as LP and then change it to HC.
					// this is OK as long as the user has not started to transcribe any of the images. 
					// This is tested in the view, so if we are here, it is OK to remove any image records and files for the transcription
					// read any image records by transcription
					$image_records = $allocation_images_model
						->where('transcription_index', $session->current_TP_index)
						->orderby('original_image_file_name')
						->findAll();
					// any found?
					if ( $image_records )
						{
							foreach ( $image_records as $image_record )
								{
									// remove image from scans folder
									unlink($image_record['image_url']);
								}
							// remove images from DB
							$allocation_images_model
								->where('transcription_index', $session->current_TP_index)
								->delete(); 
						}
					// delete the image scan name in TP
					$transcription_model
						->where('BMD_header_index', $session->current_TP_index)
						->set(['BMD_scan_name' => NULL])
						->update();
					break;		
				case 'LP':	
					// $_FILES contains 6 arrays; name of files, full_path, type, tmp_name, error, size 
					if ( isset($_FILES['images']['name']) )
						{
							// read errors
							foreach ( $_FILES['images']['error'] as $key => $error )
								{
									// increment total count
									$image_in = $image_in + 1;
									// process file only if no error
									if ( $error == 0 )
										{
											// increment image count
											$image_no_err = $image_no_err + 1;
											// create file name
											$image_name = $session->current_allocation[0]['BMD_allocation_index'].'_'.$image_no_err.'_'.$_FILES['images']['name'][$key];
											$image_name_noext = pathinfo($image_name, PATHINFO_FILENAME);
											$image_path = getcwd().'/Users/'.$session->current_project['project_name'].'/'.$session->identity_userid.'/Scans/';
											$image_url = $image_path.$image_name;
											// Upload file
											if ( move_uploaded_file($_FILES['images']['tmp_name'][$key],$image_url) )
												{
													// increment images uploaded
													$image_out = $image_out + 1;
													// make image extension lower case
													$image_extension = pathinfo($image_url, PATHINFO_EXTENSION);
													$image_extension = strtolower($image_extension);
													// do I have a pdf file
													switch ( $image_extension )
														{
															case 'pdf':
																// extract images from pdf
																$command = '/usr/local/bin/pdfimages -j '.$image_url.' '.$image_path.$image_name_noext;
																exec($command, $output, $retval);
																// remove pdf file as no longer needed
																unlink($image_url);
																break;
															default:
																break;
														}
												}
										}
								}
								
							// now get the names of the images just created for this allocation in order to write image record
							$uploaded_images = glob($image_path.$session->current_allocation[0]['BMD_allocation_index'].'*');
							// read matched files
							foreach ( $uploaded_images as $img )
								{
									// get file name
									$img_name = pathinfo($img, PATHINFO_BASENAME);
									$ori_name = explode('_', $img_name)[2];
									// does this image already exist against this assignment?
									$exists = $allocation_images_model
										->where('allocation_index', $session->current_allocation[0]['BMD_allocation_index'])
										->where('original_image_file_name', $ori_name)
										->find();
									if ( ! $exists )
										{
											// write image record
											$allocation_images_model
												->set(['project_index' => $session->current_project['project_index']])
												->set(['allocation_index' => $session->current_allocation[0]['BMD_allocation_index']])
												->set(['transcription_index' => $session->current_TP_index])
												->set(['identity_index' => $session->current_identity[0]['BMD_identity_index']])
												->set(['image_id' => NULL])
												->set(['original_image_file_name' => $ori_name])
												->set(['image_file_name' => $img_name])
												->set(['image_url' => $img])
												->set(['image_status' => 'bt'])
												->set(['trans_start_date' => NULL])
												->set(['trans_complete_date' => NULL])	
												->insert();
										}
								}
						}
					// load images
					$session->allocation_images = $allocation_images_model
						->where('allocation_index', $session->current_allocation[0]['BMD_allocation_index'])
						->orderby('original_image_file_name')
						->findAll();
					break;
				case 'FT':
					break;
			}
		
		// update the source and comment
		// current TP index is set in change step 1
		$transcription_comments_model
			->where('transcription_index', $session->current_TP_index)
			->where('comment_sequence', 10)
			->set(['comment_text' => $doc_comment])
			->set(['source_text' => $doc_source])
			->update();		
		
		// update the TP file name, source code and first image
		// this is not possible when preserving the loaded csv file name
		//$file_name_array = explode('_', $session->current_TP_file_name);
		//$bmd_file_name = trim($chapman_code).trim($church_code).'_'.$file_name_array[1];
		if ( $session->current_allocation[0]['source_code'] == 'LP' AND $session->current_BMD_scan_name == '' )
			{
				// set TP scan name with first scan name from images list. 
				// This situation will arrive if the alloc source has been chnaged from HC to LP.
				$session->current_BMD_scan_name = $session->allocation_images[0]['image_file_name'];
			}
		// update TP
		$transcription_model
			->where('BMD_header_index', $session->current_TP_index)
			->set(['BMD_file_name' => $session->current_TP_file_name])
			->set(['source_code' => $session->current_allocation[0]['source_code']])
			->set(['BMD_scan_name' => $session->current_BMD_scan_name])
			->update();

		// echo the response
		if ( $session->current_allocation[0]['source_code'] == 'LP' )
			{
				echo 'SUCCESS - Assignment and Transcription Package have been edited to include your changes and with '.$image_no_err.' added images. Start Transcribing!';
			}
		else
			{
				echo 'SUCCESS - Assignment and Transcription Package have been edited to include your changes. Start Transcribing!';
			}
	}
	
	public function change_assignment_step1($start_message)
	{		
		// initialise method
		$session = session();
		$allocation_image_sources_model = new Allocation_Image_Sources_Model();
		$project_types_model = new Project_Types_Model();
		$register_type_model = new Register_Type_Model();
		$document_sources_model = new Document_Sources_Model();
		$transcription_model = new Transcription_Model();
		$transcription_comments_model = new Transcription_Comments_Model();
		$allocation_images_model = new Allocation_Images_Model();		
		
		switch ($start_message) 
			{
				case 0:
					// load MongoDB
					// define mongodb - see common helper
					define_environment(3);
					$mongodb = define_mongodb();

					// does this allocation have an image source of 'LP', if so, get the images from the DB
					if ( $session->current_allocation[0]['source_code'] == 'LP' )
						{
							// get images
							$session->allocation_images = $allocation_images_model
								->where('allocation_index', $session->current_allocation[0]['BMD_allocation_index'])
								->orderby('original_image_file_name')
								->findAll();
						}
							
					// Images Sources - comes from FreeComETT DB
					$session->allocation_image_sources = $allocation_image_sources_model
						->where('project_index', $session->current_project['project_index'])
						->where('source_manual', 'Y')
						->orderby('source_order')
						->findAll();
					// all found?
					if ( !$session->allocation_image_sources )
						{
							$session->set('message_2', 'Cannot change assignment. Image sources cannot be loaded. Report to '.$session->linbmd2_email);
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('allocation/manage_allocations/2') );
						}
						
					// create county groups - comes from FreeComETT DB
					$county_groups = array();
					foreach ( $session->freeukgen_source_values as $key => $source_value )
						{
							if ( str_contains($key, 'counties') )
								{
									$group = explode('_', $key);
									$county_groups[] = $group[1];
								}
						}
					$session->county_groups = $county_groups;
					
					// get register types - comes from FreeComETT DB
					$session->register_types = $register_type_model
						->where('project_index', $session->current_project['project_index'])
						->where('register_active', 'yes')
						->orderby('register_order')
						->findAll();
						
					// get document sources - comes from FreeComETT DB
					$session->document_sources = $document_sources_model
						->orderby('document_source')
						->findAll();
						
					// get the transcription comments and source
					// in FreeREG there is a 1:1 link between assignments and Transcription Packages. 
					// so I can get the TP using the allocation index
					$source_text = '';
					$comment_text = '';
					$current_transcription = $transcription_model
						->where('BMD_allocation_index', $session->current_allocation[0]['BMD_allocation_index'])
						->find();
					if ( ! $current_transcription )
						{
							$source_text = '';
							$comment_text = '';
						}
					else
						{
							$session->current_TP_index = $current_transcription[0]['BMD_header_index']; // for use in update step 2
							$session->current_TP_file_name = $current_transcription[0]['BMD_file_name']; // for use in update step 2
							$session->current_BMD_scan_name = $current_transcription[0]['BMD_scan_name']; // for use in update step 2
							$current_comments = $transcription_comments_model
								->where('transcription_index', $current_transcription[0]['BMD_header_index'])
								->find();
							if ( ! $current_comments )
								{
									$source_text = '';
									$comment_text = '';
								}
							else
								{
									$source_text = $current_comments[0]['source_text'];
									$comment_text = $current_comments[0]['comment_text'];
								}
						}
					$current_allocation = $session->current_allocation;
					$current_allocation[0]['source_text'] = $source_text;
					$current_allocation[0]['comment_text'] = $comment_text;
					$session->current_allocation = $current_allocation;
					
					// set assignment mode
					$session->assignment_mode = 'change';
							
					// message defaults
					$session->set('message_1', '');
					$session->set('message_class_1', '');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Please enter the data required to create your assignment.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
		
		// show views			
		echo view('templates/header');
		echo view('linBMD2/manage_assignment_step1'); // same view used for both create and change
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');
	}	
	
	public function get_places()
	{
		// initialise
		$session = session();
		define_environment(3);
		$mongodb = define_mongodb();
		// get search term
		$search_term = $_POST['search_term'];
		// get matching counties
		$results = $mongodb['database']->selectCollection('places')->find
			(
				[
					'county' => $search_term, 
					'disabled' => 'false'
				]
			)->toArray();
		// prepare return array
		$search_result = array();
		if ( $results )
			{
				foreach($results as $result)
					{
						$place_name = str_replace(",", "", $result['place_name']);
						$search_result[] = $place_name;
					}
			}
		// return result
		echo json_encode($search_result);
	}
	
	public function get_churches()
	{
		// initialise
		$session = session();
		define_environment(3);
		$mongodb = define_mongodb();
		// get search terms
		$country = $_POST['country'];
		$county = $_POST['county'];
		$county = explode(' => ', $county)[0];
		$place = $_POST['place'];

		// get place id
		$results = $mongodb['database']->selectCollection('places')->find
			(
				[
					'country' => $country, 
					'county' => $county, 
					'place_name' => $place,
					'disabled' => 'false'
				]
			)->toArray();
		// get churches
		$results = $mongodb['database']->selectCollection('churches')->find(['place_id' => new ObjectId($results[0]['_id'])])->toArray();
		// prepare return array
		$search_result = array();
		if ( $results )
			{
				foreach($results as $result)
					{
						$search_result[] = $result['church_name'].' => '.$result['church_code'];
					}
			}
		// return result
		echo json_encode($search_result);
	}
	
	public function doublons()
	{
		// initialise
		$session = session();
		$allocation_model = new Allocation_Model();
		$allocation_images_model = new Allocation_Images_Model();
		$doublons_out = array();
		
		// get input
		$sel_images = explode(',', $_POST['sel_images']);

		// read images
		$i = 0;
		foreach ( $sel_images as $image_name )
			{
				// check doublons
				$doublons = $allocation_images_model
					->where('identity_index', $session->current_identity[0]['BMD_identity_index'])
					->where('original_image_file_name', $image_name)
					->findAll();
							
				if ( $doublons )
					{
						// image is already attached to an assignment
						foreach ( $doublons as $doublon )
							{
								// get assignment name
								$assignment = $allocation_model
									->where('BMD_allocation_index', $doublon['allocation_index'])
									->find();
								$doublons_out[] = $image_name.' => '.$assignment[0]['BMD_allocation_name'];
							}
					}
			}		
					
		// return result
		echo json_encode($doublons_out);
	}
	
	public function remove_image_from_assignment()
	{
		// initialise
		$session = session();
		$allocation_images_model = new Allocation_Images_Model();
		$return_code = false;
		
		// get input
		$allocation_index = $_POST['allocation_index'];
		$image_index = $_POST['image_index'];
		
		// get image record
		$image_record = $allocation_images_model
			->where('allocation_index', $allocation_index)
			->where('image_index', $image_index)
			->find();

		// remove image from DB
		if ( $allocation_images_model->where('allocation_index', $allocation_index)->where('image_index', $image_index)->delete() )
			{
				$return_code = true;
			}
			
		// remove image from scans folder
		unlink($image_record[0]['image_url']);
					
		// return result
		echo $return_code;
	}
	
	public function close_freereg_assignment_step1($start_message, $trans_index)
	{		
		// initialise method
		$session = session();
		define_environment(3);
		$mongodb = define_mongodb();
		$allocation_model = new Allocation_Model();
		$allocation_images_model = new Allocation_Images_Model();
		$transcription_model = new Transcription_Model();
		$transcription_comments_model = new Transcription_Comments_Model();
		$detail_data_model = new Detail_Data_Model();
		
		$session->image_count = 0;
		$session->detail_count = 0;
		$session->upload_date = null;
		$session->upload_status = null;
		$session->csv_file = null;
		$session->assignment_name = null;
		$session->assignment_start = null;
		$session->TP_index = null;
		$session->AL_index = null;
		$session->caller = $session->_ci_previous_url;
		if (str_contains($session->caller, 'allocation')) 
			{
				$session->return_route = 'allocation/manage_allocations';
			}
		else
			{
				$session->return_route = 'transcribe/transcribe_step1';
			}
		
		// FreeREG has decided that assignments and related transcription package should be deleted rather than closed.
		// Since this is a more radical approach than just soft closing the elements, ask user to confirm.
		
		// determine the caller to get the allocation_index or transcription_index
		if (str_contains($session->caller, 'allocation')) 
			{
				// called from allocation, so incoming index = allocation_index
				$session->AL_index = $trans_index;
			}
		else
			{
				// called from transcribe, so incoming index = transcription index
				// get allocation index.
				// I can do this because of 1:1 connection between allocation and transcription in FreeREG
				$current_transcription = $transcription_model
					->where('BMD_header_index', $trans_index)
					->find();
				if ( ! $current_transcription )
					{
						$session->set('message_2', 'Cannot close assignment. TP cannot be loaded. Report to '.$session->linbmd2_email);
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url($session->return_route.'/1') );
					}
				$session->AL_index = $current_transcription[0]['BMD_allocation_index'];
			}
			
		// get allocation record
		$current_allocation = $allocation_model
			->where('BMD_allocation_index', $session->AL_index)
			->find();
		if ( ! $current_allocation )
			{
				$session->set('message_2', 'Cannot close assignment. AL cannot be loaded. Report to '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url($session->return_route.'/1') );
			}
		
		// if allocation was created from back, it cannot be closed here
		if ( $current_allocation[0]['source_code'] == 'FS' )
			{
				$session->set('message_2', 'Assignment, '.$current_allocation[0]['BMD_allocation_name'].', was created in FreeREG and imported to FreeComETT. Please close it in FreeREG. It will then be removed from FreeComETT.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url($session->return_route.'/1') );
			}
			
		// get TP
		$current_transcription = $transcription_model
			->where('BMD_allocation_index', $session->AL_index)
			->find();
		//if ( $current_transcription )
			//{
				//// test whether created from CSV and upload date blank, if so cannot delete
				//if ( $current_allocation[0]['source_code'] == 'FR' AND $current_transcription[0]['BMD_submit_date'] == null )
					//{
						//$session->set('message_2', 'Assignment, '.$current_allocation[0]['BMD_allocation_name'].', was created by loading the CSV file to FreeComETT. It has not been uploaded to FreeREG and so cannot be closed here as closing it would risk data loss. Please upload first before closing the assignment.');
						//$session->set('message_class_2', 'alert alert-danger');
						//return redirect()->to( base_url($session->return_route.'/1') );
					//}
			//}
			
		// gather data to show to user in order to confirm deletion
		// assignment info
		$session->assignment_name = $current_transcription[0]['BMD_file_name'];
		$session->assignment_start = $current_allocation[0]['BMD_start_date'];
		// any images?
		$allocation_images = $allocation_images_model
			->where('allocation_index', $session->AL_index)
			->findAll();
		if ( $allocation_images )
			{
				$session->image_count = count($allocation_images);
			}	
		// get TP data
		if ( $current_transcription )
			{
				$session->TP_index = $current_transcription[0]['BMD_header_index'];
				// get detail records
				$current_details = $detail_data_model
					->where('BMD_header_index', $current_transcription[0]['BMD_header_index'])
					->find();
				if ( $current_details )
					{
						$session->detail_count = count( $current_details );
					}
				// set upload details
				$session->upload_date = $current_transcription[0]['BMD_submit_date'];
				$session->upload_status = $current_transcription[0]['BMD_submit_status'];
				$session->csv_file = $current_transcription[0]['BMD_file_name'];
			}

		// message defaults
		$session->set('message_1', '');
		$session->set('message_class_1', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// show views			
		echo view('templates/header');
		echo view('linBMD2/close_assignment');
		echo view('templates/footer');	
	}
	
	public function close_freereg_assignment_step2()
	{		
		// initialise method
		$session = session();
		define_environment(3);
		$mongodb = define_mongodb();
		$allocation_images_model = new Allocation_Images_Model();
	
		// user has confirmed close == delete
		// delete any images on server
		$image_records = $allocation_images_model
			->where('allocation_index', $session->AL_index)
			->find();
		if ( $image_records )
			{
				foreach ( $image_records as $image_record )
					{
						// remove image from scans folder
						unlink($image_record['image_url']);
					}
			}
		// delete table entries for this project/user in all tables
		$models = array('Transcription_Detail_Def_Model', 'Transcription_Model', 'Transcription_Comments_Model', 'Detail_Data_Model', 'Detail_Comments_Model', 'Allocation_Model', 'Allocation_Images_Model', 'Transcription_CSV_File_Model' );
		foreach ( $models as $model_name )
			{
				switch ($model_name) 
					{
						case 'Transcription_Detail_Def_Model':
							$model = new Transcription_Detail_Def_Model();
							$index_field = 'transcription_index';
							$index_value = $session->TP_index;
							break;
						case 'Transcription_Comments_Model':
							$model = new Transcription_Comments_Model();
							$index_field = 'transcription_index';
							$index_value = $session->TP_index;
							break;
						case 'Transcription_Model':
							$model = new Transcription_Model();
							$index_field = 'BMD_header_index';
							$index_value = $session->TP_index;
							break;
						case 'Detail_Data_Model':
							$model = new Detail_Data_Model();
							$index_field = 'BMD_header_index';
							$index_value = $session->TP_index;
							break;
						case 'Detail_Comments_Model':
							$model = new Detail_Comments_Model();
							$index_field = 'BMD_header_index';
							$index_value = $session->TP_index;
							break;
						case 'Transcription_CSV_File_Model':
							$model = new Transcription_CSV_File_Model();
							$index_field = 'transcription_index';
							$index_value = $session->TP_index;
							break;
						case 'Allocation_Model':
							$model = new Allocation_Model();
							$index_field = 'BMD_allocation_index';
							$index_value = $session->AL_index;
							break;
						case 'Allocation_Images_Model':
							$model = new Allocation_Images_Model();
							$index_field = 'allocation_index';
							$index_value = $session->AL_index;
							break;
					}
				$model
					->where($index_field, $index_value)
					->delete();
			}
			
		// issue 143 stiplates that the file lock parameters, locked_by_transcriber and locked_by_coordinator, in the freereg1_csv_files collection in FreeREG backend should be set to false when closing/deleting an imported file
		// Is this an imported file, ie it exists on bankend?
		$update_flag = 0;
		// try with upper case CSV
		$csv_file_name = $session->current_transcription[0]['BMD_file_name'].'.CSV';
		$imported_file = $mongodb['database']->selectCollection('freereg1_csv_files')->find
			(
				[
					'file_name' => $csv_file_name,
				]
			)->toArray();
			if ( $imported_file )
				{
					// set update flag
					$update_flag = 1;
				}
			else
				{
					// try with lower case CSV
					$csv_file_name = $session->current_transcription[0]['BMD_file_name'].'.csv'; 
					$imported_file = $mongodb['database']->selectCollection('freereg1_csv_files')->find
						(
							[
								'file_name' => $csv_file_name,
							]
						)->toArray();
						if ( $imported_file )
							{
								// set update flag
								$update_flag = 1;
							}
				}	
		// if update flag == 1 then record found, so put on locks
		if ( $update_flag == 1 )
			{		
				$result = $mongodb['database']->selectCollection('freereg1_csv_files')->updateOne
					(
						['file_name' => $csv_file_name],
						['$set' => ['locked_by_transcriber' => false, 'locked_by_coordinator' => false]]
					);
				$modified_documents = $result->getModifiedCount();
			}
		
		$session->set('message_2', 'Assignment, '.$session->assignment_name.', has been CLOSED. All records for it in FreeComETT have been DELETED');
		$session->set('message_class_2', 'alert alert-success');
		
		// return to caller
		return redirect()->to( base_url($session->return_route.'/1') );
	}
	
	public function upload_csv_file()
	{
		echo view('templates/header');
		echo view('linBMD2/new_upload_csv');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');
	}


	// DS 24 Nov 25
	// replaced with upload_csv_file()
	public function load_csv_file_step1($start_message)
	{
		// initialise method
		$session = session();
		define_environment(3);
		$mongodb = define_mongodb();
		$transcription_model = new Transcription_Model();
		// sort
		if ( ! isset($session->alloc_sort_by) )
			{
				$session->alloc_sort_by = 'allocation.Change_date';
				$session->alloc_sort_order = 'DESC';
				$session->alloc_sort_name = 'Last change date/time';
			}
		
		// if there is an import in progress the user cannot start another
		$import_in_progress = $transcription_model
			->where('project_index', $session->current_project['project_index'])
			->where('BMD_identity_index', $session->current_identity[0]['BMD_identity_index'])
			->like('BMD_submit_status', 'Import CSV in Progress')
			->findAll();
		if ( $import_in_progress )
			{
				$session->set('message_2', 'You are already importing a CSV file. Please wait until that has finished before importing another.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('allocation/manage_allocations/1') );
			}
		
		// this method show a list of a user's csv files from the FreeREG backend in order for him to select one for load to FreeComETT.
		// csv files are held in the backend collection called physical_files. CSV file are retrieved by userid.
		$collection_physical_files = $mongodb['database']->selectCollection('physical_files');
		
// temporary in order to test
if ( $session->identity_userid == 'freeregdev' )
	{
		$temp_userid = "ACooke262";
	}
else
	{
		$temp_userid = $session->identity_userid;
	}

		$session->physical_files = $collection_physical_files->find
			(
				[
					'userid' => $temp_userid,
					'file_processed' => true,
				]
			)->toArray();
		if ( ! $session->physical_files )
			{
				$session->set('message_2', 'You have no CSV files registered on FreeREG = nothing to import.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('allocation/manage_allocations/1') );
			}
		// read all files and convert dates
		$physical_files = $session->physical_files;
		foreach ( $physical_files as $key => $physical_file )
			{
				// does the base uploaded date key exist
				$physical_files[$key]['base_date'] = '';
				if ( isset($physical_file['base_uploaded_date']) )
					{
						$base_date = $physical_file['base_uploaded_date']->toDateTime();
						$base_date = $base_date->format('d-M-Y H:i:s');
						$physical_files[$key]['base_date'] = $base_date;
					}
				
				$physical_files[$key]['proc_date'] = '';
				if ( isset($physical_file['file_processed_date']) )
					{
						$proc_date = $physical_file['file_processed_date']->toDateTime();
						$proc_date = $proc_date->format('d-M-Y H:i:s');
						$physical_files[$key]['proc_date'] = $proc_date;
					}
			}
		$session->physical_files = $physical_files;
		
		// set messages
		$session->set('message_1', 'Please choose the CSV file you wish to load...');
		$session->set('message_class_1', 'alert alert-primary');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
			
		// show views			
		echo view('templates/header');
		echo view('linBMD2/load_csv_file_step1');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');
	}
	
	public function load_csv_file_step2()
	{
		// initialise method
		$session = session();
		$allocation_model = new Allocation_Model();
		$transcription_model = new Transcription_Model();
		$detail_data_model = new Detail_Data_Model();
		$def_fields_model = new Def_Fields_Model();
		$register_type_model = new Register_Type_Model();
		$document_sources_model = new Document_Sources_Model();
		$transcription_detail_def_model = new Transcription_Detail_Def_Model();
		define_environment(3);
		$mongodb = define_mongodb();
		$collection_counties = $mongodb['database']->selectCollection('counties');
		$current_assignment = array();
		
		// get inputs
		$csv_file_name = $this->request->getPost('csv_file_name');
		$csv_file_id = $this->request->getPost('csv_file_id');
		
		// has this CSV file already been loaded
		$loaded = $allocation_model
			->where('project_index', $session->current_project['project_index'])
			->where('BMD_reference', $csv_file_name)
			->find();
			if ( $loaded )
				{
					$session->set('message_2', 'You have already loaded this file, '.$csv_file_name.', to FreeComETT on '.$loaded[0]['BMD_start_date']);
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('allocation/manage_allocations/1') );
				}
		
		// get source info
		// set source key depending on environment
		// get_source_info = common_helper = get instantiation parameters
		$source_key = 'csv_files_path_'.$session->environment;
		$source_info = get_source_info($session->current_project['project_index'], $source_key);
		if ( $source_info == 'error' )
			{
				$session->set('message_2', 'Cannot load CSV access information in method allocation/load_csv_file_step2. Report to '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('allocation/manage_allocations/1') );
			}
		// set path and file name in source_info	
		$source_info[0]['source_path'] = '/'.$session->identity_userid;
		$source_info[0]['source_name'] = '/'.$csv_file_name;
		
// temporary in order to test
if ( $session->identity_userid == 'freeregdev' )
	{
		$csv_file_name = 'STSWLHBU.csv';
		$source_info[0]['source_path'] = '/'.'ACooke262';
		$source_info[0]['source_name'] = '/'.$csv_file_name;
	}
		
		// get the csv data - method in common_helper
		$csv_data = get_source_data($source_info[0]);
		if ( $csv_data == 'error' )
			{
				$session->set('message_2', 'Cannot load CSV file in method Allocation/load_csv_file_step2. Report to '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('allocation/manage_allocations/1') );
			}

		// do I have a genuine CSV file?
		// get first line, first field should be +INFO.
		// create array by line end
		$csv_line_array = preg_split('/\r\n|\r|\n/', $csv_data);
		$csv_line0 = str_getcsv($csv_line_array[0]);
		if ( $csv_line0[0] != '+INFO' )
			{
				$session->set('message_2', 'The CSV file, '.$csv_file_name.', retrieved from backend doesn\'t appear to be a genuine CSV file. Report to '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('allocation/manage_allocations/1') );
			}
	
		// parse the csv_line_array to get the data required to create the Assignment
		// by line,
		// 0 = '+INFO', $session->identity_emailid, 'Password', 'SEQUENCED', $type_name_upper, 'UTF-8'
		// 1 = '#', 'CCC', $session->realname, $session->syndicate_name, $file_name.'.CSV', $session->current_transcription[0]['BMD_start_date']
		// 2 = '#', "CREDIT", $session->identity_userid note: this line may not exist
		// 3 = "#", date("d-M-Y"), $session->current_allocation[0]['BMD_reference'], $source_text, $comment_text
		// 4 = '#','DEF' NOTE: DEF may not exist in the csv if it has been created by winREG. In this case look for +LDS
		// 5 = mandatory fields for the event type + variable fields.
		// 6 and subsequent data lines matching fields from the def line
		
		// the following are the data required and where it comes from
		// $ass_name = does not exist in the CSV;
		// $county_group = does not exist in the csv - infer from the county;
		// $county = does not exist in the csv - infer from the chapman code;
		// $chapman_code = first data line, field 0; but would be better to get the mandatory fields in their order and then scan first data line
		// $place = first data line, field 1;
		// $church = first data line, field 2;
		// $church_code = does not exist in csv, infer from csv file name;
		// $source_code = does not exist in csv; use FR - defined in table allocation_image_sources
		// $register_code = first data line, field 3;
		// $session->document_source = line 3, field 3;
		// $session->document_comment = line 3, field 4;
		
		// assignment name and reference
		$current_assignment['BMD_allocation_name'] = 'Assignment imported from => '.$csv_file_name;
		$current_assignment['BMD_reference'] = $csv_file_name;
		
		// event type, BAPTISM, MARRIAGE, BURIAL
		$current_assignment['event_type'] = $csv_line0[4];
		$type_array_key = array_search($current_assignment['event_type'], array_column($session->project_types, 'type_name_upper'));
		$current_assignment['event_type_lower'] = $session->project_types[$type_array_key]['type_name_lower'];
	
		// find DEF line in order to get the fields used 
		$def_line_key = -1;
		foreach ( $csv_line_array as $key => $csv_line )
			{
				if ( $csv_line === '#,DEF' )
					{
						// def line was found
						$def_line_key = $key;
						// the data definition is in the next line
						$csv_def_fields = str_getcsv($csv_line_array[$def_line_key + 1]);
						$first_data_line_key = $def_line_key + 2;
						break;
					}
			}
		// do I have a def line key
		if ( $def_line_key == -1 )
			{
				// in this case the way to find the first data line key is to read the lines in the file until 
				// neither + or # is the first letter of the line.
				foreach ( $csv_line_array as $key => $csv_line )
					{
						if ( substr($csv_line, 0, 1) != '+' AND substr($csv_line, 0, 1) != '#' )
							{
								$first_data_line_key = $key;
								break;
							}
					}
				
				// get the field defs for this event type
				$source_info = get_source_info($session->current_project['project_index'], $current_assignment['event_type_lower'].'_entry_order_definition');
				if ( $source_info == 'error' )
					{
						$session->set('message_2', 'Cannot load CSV LDS field definitions in method Allocation/load_csv_file_step2. Report to '.$session->linbmd2_email);
						$session->set('message_class_2', 'alert alert-danger');
						return redirect()->to( base_url('allocation/manage_allocations/1') );
					}
				$source_data = get_source_data($source_info[0]);
				$source_value = get_source_value($source_data, $source_info[0]);
				// clean up the array
				// array elements have two values, 1) the field name and 2) the field order in the csv
				foreach ( $source_value as $field )
					{
						$field_array = explode('=>', $field);
						$csv_def_fields[$field_array[1] - 1] = $field_array[0];
					}
				// now clean up the field names
				foreach ( $csv_def_fields as $key => $field)
					{
						$field = str_replace('{', '', $field);
						$field = str_replace(':', '', $field);
						$field = trim($field);
						$csv_def_fields[$key] = $field;
					}	
			}
		
		// first data line
		$csv_data_fields = str_getcsv($csv_line_array[$first_data_line_key]);

		// load chapman_code
		$current_assignment['REG_chapman_code'] = $csv_data_fields[0];
		
		// get county name for assignment using chapman_code
		$mongo_county = $collection_counties->find
			(
				[
					'chapman_code' => $csv_data_fields[0],
				]
			)->toArray();
		if ( count($mongo_county) != 1 )
			{
				$session->set('message_2', 'Error retrieving county data with chapman_code, '.$csv_data_fields[0].'. Report to '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('allocation/manage_allocations/1') );
			}
		// load county 
		$current_assignment['REG_county'] = $mongo_county[0]['county_description'];
		
		// get the county group name
		// create county groups
		$county_groups = array();
		foreach ( $session->freeukgen_source_values as $key => $source_value )
			{
				if ( str_contains($key, 'counties') )
					{
						$group = explode('_', $key);
						$county_groups[] = $group[1];
					}
			}
		foreach ( $county_groups as $cty_grp )
			{
				foreach ( $session->freeukgen_source_values['counties_'.$cty_grp] as $entry )
					{
						$cty = explode(' => ', $entry);
						if ( $cty = $current_assignment['REG_county'] )
							{
								$current_assignment['REG_county_group']	= $cty_grp;
								break 2;
							}	
					}
			}
			
		// get place
		$data_array_key = array_search('place_name', $csv_def_fields);
		if ( $data_array_key == false )
			{
				$current_assignment['REG_place'] = null;
			}
		else
			{
				$current_assignment['REG_place'] = $csv_data_fields[$data_array_key];
			}
		
		// get church and church code and register type - null translates to 'unspecified'
		$current_assignment['REG_register_type'] = null;
		$data_array_key = array_search('church_name', $csv_def_fields);
		if ( $data_array_key == false )
			{
				$current_assignment['REG_church_name'] = null;
				$current_assignment['REG_church_code'] = null;
			}
		else
			{
				// isolate register type
				$register_type = substr($csv_data_fields[$data_array_key], -2);
				$valid = $register_type_model
					->where('project_index', $session->current_project['project_index'])
					->where('register_code', $register_type)
					->find();
				if ( $valid )
					{
						$current_assignment['REG_church_name'] = trim(substr($csv_data_fields[$data_array_key], 0, strlen($csv_data_fields[$data_array_key])-2));
						$current_assignment['REG_church_code'] = substr($csv_file_name, 3, 3);
						$current_assignment['REG_register_type'] = $register_type;
					}
				else
					{
						$current_assignment['REG_church_name'] = trim($csv_data_fields[$data_array_key]);
						$current_assignment['REG_church_code'] = substr($csv_file_name, 3, 3);
						$current_assignment['REG_register_type'] = 'UK';
					}	
			}
			
		// try to get the real church code
		$results = $mongodb['database']->selectCollection('places')->find
			(
				[
					'country' => $current_assignment['REG_county_group'], 
					'county' => $current_assignment['REG_county'], 
					'place_name' => $current_assignment['REG_place'],
					//'disabled' => 'false'
				]
			)->toArray();
		// any results
		if ( $results )
			{
				// try to get church
				$results = $mongodb['database']->selectCollection('churches')->find(['place_id' => new ObjectId($results[0]['_id'])])->toArray();
				if ( $results )
					{
						// how many records? if 1 then load church code otherwise leave it as it is
						if ( count($results) == 1 )
							{
								$current_assignment['REG_church_code'] = $results[0]['church_code'];
								$current_assignment['REG_church_name'] = $results[0]['church_name'];
							}
					}
			}
			
		// issue 194 stipulates that the original file name should be retained by FreeComETT
		// but what happens if the original church code no longer matches the church name.
		// in that case modify the church code in the original file name.
		// get original church code
		$csv_file_orig_church_code = substr($csv_file_name, 3, 3);
		// is it different from the one I have just found
		if ( $csv_file_orig_church_code != $current_assignment['REG_church_code'] )
			{
				// if so, create the correct csv file name
				// csv file name elements are,
				// Chapman code, start position 0, length 3 chars
				// Church code, start position 3, length 3 chars
				// Event Type, start position 6, length 2 chars
				// Unique identifier, start position 8, length to field length
				$csv_file_name = $current_assignment['REG_chapman_code'].$current_assignment['REG_church_code'].substr($csv_file_name, 6, 2).substr($csv_file_name, 8, strlen($csv_file_name) - 1);				
			}
				
		// register_type
		if ( $current_assignment['REG_register_type'] == null )
			{
				$data_array_key = array_search('register_type', $csv_def_fields);
				if ( $data_array_key == false )
					{
						$current_assignment['REG_register_type'] = 'UK';
					}
				else
					{
						$current_assignment['REG_register_type'] = $csv_data_fields[$data_array_key];
					}
			}
			
		// document source and doc comment
		$session->document_source = null;
		$session->document_comment = null;
		$csv_line3 = str_getcsv($csv_line_array[3]);
		if ( array_key_exists('2', $csv_line3) )
			{
				$session->document_source = $csv_line3[2];
			}
		if ( array_key_exists('3', $csv_line3) )
			{
				$session->document_comment = $csv_line3[3];
			}
		// massage document source as per issue 192
		// if document source not blank,
		if ( $session->document_source != '' )
			{
				// is it a recognised document source, if so leave it
				$result =  $document_sources_model
					->where('document_source', $session->document_source)
					->find();
				// if not a recognised document source,
				if ( !$result )
					{
						// move it to the document comment
						// add full stop to source if not already present.
						$source_length = strlen($session->document_source);
						$source_last = $source_length - 1;
						// add full stop if not already there
						if ( $session->document_source[$source_last] != '.' )
							{
								$session->document_source = $session->document_source.'.';
							}
						// now prepend source to comment
						$session->document_comment = $session->document_source.' '.$session->document_comment;
						// and blank source
						$session->document_source = '';
					}
			}
			
		// create the assignment
		$allocation_model
			->set(['project_index' => $session->current_project['project_index']])
			->set(['BMD_identity_index' => $session->current_identity[0]['BMD_identity_index']])
			->set(['BMD_syndicate_index' => $session->current_syndicate[0]['BMD_syndicate_index']])
			->set(['BMD_allocation_name' => $current_assignment['BMD_allocation_name']])
			->set(['BMD_reference' => $current_assignment['BMD_reference']])
			->set(['BMD_start_date' => date('d-M-Y')])
			->set(['BMD_end_date' => ''])
			->set(['BMD_start_page' => 1])
			->set(['BMD_last_uploaded' => null])
			->set(['BMD_end_page' => null])
			->set(['BMD_year' => date('Y')])
			->set(['BMD_quarter' => 0])
			->set(['BMD_letter' => ''])
			->set(['BMD_type' => 'C']) // C = Composite, ie assignment could contain any event type.
			->set(['BMD_scan_type' => 'jpg'])
			->set(['BMD_last_action' => 'Create '.$session->current_project['allocation_text']])
			->set(['BMD_status' => 'Open'])
			->set(['BMD_sequence' => 'SEQUENCED'])
			->set(['data_entry_format' => 'composite'])
			->set(['scan_format' => 'FreeREG'])
			->set(['REG_assignment_id' => null])
			->set(['REG_county_group' => $current_assignment['REG_county_group']])
			->set(['REG_county' => $current_assignment['REG_county']])
			->set(['REG_chapman_code' => $current_assignment['REG_chapman_code']])
			->set(['REG_place' => $current_assignment['REG_place']])
			->set(['REG_church_name' => $current_assignment['REG_church_name']])
			->set(['REG_church_code' => $current_assignment['REG_church_code']])
			->set(['REG_register_type' => $current_assignment['REG_register_type']])
			->set(['REG_image_folder_name' => null])
			->set(['source_code' => 'FR'])
			->insert();
			
		// get the insert key
		$allocation_index = $allocation_model->getInsertID();
					
		// load allocation record
		$session->current_allocation = $allocation_model
			->where('project_index', $session->current_project['project_index'])
			->where('BMD_allocation_index', $allocation_index)
			->find();
			
		// there are no images to get
		
		// create the transcription Package - see transcription_helper
		// strip off .csv 
		$bmd_file_name = explode('.', $csv_file_name);
		$tp_index = FreeREG_create_transcription_package($session->current_allocation[0], $bmd_file_name[0]);
		
		// update current data entry format = event type
		// I'm using BMD_submit_status to indicate that an import is in progress in the background for this transcription.
		// if BMD_submit_status is = 'import_csv', an import is in progress.
		// after import is finished this indicator is reset to NULL.
		// in TP home, if the indicator = 'import_csv', the transcription will not be shown to the user.
		// update BMD_submit_status 
		$transcription_model
			->where('BMD_header_index', $tp_index)
			->set(['current_data_entry_format' => $current_assignment['event_type_lower']])
			->set(['BMD_submit_status' => 'Import CSV in Progress'])
			->set(['import_in_progress' => 1])
			->update();
		// get TP
		$session->current_tp = $transcription_model
			->where('BMD_header_index', $tp_index)
			->find();
		
		// populate postfields fields
		$postfields = array	(
								'tp_index' => $tp_index,
								'csv_file_name' => $csv_file_name,
								'csv_file_id' => $csv_file_id,
								'first_data_line_key' => $first_data_line_key,
								'csv_def_fields' => json_encode($csv_def_fields),
								'csv_line_array' => json_encode($csv_line_array, JSON_INVALID_UTF8_IGNORE),
								'project_index' => $session->current_project['project_index'],
								'BMD_identity_index' => $session->current_identity[0]['BMD_identity_index'],
								'data_entry_format' => $session->project_types[$type_array_key]['type_name_lower'],
								'register_type' => $current_assignment['REG_register_type'],
								'REG_church_name' => $current_assignment['REG_church_name'],
								'password' => $session->UserPassword_base64,
								'project' => $session->current_project['project_name'],	
							);			
	
		// load data lines to DB in background process
		$curl_url = base_url('allocation_import_csv_data_background/import/');	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curl_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_STDERR, fopen(getcwd()."/curl.log", 'a+'));
		// run the curl
		$result = curl_exec($ch);
		curl_close($ch);

		// show change assignment to allow user to update stuff
		$session->set('message_2', 'Your CSV import is being processed in the background. You can already change the assignment header information here if you wish.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('allocation/change_assignment_step1/0') );
	}
	
	
	
}
