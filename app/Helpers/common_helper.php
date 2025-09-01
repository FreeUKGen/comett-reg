<?php

use App\Models\Districts_Model;
use App\Models\Allocation_Model;
use App\Models\Syndicate_Model;
use App\Models\User_Parameters_Model;
use App\Models\Parameter_Model;
use App\Models\Identity_Model;
use App\Models\Transcription_Cycle_Model;
use App\Models\Project_Types_Model;
use App\Models\Freeukgen_Sources_Model;
use App\Models\Condition_Model;
use App\Models\Title_Model;
use App\Models\Licence_Model;
use App\Models\Relationship_Model;
use App\Models\Person_Status_Model;
use App\Models\Project_DB_Model;

function load_variables()
	{
		// inialise
		$session = session();
		$districts_model = new Districts_Model;
		$syndicate_model = new Syndicate_Model();
		$allocation_model = new Allocation_Model();
		$user_parameters_model = new User_Parameters_Model();
		$parameter_model = new Parameter_Model();
		$identity_model = new Identity_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		$project_types_model = new Project_Types_Model();
		$condition_model = new Condition_Model();
		$title_model = new Title_Model();
		$licence_model = new Licence_Model();
		$relationship_model = new Relationship_Model();
		$person_status_model = new Person_Status_Model();
		
		// clean up logs - keep logs for 15 days	
		$keep_from = strtotime('-15 days');

		// do log clean up
		$dir = new DirectoryIterator(dirname(WRITEPATH.'logs/*.log'));
		foreach ($dir as $fileinfo) 
			{
				if (!$fileinfo->isDot() AND $fileinfo->getExtension() == 'log' ) 
					{
						$ctime = $fileinfo->getCTime();
						if ( $ctime < $keep_from )
							{
								unlink($fileinfo->getPathname());
							}
					}
			}
			
		// test time out
		if ( ! $session->has('realname') )
			{
				// ask to resignin
				return redirect()->to( base_url('/home/signout/') );
			}
		
		// get districts
		$districts = $districts_model->findAll();
			
		// get syndicates
		$syndicates = $syndicate_model	
			->where('project_index', $session->current_project[0]['project_index'])
			->orderby('status', 'ASC')
			->orderby('BMD_syndicate_name', 'ASC')
			->findAll();		
		
		// get allocations
		$allocations = $allocation_model->orderby('BMD_allocation_name', 'ASC')
			->where('BMD_status', 'Open')
			->where('BMD_identity_index', $session->BMD_identity_index)
			->orwhere('BMD_identity_index', 999999)
			->where('project_index', $session->current_project[0]['project_index'])
			->orwhere('project_index', 999999)
			->findAll();
							
		// load alphabet
		$alphabet = [	"A" => "A", "B" => "B", "C" => "C", "D" => "D", "E" => "E", "F" => "F", "G" => "G", "H" => "H", "I" => "I", "J" => "J", "K" => "K", 					"L" => "L",
							"M" => "M", "N" => "N",  "O" => "O", "P" => "P", "Q" => "Q", "R" => "R", "S" => "S", "T" => "T",  "U" => "U",  "V" => "V", "W" => "W",  "X" => "X", 
							"Y" => "Y", "Z" => "Z",	
							];
		// load types for this project
		$session->project_types = $project_types_model
			->where('project_index', $session->current_project[0]['project_index'])
			->orderby('type_order')
			->find();					
		// load quarters
		$quarters = [ "1" => "MAR", "2" => "JUN", "3" => "SEP", "4" => "DEC"];
		// load quarters long name
		$quarters_short_long = [ "0" => "Select :", "1" => "March", "2" => "June", "3" => "September", "4" => "December"];
		// load month to quarter
		$month_to_quarter = [ "01" => "01", "02" => "01", "03" => "01", "04" => "02", "05" => "02", "06" => "02", "07" => "03", "08" => "03", "09" => "03", "10" => "04",
											"11" => "04", "12" => "04"];
		// load death months
		$session->set('valid_days', [ "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", 
											"13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23",
											"24", "25", "26", "27", "28", "29", "30", "31", "- ", "AB" ] );
		// load death months
		$session->set('valid_2letter_month_codes', [ "JA", "FE", "MR", "AP", "MY", "JE", "JY", "AU", "SE", "OC", "NO", "DE", "- ", "OU", "UT" ] );
		// load marriage months
		$session->set('marriage_months', [ "JAN" => "01", "FEB" => "02", "MAR" => "03", "APR" => "04", "MAY" => "05",
											"JUN" => "06", "JUL" => "07", "AUG" => "08", "SEP" => "09", "OCT" => "10",
											"NOV" => "11", "DEC" => "12" ] );
		// valid 3 letter codes									
		$session->set('valid_3letter_month_codes', [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ] );
		
		// load scan name types
		$scan_name_types = [ "Y" => "Year", "Q" => "Quarter", ];
		// load yesno
		$yesno = [ "Y" => "Yes", "N" => "No", ];
		// load current date and login time stamp
		$current_date = date("d-M-Y");
		// load system parameters
		$parameters = $parameter_model->findAll();
		// load programme name
		$parameter = $parameter_model->where('Parameter_key', 'programname')->findAll();
		$session->set('programname', $parameter[0]['Parameter_value']);
		// load version
		$parameter = $parameter_model->where('Parameter_key', 'version')->findAll();
		$session->set('version', $parameter[0]['Parameter_value']);
		// load uploadagent
		$parameter = $parameter_model->where('Parameter_key', 'uploadagent')->findAll();
		$session->set('uploadagent', $parameter[0]['Parameter_value']);
		// initialise reference extension array
		$reference_extension_array = array();
		$reference_extension_control = '0';
		// comment types
		$comment_types =	[ 
								"C" => "COMMENT = transcribed data differs in some way from what is in the index", 
								"T" => "THEORY = transcribed data is what is in the index but there is reason to believe the index is wrong", 
								"N" => "no type = Used to give information about the transcription", 
								"B" => "Add a +BREAK line",
								"P" => "Add a +PAGE line (only if 2 or more page scan)",
								"R" => "THEORY REF = used to indicate a reference to a late registration in standard format.",
								"S" => "SUGGESTION = Your Coordinator has left you a suggestion for this line(s).",
							];
		// load transcrition cycle
		$transcription_cycles = $transcription_cycle_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('available', 1)
			->orderby('BMD_cycle_sort', 'ASC')
			->findAll();
		
		// load fonts from fonts folder
		$fontDir = getenv('app.fontDir') ?? getcwd().'/Fonts';
		$dir = new DirectoryIterator(dirname($fontDir . '/*.*'));
		$data_entry_fonts = array();
		foreach ($dir as $fileinfo) 
			{
				if (!$fileinfo->isDot()) 
					{
						$font_name_array = explode('.', $fileinfo->getFilename());
						$data_entry_fonts[] = $font_name_array[0];
					}
			}
		asort($data_entry_fonts);
		// load font_styles
		$data_entry_styles = array('normal', 'bold', 'bolder', 'lighter');
		asort($data_entry_styles);
		// create the roman2arabic conversion array - according to M Cope ony numbers 1 to 27 are used.
		$session->roman2arabic = array	(
											"I" => 1,
											"II" => 2,
											"III" => 3,
											"IV" => 4,
											"V" => 5,
											"VI" => 6,
											"VII" => 7,
											"VIII" => 8,
											"IX" => 9,
											"X" => 10,
											"XI" => 11,
											"XII" => 12,
											"XIII" => 13,
											"XIV" => 14,
											"XV" => 15,
											"XVI" => 16,
											"XVII" => 17,
											"XVIII" => 18,
											"XIX" => 19,
											"XX" => 20,
											"XXI" => 21,
											"XXII" => 22,
											"XXIII" => 23,
											"XXIV" => 24,
											"XXV" => 25,
											"XXVI" => 26,
											"XXVII" => 27,
										);
		// scan formats
		$session->scan_formats = array	(	
											'select' => 'Select :',
											'handwritten' =>'Handwritten', 
											'typed' => 'Typed',
											'printed' => 'Printed'
										);
										
		// colours
		$session->colours = array		(	
											'select' => 'Select :',
											'red' =>'Red', 
											'green' => 'Green',
											'blue' => 'Blue',
											'Pink' => 'Pink',
											'black' => 'Black',
											'lightgreen' => 'Light Green',
											'lightblue' => 'Light Blue'
										);
										
		// load data entry formats
		$session->data_entry_formats = array(
											'select' => 'Select :',
											'baptism' => 'Baptism',
											'burial' => 'Burial',
											'marriage' => 'Marriage',
											);
		
		// load conditions
		$session->conditions = $condition_model
			->findAll();
		// load titles
		$session->titles = $title_model
			->findAll();
		// load licences
		$session->licences = $licence_model
			->findAll();
		// load relationships
		$session->relationships = $relationship_model
			->findAll();
		// load relationships
		$session->person_status = $person_status_model
			->findAll();
		// load sex
		$session->sex = ['male', 'female', 'unknown', 'Male', 'Female', 'Unknown', 'MALE', 'FEMALE', 'UNKNOWN'];								
	
		// load to session
		$session->set('districts', $districts);
		$session->set('syndicates', $syndicates);
		$session->set('allocations', $allocations);
		$session->set('alphabet', $alphabet);
		$session->set('quarters', $quarters);
		$session->set('quarters_short_long', $quarters_short_long);
		$session->set('month_to_quarter', $month_to_quarter);
		$session->set('scan_name_types', $scan_name_types);
		$session->set('yesno', $yesno);
		$session->set('current_date', $current_date);
		$session->set('parameters', $parameters);
		$session->set('reference_extension_array', $reference_extension_array);
		$session->set('reference_extension_control', $reference_extension_control);
		$session->set('comment_types', $comment_types);
		$session->set('transcription_cycles', $transcription_cycles);
		$session->set('data_entry_fonts', $data_entry_fonts);
		$session->set('data_entry_styles', $data_entry_styles);
	}
	
