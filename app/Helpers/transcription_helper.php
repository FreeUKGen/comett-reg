<?php

// this helper to provide functions for managing transcriptions

use App\Models\Transcription_Model;
use App\Models\Syndicate_Model;
use App\Models\Allocation_Model;
use App\Models\Allocation_Images_Model;
use App\Models\Allocation_Image_Sources_Model;
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

function FreeREG_create_transcription_package($current_assignment, $bmd_file_name = '')
	{				
		// initialise method
		$session = session();
		$allocation_model = new Allocation_Model();
		$allocation_images_model = new Allocation_Images_Model();
		$allocation_image_sources_model = new Allocation_Image_Sources_Model();
		$def_fields_model = new Def_Fields_Model();
		$transcription_model = new Transcription_Model();
		$transcription_detail_def_model = new Transcription_Detail_Def_Model();
		$transcription_comments_model = new Transcription_Comments_Model();
		$identity_model = new Identity_Model();
		
		// load image source record for this assignment
		$assignment_source = $allocation_image_sources_model
			->where('project_index', $session->current_project['project_index'])
			->where('source_code', $current_assignment['source_code'])
			->findAll();
		
		// load assignment images for those records requiring them
		$image = '';
		if ( $assignment_source[0]['source_images'] == 'yes' )
			{
				$assignment_images = $allocation_images_model
					->where('allocation_index', $current_assignment['BMD_allocation_index'])
					->where('transcription_index', NULL)
					->orderby('original_image_file_name')
					->findAll();
				$image = $assignment_images[0]['image_file_name'];
			}
			
		// load standard data dictionary for all event types
		$standard_data_dictionary = $def_fields_model
			->where('project_index', $session->current_project['project_index'])
			->findAll();
						
		// create transcription

		// I have all the info required to create the transcripton header, so create it!
		// three tables will be inserted to,
		// a) the transcription table
		// b) the transcription comment table
		// c) the transcription detail def fields table
		// the assignment image table will be updated with the transcription index attached to the scan(s).
		
		// set TP (Transcription Package) sequence
		$tp_seq = $current_assignment['REG_TP_seq'] + 1;
		$tp_seq = str_pad($tp_seq, 3, '0', STR_PAD_LEFT);
		
		// set the TP file name but only if file name not passed in from load csv file routine in allocation controller - see issue 194
		if ( $bmd_file_name == '' )
			{
				// this is built from
				// chapman_code+church_code+current_linux-time_stamp
				// set TP file name
				$TP_date = new DateTimeImmutable();
				$milli = (int) $TP_date->format('Uv'); // Timestamp in milliseconds
				// church code may be null
				if ( $current_assignment['REG_church_code'] == NULL )
					{
						$current_assignment['REG_church_code'] = '   ';
					}

				$bmd_file_name = trim($current_assignment['REG_chapman_code']).trim($current_assignment['REG_church_code']).'_'.$milli;
			}	
		
		// set TP type
		$type = 'C';			
	
		// set last action
		switch ( $session->current_project['project_index'] )
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
		
		// insert header to transcription table
		$transcription_model
			->set(['project_index' => $session->current_project['project_index']])
			->set(['BMD_identity_index' => $session->BMD_identity_index])
			->set(['BMD_allocation_index' => $current_assignment['BMD_allocation_index']])
			->set(['BMD_syndicate_index' => $current_assignment['BMD_syndicate_index']])
			->set(['BMD_file_name' => $bmd_file_name])
			->set(['BMD_scan_name' => $image])
			->set(['BMD_start_date' => $session->current_date])
			->set(['BMD_end_date' => NULL])
			->set(['BMD_submit_date' => NULL])
			->set(['BMD_submit_status' => NULL])
			->set(['BMD_submit_fail_message' => NULL])
			->set(['BMD_current_page' => NULL])
			->set(['BMD_current_page_suffix' => NULL])
			->set(['BMD_next_page' => NULL])
			->set(['BMD_records' => 0])
			->set(['BMD_last_action' => $last_action])
			->set(['BMD_image_x' => 1])
			->set(['BMD_image_y' => 150])
			->set(['BMD_image_rotate' => 0])
			->set(['BMD_image_scroll_step' => 50])
			->set(['BMD_panzoom_x' => 1])
			->set(['BMD_panzoom_y' => 1])
			->set(['BMD_panzoom_z' => 1])
			->set(['BMD_sharpen' => 2])
			->set(['BMD_font_family' => $session->data_entry_font])
			->set(['zoom_lock' => 'N'])
			->set(['header_x' => 1920])
			->set(['header_y' => 1080])
			->set(['BMD_header_status' => 0])
			->set(['source_code' => $current_assignment['source_code']])
			->insert();
			
		// get inserted index		
		$transcription_index = $transcription_model->getInsertID();
	
		// insert comment and source
		$transcription_comments_model
			->set(['transcription_index' => $transcription_index])
			->set(['project_index' => $session->current_project['project_index']])
			->set(['identity_index' => $session->BMD_identity_index])
			->set(['comment_sequence' => 10])
			->set(['comment_text' => $session->document_comment])
			->set(['source_text' => $session->document_source])	
			->insert();
			
		// attach transcription index to images
		if ( $assignment_source[0]['source_images'] == 'yes' )
			{
				foreach ( $assignment_images as $image )
					{
						// update image record with transcription index
						$allocation_images_model
							->where('image_index', $image['image_index'])
							->set(['transcription_index' => $transcription_index])
							->update();
					}
			}
			
		// insert detail defs to Transcription detail defs table
		// read the standard def entries and create the data array for insert to transcription detail def records
		foreach ( $standard_data_dictionary as $record )
			{
				// remove record index to avoid duplicate primary indexes
				unset($record['field_index']);
				// set the additional fields not in standard def
				$record['transcription_index'] = $transcription_index;
				$record['identity_index'] = $session->BMD_identity_index;
				// insert the record
				$transcription_detail_def_model->insert($record);					
			}

		// update allocation tp seq
		$allocation_model
			->set(['REG_TP_seq' => $tp_seq])
			->where('BMD_allocation_index', $current_assignment['BMD_allocation_index'])
			->update();			
		
		// update identity with the last allocation and next page
		$identity_model
			->set(['last_allocation' => $current_assignment['BMD_allocation_index']])
			->set(['last_transcription' => $transcription_index])
			->set(['last_page_in_last_transcription' => 0])
			->update($session->BMD_identity_index);
			
		// reload identity
		$session->current_identity = $identity_model	
			->where('project_index', $session->current_project['project_index'])
			->where('BMD_identity_index', $session->BMD_identity_index)
			->find();
			
		// that's it - return to caller
		return($transcription_index);
	}