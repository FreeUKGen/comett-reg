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

class Allocation_import_csv_data_background extends BaseController
{	
	function __construct() 
	{
        helper('common');
        helper('transcription');
    }
	
	public function import()
    {
		// initialise method
		$session = session();
		$transcription_model = new Transcription_Model();
		$detail_data_model = new Detail_Data_Model();
		$def_fields_model = new Def_Fields_Model();
		$transcription_detail_def_model = new Transcription_Detail_Def_Model();
		
// to test, output parms to doc
$fh = fopen(getcwd().'/tmp/'.$_POST['tp_index'].'_'.$_POST['csv_file_name'].'.txt', 'w');
fwrite($fh, 'tp_index => '.$_POST['tp_index']."\r\n");
fwrite($fh, 'csv_file_name => '.$_POST['csv_file_name']."\r\n");
fwrite($fh, 'csv_file_id => '.$_POST['csv_file_id']."\r\n");
fwrite($fh, 'first_data_line_key => '.$_POST['first_data_line_key']."\r\n");
fwrite($fh, ' '."\r\n");
fwrite($fh, 'csv_def_fields => '.$_POST['csv_def_fields']."\r\n");
fwrite($fh, ' '."\r\n");
fwrite($fh, 'csv_line_array => '.$_POST['csv_line_array']."\r\n");
fwrite($fh, ' '."\r\n");
		
		// get def fields
		$csv_def_fields = json_decode($_POST['csv_def_fields'], true);
		// get data
		$csv_line_array = json_decode($_POST['csv_line_array'], true);
		$import_total = 0;
		$i = $_POST['first_data_line_key'];
		for ($i; $i < count($csv_line_array); $i++ ) 
			{			
				if ( !empty($csv_line_array[$i]) )
					{
						$import_total = $import_total + 1;
					}
			}
fwrite($fh, 'import_total => '.$import_total."\r\n");
		$import_count = 0;
				
		// read detail lines and populate detail records in detail table
		// get data start array key
		$i = $_POST['first_data_line_key'];
		$number_of_fields = count($csv_def_fields);
		$line_sequence = 0;

		for ($i; $i < count($csv_line_array); $i++ ) 
			{			
				$insert_array = array();
				if ( !empty($csv_line_array[$i]) )
					{
						// parse data
						$csv_data_fields = str_getcsv($csv_line_array[$i]);

						// read data fields and create insert array
						foreach ( $csv_data_fields as $key => $value )
							{
								// clean value
								$value = str_replace('"', '', $value);
								$value = trim($value);

								// get def field definition
								$def_field = $def_fields_model
									->where('project_index', $_POST['project_index'])
									->where('table_fieldname', $csv_def_fields[$key])
									->find();

								if ( $def_field )
									{
										switch ($def_field[0]['field_type']) 
											{
												// clean date
												case 'date':
													$data_array = explode(' ', $value);
													if ( strlen($data_array[0]) == 1 )
														{
															$value = '0'.$value;
														}
													break;
												// clean church name
												case 'church_name':
													$value = $_POST['REG_church_name'];
													break;
												// clean sex
												case 'sex':
													if ( $value == 'f' OR $value == 'F' )
														{
															$value = 'Female';
														}
													if ( $value == 'm' OR $value == 'M' )
														{
															$value = 'Male';
														}
													break;
												case 'notes':
													// if a notes field is all z, blank it
													$z = 1;
													foreach (mb_str_split($value) as $char) 
														{
															if ( $char != 'z' )
																{
																	$z = 0;
																}
														}
												
													if ( $z == 1 )
														{	
															$value = '';
														}
													break;	
											}
									}
								// save to insert array
								$insert_array[$csv_def_fields[$key]] = $value;
							}
						// add other data elements
						$line_sequence = $line_sequence + 10;
						$insert_array['project_index'] = $_POST['project_index'];
						$insert_array['BMD_identity_index'] = $_POST['BMD_identity_index'];
						$insert_array['BMD_header_index'] = $_POST['tp_index'];
						$insert_array['data_entry_format'] = $_POST['data_entry_format'];
						$insert_array['BMD_line_sequence'] = $line_sequence;
						$data_array_key = array_search('register_type', $insert_array);
						if ( $data_array_key == false )
							{
								$insert_array['register_type'] = $_POST['register_type'];
							}
						// and insert record
						$detail_data_model->insert($insert_array);						
						
						// update header every 10 records written
						$import_count = $import_count + 1;
						if ( $import_count == 10 )
							{
								$import_written = $line_sequence / 10;
								$transcription_model
									->where('BMD_header_index', $_POST['tp_index'])
									->set(['BMD_submit_status' => 'Import CSV in Progress. '.$import_written.' of '.$import_total.' lines imported.'])
									->update();
								$import_count = 0;
							}
					}
			}

		// update TP with number of records and remove import_csv flag
		$transcription_model
			->where('BMD_header_index', $_POST['tp_index'])
			->set(['BMD_records' => $line_sequence / 10])
			->set(['BMD_submit_status' => 'Import CSV Complete'])
			->set(['import_in_progress' => 0])
			->update();
			
		// update the transcription def fields to match imported fields
		// first uncheck and unshow all fields
		$transcription_detail_def_model
			->where('transcription_index', $_POST['tp_index'])
			->set(['field_check' => 'N'])
			->set(['field_show' => 'N'])
			->update();
		// now read csv def fields and check and show fields in transcription def fields
		foreach ( $csv_def_fields as $field )
			{
				$transcription_detail_def_model
					->where('transcription_index', $_POST['tp_index'])
					->where('table_fieldname', $field)
					->set(['field_check' => 'Y'])
					->set(['field_show' => 'Y'])
					->update();
			}

		// delete old csv file in backend if in LIVE.
		// leave them in test so that they can be used again
		if ( $session->environment == 'LIVE' )
			{
				// set curl parms
				$curl_url = 'https://freereg.org.uk/physical_files/'.$_POST['csv_file_id'].'?';
				$postfields = array	(
										'locale' => 'en',
										'userid' => $_POST['BMD_identity_index'],
										'password' => $_POST['password'],
										'project' => $_POST['project'],
										'freecomett' => 'yes'
									);			
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $curl_url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($ch, CURLOPT_USERPWD, 'test:test'); // temporary for test3 only
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
				//curl_setopt($ch, CURLOPT_VERBOSE, true);
				//curl_setopt($ch, CURLOPT_STDERR, fopen(getcwd()."/curl.log", 'a+'));
				// run the curl
				$curl_result = curl_exec($ch);
				curl_close($ch);
			}
			
		// die		
		die;
    }

}