function get_string_between($string, $start, $end)
	{
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
	
function BMD_file_exists_on_project($BMD_file_name) // does this file name already exist on FreeBMD?
	{
		// initialise
		$session = session();
		$session->set('BMD_file_exists_on_project', '0');
		
		// test bmd file exists on server
		$curl_url = $session->curl_url;
		
		// create the curl parameters
		$encoding = 'iso8859-1';
		//$encoding = 'utf8';
		// set up the fields to pass
		$postfields =	array	(
									"__bmd_0" => "Download",
									"__bmd_1" => $session->identity_userid,
									"__bmd_2" => $session->identity_password,
									"encoding" => $encoding,
									"downloaddo_".$BMD_file_name => "Download",
								);
		// set up the curl depending on environment
		$ch = curl_init($session->curl_url);			
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// execute the curl.	
		$curl_result = curl_exec($ch);

		// do I have a bmd file?
		$data_array = explode(',', $curl_result);
		if ( $data_array[0] == '+INFO' )
			{
				$session->set('BMD_file_exists_on_project', '1');
			}	
	}
	
function manage_syndicate_DB()
	{
		// initialise method
		$session = session();
		$syndicate_model = new Syndicate_Model();
		$identity_model = new Identity_Model();
		
		// depends on project
		switch ($session->current_project[0]['project_name']) 
			{
				case 'FreeBMD':
					// set all not active
					$syndicate_model
						->where('project_index', $session->current_project[0]['project_index'])
						->set(['status' => '1'])
						->update();
					
					// get syndicates from FreeBMD server
					$db = \Config\Database::connect($session->syndicate_DB);
					$sql =	"
								SELECT * 
								FROM SyndicateTable 
								WHERE SyndicateTable.SyndicateShortDesc NOT LIKE 'This syndicate is no longer active having completed its agreed allocations.'
								ORDER BY SyndicateTable.SyndicateID
							";
					$query = $db->query($sql);
					$active_project_syndicates = $query->getResultArray();
					
					// read active project syndicates
					foreach ( $active_project_syndicates as $active_syndicate )
						{
							// does this syndicate exist in FreeComETT syndicates table
							$exists	= $syndicate_model
								->where('project_index', $session->current_project[0]['project_index'])
								->where('BMD_syndicate_name', $active_syndicate['SyndicateName'])
								->find();
								
							// found?
							if ( $exists )
								{
									// Update it as active
									$syndicate_model
										->where('project_index', $session->current_project[0]['project_index'])
										->where('BMD_syndicate_name', $active_syndicate['SyndicateName'])
										->set(['BMD_syndicate_email' => $active_syndicate['SyndicateEmail']])
										->set(['status' => '0'])
										->update();
								}
							else
								{
									// insert it as active
									$syndicate_model
										->set(['project_index' => $session->current_project[0]['project_index']])
										->set(['BMD_syndicate_index' => $active_syndicate['SyndicateID']])
										->set(['BMD_syndicate_name' => $active_syndicate['SyndicateName']])
										->set(['BMD_syndicate_leader' => $active_syndicate['CorrectionsContact']])
										->set(['BMD_syndicate_email' => $active_syndicate['SyndicateEmail']])
										->set(['saved_email' => $active_syndicate['SyndicateEmail']])
										->set(['BMD_syndicate_credit' => 'N'])
										->set(['status' => '0'])
										->set(['new_user_environment' => 'TEST'])
										->insert();
								}
						}
					break;
				case 'FreeREG':
					// define mongodb
					define_environment(2);
					$mongodb = define_mongodb();
					
					$collection_syndicates = $mongodb['database']->{'syndicates'};
					$collection_userid = $mongodb['database']->{'userid_details'};
					
					// get all syndicate details
					$active_project_syndicates = $collection_syndicates->find()->toArray();
					
					// read active project syndicates
					foreach ( $active_project_syndicates as $active_syndicate )
						{
							// get coordinator details for this syndicate
							$coord = $identity_model
								->where('project_index', $session->current_project[0]['project_index'])
								->where('BMD_user', $active_syndicate['syndicate_coordinator'])
								->find();

							// coord exists in FreeComETT identity table?
							if ( ! $coord )
								{
									// add it if not
									$identity_model
										->set(['BMD_user' => $active_syndicate['syndicate_coordinator']])
										->set(['role_index' => 2])
										->set(['project_index' => $session->current_project[0]['project_index']])
										->insert();
								}
							
							// get coordinator details
							$coord = $collection_userid->findOne
								(
									[
										'userid' => $active_syndicate['syndicate_coordinator']
									]
								);
								
							// decode syndicate IDid
							$id = $active_syndicate['_id']->__toString();
							
							// does this syndicate exist in FreeComETT syndicates table
							$exists	= $syndicate_model
								->where('project_index', $session->current_project[0]['project_index'])
								->where('BMD_syndicate_index', $id)
								->find();
													
							// found?
							if ( $exists )
								{
									// Update it
									$syndicate_model
										->where('project_index', $session->current_project[0]['project_index'])
										->where('BMD_syndicate_index', $id)
										->set(['BMD_syndicate_email' => $coord['email_address']])
										->set(['BMD_syndicate_name' => $active_syndicate['syndicate_code']])
										->update();
								}
							else
								{					
									// insert it as active
									$syndicate_model
										->set(['project_index' => $session->current_project[0]['project_index']])
										->set(['BMD_syndicate_index' => $id])
										->set(['BMD_syndicate_name' => $active_syndicate['syndicate_code']])
										->set(['BMD_syndicate_leader' => $coord['person_forename'].' '.$coord['person_surname']])
										->set(['BMD_syndicate_email' => $coord['email_address']])
										->set(['saved_email' => $coord['email_address']])
										->set(['BMD_syndicate_credit' => 'N'])
										->set(['status' => '0'])
										->set(['new_user_environment' => 'TEST'])
										->insert();
								}
						}
					break;
				case 'FreeCEN':
					break;
			}
	}
	
function define_mongodb()
	{
		// initialise
		$session = session();
		$project_db_model = new Project_DB_Model();
		$mongodb = array();
		
		// test time out
		if ( ! $session->has('realname') )
			{
				// ask to resignin
				return redirect()->to( base_url('/home/signout/') );
			}

		// get DB params for the environment
//d($session->current_project[0]['project_index']);
//dd($session->environment);
		$db_parms = $project_db_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('environment', $session->environment)
			->find();
		if ( !$db_parms ) die('Sorry a fatal error has occurred and I cannot continue. The error is: cannot get DB_parms in common_helper[define_mongodb()]. Please send an email to '.$session->linbmd2_email.' reporting this problem. Thank you for your help.');
//dd($db_parms);		
		// define mongodb client
		$mongodb['client'] = new \MongoDB\Client($db_parms[0]['DB_driver'].$db_parms[0]['DB_hostname'].':'.$db_parms[0]['DB_hostport']);
		// define mongodb database
		$mongodb['database'] = $mongodb['client']->selectDatabase($db_parms[0]['DB_database']);
		// return the array
		return $mongodb;
	}

function define_environment($level)
	{
		// initialise
		$session = session();
		$parameter_model = new Parameter_Model();
		$environment = '';

		// A word about environment
		// The environment tells FreeComETT whether to use TEST or LIVE servers.
		// It can be set 
		// - at application level in the parameters table
		// - at project level in the projects table
		// - at user level in the identity table. A new user is always added as TEST until his coordinator moves him to LIVE.
		// Thus this is a hierachy
		// Global first, then project, then user
		
		// the parameter passed to this method tells the method to which level it should go to determine the environment.
		// level 1 = global level.
		// level 2 = project level.
		// level 3 = user level.
		
		// Get Global environment
		$parameter = $parameter_model->where('Parameter_key', 'environment')->find();
		$session->set('environment_global', $parameter[0]['Parameter_value']);
		
		// project environment is set in Projects controller = $session->environment_project
		// user environment is set in this method = $session->environment_user
		
		// set the $session->environment variable - default is LIVE
		$session->environment = 'LIVE';
		if ( $session->environment_global == 'TEST' AND $level >= 1)
			{
				$session->environment = $session->environment_global;
			}
		elseif ( $session->environment_project == 'TEST' AND $level >= 2 )
			{
				$session->environment = $session->environment_project;
			}
		elseif ( $session->environment_user == 'TEST' AND $level == 3 )
			{
				$session->environment = $session->environment_user;
			}
		
		// return
		return;
	}

function get_source($key)
	{
		// initialise
		$session = session();
		$source_info = 'error';
		$source_data = 'error';
		$source_value = 'error';
		// get the source info
		$source_info = get_source_info($session->current_project[0]['project_index'], $key);
		if ( $source_info != 'error' ) $source_data = get_source_data($source_info);
		if ( $source_data != 'error' ) $source_value = get_source_value($source_data, $source_info[0]);
		
		return $source_value;
	
	}
	
function get_source_info($project, $key)
	{
		// initialise
		$session = session();
		$sources_model = new Freeukgen_Sources_Model();
		
		// get source info
		$source_info = $sources_model
			->where('project_index', $project)
			->where('source_key', $key)
			->find();
			
		// found?
		if ( ! $source_info )
			{
				$source_info = 'error';
			}
			
		// return the source info
		return $source_info;
	}
	
function get_source_data($source_info)
	{
		// initialise
		$session = session();
	
		// set cURL
		$curl_url = $source_info['source_protocol'].$source_info['source_URL'].$source_info['source_port'].$source_info['source_folder'].$source_info['source_path'].$source_info['source_name'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curl_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $source_info['source_user'].':'.$source_info['source_password']);
					
		// and execute it
		$source_data = curl_exec($ch);
		
		// success?
		if ( curl_getinfo($ch, CURLINFO_RESPONSE_CODE) == '404' )
			{
				$source_data = 'error';
			}
			
		// close the cURL
		curl_close($ch);
			
		// return data
		return $source_data;
	}
	
function get_source_value($source_data, $source_info)
	{
		// initialise
		$session = session();
	
		// if reserved word $#none#$, return the data string as is
		if ( $source_info['source_field'] == '$#none#$' )
			{
				//return source data string
				return $source_data;
			}
			
		// find the section
		if ( $source_info['source_section'] != '$#none#$' )
			{
				// get the section last occurence starting position
				$position = strrpos($source_data, $source_info['source_section']);
				if ( $position === false )
					{
						return 'error';
					}
				// make a substring from the section starting position to end of string
				$source_data = substr($source_data, $position);
			}		
		
		// does the data contain the key? return error if not.
		if ( ! str_contains($source_data, $source_info['source_field']) )
			{
				return 'error';
			}
		
		// OK the string does contain the key
		// 1) find its key_start_position and its key_length
		// 2) start search for separator from key_start_position + key_length -1 using offset in strpos. Find the separator_start_position of the separator
		// 3) start search for value_start_delimiter from separator_start_position + separator_length -1
		// 4) start search for value_end_delimiter from start_delimiter_start_position + start_delimiter_length -1
		// 5) get value between start_delimiter_position + 1 and end_delimiter_position - 1
		// apply type
	
		// 1)
		$key_start_pos = strpos($source_data, $source_info['source_field']);

		// 2)
		$sep_offset = $key_start_pos + strlen($source_info['source_field']) -1;
		$sep_start_pos = strpos($source_data, $source_info['source_separator'], $sep_offset);

		// 3)
		$start_delim_offset = $sep_start_pos + strlen($source_info['source_separator']) -1;
		$start_delim_start_pos = strpos($source_data, $source_info['source_value_start_delim'], $start_delim_offset);

		// 4)
		$end_delim_offset = $start_delim_start_pos + strlen($source_info['source_value_start_delim']);
		$end_delim_start_pos = strpos($source_data, $source_info['source_value_end_delim'], $end_delim_offset);

		// 5)
		$val_start_pos = $start_delim_start_pos + 1;
		$val_len = $end_delim_start_pos - $val_start_pos;
		$source_value = substr($source_data, $val_start_pos, $val_len);
		
		// 6)
		if ( $source_info['source_return_type'] == 'array' )
			{
				// explode source_value on ,
				$source_value_array = explode(',', $source_value);
				foreach ( $source_value_array as $key => $value )
					{
						$value = trim($value);
						$value = trim($value, '\'');
						$value = str_replace('\'', '', $value);
						$source_value_array[$key] = $value;
					}
				$source_value = $source_value_array;
			}
						
		// return data
		return $source_value;
	}
	
function get_image_for_parameters($image_record)
	{
		// initialise
		$session = session();	
		
		// get url and current index		
		$url = $image_record['image_url'];
		$session->current_image_index = $image_record['image_index'];	
					
		// get image info to get mime type
		$imageInfo = getimagesize($url);					
		// get image size
		$session->x_size = $imageInfo[0];
		$session->y_size = $imageInfo[1];
		// get mime type
		$session->mime_type = $imageInfo['mime'];
				
		// encode to base 64
		$session->params_fileEncode = base64_encode(file_get_contents($url));
	}
