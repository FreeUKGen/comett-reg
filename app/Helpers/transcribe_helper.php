<?php namespace App\Controllers;
use App\Models\Detail_Data_Model;
use App\Models\Detail_Comments_Model;
use App\Models\Districts_Model;
use App\Models\Volumes_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Def_Fields_Model;
use App\Models\Transcription_Detail_Def_Model;
use App\Models\Transcription_Model;
use App\Models\Transcription_Comments_Model;
use App\Models\Project_Types_Model;
use App\Models\Transcription_CSV_File_Model;
use App\Models\Condition_Model;
use App\Models\Title_Model;
use App\Models\Licence_Model;
use App\Models\Relationship_Model;
use App\Models\Person_Status_Model;
use App\Models\Data_Group_Model;
use App\Models\Allocation_Images_Model;
use App\Models\User_Data_Entry_Layouts_Model;
use App\Models\User_Data_Entry_Layout_Fields_Model;
use App\Models\Transcription_Current_Layout_Model;
use App\Models\Identity_Last_Indexes_Model;
use MongoDB\BSON\Regex;
	
	function comment_update()
	{
		// initialise
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		$detail_comments_model = new Detail_Comments_Model();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// get inputs
		$session->set('comment_type', $_POST['comment_type']);
		$session->set('comment_span', $_POST['comment_span']);
		$session->set('comment_text', $_POST['comment_text']);
		// do tests
		switch ($session->comment_type) 
			{
				case "B":
					// comment span
					if ( $session->comment_span != '' )
						{
							$session->set('comment_span', '');
							$session->set('message_2', 'You cannot enter span for a +BREAK line');
							$session->set('message_class_2', 'alert alert-danger');
							$session->set('message_error', 'error');
							return;
						}
					// comment must be blank
					if ( $session->comment_text != '' )
						{
							$session->set('comment_text', '');
							$session->set('message_2', 'You cannot enter text for a +BREAK line.');
							$session->set('message_class_2', 'alert alert-danger');
							$session->set('message_error', 'error');
							return;
						}
					break;
				case "P": // add page
					// comment span
					if ( $session->comment_span != '' )
						{
							$session->set('comment_span', '');
							$session->set('message_2', 'You cannot enter span for a +PAGE line');
							$session->set('message_class_2', 'alert alert-danger');
							$session->set('message_error', 'error');
							return;
						}
					// comment must be blank
					if ( $session->comment_text != '' )
						{
							$session->set('comment_text', '');
							$session->set('message_2', 'You cannot enter text for a +PAGE line.');
							$session->set('message_class_2', 'alert alert-danger');
							$session->set('message_error', 'error');
							return;
						}
					// +PAGE cannot be added to last line
					$last_detail_key = array_key_last($session->transcribe_detail_data);
					$last_detail_index = $session->transcribe_detail_data[$last_detail_key]['BMD_index'];	
					if ( $last_detail_index == $session->line_index )
						{
							$session->set('message_2', 'You cannot enter a +PAGE line for the last detail line.');
							$session->set('message_class_2', 'alert alert-danger');
							$session->set('message_error', 'error');
							return;
						}
					break;
				default:
					// comment span
					if ( ! is_numeric($session->comment_span) )
						{
							$session->set('message_2', 'Span must be a number.');
							$session->set('message_class_2', 'alert alert-danger');
							$session->set('message_error', 'error');
							return;
						}
					if ( $session->comment_span <= 0 )
						{
							$session->set('message_2', 'Span must be greater than 0');
							$session->set('message_class_2', 'alert alert-danger');
							$session->set('message_error', 'error');
							return;
						}
					// comment text
					if ( $session->comment_text == '' )
						{
							$session->set('message_2', 'Please enter some text in order to create the annotation.');
							$session->set('message_class_2', 'alert alert-danger');
							$session->set('message_error', 'error');
							return;
						}
					break;	
			}
		
		// update record
		$data =	[
					'BMD_identity_index' => $session->BMD_identity_index,
					'BMD_header_index' => $session->transcribe_detail[0]['BMD_header_index'],
					'BMD_line_index' => $session->transcribe_detail[0]['BMD_index'],
					'BMD_line_sequence' => $session->transcribe_detail[0]['BMD_line_sequence'],
					'BMD_comment_type' => $session->comment_type,
					'BMD_comment_span' => $session->comment_span,
					'BMD_comment_text' => $session->comment_text,
				];
				
		// insert record - annotation records are always inserted.
		$detail_comments_model->insert($data);
		$session->set('message_2', 'Annotation line added.');
		$session->set('message_class_2', 'alert alert-success');
		$session->set('message_error', 'success');				
			
		// reload data
		$session->set('transcribe_detail_comments', $detail_comments_model	
			->where('BMD_line_index', $session->transcribe_detail[0]['BMD_index'])
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('BMD_header_index', $session->transcribe_detail[0]['BMD_header_index'])
			->find());
	}
	
	function comment_remove($comment_line_index)
	{
		// initialse
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		$detail_comments_model = new Detail_Comments_Model();
		// remove record
		$detail_comments_model->delete($comment_line_index);
		$session->set('message_2', 'Annotation line removed.');
		$session->set('message_class_2', 'alert alert-success');
		$session->set('message_error', 'success');
		// reload data
		$session->set('transcribe_detail_comments', $detail_comments_model	
			->where('BMD_line_index', $session->transcribe_detail[0]['BMD_index'])
			->where('BMD_identity_index', $session->BMD_identity_index)
			->where('BMD_header_index', $session->transcribe_detail[0]['BMD_header_index'])
			->find());
		// return
		return;				
	}
	
	function comment_select($detail_line_index)
	{
		// initialse
		$session = session();
		$detail_comments_model = new Detail_Comments_Model();
		$detail_data_model = new Detail_Data_Model();
		// if no error get the data, otherwise just show error
		if ( $session->message_error != 'error' )
			{
				// get the line detail
				$session->set('transcribe_detail', $detail_data_model	
					->where('BMD_index', $detail_line_index)
					->where('BMD_identity_index', $session->BMD_identity_index)
					->where('BMD_header_index', $session->current_transcription[0]['BMD_header_index'])
					->find());
					
				// get the comment lines and load fields
				$session->set('transcribe_detail_comments', $detail_comments_model	
					->where('BMD_line_index', $detail_line_index)
					->where('BMD_identity_index', $session->BMD_identity_index)
					->where('BMD_header_index', $session->current_transcription[0]['BMD_header_index'])
					->find());
				
				// load session fields
				$session->set('line_index', $detail_line_index);
				$session->set('line_sequence', '');
				$session->set('comment_type', '');
				$session->set('comment_span', '');
				$session->set('comment_text', '');
			}
		return;
	}
	
	function select_trans_line($line_index)
	{
		// initialse
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		$transcription_model = new Transcription_Model();
		
		// turn off verify on the fly flag so that system shows verify on the fly in transcribe.
		$session->verify_onthefly = 0;
		
		// save current image parameters
		$session->set('save_panzoom_x', $session->panzoom_x);
		$session->set('save_panzoom_y', $session->panzoom_y);
		$session->set('save_panzoom_z', $session->panzoom_z);
		$session->set('save_sharpen', $session->sharpen);
		$session->set('save_rotation', $session->rotation);
		$session->set('save_image_file_name', $session->current_transcription[0]['BMD_scan_name']); //276
		
		// get the line to modify
		$array_key = array_search($line_index, array_column($session->transcribe_detail_data, 'BMD_index'));
		$session->current_line = $session->transcribe_detail_data[$array_key];
	
		// get previous line
		if ( $array_key - 1 < 0 )
			{
				$session->lastEl = array();
			}
		else
			{
				$session->lastEl = $session->transcribe_detail_data[$array_key - 1];
			}

		// set image parameters for this line
		$session->set('panzoom_x', $session->current_line['BMD_line_panzoom_x']);
		$session->set('panzoom_y', $session->current_line['BMD_line_panzoom_y']);
		$session->set('panzoom_z', $session->current_line['BMD_line_panzoom_z']);
		$session->set('sharpen', $session->current_line['BMD_line_sharpen']);
		$session->set('rotation', $session->current_line['BMD_line_image_rotate']);
		$session->set('current_image_file_name', $session->current_line['image_file_name']); //276
		// update image on transcription //276
		$transcription_model
			->where('BMD_header_index', $session->current_transcription[0]['BMD_header_index'])
			->set(['BMD_scan_name' => $session->current_image_file_name])
			->update();
		// setup image and parameters but only if source requires them. //276
		$session->image_count = 0;
		if ( $session->image_source[0]['source_images'] == 'yes' )
			{
				setup_image_and_parameters();
			}
	
		// save line sequence
		$session->modify_line_sequence = $session->current_line['BMD_line_sequence'];
		
		// get data entry format fields and load data entry fields
		foreach ( $session->current_transcription_def_fields as $field_line )
			{
				foreach ( $field_line as $field )
					{
						// get html field name
						$fn = $field['html_name'];
						$session->fieldname = $fn;
						
						// load data entry field from current line
						$session->$fn = $session->current_line[$field['table_fieldname']];
					}
			}
		
		// set line_edit flag
		$session->set('line_edit_flag', 1);
		// set error data group flag
		$session->set('error_data_group', '');
		// set valid volume flag
		$session->volume_ok = 'N';
		// set valid firstname flag
		$session->firstname_ok = 'N';
		// set view type
		$session->set('show_view_type', 'transcribe');
		$session->image_processed = ''; //276
		
		// set message
		$session->set('message_2', 'You requested to modify line number => '.$session->current_line['BMD_line_sequence'].'. Data for this line  is shown below the image.');
		$session->set('message_class_2', 'alert alert-warning');
	}
	
	function transcribe_initialise_step1($start_message, $controller, $controller_title)
	{
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		$detail_comments_model = new Detail_Comments_Model();
		$def_ranges_model = new Def_Ranges_Model();
		$transcription_detail_def_model = new Transcription_Detail_Def_Model();
		$transcription_comments_model = new Transcription_Comments_Model();
		$data_group_model = new Data_Group_Model();
		$allocation_images_model = new Allocation_Images_Model();
		$project_types_model = new Project_Types_Model();
		$licence_model = new Licence_Model();
		$person_status_model = new Person_Status_Model();
		$condition_model = new Condition_Model();
		$title_model = new Title_Model();
		$user_data_entry_layouts_model = new User_Data_Entry_Layouts_Model();
		$transcription_current_layout_model = new Transcription_Current_Layout_Model();
		$def_fields_model = new Def_Fields_Model();
		
		$session->search_message = '';
			
		// get all image records
		$session->image_records = $allocation_images_model
			->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
			->orderby('original_image_file_name')
			->find();
		// count images
		$session->image_count = 0;
		if ( $session->image_records )
			{
				$session->image_count = count($session->image_records);
			}
		// get current image array key
		$session->current_image_array_key = array_search($session->current_image_file_name, array_column($session->image_records, 'image_file_name'));
		// set current image number for display
		$session->current_image_number = $session->current_image_array_key + 1;
		
		// load current data dictionary for this transcription
		load_current_data_dictionary();
	
		// try to reduce the amount of empty data flying around by selecting only fields that are required for this data set.
		// create the desired fields select statement ($df)
		$df = '';
		// add required fields ($rf) to select
		$rf = [ "BMD_identity_index", "BMD_index", "BMD_header_index", "BMD_line_sequence", "BMD_status", "line_verified", "BMD_line_panzoom_x", "BMD_line_panzoom_y", "BMD_line_panzoom_z", "BMD_line_sharpen", "BMD_line_image_rotate", "data_entry_format", "image_file_name"];
		foreach ( $rf as $f )
			{
				$df = $df.$f.', ';
			}
		// get all existing details for this header but only select desired fields
		foreach ( $session->current_transcription_def_fields as $fields_line )
			{
				foreach ($fields_line as $f) 
					{
						$df = $df.$f['table_fieldname'].', ';
					}
			}
			
		// set search
		if ( ! isset($session->search_values) ) $session->search_values = array();
						
		// set pagination
		$pagination = array();
		 
		if ( ! is_numeric($session->last_n) ) $session->last_n = 8; // set to 8 if not numeric
		if ( $session->last_n == 0 ) $session->last_n = 8; // set to 8 if 0
		
		$pagination['total_records'] = $detail_data_model	
			->where('BMD_header_index',  $session->current_transcription[0]['BMD_header_index'])
			->orHavingLike($session->search_values)
			->distinct()
			->countAllResults();

		if ( $pagination['total_records'] == 0 ) 
			{
				$session->search_message = 'No records found with your entered search term.';
				$session->last_n = 0; // if no records set to 0
				$session->search_values = array();
			}

		if ( $session->search_values ) $session->last_n = $pagination['total_records']; // if search show all found records
		
		if ( $session->last_n > $pagination['total_records'] ) $session->last_n = $pagination['total_records']; // set to number of records
		
		$pagination['records_per_page'] = $session->last_n;	
		$pagination['offset'] = $pagination['total_records'] - $session->last_n;		
		
		if ( $pagination['offset'] == 0 ) 
			{ 
				$pagination['offset'] = 0;
				$pagination['records_per_page'] = $pagination['total_records'];
			}
		else
			{
				$pagination['records_per_page'] = $session->last_n;
			}
			
		$session->pagination = $pagination;
	
		// get detail lines
		$session->transcribe_detail_data = $detail_data_model
			->select($df)
			->where('BMD_header_index',  $session->current_transcription[0]['BMD_header_index'])
			->where('data_entry_format',  $session->current_transcription[0]['current_data_entry_format'])
			->orHavingLike($session->search_values)
			->distinct()
			->orderby('BMD_line_sequence','ASC')
			->findAll($pagination['records_per_page'], $pagination['offset']);
		
		// get comments
		$session->transcribe_detail_comments = $detail_comments_model	
			->where('BMD_header_index',  $session->current_transcription[0]['BMD_header_index'])
			->orderby('BMD_line_sequence','ASC')
			->findAll();
																										
		// set defaults
		switch ($start_message) 
			{
				case 0:
					// set verify mode text
					switch ($session->current_identity[0]['verify_mode']) 
						{
							case 'after':
								$session->verify_mode_text = 'Verify in Verify Module';
								break;
							case 'onthefly':
								$session->verify_mode_text = 'Verify line-by-line.';
								break;
							default:
								$session->verify_mode_text = 'No Verify Mode specified';
								break;
						}
					// set zoom status
					if ( $session->current_transcription[0]['zoom_lock'] == 'Y' )
						{
							$session->zoom_status = 'Image zoom not allowed.';
						}
					else
						{
							$session->zoom_status = 'Image zoom allowed.';
						}
							
					// turn off verify on the fly flag so that system shows verify on the fly in transcribe.
					$session->verify_onthefly = 0;
				
					// message defaults
					$session->set('message_1', '');
					$session->set('message_class_1', '');						
					$session->set('element', $session->current_transcription[0]['BMD_scan_name']);
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					// flow control
					$session->set('show_view_type', 'transcribe');
					$session->set('confirm', 'N');
					$session->set('district_ok', 'N');
					$session->set('page_ok', 'N');
					$session->set('volume_ok', 'N');
					$session->set('registration_ok', 'N');
					$session->set('firstname_ok', 'N');
					$session->set('surname_ok', 'N');
					$session->set('same_ok', 'N');
					$session->set('line_edit_flag', 0);
					$session->set('last_detail_index', 0);
					$session->def_update_flag = 0;
					if ( $session->insert_line_flag == 0 )
						{
							$session->insert_before_line_sequence = 0;
						}
					if ( $session->line_added_flag == 0 )
						{
							$session->insert_line_sequence = 0;
						}
					// return routes depend on calling controller
					$session->set('return_route', $controller.'/transcribe_'.$controller.'_step2');
					$session->set('return_route_step1', $controller.'/transcribe_'.$controller.'_step1/0');
					// controller
					$session->set('controller', $controller);
					// set keying history table title
					$session->set('table_title', $controller_title);
					
					// get the current data entry format for this transcription
					$session->def_range = $def_ranges_model
						->where('data_entry_format', $session->current_transcription[0]['current_data_entry_format'])
						->findAll();
										
					// initialise input fields
					foreach ( $session->current_transcription_def_fields as $field_line )
						{
							foreach ( $field_line as $field )
								{
									// blank input and dup fields
									$fn = $field['html_name'];
									$dn = $field['dup_fieldname'];
									$session->$fn = '';
									$session->$dn = '';
								}
						}
					
					// if any detail data found
					if ( $session->transcribe_detail_data )
						{
							// get last record transcribed
							$detailDataCount = count($session->transcribe_detail_data);											
							$session->lastEl = $session->transcribe_detail_data[$detailDataCount - 1];
							// set standard fields
							$session->set('line', $session->lastEl['BMD_line_sequence'] + 10);
							$session->set('last_detail_index', $session->lastEl['BMD_index']);
							$session->set('zoom_lock', $session->current_transcription[0]['zoom_lock']);
							$session->set('image_y', $session->current_transcription[0]['BMD_image_y']);
							
							// set the fields
							foreach ( $session->current_transcription_def_fields as $field_line )
								{								
									foreach ( $field_line as $field )
										{
											// set dup field
											$dn = $field['dup_fieldname'];
											$session->$dn = $session->lastEl[$field['table_fieldname']];
											// set auto copy
											if ( $field['auto_copy'] == 'Y' )
												{
													$fn = $field['html_name'];
													$session->$fn = $session->lastEl[$field['table_fieldname']];
												}
											// set auto focus
											if ( $field['auto_focus'] == 'Y' )
												{
													$session->position_cursor = $field['html_name'];
												}
										}
								}							
							
							// set image panzoom parameters for next line to be keyed
							// default to header values because they are updated when a line is added to DB but adjust y by scroll step to present next line in image
							$session->panzoom_x = $session->current_transcription[0]['BMD_panzoom_x'];
							$session->panzoom_y = $session->current_transcription[0]['BMD_panzoom_y'] - $session->current_transcription[0]['BMD_image_scroll_step'];
							$session->panzoom_z = $session->current_transcription[0]['BMD_panzoom_z'];
						}
					else
						{
							// no detail data - initalise lastEl
							$session->lastEl = array();
							// no detail data for this transcription, so set default values
							$session->saved_line_sequence = 0;
							$session->line = 10;
							$session->surname = '';
							// set image and panzoom parameters from transcription header
							// when in INPRO and new transcription because lastEl is empty x and y values are recalculated in the transcribe_panzoom.php based on the panzoom div size.
							$session->panzoom_x = $session->current_transcription[0]['BMD_panzoom_x'];
							$session->panzoom_y = $session->current_transcription[0]['BMD_panzoom_y'];							
							$session->panzoom_z = $session->current_transcription[0]['BMD_panzoom_z'];
							$session->sharpen = $session->current_transcription[0]['BMD_sharpen'];
							$session->scroll_step = $session->current_transcription[0]['BMD_image_scroll_step'];
							$session->image_y = $session->current_transcription[0]['BMD_image_y'];
							$session->zoom_lock = $session->current_transcription[0]['zoom_lock'];
						}	

					// reset position cursor
					// by default to first data entry field
					$session->set('position_cursor', $session->current_transcription_def_fields[1][0]['html_name']);
					// then depends on project
					switch ( $session->current_project['project_index'] )
						{
							case 1:
								if ( empty($session->surname) )
									{
										$session->set('position_cursor', 'surname');
									}
								break;
							case 2:
								break;
							case 3:
								break;
						}								
					
					// initialise error_field
					$session->set('error_field', '');
					$session->set('error_data_group', '');
					
					// get any header comments.
					$session->comment_text = '';
					$session->source_text = '';
					$session->comment_text_array =	$transcription_comments_model
						->where('project_index', $session->current_project['project_index'])
						->where('identity_index', $session->BMD_identity_index)
						->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
						->where('comment_sequence', 10)
						->find();
					// any found ?
					if ( $session->comment_text_array )
						{
							$session->comment_text = $session->comment_text_array[0]['comment_text'];
							$session->source_text = $session->comment_text_array[0]['source_text'];
						}
						
					// Check for Suggestion Comments
					$suggestion_comments = $detail_comments_model
						->where('project_index', $session->current_project['project_index'])
						->where('BMD_header_index', $session->current_transcription[0]['BMD_header_index'])
						->where('BMD_comment_type', 'S')
						->findAll();
					if ( $suggestion_comments )
						{
							$session->message_2 = 'Your Coordinator has left you suggestions. Look for "S" against a detail line. You need to clear all suggestions before you can upload.';
							$session->message_class_2 = 'alert alert-warning';
						}
						
					// get data groups for this data set and create array for view
					$session->data_group_titles_view = array();
					$session->data_group_titles = $data_group_model
						->where('project_index', $session->current_project['project_index'])
						->where('data_set', $session->current_transcription[0]['current_data_entry_format'])
						->findAll();
					if ( $session->data_group_titles )
						{
							foreach ( $session->data_group_titles as $data_group_title )
								{
									$data_group_titles_view[$data_group_title['data_group_number']] = $data_group_title['data_group_title'];
								}
							$session->data_group_titles_view = $data_group_titles_view;
						}
						
					// create $current_used_transcription_def_fields for show view
					create_current_used_transcription_def_fields($session->current_transcription[0]['current_data_entry_format']);
					
					// count the number of detail records for each data type if FreeREG
					if ( $session->current_project['project_index'] == 2 )
						{
							if ( $session->event_types )
								{
									$counts = array();
									foreach ( $session->event_types as $event_type )
										{
											$counts[$event_type['type_name_lower']] = $detail_data_model
												->where('BMD_header_index', $session->current_transcription[0]['BMD_header_index'])
												->where('data_entry_format', $event_type['type_name_lower'])
												->countAllResults();
										}
									$session->counts = $counts;									
								}
						}
						
					// load licences
					$results = $licence_model
						->orderby('Licence_popularity', 'DESC')
						->findAll();
					foreach ( $results as $result )
						{
							$licences[] = $result['Licence'];
						}
					$session->licences = $licences;
					
					// load person status
					$results = $person_status_model
						->orderby('Person_status_popularity', 'DESC')
						->findAll();
					foreach ( $results as $result )
						{
							$person_statuses[] = $result['Person_status'];
						}
					$session->person_statuses = $person_statuses;
					
					// load condition
					$results = $condition_model
						->orderby('Condition_popularity', 'DESC')
						->findAll();
					// load groom conditions
					foreach ( $results as $result )
						{
							$list = ['m', 'b'];
							if ( in_array($result['condition_sex'], $list) )
								{
									$conditions[] = $result['Condition'];
								}
						}
					$session->conditions_m = $conditions;
					// load bride conditions
					$conditions = array();
					foreach ( $results as $result )
						{
							$list = ['f', 'b'];
							if ( in_array($result['condition_sex'], $list) )
								{
									$conditions[] = $result['Condition'];
								}
						}
					$session->conditions_f = $conditions;
					// load all
					$conditions = array();
					foreach ( $results as $result )
						{
							$conditions[] = $result['Condition'];
						}
					$session->conditions_all = $conditions;
					
					// load title
					$results = $title_model
						->orderby('Title', 'ASC')
						->findAll();
					foreach ( $results as $result )
						{
							$titles[] = $result['Title'];
						}
					$session->titles = $titles;
					
					// load marked male
					$marked_m['NO'] = 'N0';
					$marked_m['YES'] = 'YES';
					$session->marked_m = $marked_m;
					
					// load marked female
					$marked_f['NO'] = 'N0';
					$marked_f['YES'] = 'YES';
					$session->marked_f = $marked_f;
					
					// load sex
					$sex['Male'] = 'Male';
					$sex['Female'] = 'Female';
					$session->sex = $sex;
					
					// load predefined data entry layouts - these are identified by an identity index = 999999
					$predefined_data_entry_layouts = $user_data_entry_layouts_model
						->where('project_index', $session->current_project['project_index'])
						->where('identity_index', 999999)
						->where('event_type',  $session->current_transcription[0]['current_data_entry_format'])
						->orderby('layout_name')
						->findAll();
					$layout_dropdown = array();
					foreach ( $predefined_data_entry_layouts as $layout )
						{
							$layout_dropdown[$layout['layout_index']] = $layout['layout_name'];
						}
					$session->predefined_layout_dropdown = $layout_dropdown;
					
					// load user data entry layouts
					$user_data_entry_layouts = $user_data_entry_layouts_model
						->where('project_index', $session->current_project['project_index'])
						->where('identity_index', $session->BMD_identity_index)
						->where('event_type',  $session->current_transcription[0]['current_data_entry_format'])
						->findAll();
					$layout_dropdown = array();
					foreach ( $user_data_entry_layouts as $layout )
						{
							$layout_dropdown[$layout['layout_index']] = $layout['layout_name'];
						}
					$session->layout_dropdown = $layout_dropdown;
					
					// set current layout this transcription, this event type
					$session->current_layout = 0;
					$layout_set = $transcription_current_layout_model
						->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
						->where('event_type', $session->current_transcription[0]['current_data_entry_format'])
						->find();
					if ( $layout_set )
						{
							$session->current_layout = $layout_set[0]['current_layout_index'];
						}										
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', ucfirst($session->current_transcription[0]['current_data_entry_format']).' => '.$session->current_transcription[0]['BMD_file_name'].' => '.$session->current_transcription[0]['BMD_scan_name'].' => Approximately '.$session->current_transcription[0]['BMD_records'].' records transcribed from this scan. Enter your transcription data from scan image.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
					break;
			}
	}
	
	function transcribe_show_step1($controller)
	{	
		// initialise
		$session = session();				
		
		// show header																
		echo view('templates/header');
				
		// show button depending on verify
		if ( $session->verify_onthefly == 1 )
			{
				$button_view = 'linBMD2/transcribe_buttons_verify';
			}
		else
			{	
				$button_view = 'linBMD2/transcribe_buttons';
			}
			
		// determine the very last data entry field ID that will be checked and shown so that I can detect tab out in javascript
		foreach ( $session->current_transcription_def_fields as $field_line )
			{
				foreach ( $field_line as $field )
					{
						if ( $field['data_entry_format'] == $session->current_transcription[0]['current_data_entry_format'] AND $field['field_check'] == 'Y' AND $field['field_show'] == 'Y')
							{
								$session->last_id = $field['html_id'];
							}
					}
			}
			
		// calulate the number of collapsing data entry columns required.
		// here's how,
		// 1) Bootstrap defines 12 columns in any element such as screen width.
		// 2) data sets are defined in the def_fields table by the data_entry_format eg. marriages. One data set is processed at a time.
		// 3) within the data set, data groups are defined in the def_fields table by field_line
		// 4) fields are defined by order within each data group within each data set.
		// 5) within a data set there can only be 1, 2, 3, 4, 6 or 12 data groups.
		// 6) the number of data groups = number of collapsible data entry columns
		// 7) the bootstrap width of the collapsible columns = 12 / number of data groups
		// 8) eg say there are 4 data groups in the data set. 12 bootstrap cols / 4 = 3 cols each collapsible data group column.
		// count number of elements = number of data groups,
		// now that all fields can be accessed from the transcription field parameters, current_transcription_def_fields will have all fields,
		// so, I need to select groups that have "shown" fields and are in the current_data_entry_format
		$session->data_groups = 0;
		foreach ( $session->current_transcription_def_fields as $field_line )
			{
				foreach ( $field_line as $field )
					{
						if ( $field['data_entry_format'] == $session->current_transcription[0]['current_data_entry_format'] AND $field['field_check'] == 'Y' AND $field['field_show'] == 'Y')
							{
								$session->data_groups = $session->data_groups + 1;
								break;
							}
					}
			}

		$test_array = [1, 2, 3, 4, 6, 12];
		$valid = in_array($session->data_groups, $test_array);

		if (! $valid )
			{
				// we have a problem because data group count is not valid. Force 1.
				// when number of data groups is one all fields are shown in one row. There are no collapsible data entry cols in this case.
				// so far all FreeBMD data sets only have one data group, while FreeREG have multiple
				$session->data_groups = 1;
			}
		// calulate bootstrap number of cols for collapsible data entry column
		$session->bootstrap_cols = 12 / $session->data_groups;
				
		// show views depending on view type
		switch ($session->show_view_type) 
			{
				// normal transcription - details enter is build up from multiple small views that are selected depending on project
				// the order of views matters here!
				case 'transcribe':
					switch ($session->current_project['project_index'])
						{
							case 1:
								echo view('linBMD2/transcribe_details_enter_form');
								echo view('linBMD2/transcribe_details_enter_comment');
								echo view('linBMD2/transcribe_details_enter_last');
								echo view('linBMD2/transcribe_details_enter_image');
								echo view('linBMD2/transcribe_details_enter');
								echo view($button_view);
								echo view('linBMD2/transcribe_details_show');
								echo view('linBMD2/transcribe_panzoom');
								echo view('linBMD2/transcribe_script');
								echo view('linBMD2/searchTableNew');
								echo view('linBMD2/sortTableNew');
								break;
							case 2:
								echo view('linBMD2/transcribe_details_enter_title');
								echo view('linBMD2/transcribe_details_enter_form');
								if ( $session->image_source[0]['source_images'] == 'yes' )
									{
										echo view('linBMD2/transcribe_details_enter_image');
										echo view('linBMD2/transcribe_panzoom');
									}
								echo view('linBMD2/FreeREG_transcribe_details_enter_new');
								echo view($button_view);
								if ( count($session->transcribe_detail_data) > 0 )
									{
										echo view('linBMD2/FreeREG_transcribe_details_show');
									}
								echo view('linBMD2/transcribe_script');
								echo view('linBMD2/searchTableNew');
								echo view('linBMD2/sortTableNew');
								echo view('linBMD2/transcribe_dragable');
								break;
							case 3:
								break;
						}
					break;
				// confirm page if not standard
				case 'confirm_page':
					echo view('linBMD2/transcribe_page_confirmation');
					echo view('linBMD2/transcribe_panzoom');
					break;
				// confirm district if not standard
				case 'confirm_district':
					echo view('linBMD2/transcribe_district_confirmation');
					echo view('linBMD2/transcribe_panzoom');
					break;
				// confirm volume if not standard
				case 'confirm_volume':
					echo view('linBMD2/transcribe_volume_confirmation');
					echo view('linBMD2/transcribe_panzoom');
					break;
				// confirm registraion if year not standard
				case 'confirm_registration':
					echo view('linBMD2/transcribe_registration_confirmation');
					echo view('linBMD2/transcribe_panzoom');
					break;
				// confirm forenames correct
				case 'confirm_firstnames':
					echo view('linBMD2/transcribe_firstname_confirmation');
					echo view('linBMD2/transcribe_panzoom');
					break;
				// confirm surname correct
				case 'confirm_surname':
					echo view('linBMD2/transcribe_surname_confirmation');
					echo view('linBMD2/transcribe_panzoom');
					break;
				// confirm surname correct
				case 'confirm_same':
					echo view('linBMD2/transcribe_same_confirmation');
					echo view('linBMD2/transcribe_panzoom');
					break;
			}
		
		// show footer
		echo view('templates/footer');
	}
	
	function transcribe_get_transcribe_inputs($controller)
	{
		// initialise method
		$session = session();
		$transcription_detail_def_model = new Transcription_Detail_Def_Model();
		
		if ( $session->district_added == 1 )
			{
				$_POST = $session->save_post_data;
				$session->district_added = 0;
			}
		$session->save_post_data = $_POST;

		// get data entry and validate
		foreach ( $session->current_transcription_def_fields as $fields_line )
			{
				foreach ($fields_line as $field) 
					{
						// get checked fields
						if ( $field['data_entry_format'] == $session->current_transcription[0]['current_data_entry_format'] AND $field['field_check'] == 'Y' AND $field['field_show'] == 'Y' )
							{
								// set html field name
								$fn = $field['html_name'];
			
								// set data field
								$session->$fn = rtrim($_POST[$fn]);
							}
					}
			}
			
		// get panzoom data elements 
		if ( $session->image_source[0]['source_images'] == 'yes' )
			{
				$session->set('panzoom_x', $_POST['panzoom_x']);
				$session->set('panzoom_y', $_POST['panzoom_y']);
				$session->set('panzoom_z', $_POST['panzoom_z']);
				$session->set('sharpen', $_POST['sharpen']);
		
				// get new image height
				$session->set('new_image_y', $_POST['newHeight']);
				// calculate actual scroll step ratio
				$scroll_step_ratio = $session->new_image_y / $session->current_transcription[0]['BMD_image_y'];
				// calculate new scroll step
				$session->scroll_step = $session->current_transcription[0]['BMD_image_scroll_step'] * $scroll_step_ratio;
			}
			
		// get comment text input if set - it might have been made blank by the user but this is considered as set.
		if ( isset($_POST['comment_text']) )
			{
				// set it to entered value
				$session->set('comment_text', $_POST['comment_text']);
			}
		
		// get defFields input
		$session->set('defFields', json_decode($_POST['defFields']));
		
		// update detail defs with column_width in case user used resize or field widths have been recalculated
		if ( $session->defFields )
			{
				// update def fields
				foreach ( $session->defFields as $fields_line  )
					{
						foreach ($fields_line as $field) 
							{
								$transcription_detail_def_model
									->set(['column_width' => $field->column_width])
									->set(['column_height' => $field->column_height])
									->update($field->field_index);
							}
					}
				
				// set update flag
				$session->def_update_flag = 1;		
			}
			
		// load current data dictionary
		load_current_data_dictionary();	
	}
				
	function transcribe_get_confirm_district_inputs($controller)
	{
		// initialise method
		$session = session();	
		// get inputs
		$session->set('synonym_ok', $_POST['confirm_synonym']);
		$session->set('synonym', $_POST['synonym']);
		$session->set('district_ok', $_POST['confirm']);
	}
	
	function transcribe_get_confirm_page_inputs($controller)
	{			
		// initialise method
		$session = session();	
		// get inputs
		$session->set('page_ok', $_POST['confirm']);
	}
	
	function transcribe_get_confirm_volume_inputs($controller)
	{
		// initialise method
		$session = session();	
		// get inputs
		$session->set('volume', $_POST['volume']);
		$session->set('volume_ok', $_POST['confirm']);
	}
	
	function transcribe_get_confirm_registration_inputs($controller)
	{			
		// initialise method
		$session = session();	
		// get inputs
		$session->set('registration_ok', $_POST['confirm']);
	}
	
	function transcribe_get_confirm_firstname_inputs($controller)
	{			
		// initialise method
		$session = session();	
		// get inputs
		$session->set('firstname_ok', $_POST['confirm']);
	}
	
	function transcribe_get_confirm_surname_inputs($controller)
	{			
		// initialise method
		$session = session();	
		// get inputs
		$session->set('surname_ok', $_POST['confirm']);
	}
	
	function transcribe_get_confirm_same($controller)
	{			
		// initialise method
		$session = session();	
		// get inputs
		$session->set('same_ok', $_POST['confirm']);
	}
	
	function transcribe_validate_transcribe_inputs($controller)
	{
		// initialise method
		$session = session();
		$current_values = array();
		$last_values = array();
				
		// turn off verify on the fly flag so that system shows verify on the fly in transcribe.
		$session->verify_onthefly = 0;
		
		// test length of comment_text for FreeBMD
		if ( $session->current_project['project_index'] == 1 )
			{
				if ( strlen($session->comment_text) > 100 )
					{
						$session->set('position_cursor', 'comment_text');
						$session->set('error_field', 'comment_text');
						$session->set('message_2', 'Please limit your comment text to 100 characters max.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('message_error', 'error');
						return;
					}
			}
		
		// get data entry format fields
		foreach ( $session->current_transcription_def_fields as $fields_line )
			{
				foreach ($fields_line as $field) 
					{
						if ( $field['data_entry_format'] == $session->current_transcription[0]['current_data_entry_format'] )
							{
								// select checked fields
								if ( $field["field_check"] == 'Y' )
									{
										// initialise for each field
										$session->set('message_error', '');
										$session->set('message_2', '');
										$session->set('message_class_2', '');
										$session->set('position_cursor', '');
										$session->set('error_field', '');
										$session->set('error_data_group', '');
						
										// get html field name and remove trailing spaces
										$fn = $field['html_name'];
										$session->fieldname = $fn;
										$session->fieldline = $field['field_line'];
										$session->$fn = trim($session->$fn);
										$str_length = strlen($session->$fn);
					
										// if user has requested no checks there should be a # at the end position of the input field
										// get last character of input field and test for checks flag. Set checks and strip #
										// checks = 1 = do the checks = default
										$session->checks = 1;
										if ( substr($session->$fn, -1) == '#' ) 
											{
												// set checks flag
												$session->checks = 0;
												// remove no checks flag at end of data
												$session->$fn = substr($session->$fn, 0, -1);
											}
			
											// if user has requested no capitalise there should be a @ at the end position of the input field
											// get last character of input field and test for capitalise flag. Set capitalise and strip @
											// capitalise = 1 = do the capitalisation = default
											$session->capitalise = 1;
											if ( substr($session->$fn, -1) == '@' ) 
												{
													// set capitalise flag
													$field['capitalise'] = 'none';
													// remove no capitalise flag at end of data
													$session->$fn = substr($session->$fn, 0, -1);
												}

										// apply capitalisation
										if ( isset($session->$fn) )
											{
												switch ($field['capitalise'])
													{
														case 'UPPER':
															$session->$fn = strtoupper($session->$fn);
															break;
														case 'lower':
															$session->$fn = strtolower($session->$fn);
															break;
														case 'First':
															$field_exploded = explode(' ', $session->$fn);
															$session->$fn = '';
															// test for multiple names ie, John Frank Kenneth or John I. N.
															foreach ( $field_exploded as $ef )
																{
																	$ef = strtolower($ef);
																	$ef = ucfirst($ef);
																	// now explode on . to see if I have initials like I.N
																	$in = explode('.', $ef);
																	if ( $in[0] != $ef )
																		{
																			$ef = '';
																			foreach ( $in as $ni )
																				{
																					if ( $ni != '' )
																						{
																							$ni = ucfirst($ni);
																							$ef = $ef .=$ni.'.';
																						}
																				}
																		}
																	else
																		{
																			$ef = ucfirst($ef);
																		}
																	$session->$fn .= $ef.' ';
																}
															$session->$fn = trim($session->$fn);
																							
															// if district
															if ( $field['html_name'] == 'district' )
																{
																	// are there spaces? if so it will have been capitalised correctly so don't do anything here
																	$district_spaces = explode(' ', $session->$fn);
																	// if count is 1 there are no spaces
																	if ( count($district_spaces) == 1 )
																		{
																			// so, apply special capitalation if field as per N.york the above will not capitalise y - so fix it
																			$field_exploded = explode('.', $session->$fn);
																			if ( count($field_exploded) > 1 )
																				{
																					$session->$fn = '';
																					foreach ( $field_exploded as $ef )
																						{
																							$ef = strtolower($ef);
																							$ef = ucfirst($ef);
																							$session->$fn .= $ef.'.';
																						}
																					$session->$fn = trim($session->$fn, '.');
																				}
																		}
																}
															break;
														case 'none':
															break;
														default:
															break;
													}
											}							
				
										// whatever happens check for blank if blank not allowed
										// cannot use "empty" here because PHP treats a 0 zero in a field as empty
										if ( strlen($session->$fn) == 0 AND $field['blank_OK'] == 'N' )
											{
												$session->set('position_cursor', $session->fieldname);
												$session->set('error_field', $session->fieldname);
												$session->set('error_data_group', 'group_'.$session->fieldline);
												$session->set('message_2', $field['column_name'].' cannot be blank. Enter ? to force blank.');
												$session->set('message_class_2', 'alert alert-danger');
												$session->set('message_error', 'error');
												return;
											}
			
										// if not blank and blank allowed and it should be blank 
										if ( $session->checks == 1 AND ! empty($session->$fn) AND $field['special_test'] == 'should_be_blank' )
											{
												$session->set('position_cursor', $session->fieldname);
												$session->set('error_field', $session->fieldname);
												$session->set('error_data_group', 'group_'.$session->fieldline);
												$session->set('message_2', $field['column_name'].' should be blank but it is not. Enter # at end of data to bypass check.');
												$session->set('message_class_2', 'alert alert-danger');
												$session->set('message_error', 'error');
												return;
											}
				
										// UCF tests
										if ( $session->checks == 1 ) 
											{
												$session->set('position_cursor', '');
												$session->set('error_field', '');
												$session->set('error_data_group', '');
												$session->set('message_error', '');
												if ( ! empty($session->$fn) ) { validation_tests_UCF_pass($session->$fn, $field); }
												if ( $session->message_error != '' ) { return; }
											}

										// second pass tests
										if ( $session->checks == 1 ) 
											{
												$session->set('position_cursor', '');
												$session->set('error_field', '');
												$session->set('error_data_group', '');
												$session->set('message_error', '');
												if ( ! empty($session->$fn) ) { validation_tests_second_pass($session->$fn, $field); }
												if ( $session->message_error != '' ) { return; }
											}
					
										// store this value for later full line test			
										$current_values[$field['table_fieldname']] = $session->$fn;
										if ( $session->lastEl )
											{
												$last_values[$field['table_fieldname']] = $session->lastEl[$field['table_fieldname']];
											}
										else
											{
												$last_values[$field['table_fieldname']] = '';
											}
									}
							}		
					}
			
				// check current data line same as last
				$current_last_same = array_diff($current_values, $last_values);
				if ( count($current_last_same) == 0 AND $session->same_ok == 'N')
					{
						// current line and last line are the same, so ask for confirmation
						$session->current_values = $current_values;
						$session->last_values = $last_values;
						$session->set('show_view_type', 'confirm_same');
						$session->set('message_2', 'This line appears to be exactly the same as the last line you entered. Is this deliberate?');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('message_error', 'error');
						return;
					}
			}
	}
	
	function transcribe_validate_confirm_district_inputs($controller)
	{
		// initialise method
		$session = session();	
		$session->set('message_error', '');
		$session->set('show_view_type', 'transcribe');
		
		$districts_model = new Districts_Model();
		$volumes_model = new Volumes_Model();
		// has user confirmed both synonym and district?
		if ( $session->synonym_ok == 'Y' AND $session->district_ok == 'Y' )
		{
			$session->set('show_view_type', 'confirm_district');
			$session->set('message_2', 'You cannot confirm both synonym and district.');
			$session->set('message_class_2', 'alert alert-danger');
			$session->set('message_error', 'error');
			return;
		}
		// did user confirm synonym
		if ( $session->synonym_ok == 'Y' )
			{
				// is synonym a valid district?
				$session->set('transcribe_synonym', $districts_model->where('District_name', $session->synonym)->findAll());
				$synonym_volumes = $volumes_model
									->where('district_index', $session->transcribe_synonym[0]['district_index'])
									->where('BMD_type', $session->current_allocation[0]['BMD_type'])
									->findAll();
				if ( ! $session->transcribe_synonym OR ! $synonym_volumes )
					{
						$session->set('show_view_type', 'confirm_district');
						$session->set('message_2', 'You must enter a valid district for the synonym OR no volume data was found for the synonym.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('message_error', 'error');
						return;
					}
				// a valid synonym was confirmed by user
				// add district to table
				$data =	[
									'District_name' => strtoupper($session->district),
									'Added_by_user' => $session->identity_userid,
									'active'		=> 'YES',
								];
				$id = $districts_model->insert($data);
				// now get all volume records regardless of BMD_type
				$synonym_volumes = $volumes_model
									->where('district_index', $session->transcribe_synonym[0]['district_index'])
									->findAll(); 
				// read all volume info for synonym and create volume records for the new district
				foreach ( $synonym_volumes as $synonym )
					{
						$data =	[
											'district_index' => $id,
											'volume_from' => $synonym['volume_from'],
											'volume_to' => $synonym['volume_to'],
											'volume' => $synonym['volume'],
											'BMD_type' => $synonym['BMD_type'],
										];
						$volumes_model->insert($data);
					}
			}
		else
			{
				// if synonym not confirmed, did user confirm district?							
				if ( $session->district_ok == 'N' )
					{
						$session->set('position_cursor', $session->fieldname);
						$session->set('message_2', 'You did not confirm this district => '.$session->district.'. Please correct it.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('message_error', 'error');
						return;
					}
				else
					{
						// user confirmed district so add it to districts file
						$data =	[
									'District_name' => strtoupper($session->district),
									'Added_by_user' => $session->identity_userid,
									'active'		=> 'YES',
								];
						$districts_model->insert($data);
					}
			}
	}
				
	function transcribe_validate_confirm_page_inputs($controller)
	{			
		// initialise method
		$session = session();
		$session->set('message_error', '');
		$session->set('show_view_type', 'transcribe');
		
		// test confirm
		if ( $session->page_ok == 'N' )
			{
				$session->set('message_2', 'You did not confirm this page number => '.$session->page.'. Please correct it.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
	}
	
	function transcribe_validate_confirm_volume_inputs($controller)
	{
		// initialise method
		$session = session();
		$session->set('message_error', '');
		$session->set('show_view_type', 'transcribe');
		
		// did user confirm?
		if ( $session->volume_ok == 'N' )
			{
				$session->set('message_2', 'You did not confirm this the volume => '.$session->volume.'. Please correct it or confirm the district.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}	
	}
	
	function transcribe_validate_confirm_registration_inputs($controller)
	{			
		// initialise method
		$session = session();
		$session->set('message_error', '');
		$session->set('show_view_type', 'transcribe');
		
		// test confirm
		if ( $session->registration_ok == 'N' )
			{
				$session->set('message_2', 'You did not confirm this registration number => '.$session->registration.'. Please correct it.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
	}
	
	function transcribe_validate_confirm_firstname_inputs($controller)
	{			
		// initialise method
		$session = session();
		$session->set('message_error', '');
		$session->set('show_view_type', 'transcribe');
			
		// test confirm
		if ( $session->firstname_ok == 'N' )
			{
				$session->set('message_2', 'You did not confirm blank forenames. Please correct them.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
	}
	
	function transcribe_validate_confirm_surname_inputs($controller)
	{			
		// initialise method
		$session = session();
		$session->set('message_error', '');
		$session->set('show_view_type', 'transcribe');
			
		// test confirm
		if ( $session->surname_ok == 'N' )
			{
				$session->set('message_2', 'You did not confirm surname out of sequence.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
	}
	
	function transcribe_validate_confirm_same($controller)
	{			
		// initialise method
		$session = session();
		$session->set('message_error', '');
		$session->set('show_view_type', 'transcribe');
			
		// test confirm
		if ( $session->same_ok == 'N' )
			{
				$session->set('message_2', 'You did not confirm duplicate line.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
	}
	
	function transcribe_update($controller)
	{
		// initialise method
		$session = session();	
		$transcription_model = new Transcription_Model();
		$detail_data_model = new Detail_Data_Model();
		$transcription_comments_model = new Transcription_Comments_Model();
		$transcription_detail_def = new Transcription_Detail_Def_Model();
		define_environment(3);
		$mongodb = define_mongodb();
		
		// trap blank lines - this has been added to try to trap the double click phenomen where the user double clicks the submit button
		$trap_blank_line = 1;
		foreach ( $session->current_transcription_def_fields as $field_line )
			{
				foreach ( $field_line as $field )
					{
								// get html field name
								$fn = $field['html_name'];
								
								// test data field
								if ( $session->$fn != '' )
									{
										// if any of the fields are blank trap line
										$trap_blank_line = 0;
									}
							
					}
			}
			
		// trap same sequence as last line added
			
		// if no blanks found continue
		if ( $trap_blank_line == 0 )
			{		
				// are we inserting a line or adding a line to end or updating existing line?
				// if line edit flag == 1, we are modifying a line
				if ( $session->insert_line_flag == 1 )
					{
						// we are inserting a line
						$line_sequence = $session->insert_line_sequence;
						$session->line_added_flag = 1;
						$session->modify_line_sequence = 0;
					}
				else
					{
						// we are adding a line
						if ( $session->line_edit_flag == 0 )
							{
								// we are still adding a line
								$line_sequence = $session->line;
								$session->line_added_flag = 0;
								$session->modify_line_sequence = 0;
							}
						else
							{
								// we are modifying an existing line
								$line_sequence = $session->modify_line_sequence;
								$session->line_added_flag = 0;
							}
					}
			
				// initialise database fields
				// set standard fields for add/update of detail data
				switch ($session->current_project['project_index'])
					{
						case 1:
							$chapman_code = NULL;
							$place_name = NULL;
							$church_name = NULL;
							$register_type = NULL;
							$image_file_name = NULL;
							break;
						case 2:
							$chapman_code = $session->current_allocation[0]['REG_chapman_code'];
							$place_name = $session->current_allocation[0]['REG_place'];
							$church_name = $session->current_allocation[0]['REG_church_name'];
							$register_type = $session->current_allocation[0]['REG_register_type'];
							$image_file_name = $session->current_image_file_name;
							break;
						case 3:
							$chapman_code = NULL;
							$place_name = NULL;
							$church_name = NULL;
							$register_type = NULL;
							$image_file_name = NULL;
							break;
					}
			
				// load data array
				$data =	[
							'project_index' => $session->current_project['project_index'],
							'BMD_identity_index' => $session->BMD_identity_index,
							'BMD_header_index' => $session->current_transcription[0]['BMD_header_index'],
							'data_entry_format' => $session->current_transcription[0]['current_data_entry_format'],
							'BMD_line_sequence' => $line_sequence,
							'line_verified' => 'NO',
							'BMD_status' => '0',
							'detail_x' => $session->actual_x,
							'detail_y' => $session->actual_y,
							'chapman_code' => $chapman_code,
							'place_name' => $place_name,
							'church_name' => $church_name,
							'register_type' => $register_type,
							'image_file_name' => $image_file_name,
						];
		
				// set verified if verify on the fly
				if ( $session->verify_onthefly == 1 )
					{
						$data['line_verified'] = 'YES';
					}
		
				// get data entry format and add to data array by table field name
				foreach ( $session->current_transcription_def_fields as $field_line )
					{
						foreach ( $field_line as $field )
							{
								// get html field name
								$fn = $field['html_name'];
									
								// check for auto full stop and there isn't one already
								if ( $field['auto_full_stop'] == 'Y' AND substr($session->$fn, -1) != '.')
									{
										// add full stop to field value and store in data array
										$data[$field['table_fieldname']] = $session->$fn.'.';
									}
								else
									{
										// store in data array
										$data[$field['table_fieldname']] = $session->$fn;
									}
						
								// add names etc to tables; update_surnames and update_firstnames are functions in the update_names_helper
								switch ($field['field_type'])
									{
										case 'sur_name':
											if ( $session->$fn != null )
												{
													update_surnames($session->$fn);
												}
											break;
										case 'fore_name':
											if ( $session->$fn != null )
												{
													$forenames = explode(' ', $session->$fn);
													foreach ($forenames as $forename)
														{
															if ( $forename != null )
																{
																	update_firstnames($forename);
																}
														}
												}
											break;
										case 'occupation':
											if ( $session->$fn != null )
												{
													update_occupations($session->$fn);
												}
											break;
										case 'parish':
											if ( $session->$fn != null )
												{
													update_parishes($session->$fn);
												}
											break;
									}
							}
					}
				
				// add to DB if line edit = 0 / update DB if line edit = 1
				if ( $session->line_edit_flag == 0 )
					{
						// insert record but only if saved line sequence is different to current line sequence
						if ( $session->saved_line_sequence != $line_sequence)
							{
								// set panzoom parameters
								if ( $session->image_source[0]['source_images'] == 'yes' )
									{
										$data['BMD_line_panzoom_x'] = $session->panzoom_x;
										$data['BMD_line_panzoom_y'] = $session->panzoom_y;
										$data['BMD_line_panzoom_z'] = $session->panzoom_z;
										$data['BMD_line_sharpen'] = $session->sharpen;
										$data['BMD_line_image_rotate'] = $session->rotation;
									}
								else
									{
										$data['BMD_line_panzoom_x'] = 0;
										$data['BMD_line_panzoom_y'] = 0;
										$data['BMD_line_panzoom_z'] = 0;
										$data['BMD_line_sharpen'] = 0;
										$data['BMD_line_image_rotate'] = 0;
									} 
								// insert line
								$session->last_detail_index = $detail_data_model->insert($data);
								
								// resequence again in case line inserted at beginning of data
								$new_sequence = 0;	
								// get detail lines in sequence order
								$all_detail_lines = $detail_data_model
									->where('project_index', $session->current_project['project_index'])
									->where('BMD_header_index',  $session->current_transcription[0]['BMD_header_index'])			
									->orderby('BMD_line_sequence','ASC')
									->findAll();						
								// loop through all detail lines incrementing sequence by 10 each time and update, leave all other data same
								foreach ( $all_detail_lines as $dd )
									{
										$new_sequence = $new_sequence + 10;
										$detail_data_model
											->set(['BMD_line_sequence' => $new_sequence])
											->update($dd['BMD_index']);
									}	
								// load $session->transcribe_detail_data again
								$session->transcribe_detail_data = 	$detail_data_model
									->where('project_index', $session->current_project['project_index'])
									->where('BMD_header_index',  $session->current_transcription[0]['BMD_header_index'])			
									->orderby('BMD_line_sequence','ASC')
									->findAll();
								// set lines to show if a line was inserted
								if ( $session->insert_line_flag == 1 )
									{
										// count records
										$session->last_n = count($session->transcribe_detail_data);
									}
										
								// turn off insert line flag
								$session->insert_line_flag = 0;

								// find last inserted record by index
								$array_key = array_search($session->last_detail_index, array_column($session->transcribe_detail_data, 'BMD_index'));
								// and save its line sequence
								$session->insert_line_sequence = $session->transcribe_detail_data[$array_key]['BMD_line_sequence'];
						
								// update record count on header and image parameters
								// if anything has been added or changed initialise verify flags.
								$data =	[
											'BMD_records' => $session->current_transcription[0]['BMD_records'] + 1,
											'BMD_panzoom_x' => $session->panzoom_x,
											'BMD_panzoom_y' => $session->panzoom_y,
											'BMD_panzoom_z' => $session->panzoom_z,
											'BMD_sharpen' => $session->sharpen,
											'BMD_image_scroll_step' => $session->scroll_step,
											'BMD_image_rotate' => $session->rotation,
											'verified' => 'NO',
											'last_verified_detail_index' => 0,
											'BMD_image_y' => $session->new_image_y,
										];
								$transcription_model->update($session->current_transcription[0]['BMD_header_index'], $data);
								
								// create reporting data
								$last_detail_line_report = $detail_data_model
									->where('BMD_index', $session->last_detail_index)
									->find();
								$detail_line = $last_detail_line_report[0];
								load_report_data($detail_line, 'add');
							}
					}
				else
					{					
						// update detail record
						$detail_data_model->update($session->current_line['BMD_index'], $data);
						
						// restore image parameters
						$session->set('panzoom_x', $session->save_panzoom_x);
						$session->set('panzoom_y', $session->save_panzoom_y);
						$session->set('panzoom_z', $session->save_panzoom_z);
						$session->set('sharpen', $session->save_sharpen);
						$session->set('rotation', $session->save_rotation);
					}
				
				// load the header again
				$session->current_transcription = $transcription_model	
					->where('BMD_header_index',  $session->current_transcription[0]['BMD_header_index'])
					->find();
																		
				// delete sequence 10 for any transcription comments
				$transcription_comments_model
					->where('project_index', $session->current_project['project_index'])
					->where('identity_index', $session->BMD_identity_index)
					->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
					->where('comment_sequence', 10)
					->delete();
				// now add it again
				$data =	[
							'transcription_index' => $session->current_transcription[0]['BMD_header_index'],
							'project_index' => $session->current_project['project_index'],
							'identity_index' => $session->BMD_identity_index,
							'comment_sequence' => 10,
							'comment_text' => $session->comment_text,
							'source_text' => $session->source_text,
						];
				$transcription_comments_model->insert($data);
				
				// issue 143 stiplates that the file lock parameters, locked_by_transcriber and locked_by_coordinator, in the freereg1_csv_files collection in FreeREG backend should be set to true when updating an imported file
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
								['$set' => ['locked_by_transcriber' => true, 'locked_by_coordinator' => true]]
							);
						$modified_documents = $result->getModifiedCount();
					}	
				
				// initialise page load
				$session->search_values = array();
				$session->set('show_view_type', 'transcribe');	
			}
	}
	
	function scroll_step()
	{
		$session = session();
		$session->set('panzoom_y', $session->panzoom_y - $session->scroll_step);
	}
			
	function validation_tests_UCF_pass($current_input_data, $field)
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');	
				
		// check that if _ is present in the field
		// any number of _ are allowed
		// no checks if there are any
		$offset = 0;
		$under_allpos = array();
		while (	($pos = mb_strpos($current_input_data, '_', $offset)) !== FALSE ) 
			{
				$offset = $pos + 1;
				$under_allpos[] = $pos;
			}
		// any found
		if ( count($under_allpos) > 0 )
			{
				// set checks flag for second pass
				$session->checks = 0;
			}
		
		// ? check is by project
		switch ($session->current_project['project_index'])
			{
				case 1: // FreeBMD
					// check that if ? is present in the field, it is the one and only character
					$str_length = strlen($current_input_data);
					$str_position = strpos($current_input_data, '?');
					if ( $str_position !== false )
						{
							// there is a ? mark but it must be only character
							if ( $str_length > 1 )
								{
									$session->set('position_cursor', $session->fieldname);
									$session->set('error_field', $session->fieldname);
									$session->set('error_data_group', 'group_'.$session->fieldline);
									$session->set('message_2', 'The reserved character - ? = field is empty - is present in the field '.$field['column_name'].' but it is not the only character in the field.');
									$session->set('message_class_2', 'alert alert-danger');
									$session->set('message_error', 'error');
									return;
								}
							else
								{
									// set checks flag for second pass
									$session->checks = 0;
								}
						}
					break;
				case 2: // FreeREG
					// check ? is last character of any word in the field
					if ( $current_input_data != null )
						{
							$field_array = explode(' ', $current_input_data);
							foreach ( $field_array as $word )
								{
									$str_length = strlen($word);
									$str_position = strpos($word, '?');
									// was a ? found
									if ( $str_position !== false )
										{
											// there is a ? mark. it must be the last character of the word
											if ( $str_length != $str_position + 1 )
												{
													$session->set('position_cursor', $session->fieldname);
													$session->set('error_field', $session->fieldname);
													$session->set('error_data_group', 'group_'.$session->fieldline);
													$session->set('message_2', 'The reserved character - ? = word is uncertain - is present in the field '.$field['column_name'].' but it is not the last character of a word in the field.');
													$session->set('message_class_2', 'alert alert-danger');
													$session->set('message_error', 'error');
													return;
												}
											else
												{
													// set checks flag for second pass
													$session->checks = 0;
												}
										}
								}
						}
					break;
			}
								
		// check check if * is present - see here for rules, www.freebmd.org.uk/Format.shtml
		// get the string positions - There could be multiple *
		// It cannot be immediately before or after a _ or *
		// there may be multiple *
		// also using mb_strpos for multibyte UTF-8 strings
		// get all *'s
		$offset = 0;
		$star_allpos = array();
		while (	($pos = mb_strpos($current_input_data, '*', $offset)) !== FALSE ) 
			{
				$offset = $pos + 1;
				$star_allpos[] = $pos;
			}
			
		// are there any stars present
		if ( count($star_allpos) > 0 )
			{
				// set checks flag for second pass
				$session->checks = 0;
			
				// now do checks
				// read all instances
				foreach ( $star_allpos as $key => $pos )
					{
						// any forbidden characters before?
						if ( in_array(substr($current_input_data, $pos - 1, 1), array('_', '*')) )
							{
								$session->set('position_cursor', $session->fieldname);
								$session->set('error_field', $session->fieldname);
								$session->set('error_data_group', 'group_'.$session->fieldline);
								$session->set('message_2', $field['column_name'].' - A * cannot immediately follow a _ or a *. See www.freebmd.org.uk/Format.shtml');
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('message_error', 'error');
								return;
							}
						// any forbidden characters after?
						if ( in_array(substr($current_input_data, $pos + 1, 1), array('_', '*')) )
							{
								$session->set('position_cursor', $session->fieldname);
								$session->set('error_field', $session->fieldname);
								$session->set('error_data_group', 'group_'.$session->fieldline);
								$session->set('message_2', $field['column_name'].' - A * cannot immediately precede a _ or a *. See www.freebmd.org.uk/Format.shtml');
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('message_error', 'error');
								return;
							}
					}
			}
							
		// check if SQUARE brackets are present - see here for rules, www.freebmd.org.uk/Format.shtml
		// get the string positions - There could be multiple square bracket sets
		// also using mb_strpos for multibyte UTF-8 strings
		// get all open square brackets
		$offset = 0;
		$open_bracketallpos = array();
		while (	($pos = mb_strpos($current_input_data, '[', $offset)) !== FALSE ) 
			{
				$offset = $pos + 1;
				$open_bracketallpos[] = $pos;
			}
		// get all close square brackets
		$offset = 0;
		$close_bracketallpos = array();
		while (	($pos = mb_strpos($current_input_data, ']', $offset)) !== FALSE ) 
			{
				$offset = $pos + 1;
				$close_bracketallpos[] = $pos;
			}

		// now do checks on square brackets if any are found
		if ( count($open_bracketallpos) != 0 OR count($close_bracketallpos) != 0 )
			{
				// set checks flag for second pass
				$session->checks = 0;
				
				// is there an imbalance
				if ( count($open_bracketallpos) != count($close_bracketallpos) )
					{
						$session->set('position_cursor', $session->fieldname);
						$session->set('error_field', $session->fieldname);
						$session->set('error_data_group', 'group_'.$session->fieldline);
						$session->set('message_2', $field['column_name'].' - Please check your square brackets are complete (balanced).');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('message_error', 'error');
						return;
					}
				// are they nested? 
				if ( count($open_bracketallpos) > 1 AND $open_bracketallpos[1] <  $close_bracketallpos[0] )
					{
						$session->set('position_cursor', $session->fieldname);
						$session->set('error_field', $session->fieldname);
						$session->set('error_data_group', 'group_'.$session->fieldline);
						$session->set('message_2', $field['column_name'].' - Square brackets cannot be nested.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('message_error', 'error');
						return;
					}
							
				// open and close brackets are balanced and not nested
				// now do checks on each bracket set
				foreach ( $open_bracketallpos as $open_key => $open_pos )
					{									
						// are they in the right order?
						if ( $close_bracketallpos[$open_key] < $open_pos )
							{
								$session->set('position_cursor', $session->fieldname);
								$session->set('error_field', $session->fieldname);
								$session->set('error_data_group', 'group_'.$session->fieldline);
								$session->set('message_2', $field['column_name'].' - Please check your square brackets are in the correct order.');
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('message_error', 'error');
								return;
							}
						// is anything entered between them?
						if (  $close_bracketallpos[$open_key] == $open_pos + 1 )
							{
								$session->set('position_cursor', $session->fieldname);
								$session->set('error_field', $session->fieldname);
								$session->set('error_data_group', 'group_'.$session->fieldline);
								$session->set('message_2', $field['column_name'].' - Square brackets are present but there is no data between them.');
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('message_error', 'error');
								return;
							}
						// are there enough characters between them - there must be at least two
						if ( $close_bracketallpos[$open_key] < $open_pos + 3 )
							{
								$session->set('position_cursor', $session->fieldname);
								$session->set('error_field', $session->fieldname);
								$session->set('error_data_group', 'group_'.$session->fieldline);
								$session->set('message_2', $field['column_name'].' - Square brackets must have at least 2 characters between them.');
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('message_error', 'error');
								return;
							}
						// get data between the brackets and test for multiple same characters
						$contents_length = ($close_bracketallpos[$open_key]) - ($open_pos + 1);
						$contents = substr($current_input_data, $open_pos + 1, $contents_length);
						$contents_array = str_split($contents);
						foreach ( $contents_array as $char )
							{
								$offset = 0;
								$char_allpos = array();
								while (	($pos = mb_strpos($contents, $char, $offset)) !== FALSE ) 
									{
										$offset = $pos + 1;
										$char_allpos[] = $pos;
									}
								if ( count($char_allpos) > 1 )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' - It makes no sense to have the same character - '.$char.' - muliple times within
										 square brackets - see www.freebmd.org.uk/Format.shtml.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
							}
					}
			}
							
		// check if CURLY brackets are present - see here for rules, www.freebmd.org.uk/Format.shtml
		// get the string positions - There could be multiple curly bracket sets
		// also using mb_strpos for multibyte UTF-8 strings
		// get all open curly brackets
		$offset = 0;
		$open_bracketallpos = array();
		while (	($pos = mb_strpos($current_input_data, '{', $offset)) !== FALSE ) 
			{
				$offset = $pos + 1;
				$open_bracketallpos[] = $pos;
			}
		// get all close curly brackets
		$offset = 0;
		$close_bracketallpos = array();
		while (	($pos = mb_strpos($current_input_data, '}', $offset)) !== FALSE ) 
			{
				$offset = $pos + 1;
				$close_bracketallpos[] = $pos;
			}
			
		// now do checks on curly brackets if any are found
		if ( count($open_bracketallpos) != 0 OR count($close_bracketallpos) != 0 )
			{
				// set checks flag for second pass
				$session->checks = 0;
								
				// is there an imbalance
				if ( count($open_bracketallpos) != count($close_bracketallpos) )
					{
						$session->set('position_cursor', $session->fieldname);
						$session->set('error_field', $session->fieldname);
						$session->set('error_data_group', 'group_'.$session->fieldline);
						$session->set('message_2', $field['column_name'].' - Please check your curly brackets are complete (balanced).');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('message_error', 'error');
						return;
					}
				// are they nested? 
				if ( count($open_bracketallpos) > 1 AND $open_bracketallpos[1] <  $close_bracketallpos[0] )
					{
						$session->set('position_cursor', $session->fieldname);
						$session->set('error_field', $session->fieldname);
						$session->set('error_data_group', 'group_'.$session->fieldline);
						$session->set('message_2', $field['column_name'].' - Curly brackets cannot be nested.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('message_error', 'error');
						return;
					}
									
				// open and close brackets are balanced and not nested
				// now do checks on each bracket set
				foreach ( $open_bracketallpos as $open_key => $open_pos )
					{									
						// are they in the right order?
						if ( $close_bracketallpos[$open_key] < $open_pos )
							{
								$session->set('position_cursor', $session->fieldname);
								$session->set('error_field', $session->fieldname);
								$session->set('error_data_group', 'group_'.$session->fieldline);
								$session->set('message_2', $field['column_name'].' - Please check your curly brackets are in the correct order.');
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('message_error', 'error');
								return;
							}
						// is anything entered between them?
						if (  $close_bracketallpos[$open_key] == $open_pos + 1 )
							{
								$session->set('position_cursor', $session->fieldname);
								$session->set('error_field', $session->fieldname);
								$session->set('error_data_group', 'group_'.$session->fieldline);
								$session->set('message_2', $field['column_name'].' - Curly brackets are present but there is no data between them.');
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('message_error', 'error');
								return;
							}
						// does the open bracket immediately follow a proscribed character
						if ( in_array(substr($current_input_data, $open_pos - 1, 1), array(' ', ']', '}')) )
							{
								$session->set('position_cursor', $session->fieldname);
								$session->set('error_field', $session->fieldname);
								$session->set('error_data_group', 'group_'.$session->fieldline);
								$session->set('message_2', $field['column_name'].' - Curly brackets must immediately follow a character not a space, ] or }.');
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('message_error', 'error');
								return;
							}
						// are the contents valid
						// allowed content {min,} or {min,max}
						$contents_length = ($close_bracketallpos[$open_key]) - ($open_pos + 1);
						$contents = substr($current_input_data, $open_pos + 1, $contents_length);
						$contents_explode = explode(',', $contents);
						// , separator
						if ( $contents_explode[0] == $contents )
							{
								$session->set('position_cursor', $session->fieldname);
								$session->set('error_field', $session->fieldname);
								$session->set('error_data_group', 'group_'.$session->fieldline);
								$session->set('message_2', $field['column_name'].' - Curly bracket contents are not formatted corectly. Must be in format {min,max}. You have no , separator.');
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('message_error', 'error');
								return;
							}
						// too many elements
						if ( count($contents_explode) > 2 )
							{
								$session->set('position_cursor', $session->fieldname);
								$session->set('error_field', $session->fieldname);
								$session->set('error_data_group', 'group_'.$session->fieldline);
								$session->set('message_2', $field['column_name'].' - Curly bracket contents are not formatted corectly. Must be in format {min,max}.');
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('message_error', 'error');
								return;
							}
						// numeric min, min must be present in all cases
						if ( ! is_numeric($contents_explode[0]) )
							{
								$session->set('position_cursor', $session->fieldname);
								$session->set('error_field', $session->fieldname);
								$session->set('error_data_group', 'group_'.$session->fieldline);
								$session->set('message_2', $field['column_name'].' - Curly bracket contents are not formatted corectly. Must be in format {min,max} where min is a numeric value');
								$session->set('message_class_2', 'alert alert-danger');
								$session->set('message_error', 'error');
								return;
							}
						// if max present continue tests
						if ( count($contents_explode) == 2 )
							{
								// numeric max
								if ( ! is_numeric($contents_explode[1]) )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' - Curly bracket contents are not formatted corectly. Must be in format {min,max} where max is a numeric value');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								// min < max
								if ( $contents_explode[0] >= $contents_explode[1] )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' - Curly bracket contents are not formatted corectly. Must be in format {min,max} where min is less than max.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
							}
					}	
			}				
	}
	
	function validation_tests_second_pass($current_input_data, $field)
	{
		// initialise
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		$districts_model = new Districts_Model();
		$volumes_model = new Volumes_Model();
		$condition_model = new Condition_Model();
		$title_model = new Title_Model();
		$licence_model = new Licence_Model();
		$relationship_model = new Relationship_Model();
		$person_status_model = new Person_Status_Model();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		unset($session->official_volume);

		// perform test according to field type
		switch ($field['field_type'])
			{
				case 'page':
					// test numeric
					if ( ! is_numeric($current_input_data) )
						{
							$session->set('position_cursor', $session->fieldname);
							$session->set('error_field', $session->fieldname);
							$session->set('error_data_group', 'group_'.$session->fieldline);
							$session->set('message_2', $field['column_name'].' Should be numeric. Add # to end of data to bypass data tests this time only.');
							$session->set('message_class_2', 'alert alert-danger');
							$session->set('message_error', 'error');
							return;
						}
					break;
				case 'd_or':
				case 'date':
					// perform checks depending on date format
					switch ($field['date_format'])
						{
							case 'mm.yy':
								// set allocation year
								$allocation_year = substr($session->current_allocation[0]['BMD_year'], 2, 2);
								// set DOR year to 2 digit alloction year if this is a DOR field and the year was not entered by the user
								if ( $field['html_name'] == 'DOR' OR $field['html_name'] == 'reg' )
									{
										if ( strlen($current_input_data) == 2 )
											{
												$current_input_data = $current_input_data.'.'.$allocation_year;
											}
									}
								// check date format, array[0] will be same as input string if no . found
								$date = explode('.', $current_input_data);
								// did explode work, was . found?
								if ( $date[0] == $current_input_data )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format. No . found.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
													
								// explode worked, correct number of elements?
								if ( count($date) > 2 )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - too many .\'s ?.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
													
								// explode worked, correct number of digits
								if ( strlen($date[0]) != 2 OR strlen($date[1]) != 2)
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - month should be = 2 digits, year should be = 2 digits');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
												
								// explode worked, is month in range?
								if ( $date[0] < 01 OR $date[0] > 12 )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - month must be in range 01 - 12.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
												
								// explode worked, is year = to allocation year?
								$allocation_year = substr($session->current_allocation[0]['BMD_year'], 2, 2);
								if ( $date[1] != $allocation_year )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - year should be equal to allocation year. Add # at end of data to bypass check.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								break;
							case 'mmyy':
								// set DOR year to 2 digit allocation year if this is a DOR field and the year was not entered by the user
								if ( $field['html_name'] == 'DOR' AND strlen($current_input_data) == 2 )
									{
										$allocation_year = substr($session->current_allocation[0]['BMD_year'], 2, 2);
										$current_input_data = $current_input_data.$allocation_year;
									}
									
								// isolate month
								$month = substr($current_input_data, 0, 2);
								$shortyear = substr($current_input_data, 2, 2);
								
								// substring, is month in range?
								if ( $month < 01 OR $month > 12 )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - month must be in range 01 - 12.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								// explode worked, is year = to allocation year?
								$allocation_year = substr($session->current_allocation[0]['BMD_year'], 2, 2);
								if ( $shortyear != $allocation_year )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - year should be equal to allocation year - '.$allocation_year.'. Add # at end of data to bypass check.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								
								break;
							case 'ddccyyyy':
								// isolate day, month and year
								$day = substr($current_input_data, 0, 2);
								$month = strtoupper(substr($current_input_data, 2, 2));
								$year = substr($current_input_data, 4);
								
								// test valid day
								if ( ! in_array($day, $session->valid_days) )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - day is not valid.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
									
								// test valid month
								if ( ! in_array($month, $session->valid_2letter_month_codes) )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - month is not valid.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								
								// test valid year
								// numeric
								if ( ! is_numeric($year) )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - year is not numeric.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								// dob year must be 4 digits long
								if ( mb_strlen($year) != 4 )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - year must be 4 digits long. Use # at end of field to avoid error checking.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								// dob cannot be > allocation year
								if ( $year > $session->current_allocation[0]['BMD_year'] )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - birth year cannot be greater than death year.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								break;
							case 'mmm':
								// month must be in short form
								if ( ! array_key_exists($current_input_data, $session->marriage_months) )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - should be like AUG for example. Add # to override this test.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								break;
							case 'dd cc yyyy':
								// isolate day
								$day = substr($current_input_data, 0, 2);
								$month = strtoupper(substr($current_input_data, 3, 2));
								$year = substr($current_input_data, 6);
								
								// test valid day
								if ( ! in_array($day, $session->valid_days) )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - day is not valid.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
									
								// test valid month
								if ( ! in_array($month, $session->valid_2letter_month_codes) )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - month is not valid.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								
								// test valid year
								// numeric
								if ( ! is_numeric($year) )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - year is not numeric.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								// dob year must be 4 digits long
								if ( mb_strlen($year) != 4 )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - year must be 4 digits long.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								// dob cannot be > allocation year
								if ( $year > $session->current_allocation[0]['BMD_year'] )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - birth year cannot be greater than death year.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								break;
							case 'dd mmm yyyy':
								// strip off julian year if it has been added
								$current_input_data_array = explode('/', $current_input_data);
								$current_input_data = $current_input_data_array[0];
								// massage day
								$data_array = explode(' ', $current_input_data);
								if ( strlen($data_array[0]) == 1 )
									{
										$current_input_data = '0'.$current_input_data;
									}
								// isolate day/month and year
								$data_array = explode(' ', $current_input_data);
								//$day = substr($current_input_data, 0, 2);
								//$month = substr($current_input_data, 3, 3);
								//$year = substr($current_input_data, 7);
								
								// test valid day
								if ( ! in_array($data_array[0], $session->valid_days) )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - day is not valid.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
									
								// test valid month
								if ( ! in_array($data_array[1], $session->valid_3letter_month_codes) )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - month is not valid.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								
								// test valid year
								// numeric
								if ( ! is_numeric($data_array[2]) )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - year is not numeric.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								// year must be 4 digits long
								if ( mb_strlen($data_array[2]) != 4 )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - year must be 4 digits long.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
								// year must be in range
								if ( $data_array[2] < 1000 OR $data_array[2] > 2000 )
									{
										$session->set('position_cursor', $session->fieldname);
										$session->set('error_field', $session->fieldname);
										$session->set('error_data_group', 'group_'.$session->fieldline);
										$session->set('message_2', $field['column_name'].' is not in the correct format - year must be in range 1000 to 2000.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										return;
									}
									
								// handle Julian to Gregorian date
								if ( $data_array[2] < 1752 )
									{
										if ( ($data_array[1] == 'Jan' OR $data_array[1] == 'Feb' ) OR ( $data_array[1] == 'Mar' AND $data_array[0] < 25 ) )
											{
												// I have a Julian date
												// add 1 to year and isolate last two digits and append to date
												$jyear = $data_array[2] + 1;
												$jyear = substr($jyear, 2);
												$current_input_data = $current_input_data.'/'.$jyear;
											}
									}							
								break;
						}
					break;
				case 'dis_trict':
					// did user validate volume
					if ( $session->volume_ok == 'N' )
						{
							// district known?
							$session->set('current_district', $districts_model->where('District_name', $current_input_data)->findAll());
							if ( ! $session->current_district )
								{
									$session->synonym = '';
									$session->set('show_view_type', 'confirm_district');
									$session->set('error_field', $session->fieldname);
									$session->set('error_data_group', 'group_'.$session->fieldline);
									$session->set('message_2', 'This district is unknown => '.$current_input_data.' <= Did you spell the new district name correctly? If not, just click Back and Type What You See. If you are sure your new spelling is right, try to find a synonym for it and Continue.');
									$session->set('message_class_2', 'alert alert-danger');
									$session->set('message_error', 'error');
									return;
								}
						}
					break;
				case 'vo_lume':
					// Do tests only if volume has not been confirmed
					if ( $session->volume_ok == 'N' )
						{
							// Is this a roman volume?
							if ( $field['volume_roman'] == 'roman' )
								{
									// convert input to UPPER
									$current_input_data = strtoupper($current_input_data);
									
									// test valid roman numeral has been entered.
									if ( ! array_key_exists($current_input_data, $session->roman2arabic) )
										{
											$session->set('position_cursor', $session->fieldname);
											$session->set('error_field', $session->fieldname);
											$session->set('error_data_group', 'group_'.$session->fieldline);
											$session->set('message_2', 'The Roman Volume you entered is invalid for FreeComETT as it is not in range I to XXVII (1 to 27). Please correct it or add # to end of input data to bypass validation.');
											$session->set('message_class_2', 'alert alert-danger');
											$session->set('message_error', 'error');
											return;
										}
										
									// valid roman numeral, so convert to decimal
									$arabic = $session->roman2arabic[$current_input_data];				
								}
							
							// get volume info for current district but only if district was found
							if ( $session->current_district )
								{
									$session->current_volumes = $volumes_model	
										->where('district_index', $session->current_district[0]['district_index'])
										->where('BMD_type', $session->current_allocation[0]['BMD_type'])
										->findAll();
							
									// any volumes found for current district?											
									if ( ! $session->current_volumes )
										{
											$session->set('show_view_type', 'confirm_volume');
											$session->set('error_field', $session->fieldname);
											$session->set('error_data_group', 'group_'.$session->fieldline);
											$session->set('message_2', 'No volume data found for this district => '.$session->district.' Please enter volume from scan and confirm.');
											$session->set('message_class_2', 'alert alert-danger');
											$session->set('message_error', 'error');
											return;
										}
										
									// now I need to loop through the volumes found for the current district to find the volume applicable for the year/quarter of the scan. The year and quarter are held in the allocation for the most part. This is done by looping through the current volumes array
							
									// initialise loop
									// volume found flag
									$volume_found = 0;
									// year
									$year = $session->current_allocation[0]['BMD_year'];
									// quarter - depends on value of volume_quarterformat
									switch ($field['volume_quarterformat'])
										{
											case 'allocation_quarter':
												$quarter = str_pad($session->current_allocation[0]['BMD_quarter'], 2, '0', STR_PAD_LEFT);
												break;
										}
						
									// loop through current volumes array, if match then set volume found flag, save official volume and break the loop
									foreach ( $session->current_volumes as $volume_range )
										{
											if ( $year.$quarter >= $volume_range['volume_from'] AND $year.$quarter <= $volume_range['volume_to'])
												{
													$session->official_volume = $volume_range['volume'];
													$volume_found = 1;
													break;
												}	
										}								
											
									// was a volume found?
									if ( $volume_found == 0 OR $session->official_volume == '' )
										{
											$session->set('show_view_type', 'confirm_volume');
											$session->set('error_field', $session->fieldname);
											$session->set('error_data_group', 'group_'.$session->fieldline);
											$session->set('message_2', 'No volume data found for this district, year, quarter => '.$session->district.', '.$year.', '.$quarter.'. Please enter volume from scan and confirm.');
											$session->set('message_class_2', 'alert alert-danger');
											$session->set('message_error', 'error');
											return;
										}
										
									if ( $field['capitalise'] == 'UPPER' )
										{
											$volume_test = strtolower($current_input_data);
										}
										
									// set the volume test field
									if ( $field['volume_roman'] == 'roman' )
										{
											$volume_test = $arabic;
										}
									else
										{
											// any special test?
											if ( $field['special_test'] == 'first_3_digits_only' )
												{
													$volume_test = substr($current_input_data, 0, 3);
												}
											else
												{
													$volume_test = $current_input_data;
												}
										}
										
									// does the official volume match entered volume?
									if ( $volume_test != $session->official_volume )
										{
											$session->set('show_view_type', 'confirm_volume');
											$session->set('error_field', $session->fieldname);
											$session->set('error_data_group', 'group_'.$session->fieldline);
											if ( $field['volume_roman'] == 'roman' )
												{
													$session->set('message_2', 'The roman volume number you entered is not equal to official volume number for this district and for the year and quarter of the scan => '.$session->district.'. You entered => '.$current_input_data.' = '.$arabic.'. Official volume => '.$session->official_volume.'. This is a roman volume; there is a mismatch between roman numeral and its decimal equivalent. Please confirm');
												}
											else
												{
													$session->set('message_2', 'The volume number you entered is not equal to official volume number for this district and for the year and quarter of the scan => '.$session->district.'. You entered => '.$current_input_data.'. Official volume => '.$session->official_volume.'. Please confirm');
												}
											$session->set('message_class_2', 'alert alert-danger');
											$session->set('message_error', 'error');
											return;
										}
								}
						}
					break;
				case 'fore_name':
				case 'sur_name':
					// do I have a surname or forename
					$include_field = [ "forenames", "surname" ];
					if ( in_array($field['html_name'], $include_field))
						{
							// initialise comparison fields for both surname and forenames
							$previous_surname = '';
							$previous_firstname = '';
							
							// get previous values depending what I doing
							switch (true)
								{
									// modifying a line
									case $session->line_edit_flag == 1:
										// if previous line exists set previous surname
										if ( $session->prevEl )
											{
												$previous_surname = $session->prevEl['BMD_surname'];
												$previous_firstname = $session->prevEl['BMD_firstname'];
											}
										break;
										
									// inserting a line
									case $session->insert_line_flag == 1:
										// previous line was set in insert line function
										// if previous line exists set previous surname
										if ( $session->prevEl )
											{
												$previous_surname = $session->prevEl['BMD_surname'];
												$previous_firstname = $session->prevEl['BMD_firstname'];
											}
										break;
										
									// adding a normal line
									default:
										// not modifying a line, if lastEl exists use it to get previous surname
										if ( $session->lastEl )
											{
												$previous_surname = $session->lastEl['BMD_surname'];
												$previous_firstname = $session->lastEl['BMD_firstname'];
											}
										break;
								}
						}
					
					// now do specific tests
					switch ($field['html_name'])
						{
							case 'surname':
								// do test only if surnames are different
								if ( $session->surname != $previous_surname AND $session->surname_ok == 'N')
									{	
										// is current data less than last forenames keyed == first name out of sequence - ask for confirmation
										if ( $current_input_data < $previous_surname )
											{
												$session->set('show_view_type', 'confirm_surname');
												$session->set('error_field', $session->fieldname);
												$session->set('error_data_group', 'group_'.$session->fieldline);
												$session->set('message_2', 'The surname that you entered, '.$current_input_data.', appears to be out of order compared to previous surname entered, '.$previous_surname.'. Is this correct?.');
												$session->set('message_class_2', 'alert alert-danger');
												$session->set('message_error', 'error');
												return;
											}
									}
								break;
							case 'forenames':					
								// do forenames test only if current surname same as last one and forename was not validated
								if ( $session->surname == $previous_surname AND $session->firstname_ok == 'N')
									{
										// check for auto full stop and if there is one already in previous firstname take it out
										$temp_prev_firstname = $previous_firstname;
										if ( $field['auto_full_stop'] == 'Y' AND substr($previous_firstname, -1) == '.')
											{
												// take it out
												$temp_prev_firstname = rtrim($previous_firstname, '.');
											}
													
										// is current data less than last forenames keyed == first name out of sequence - ask for confirmation
										$exclude_names = [ "Male", "Female", "male", "female", "MALE", "FEMALE" ];
										if ( $current_input_data < $temp_prev_firstname AND ! in_array($current_input_data, $exclude_names))
											{
												$session->set('show_view_type', 'confirm_firstnames');
												$session->set('error_field', $session->fieldname);
												$session->set('error_data_group', 'group_'.$session->fieldline);
												$session->set('message_2', 'The first name(s) that you entered, '.$current_input_data.', appear(s) to be out of order compared to previous first name(s) entered, '.$previous_firstname.'. Is this correct?.');
												$session->set('message_class_2', 'alert alert-danger');
												$session->set('message_error', 'error');
												return;
											}
									}
						}
					break;
				case 'ent_no':
					// test numeric
					if ( ! is_numeric($current_input_data) )
						{
							// not numeric
							$session->set('position_cursor', $session->fieldname);
							$session->set('error_field', $session->fieldname);
							$session->set('error_data_group', 'group_'.$session->fieldline);
							$session->set('message_2', $field['column_name'].' Should be numeric. Add # to end of data to bypass data tests this time only.');
							$session->set('message_class_2', 'alert alert-danger');
							$session->set('message_error', 'error');
							return;
						}
					else
						{
							// numeric - check length
							if ( strlen($current_input_data) != 3 )
								{
									// not 3 digits
									$session->set('position_cursor', $session->fieldname);
									$session->set('error_field', $session->fieldname);
									$session->set('error_data_group', 'group_'.$session->fieldline);
									$session->set('message_2', $field['column_name'].' Should be 3 numeric digits. Add # to end of data to bypass data tests this time only.');
									$session->set('message_class_2', 'alert alert-danger');
									$session->set('message_error', 'error');
									return;
								}
						}			
					break;
				case 'condition':
					if ( $current_input_data != '' )
						{
							// must be in table
							$valid = $condition_model
								->where('Condition', $current_input_data)
								->find();
							// found?
							if ( ! $valid )
								{
									$session->set('position_cursor', $session->fieldname);
									$session->set('error_field', $session->fieldname);
									$session->set('error_data_group', 'group_'.$session->fieldline);
									$session->set('message_2', $field['column_name'].' must be selected from the pick list. Add # to end of data to bypass data tests this time only.');
									$session->set('message_class_2', 'alert alert-danger');
									$session->set('message_error', 'error');
									return;
								}
						}
					break;
				case 'title':
					if ( $current_input_data != '' )
						{
							// must be in table
							$valid = $title_model
								->where('Title', $current_input_data)
								->find();
							// found?
							if ( ! $valid )
								{
									$session->set('position_cursor', $session->fieldname);
									$session->set('error_field', $session->fieldname);
									$session->set('error_data_group', 'group_'.$session->fieldline);
									$session->set('message_2', $field['column_name'].' must be selected from the pick list. Add # to end of data to bypass data tests this time only.');
									$session->set('message_class_2', 'alert alert-danger');
									$session->set('message_error', 'error');
									return;
								}
						}
					break;
				case 'licence':
					if ( $current_input_data != '' AND $current_input_data != 'Select Marriage By:' )
						{
							// must be in table
							$valid = $licence_model
								->where('Licence', $current_input_data)
								->find();
							// found?
							if ( ! $valid )
								{
									$session->set('position_cursor', $session->fieldname);
									$session->set('error_field', $session->fieldname);
									$session->set('error_data_group', 'group_'.$session->fieldline);
									$session->set('message_2', $field['column_name'].' must be selected from the pick list.');
									$session->set('message_class_2', 'alert alert-danger');
									$session->set('message_error', 'error');
									return;
								}
						}
					else
						{
							$current_input_data = '';
						}
					break;
				case 'relationship':
					if ( $current_input_data != '' )
						{
							// must be in table
							$valid = $relationship_model
								->where('Relationship', $current_input_data)
								->find();
							// found?
							if ( ! $valid )
								{
									$session->set('position_cursor', $session->fieldname);
									$session->set('error_field', $session->fieldname);
									$session->set('error_data_group', 'group_'.$session->fieldline);
									$session->set('message_2', $field['column_name'].' must be selected from the pick list. Add # to end of data to bypass data tests this time only.');
									$session->set('message_class_2', 'alert alert-danger');
									$session->set('message_error', 'error');
									return;
								}
						}
					break;
				case 'mark':
					if ( $current_input_data != '' )
						{
							// must be in list
							$list = [ "YES", "NO" ];
							if ( ! in_array($current_input_data, $list))
								{
									$session->set('position_cursor', $session->fieldname);
									$session->set('error_field', $session->fieldname);
									$session->set('error_data_group', 'group_'.$session->fieldline);
									$session->set('message_2', $field['column_name'].' must be YES or NO if entered. Add # to end of data to bypass data tests this time only.');
									$session->set('message_class_2', 'alert alert-danger');
									$session->set('message_error', 'error');
									return;
								}
						}
					break;
				case 'status':
					if ( $current_input_data != '' )
						{
							// must be in list
							$valid = $person_status_model
								->where('Person_status', $current_input_data)
								->find();
							// found?
							if ( ! $valid )
								{
									$session->set('position_cursor', $session->fieldname);
									$session->set('error_field', $session->fieldname);
									$session->set('error_data_group', 'group_'.$session->fieldline);
									$session->set('message_2', $field['column_name'].' is not valid. Add # to end of data to bypass data tests this time only.');
									$session->set('message_class_2', 'alert alert-danger');
									$session->set('message_error', 'error');
									return;
								}
						}
					break;
				case 'sex':
					if ( $current_input_data != '' )
						{
							if ( ! in_array($current_input_data, $session->sex))
								{
									$session->set('position_cursor', $session->fieldname);
									$session->set('error_field', $session->fieldname);
									$session->set('error_data_group', 'group_'.$session->fieldline);
									$session->set('message_2', $field['column_name'].' is not valid. Add # to end of data to bypass data tests this time only.');
									$session->set('message_class_2', 'alert alert-danger');
									$session->set('message_error', 'error');
									return;
								}
						}
					break;
			}
		
		// return value
		$fn = $field['html_name'];
		$session->$fn = $current_input_data;
	}
	
	function transcribe_validate_registration_select_tests()
	{
		// initialise
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// registration test for D are performed only if registration entered. is this true?
		switch ($session->current_allocation[0]['BMD_type'])
			{
				case 'B':	
					// registration test for B
					transcribe_validate_registration_tests();
					if ( $session->message_error == 'error' ) { return; }
					// format the registration field
					if ( $session->current_allocation[0]['BMD_year'] >= 1993 )
						{
							$session->set('registration', $session->reg_month.$session->reg_year);
						}
					else
						{
							$session->set('registration', $session->reg_month.'.'.$session->reg_year);
						}
					break;
				case 'M':
					// registration test for M
					if ( $session->current_allocation[0]['BMD_year'] <= 1993 )
						{
							transcribe_validate_registration_tests();
							if ( $session->message_error == 'error' ) { return; }
							// format the registration field but only for 1993 and prior
							if ( $session->current_allocation[0]['BMD_year'] < 1993 )
								{
									$session->set('registration', $session->reg_month.'.'.$session->reg_year);
								}
							else
								{
									$session->set('registration', $session->reg_month.$session->reg_year);
								}
						}
					break;
				case 'D':	
					// registration test for D
					transcribe_validate_registration_tests();
					if ( $session->message_error == 'error' ) { return; }
					// format the registration field
					if ( $session->current_allocation[0]['BMD_year'] >= 1993 )
						{
							$session->set('registration', $session->reg_month.$session->reg_year);
						}
					else
						{
							$session->set('registration', $session->reg_month.'.'.$session->reg_year);
						}
					break;
				default:
					break;
			}
	}
	
	function transcribe_validate_registration_tests()
	{
		// initialise
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// what format has been entered? length can be 2, 4 or 5; 2=month only; 4=mmyy, 5=mm.yy
		switch (strlen($session->registration))
			{
				case 2: // just the month has been entered
					// check the month
					$session->set('reg_month', $session->registration);
					transcribe_validate_reg_month();
					if ( $session->message_error != '' ) { return; }
					// check the year
					$session->set('reg_year', substr($session->current_allocation[0]['BMD_year'], 2, 2)); 
					transcribe_validate_reg_year();
					if ( $session->message_error != '' ) { return; }
					break;
				case 4: // month and year has been entered
					// check the month
					$session->set('reg_month', substr($session->registration, 0, 2));
					transcribe_validate_reg_month();
					if ( $session->message_error != '' ) { return; }
					// check the year
					$session->set('reg_year', substr($session->registration, 2, 2) ); 
					transcribe_validate_reg_year();
					if ( $session->message_error != '' ) { return; }
					break;
				case 5: // month and year has been entered with .
					// check the month
					$session->set('reg_month', substr($session->registration, 0, 2));
					transcribe_validate_reg_month();
					if ( $session->message_error != '' ) { return; }
					// check the year
					$session->set('reg_year', substr($session->registration, 3, 2) ); 
					transcribe_validate_reg_year();
					if ( $session->message_error != '' ) { return; }
					// check the separator
					$session->set('reg_separator', substr($session->registration, 2, 1) ); 
					transcribe_validate_reg_separator();
					if ( $session->message_error != '' ) { return; }
					break;
				default: // anything else is an error
					$session->set('message_2', 'Registration format not valid. Registration can be mm, or mmyy or mm.yy. If mm, webBMD will add the year for you.');
					$session->set('message_class_2', 'alert alert-danger');
					$session->set('message_error', 'error');
					return;
					break;
			}
			
	}
	
	function transcribe_validate_reg_month()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// numeric
		if ( ! is_numeric($session->reg_month) )
			{
				$session->set('message_2', 'Registration month number must be numeric.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// in range
		if ( $session->reg_month < '01' OR $session->reg_month > '12' )
			{
				$session->set('message_2', 'Registration month number must be in range 01:12.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
	}
	
	function transcribe_validate_reg_year()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// year valid?
		if ( $session->registration_ok == 'N' )
			{
				if ($session->reg_year != substr($session->current_allocation[0]['BMD_year'], 2, 2) )
					{
						$session->set('show_view_type', 'confirm_registration');
						$session->set('message_2', 'Registration year, '.$session->registration.', is normally equal to scan year (allocation year) = '.substr($session->current_allocation[0]['BMD_year'], 2, 2));
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('message_error', 'error');
						return;
					}
			}
	}
	
	function transcribe_validate_reg_separator()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// separator valid?
		if ($session->reg_separator != '.' )
			{
				$session->set('message_2', 'Registration separator must be . = full-stop.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
	}

	function transcribe_validate_volume_tests()
	{
		// initialise
		$session = session();
		$volumes_model = new Volumes_Model();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// if volume was confirmed don't test it
		if ( $session->volume_ok == 'Y' )
			{
				return;
			}

		// get volume info
		$session->set('transcribe_volumes', $volumes_model
			->where('district_index', $session->transcribe_district[0]['district_index'])
			->where('BMD_type', $session->current_allocation[0]['BMD_type'])
			->findAll());
		if ( ! $session->transcribe_volumes )
			{
				$session->set('show_view_type', 'confirm_volume');
				$session->set('volume', '');
				$session->set('message_2', 'No volume data found for this district => '.$session->district.' Please enter volume from scan and confirm.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// set volume found flag 
		$volume_found = 0;
		// set values in order to find this registration in range
		$year = $session->current_allocation[0]['BMD_year'];
		// per type quarter
		switch ($session->current_allocation[0]['BMD_type'])
			{
				case 'B':
					$quarter = $session->month_to_quarter[$session->reg_month];
					break;
				case 'M':
					switch (true)
						{
							case $session->current_allocation[0]['BMD_year'] <= 1993:
								$quarter = $session->month_to_quarter[$session->reg_month];
								break;
							case $session->current_allocation[0]['BMD_year'] > 1993:
								$marr_month = $session->marriage_months[$session->registration];
								$quarter = $session->month_to_quarter[$marr_month];
								break;
							default:
								break;
						}
					break;
				case 'D':
					if ( empty ( $session->registration ) )
						{	
							$session->set('registration_was_blank', '1');
							$session->set('registration', '01.'.$session->current_allocation[0]['BMD_year']);
							$quarter = '01';
						}
					else
						{
							$quarter = $session->month_to_quarter[$session->reg_month];
							$session->set('registration_was_blank', '0');
						}
					//$quarter = str_pad($session->current_allocation[0]['BMD_quarter'], 2, '0', STR_PAD_LEFT);
					break;	
				default:
					break;
			}
		// find range
		foreach ( $session->transcribe_volumes as $volume_range )
			{
				if ( $year.$quarter >= $volume_range['volume_from'] AND $year.$quarter <= $volume_range['volume_to'])
					{
						$session->set('volume', $volume_range['volume']);
						$volume_found = 1;
						break;
					}	
			}
		// was a volume found?
		if ( $volume_found == 0 OR $session->volume == '' )
			{
				$session->set('show_view_type', 'confirm_volume');
				$session->set('volume', '');
				$session->set('message_2', 'No volume data found for this district => '.$session->district.', '.$session->current_allocation[0]['BMD_year'].', '.$quarter.'. Please enter volume from scan and confirm.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// does it match entered volume?
		switch ($session->current_allocation[0]['BMD_type'])
			{
				case 'B':	// births
					switch (true)
						{
							case $session->current_allocation[0]['BMD_year'] > 1992:
								if ( $session->volume != $session->dis_volume )
									{
										$session->set('show_view_type', 'confirm_volume');
										$session->set('message_2', 'Scan volume/district number you entered is not equal to official volume list for this district => '.$session->district.'. You entered => '.$session->dis_volume.'. Official volume => '.$session->volume.'. Please confirm.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										$session->set('volume', '');
										return;
									}
								break;
							default:
								if ( $session->volume != $session->dis_volume )
									{
										$session->set('show_view_type', 'confirm_volume');
										$session->set('message_2', 'Scan volume/district number you entered is not equal to official volume list for this district => '.$session->district.'. You entered => '.$session->dis_volume.'. Official volume => '.$session->volume.'. Please confirm.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										$session->set('volume', '');
										return;
									}
								break;
						}
					break;
				case 'M':	// Marriages
					switch (true)
						{
							case $session->current_allocation[0]['BMD_year'] > 1993:
								if ( $session->volume != $session->dis_volume )
									{
										$session->set('show_view_type', 'confirm_volume');
										$session->set('message_2', 'Scan volume/district number you entered is not equal to official volume list for this district => '.$session->district.'. You entered => '.$session->dis_volume.'. Official volume => '.$session->volume.'. Please confirm.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										$session->set('volume', '');
										return;
									}
								break;
							default:
								if ( $session->volume != $session->dis_volume )
									{
										$session->set('show_view_type', 'confirm_volume');
										$session->set('message_2', 'Scan volume/district number you entered is not equal to official volume list for this district => '.$session->district.'. You entered => '.$session->dis_volume.'. Official volume => '.$session->volume.'. Please confirm.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										$session->set('volume', '');
										return;
									}
								break;
						}
					break;
				case 'D':	// Deaths
					switch (true)
						{
							case $session->current_allocation[0]['BMD_year'] > 1992:
								if ( $session->volume != $session->dis_volume )
									{
										$session->set('show_view_type', 'confirm_volume');
										$session->set('message_2', 'Scan volume/district number you entered is not equal to official volume list for this district => '.$session->district.'. You entered => '.$session->dis_volume.'. Official volume => '.$session->volume.'. Please confirm.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										$session->set('volume', '');
										return;
									}
								break;
							default:
								if ( $session->volume != $session->dis_volume )
									{
										$session->set('show_view_type', 'confirm_volume');
										$session->set('message_2', 'Scan volume/district number you entered is not equal to official volume list for this district => '.$session->district.'. You entered => '.$session->dis_volume.'. Official volume => '.$session->volume.'. Please confirm.');
										$session->set('message_class_2', 'alert alert-danger');
										$session->set('message_error', 'error');
										$session->set('volume', '');
										return;
									}
								break;
						}
					break;
				default:
					break;
			}
	}
	
	function test_deaths_standard()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// age can be the age or the DOB
		// test age/DOB exists.
		if ( trim($session->age) == '' )
			{
				$session->set('message_2', 'Age/DOB cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		
	}
	
	function test_deaths_prior()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// page blank?
		if ( $session->page == '' )
			{
				$session->set('message_2', 'Page cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
			
		
		// DOB valid?
		// split DOB into d:m:y
		$DOB_array = explode(' ', $session->age);
		// if arrray count is 3, I have a DOB (probably), so validate
		if ( count($DOB_array) == 3 )
			{
				// length OK?
				if ( strlen($session->age) != 10 )
					{
						$session->set('message_2', 'Age/DOB format incorrect. Too many characters.');
						$session->set('message_class_2', 'alert alert-danger');
						$session->set('message_error', 'error');
						return;
					}
				
				// split DOB into components
				$session->set('DOB_day', strtoupper($DOB_array[0]));
				$session->set('DOB_month', strtoupper($DOB_array[1]));
				$session->set('DOB_year', strtoupper($DOB_array[2]));
				
				// test DOB
				test_DOB();
				if ( $session->message_error == 'error' ) { return; }			
			}
		// set dis_volume
		$session->set('dis_volume', $session->dis_number);
	}
	
	function test_deaths_after()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// DOB valid?
		// length OK?
		if ( strlen($session->age) != 8 )
			{
				$session->set('message_2', 'Age/DOB format incorrect.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		
		// split DOB into components
		$session->set('DOB_day', strtoupper(substr($session->age, 0, 2)));
		$session->set('DOB_month', strtoupper(substr($session->age, 2, 2)));
		$session->set('DOB_year', strtoupper(substr($session->age, 4, 4))); 
		
		// test DOB
		test_DOB();
		if ( $session->message_error == 'error' ) { return; }
		
		// test dis number exists.
		if ( $session->dis_number == '' )
			{
				$session->set('message_2', 'District No cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// test reg number exists.
		if ( $session->reg_number == '' )
			{
				$session->set('message_2', 'Reg No cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// test entry number exists.
		if ( $session->ent_number == '' )
			{
				$session->set('message_2', 'Entry No cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// test registration number exists.
		if ( $session->registration == '' )
			{
				$session->set('message_2', 'DOR cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		
		// create district volume field
		$session->set('dis_volume', substr($session->dis_number, 0, 3));
	}
	
	function test_marriages_standard()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// test partnername
		if ( $session->partnername == '' )
			{
				$session->set('position_cursor', 'partnername');
				$session->set('message_2', 'Partner name cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
	}
	
	function test_marriages_prior()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// not a lot to do here
		// set dis_volume
		$session->set('dis_volume', $session->dis_number);
	}
	
	function test_marriages_after()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
	
		// test dis number exists.
		if ( $session->dis_number == '' )
			{
				$session->set('position_cursor', 'dis_number');
				$session->set('message_2', 'District No cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
			
		// registration blank?
		if ( $session->registration == '' )
			{
				$session->set('position_cursor', 'registration');
				$session->set('message_2', 'Month cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
			
		// test registration is in marriage months array
		$session->set('registration', strtoupper($session->registration));
		if ( ! array_key_exists($session->registration, $session->marriage_months) )
			{
				$session->set('position_cursor', 'registration');
				$session->set('message_2', 'Month is invalid');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
				
		if ( $session->page_ok == 'N' )
			{
				switch ($session->current_allocation[0]['BMD_type'])
					{
						case 'D':
							break;
						default:
							if ( strlen($session->page) < 3 OR strlen($session->page) > 4 )
								{
									$session->set('show_view_type', 'confirm_page');
									$session->set('message_2', 'Page number is usually 3 or 4 digits long. You entered => '.$session->page.'. Please confirm your entry or correct it by selecting No.');
									$session->set('message_class_2', 'alert alert-danger');
									$session->set('message_error', 'error');
									return;
								}
							break;
					}
			}

		// test entry number
		if ( $session->ent_number == '' )
			{
				$session->set('position_cursor', 'ent_number');
				$session->set('message_2', 'Entry no cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
			
		// test source code
		if ( $session->source_code == '' )
			{
				$session->set('position_cursor', 'source_code');
				$session->set('message_2', 'Source code cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
			
		// create district volume field
		$session->set('dis_volume', $session->dis_number);		
	}
	
	function test_births_standard()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// test partnername
		if ( $session->partnername == '' )
			{
				$session->set('message_2', 'Partner name cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
	}
	
	function test_births_prior()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// volume blank?
		if ( $session->dis_number == '' )
			{
				$session->set('message_2', 'Volume must be entered');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// page blank?
		if ( $session->page == '' )
			{
				$session->set('message_2', 'Page cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// set dis_volume
		$session->set('dis_volume', $session->dis_number);
	}
	
	function test_births_after()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// test dis number exists.
		if ( $session->dis_number == '' )
			{
				$session->set('message_2', 'District No cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// test reg number exists.
		if ( $session->reg_number == '' )
			{
				$session->set('message_2', 'Reg No cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// test entry number exists.
		if ( $session->ent_number == '' )
			{
				$session->set('message_2', 'Entry No cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// test registration number exists.
		if ( $session->registration == '' )
			{
				$session->set('message_2', 'DOR cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		
		// create district volume field
		$session->set('dis_volume', substr($session->dis_number, 0, 3));
	}
	
	function test_DOB()
	{
		// initialise
		$session = session();
		$session->set('message_error', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		
		// test DOB_day is in DOB days array
		if ( ! in_array($session->DOB_day, $session->death_days) )
			{
				$session->set('message_2', 'DOB day is invalid');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// DOB month in DOB months array?
		if ( ! in_array($session->DOB_month, $session->death_months) )
			{
				$session->set('message_2', 'DOB month is invalid');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// DOB year valid?
		if ( ! is_numeric($session->DOB_year) )
			{
				$session->set('message_2', 'DOB year must be numeric');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// DOB year valid length?
		if ( strlen($session->DOB_year) != 4 )
			{
				$session->set('message_2', 'DOB year must be 4 digits long');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
		// DOB year > scan year?
		if ( $session->DOB_year > $session->current_allocation[0]['BMD_year'] )
			{
				$session->set('message_2', 'DOB year cannot be greater than scan year.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('message_error', 'error');
				return;
			}
	}
	
	function freebmd_createCSV()
	{
		// initialise
		$session = session();
		$project_types_model = new Project_Types_Model();
		$detail_comments_model = new Detail_Comments_Model;
		$transcription_comments_model = new Transcription_Comments_Model();
		$transcription_CSV_file_model = new Transcription_CSV_File_Model();
		
		$new_next_page = $session->current_transcription[0]['BMD_next_page'];
		$csv_string = '';
		
		// check data lines for ?=unknown characters. This is done because winBMD uses an old character coding set for accented characters.
		// maybe a winBMD produced file was imported to FreeComETT.
		// winBMD uses an out-dated character set
		$session->unknown_char = 0;
		$unknown_char_lines = array();

		foreach ( $session->detail_data as $dd )
			{
				// read the transcription_detail_defs and populate line accordingly
				foreach ( $session->current_transcription_def_fields as $field )
					{
						// check ? is not first character of field
						if ( $dd[$field['table_fieldname']] != '?' AND $dd[$field['table_fieldname']] != '' )
							{
								// does unknown character exist in field - do not include first char.
								if ( substr_count($dd[$field['table_fieldname']], '?', 1) > 0 ) 
									{
										$session->unknown_char = 1;
										$unknown_char_lines[] = $dd['BMD_line_sequence'];
									}
							}	
					}
			}
		// was an unknown character found?
		if ( $session->unknown_char == 1 )
			{
				$session->set('message_2', 'Cannot create the BMD file because some fields contain unknown characters. This is probably because you imported a file created in winBMD which contained accented characters. Use the option \'Transcribe from Scan\' and \'Modify\' to fix these errors. The data lines in error are, ');
				foreach ( $unknown_char_lines as $line )
					{
						$session->message_2 = $session->message_2.$line.', ';
					}
				$session->set('message_class_2', 'alert alert-danger');				
				return;
			}
		
		// write header lines to csv string
		
		// first header line, eg +INFO,dreamstogo@gmail.com,Password,SEQUENCED,BIRTHS
		// get project type name
		$type_name =	$project_types_model
						->where('project_index',  $session->current_project['project_index'])
						->where('type_code', $session->current_allocation[0]['BMD_type'])
						->select('type_name_upper')
						->find();
						
		// create line
		$line_array = ['+INFO', $session->identity_emailid, 'Password', 'SEQUENCED', $type_name[0]['type_name_upper'], 'iso8859-1'];
		prepare_line($line_array, 'N');
		$csv_string = $csv_string.$session->write_line."\r\n";
		
		// second header line, eg #,99,dreamstogo,Richard Oliver,1988BD0430.BMD,04-Aug-2020,Y,N,N,D,0,FreeComETT
		// need to get first detail line to work out volume number format VNF, required for WinBMD, not used by FreeComETT at all
		$first_detail_line = current($session->detail_data);
		$volume_array = str_split($first_detail_line['BMD_volume']);
		switch (true)
			{
				case ( is_numeric(reset($volume_array)) AND count($volume_array) > 3 );
					$VNF = '999';
					break;
				case ( is_numeric(reset($volume_array)) AND is_numeric(end($volume_array)) AND count($volume_array) == 3 );
					$VNF = '999';
					break;
				case ( is_numeric(reset($volume_array)) AND is_numeric(end($volume_array)) );
					$VNF = '99';
					break;
				case ( is_numeric(reset($volume_array)) AND ! is_numeric(end($volume_array)) );
					$VNF = '9Z';
					break;
				case ( ! is_numeric(reset($volume_array)) AND ! is_numeric(end($volume_array)) );
					$VNF = 'XX';
					break;
				default:
					$VNF = '??';
					break;
			}

		$line_array = ['#', $VNF, $session->identity_userid, $session->current_syndicate[0]['BMD_syndicate_name'], $session->current_transcription[0]['BMD_file_name'].'.BMD', $session->current_transcription[0]['BMD_start_date'], 'Y', 'N', 'N', $session->current_allocation[0]['BMD_letter'], '0', $session->programname];
		prepare_line($line_array, 'N');
		$csv_string = $csv_string.$session->write_line."\r\n";
		
		// third header line, eg #, = comment/source related to the overall transcription.
		// get transcription comment
		$transcription_comments_model = new Transcription_Comments_Model();
		$comment_text = '';
		$comment_text_array =	$transcription_comments_model
								->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
								->where('comment_sequence', 10)
								->find();
		// any found ?
		if ( $comment_text_array )
			{
				$comment_text = $comment_text_array[0]['comment_text'];
			}
		$line_array = ['#',$comment_text];		
		prepare_line($line_array, 'N');
		$csv_string = $csv_string.$session->write_line."\r\n";
		
		// fourth header line, eg +S,1988,,GUS/1988/Births/OFHS-03,05-Aug-2020 or
		// fourth header line, eg +S,1988,Sep,GUS/1988/Births/OFHS-03,05-Aug-2020 if quarter based
		// look for quarter
		$exploded_scan_path = explode('/', $session->current_allocation[0]['BMD_reference']);
		$quarter_number = array_search($exploded_scan_path[3], $session->quarters_short_long);
		// write line
		if ( $quarter_number ) 
			{
				$line_array = ['+S', $session->current_allocation[0]['BMD_year'], $session->quarters[$quarter_number], $exploded_scan_path[4].'/'.$session->current_transcription[0]['BMD_scan_name'], $session->current_date];
			}
		else
			{
				$line_array = ['+S', $session->current_allocation[0]['BMD_year'], '', $exploded_scan_path[3].'/'.$session->current_transcription[0]['BMD_scan_name'], $session->current_date]; 
			}
		prepare_line($line_array, 'N');
		$csv_string = $csv_string.$session->write_line."\r\n";
		
		// fifth header line, eg +CREDIT,Hilary Wright,dreamstogo@gmail.com,dreamstogo, only if syndicate coordinator allows this.
		if ( $session->current_syndicate[0]['BMD_syndicate_credit'] == 'Y' )
			{
				// removed until someone shouts about this
				//$write_line = "+CREDIT,".$session->identity_realname.",".$session->identity_emailid.","."Credit"."\r\n";
				//fwrite($fp, $write_line);
			}
		
		// current page line, eg +PAGE,430
		$line_array = ['+PAGE', $session->current_transcription[0]['BMD_current_page'].$session->current_transcription[0]['BMD_current_page_suffix']];
		prepare_line($line_array, 'N');
		$csv_string = $csv_string.$session->write_line."\r\n";

		// now loop through detail lines and write to CSV
		
		// read each detail line
		foreach ( $session->detail_data as $dd )
			{
				$line_array = array();
				// read the transcription_detail_defs and populate line accordingly
				foreach ( $session->current_transcription_def_fields as $field )
					{
						$line_array[] = $dd[$field['table_fieldname']];
					}
				
				// prepare the line
				prepare_line($line_array, 'Y');
				$csv_string = $csv_string.$session->write_line."\r\n";
				
				// any comments?
				// get the comment lines and load fields
				$session->detail_comments =	$detail_comments_model	
											->where('BMD_line_index', $dd['BMD_index'])
											->where('BMD_identity_index', $session->BMD_identity_index)
											->where('BMD_header_index', $session->current_transcription[0]['BMD_header_index'])
											->find();
				
				// any comments found?
				if ( $session->detail_comments )
					{
						// process line comment by line comment
						foreach ( $session->detail_comments as $dc )
							{
								switch ($dc['BMD_comment_type'])
									{
										case 'C':
											// eg #COMMENT(5) reads DUNKLEY or HART for mother's name
											$line_array = ['#COMMENT('.$dc['BMD_comment_span'].') "'.$dc['BMD_comment_text'].'"'];		
											break;
										case 'T':
											$line_array = ['#THEORY('.$dc['BMD_comment_span'].') "'.$dc['BMD_comment_text'].'"'];	
											break;
										case 'N':
											$line_array = ['# "'.$dc['BMD_comment_text'].'"'];		
											break;
										case 'B':
											$line_array = ['+BREAK'];		
											break;
										case 'P':
											$line_array = ['+PAGE', $new_next_page];
											$new_next_page = $new_next_page + 1;
											break;
										case 'R':
											$line_array = ['#THEORY', 'REF', $dc['BMD_comment_text']];	
											break;
									}
								prepare_line($line_array, 'N');
								$csv_string = $csv_string.$session->write_line."\r\n";
							}
					}				
			}		

		// next page line
		$line_array = ['+PAGE', $new_next_page];
		prepare_line($line_array, 'N');
		$csv_string = $csv_string.$session->write_line."\r\n";
		
		// save the csv string to DB
		$csv_file =	$transcription_CSV_file_model
					->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
					->find();
					
		// found?
		if ( $csv_file )
			{
				// if found update
				$data =	[
							'csv_string' => $csv_string,
						];
				$transcription_CSV_file_model->update($session->current_transcription[0]['BMD_header_index'], $data);
			}
		else
			{
				// if not found add
				$data =	[
							'transcription_index' => $session->current_transcription[0]['BMD_header_index'],
							'csv_string' => $csv_string,
						];
				$transcription_CSV_file_model->insert($data);
			}
	}
	
	function freereg_createCSV($type_name_lower, $type_name_upper, $fr_type_code)
	{
		// initialise
		$session = session();
		$project_types_model = new Project_Types_Model();
		$detail_comments_model = new Detail_Comments_Model;
		$transcription_comments_model = new Transcription_Comments_Model();
		$transcription_CSV_file_model = new Transcription_CSV_File_Model();
		$def_fields_model = new Def_Fields_Model();
	
		$new_next_page = $session->current_transcription[0]['BMD_next_page'];
		$csv_string = '';
		$file_name_array = explode('_', $session->current_transcription[0]['BMD_file_name']);
		if ( count($file_name_array) > 1 )
			{
				$file_name = $file_name_array[0].$fr_type_code.$file_name_array[1];
			}
		else
			{
				$file_name = $session->current_transcription[0]['BMD_file_name'];
			}
		
		//// check data lines for ?=unknown characters. Must be last character in field
		//$session->unknown_char = 0;
		//$unknown_char_lines = array();
		//foreach ( $session->detail_data as $dd )
			//{
				//// select event type
				//if ( $dd['data_entry_format'] == $type_name_lower )
					//{
						//// read the transcription_detail_defs
						//foreach ( $session->current_transcription_def_fields as $field_line )
							//{
								//foreach ( $field_line as $field )
									//{
										//// check ? is last character of any word in the field
										//if ( $dd[$field['table_fieldname']] != null )
											//{
												//$field_array = explode(' ', $dd[$field['table_fieldname']]);
												//foreach ( $field_array as $word )
													//{
														//$str_length = strlen($word);
														//$str_position = strpos($word, '?');
														//// was a ? found
														//if ( $str_position !== false )
															//{
																//// there is a ? mark. It must be the last character of the word
																//if ( $str_length != $str_position + 1 )
																	//{
																		//$session->unknown_char = 1;
																		//$unknown_char_lines[$dd['BMD_line_sequence']] = $field['table_fieldname'].' => '.$dd[$field['table_fieldname']];
																	//}
															//}
													//}
											//}
									//}	
							//}
					//}
			//}
		//// was an unknown character found?
		//if ( $session->unknown_char == 1 )
			//{
				//$session->set('message_2', 'Cannot create the CSV file because some fields contain miss-placed unknown characters. The data in error are, ');
				//foreach ( $unknown_char_lines as $line )
					//{
						//$session->message_2 = $session->message_2.$line.', ';
					//}
				//$session->set('message_class_2', 'alert alert-danger');				
				//return;
			//}
		
		// create current used transcription def fields. 
		// This produces an array filled with used fields only.
		create_current_used_transcription_def_fields($type_name_lower);
		
		// first header line, eg +INFO,dreamstogo@gmail.com,Password,SEQUENCED,BIRTHS	
		// create line
		$line_array = ['+INFO', $session->identity_emailid, 'Password', 'SEQUENCED', $type_name_upper, 'UTF-8'];
		prepare_line($line_array, 'N');
		$csv_string = $csv_string.$session->write_line."\r\n";
		
		// second header line, eg ,CCC,name of transcriber,syndicate name,csv file name,start date
		$line_array = ['#', 'CCC', $session->realname, $session->syndicate_name, $file_name.'.CSV', $session->current_transcription[0]['BMD_start_date']];
		prepare_line($line_array, 'N');
		$csv_string = $csv_string.$session->write_line."\r\n";
		
		// third header line, eg CREDIT,Hilary Wright,dreamstogo@gmail.com,dreamstogo, only if syndicate coordinator allows this.
		if ( $session->current_syndicate[0]['BMD_syndicate_credit'] == 'Y' )
			{
				switch ($session->current_project['project_index'])
					{
						case 1:
							$line_array = ['#', "CREDIT", $session->realname, $session->identity_emailid];
							break;
						case 2:
							$line_array = ['#', "CREDIT", $session->identity_userid];
							break;
						case 3:
							$line_array = ['#', "CREDIT", $session->identity_userid];
							break;
					}
				prepare_line($line_array, 'N');
				$csv_string = $csv_string.$session->write_line."\r\n";
			}
			
		// fourth header line, eg. #,csv create date,source,comment
		$comment_text = '';
		$source_text = '';
		$transcription_comments_model = new Transcription_Comments_Model();
		$comment_text_array = $transcription_comments_model
			->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
			->where('comment_sequence', 10)
			->find();
		if ( $comment_text_array ) 
			{ 
				$comment_text = $comment_text_array[0]['comment_text'];
				$source_text = $comment_text_array[0]['source_text']; 
			}
		$line_array = ["#", date("d-M-Y"), $source_text, $comment_text];
		prepare_line($line_array, 'N');
		$csv_string = $csv_string.$session->write_line."\r\n";
		
		// fifth header line, eg ,DEF = the definitions for the fields as on next line
		$line_array = ['#','DEF'];		
		prepare_line($line_array, 'N');
		$csv_string = $csv_string.$session->write_line."\r\n";

		// sixth header line = fields used.
		$line_array = array();
		
		// add mandatory fields
		$mandatory_fields = $def_fields_model
			->where('project_index',  $session->current_project['project_index'])
			->where('data_entry_format', $type_name_lower)
			->where('mandatory', 1)
			->findAll();
		foreach ( $mandatory_fields as $mandatory_field )
			{
				$line_array[] = $mandatory_field['table_fieldname'];
			}
		
		// add variable fields
		foreach ( $session->current_used_transcription_def_fields as $field_line )
			{
				foreach ( $field_line as $field )
					{
						$line_array[] = $field['table_fieldname'];
					}
			}	
			
		// prepare the line	
		prepare_line($line_array, 'N');
		$csv_string = $csv_string.$session->write_line."\r\n";
		
		// now loop through detail lines and write to CSV
		// save the line aray as it contains all the field names I need to load the detail data
		$line_fields = $line_array;
		
		// read each detail line
		foreach ( $session->detail_data as $dd )
			{
				// select event type
				if ( $dd['data_entry_format'] == $type_name_lower )
					{
						$line_array = array();
						// read the line_fields
						foreach ( $line_fields as $field )
							{
								// add to line array
								$line_array[] = $dd[$field];
							}
									
						// prepare and write the line
						prepare_line($line_array, 'Y');
						$csv_string = $csv_string.$session->write_line."\r\n";
					}				
			}
			
		// save the csv string to DB
		$csv_file =	$transcription_CSV_file_model
			->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
			->where('data_entry_format', $type_name_lower)
			->find();
					
		// found?
		if ( $csv_file )
			{
				// if found update
				$transcription_CSV_file_model	
					->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
					->where('data_entry_format', $type_name_lower)
					->set(['csv_string' => $csv_string])
					->set(['csv_file_name' => $file_name])
					->update();
			}
		else
			{
				// if not found add
				$transcription_CSV_file_model
					->set(['project_index' => $session->current_project['project_index']])
					->set(['transcription_index' => $session->current_transcription[0]['BMD_header_index']])
					->set(['identity_index' => $session->BMD_identity_index])
					->set(['data_entry_format' => $type_name_lower])
					->set(['csv_string' => $csv_string])
					->set(['csv_file_name' => $file_name])
					->insert();
			}
	}
	
	function prepare_line($line_array, $quote_first)
	{
		// initialise
		$session = session();
		$session->write_line = '';
		
		// get length of array
		$line_length = count($line_array) - 1;

		// prepare line
		foreach ( $line_array as $key => $value )
			{
				// if value has meaningful CSV character, eg ',', escape with double quotes
				$out_value = $value;
				if ( $value != '' )
					{
						$value_array = explode(',', $value);
						if ( count($value_array) > 1 )
							{
								$out_value = '"'.$value.'"';
							}
					}
				
				if ( $key == 0 AND $quote_first == 'N')
					{
						$session->write_line = $session->write_line.$out_value;
					}
				else
					{
						$session->write_line = $session->write_line.$out_value;
					}
					
				if ( $key != $line_length )
					{
						// add, except for last element in line
						$session->write_line = $session->write_line.',';
					}
			}
	}
	
	function load_current_data_dictionary()
	{
		// initialise
		$session = session();
		$transcription_detail_def_model = new Transcription_Detail_Def_Model();
		$user_data_entry_layouts_model = new User_Data_Entry_Layouts_Model();
		$user_data_entry_layout_fields_model = new User_Data_Entry_Layout_Fields_Model();
		$transcription_current_layout_model = new Transcription_Current_Layout_Model();
		$def_fields_model = new Def_Fields_Model();
		
		// get all data entry fields for this transcription. The DEF format is in the allocation record and the DEF records for this transcription are created when the transcription is created
		// get by field_line
		for ($i = 1; $i <= 10; $i++) 
			{
				$transcription_def_fields = $transcription_detail_def_model
					->where('project_index', $session->current_project['project_index'])
					->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
					//->where('data_entry_format', $session->current_transcription[0]['current_data_entry_format'])
					->where('scan_format', $session->current_allocation[0]['scan_format'])
					->where('field_line', $i)
					->orderby('field_order','ASC')
					->findAll();
				
				if ( $transcription_def_fields )
					{
						$current_transcription_def_fields[$i] = $transcription_def_fields;
					}
			}
			
		// apply current data entry layout to data entry fields but only after a layout has been selected		
		// find current layout index for this transcription and event_type
		$layout_set = $transcription_current_layout_model
			->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
			->where('event_type', $session->current_transcription[0]['current_data_entry_format'])
			->find();	
		// if found then apply layout for a real layout
		if ( $layout_set )
			{					
				// has a layout been selected
				$layout_found = $user_data_entry_layouts_model
					//->where('identity_index', $session->BMD_identity_index)
					->where('event_type', $session->current_transcription[0]['current_data_entry_format'])
					->where('layout_index', $layout_set[0]['current_layout_index'])
					->find();			
				// if found, proceed to apply layout
				if ( $layout_found )
					{					
						// read current_transcripton_def_fields
						$total_lines = count($current_transcription_def_fields);
						for ($i = 1; $i <= $total_lines; $i++) 
							{
								$total_fields_this_line = count($current_transcription_def_fields[$i]);
								$field_order = 1000;
								for ($j = 0; $j < $total_fields_this_line; $j++)
									{
										// first disable field for this event type
										$current_transcription_def_fields[$i][$j]['field_check'] = 'N';
										// set field order - required for sorting
										$current_transcription_def_fields[$i][$j]['field_order'] = $field_order + 10;
										// now enable field for those fields in the current layout
										$layout_field = $user_data_entry_layout_fields_model
											->where('layout_index', $layout_set[0]['current_layout_index'])
											->where('field_name', $current_transcription_def_fields[$i][$j]['table_fieldname'])
											->find();
										if ( $layout_field )
											{
												$current_transcription_def_fields[$i][$j]['field_check'] = 'Y';
												$current_transcription_def_fields[$i][$j]['field_show'] = 'Y';
												// field order can be set by the user when changing the layout so apply it here.
												$current_transcription_def_fields[$i][$j]['field_order'] = $layout_field[0]['field_order'];
											}
									}
								// reindex the array by field order
								usort($current_transcription_def_fields[$i], function($a, $b) 
									{
										return $a['field_order'] <=> $b['field_order'];
									});
							}		 
					}
				else
					{
						// no layout found for this transcription, this event type
						// so reload this transcription def fields from standard data dictionary
						// read current_transcripton_def_fields
						$total_lines = count($current_transcription_def_fields);
						for ($i = 1; $i <= $total_lines; $i++) 
							{
								$total_fields_this_line = count($current_transcription_def_fields[$i]);
								for ($j = 0; $j < $total_fields_this_line; $j++)
									{
										// for this event type
										if ( $current_transcription_def_fields[$i][$j]['data_entry_format'] == $session->current_transcription[0]['current_data_entry_format'] )
											{
												// first disable field for this event type
												$current_transcription_def_fields[$i][$j]['field_check'] = 'N';
												// now enable field as per the standard data dictionary
												$layout_field = $def_fields_model
													->where('project_index', $session->current_project['project_index'])
													->where('data_entry_format', $session->current_transcription[0]['current_data_entry_format'])
													->where('html_id', $current_transcription_def_fields[$i][$j]['html_id'])
													->find();
												if ( $layout_field )
													{
														$current_transcription_def_fields[$i][$j]['field_check'] = 'Y';
														$current_transcription_def_fields[$i][$j]['field_show'] = 'Y';
													}
											}
									}
							}
					}
			}	
			
		// load session
		$session->current_transcription_def_fields = $current_transcription_def_fields;
		$session->current_transcription_def_fields_count = count($current_transcription_def_fields);
	}
	
	function create_current_used_transcription_def_fields($current_data_entry_format)
	{
		// initialise
		$session = session();
		$detail_data_model = new Detail_Data_Model();
		
		// are there any details for the data entry format
		$detail_data =	$detail_data_model
			->where('BMD_header_index',  $session->current_transcription[0]['BMD_header_index'])
			->where('data_entry_format', $current_data_entry_format)
			->findAll();
		
		// remove any non-used data entry fields from the current_transcription_def_fields for show view
		// non used = a column in the detail data that is blank for all records.
		// start by reading the def fields
		// array search details for any non blank entries for the field.
		// if all blank, remove it from def_fields.
		$current_used_transcription_def_fields = array();
		if ( $detail_data )
			{
				foreach ( $session->current_transcription_def_fields as $line_key => $field_line )
					{								
						foreach ( $field_line as $field_key => $field )
							{
								if ( $field['data_entry_format'] == $current_data_entry_format )
									{
										$data_found_for_field = 0;
										foreach ( $detail_data as $values )
											{
												if ( $values[$field['table_fieldname']] != '' )
													{
														$data_found_for_field = 1;
													}
											}
										if ( $data_found_for_field == 1 )
											{
												$current_used_transcription_def_fields[$line_key][$field_key] = $field;
											}
									}
							}
					}
				$session->current_used_transcription_def_fields = $current_used_transcription_def_fields;
			}
		else
			{
				$session->current_used_transcription_def_fields = $session->current_transcription_def_fields;
			}
	}
	
	function set_data_group_and_show($data_group)
	{
		// initialise method
		$session = session();	
		$session->show_data_group = $data_group;
		transcribe_show_step1($session->controller);
	}
	
	function get_upload_status()
	{
		// initialise method
		$session = session();
		$transcription_model = new Transcription_Model();	
		
		// read transcriptions
		foreach ( $session->transcriptions as $transcription )
			{
				// define Mongo
				$mongodb = define_mongodb();
				$submit_status = 'Unknown';
				$submit_message = 'Unknown';

				// has the transcription been processed?
				$collection = $mongodb['database']->selectCollection('physical_files');
				$processed = $collection->find
					(
						[
							'userid' => $session->identity_userid,
							'file_name' => $transcription['BMD_file_name'].'.CSV'
						]
					)->toArray();
				// if it exists = it has been uploaded
				if ( $processed )
					{		
						// now, has it been processed?
						if ( $processed[0]['file_processed'] == true )
							{
								// now, has it errors?
								$collection = $mongodb['database']->selectCollection('freereg1_csv_files');
								$processed = $collection->find
									(
										[
											'userid' => $session->identity_userid,
											'file_name' => $transcription['BMD_file_name'].'.CSV'
										]
									)->toArray();
								if ( $processed )
									{
										switch ($processed[0]['error'])
											{
												case 0: // no errors in uploaded file
													$submit_status = 'OK - no errors';
													$submit_message = 'Processed - no errors.';
													break;
												case 1: // errors have been found
													$submit_status = 'NOK - errors';
													$submit_message = 'Processed - with errors.';
													// get the errors
													$collection = $mongodb['database']->selectCollection('batch_errors');
													$errors = $collection->find
														(
															[
																'freereg1_csv_file_id' => $processed[0]['_id'],
															]
														)->toArray();
													if ( $errors )
														{
															foreach ( $errors as $error )
																{
																	$line = $error['record_number']*10;
																	$submit_message = $submit_message.' Line => '.$line.' Error => '.$error['error_message'];
																}
														}
													break;
											}
									}
								else
									{
										$submit_status = 'NOK - not processed';
										$submit_message = 'FreeREG says processed but no record found.';
									}
							}
						else
							{
								// file has not been processed
								if ( $processed[0]['waiting_to_be_processed'] == true )
									{
										$submit_status = 'OK - queued';
										$submit_message = 'Queued for processing.';
									}
							}
					
						// set upload status in transcription header
						$transcription_model
							->set(['BMD_submit_date' => $session->current_date])
							->set(['BMD_submit_status' => $submit_status])
							->set(['BMD_submit_message' => $submit_message])
							->update($transcription['BMD_header_index']);
					}
			}
	}
	
	function get_number_of_images()
	{
		// initialise method
		$session = session();
		$transcription_model = new Transcription_Model();
		$allocation_images_model = new Allocation_Images_Model();			
		
		// get all image records
		$session->image_records = $allocation_images_model
			->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
			->orderby('original_image_file_name')
			->find();
		// count images
		$session->image_count = 0;
		if ( $session->image_records )
			{
				$session->image_count = count($session->image_records);
			}
		// get current image array key
		$session->current_image_array_key = array_search($session->current_transcription[0]['BMD_scan_name'], array_column($session->image_records, 'image_file_name'));
		// set current image number for display
		$session->current_image_number = $session->current_image_array_key + 1;		
	}
	
	function setup_image_and_parameters()
		{
			// initialise
			$session = session();
			$identity_last_indexes_model = new Identity_Last_Indexes_Model();
			$allocation_images_model = new Allocation_Images_Model();

			// set image parameters
			$session->set('sharpen', $session->current_transcription[0]['BMD_sharpen']);
			$session->set('scroll_step', $session->current_transcription[0]['BMD_image_scroll_step']);
			$session->set('image_y', $session->current_transcription[0]['BMD_image_y']);
			$session->set('image_x', $session->current_transcription[0]['BMD_image_x']);
			$session->set('rotation', $session->current_transcription[0]['BMD_image_rotate']);
			$session->set('image', $session->current_image_file_name);
			
			// only process the image if image scan has changed
			if ( $session->image_processed != $session->image )
				{
					// set creds
					// now need to set creds depending on whether a coordinator is masquerading as one of his transcribers
					if ( $session->masquerade == 1 )
						{
							// masquerade is on, so use coordinator creds 
							$user = rawurlencode($session->coordinator_identity_userid);
							$mdp = rawurlencode($session->coordinator_identity_password);
						}
					else
						{
							// masquerade is off, so use transcriber creds 
							$user = rawurlencode($session->identity_userid);
							$mdp = rawurlencode($session->identity_password);
						}
						
					// set up image info URL
					switch ($session->current_project['project_index'])
						{
							case 1: //FreeBMD
								// set servertype and URL
								$server_split = explode('//', $session->freeukgen_source_values['image_server']);
								// initialse image			
								$url = 	$server_split[0]
										.'//'
										.$user
										.':'
										.$mdp
										.'@'
										.$server_split[1]
										.$session->current_allocation[0]['BMD_reference']
										.$session->current_image_file_name;
								break;
							case 2: //FreeREG
								// get image URL
								$image_records = $allocation_images_model
									->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
									->orderby('original_image_file_name')
									->findAll();
								//$ori_image = explode('_', $session->current_transcription[0]['BMD_scan_name'])[2];
								$ori_image = $session->current_image_file_name; //276
								$session->current_image_array_key = array_search($ori_image, array_column($image_records, 'original_image_file_name'));
								$url = $image_records[$session->current_image_array_key]['image_url'];
								$session->current_image_index = $image_records[$session->current_image_array_key]['image_index'];
								// get number of images in TP
								$session->image_count = count($image_records);
								//$session->image_count = $allocation_images_model
									//->where('transcription_index', $session->current_transcription[0]['BMD_header_index'])
									//->countAllResults();
								break;
							case 3:	//FreeCEN
								break;
						}
									
					$session->url = $url;
				
					// get fields depending on image source
					switch ( $session->current_allocation[0]['source_code'] )
						{
							case 'HC':
								// no images
								break;		
							case 'LP': // local PC - images
							case 'PD': // local PC - PDF
								// get image info to get mime type
								$imageInfo = getimagesize($url);				
								// get image size
								$session->x_size = $imageInfo[0];
								$session->y_size = $imageInfo[1];
								// get mime type
								$session->mime_type = $imageInfo['mime'];
								// encode to base 64
								$session->fileEncode = base64_encode(file_get_contents($url));
								break;
							case 'FS':
							case 'BS':
								$imageInfo = getimagesize($url);
								$session->mime_type = $imageInfo['mime'];
								$session->fileEncode = base64_encode(file_get_contents($url));
								$x_size = $imageInfo[0];
								$y_size = $imageInfo[1];
								break;
						}

					// set the image processed flag
					$session->image_processed = $session->current_image_file_name;
				}
			
			// get current font parameters
			$session->set('enter_font_family', $session->current_transcription[0]['BMD_font_family']);
					
			// set controller
			switch ($session->current_allocation[0]['BMD_type']) 
				{
					case 'B':
						$session->controller = 'births';
						break;
					case 'M':
						$session->controller = 'marriages';
						break;
					case 'D':
						$session->controller = 'deaths';
						break;
				}
				
			// set the identity last indexes by data entry format
			$last_indexes = $identity_last_indexes_model
				->where('identity_index', $session->BMD_identity_index)
				->where('project_index', $session->current_project['project_index'])
				->where('data_entry_format', $session->current_allocation[0]['data_entry_format'])
				->find();
			
			// record found
			if ( $last_indexes )
				{
					// record found, so update
					$identity_last_indexes_model
						->where('identity_index', $session->BMD_identity_index)
						->where('project_index', $session->current_project['project_index'])
						->where('data_entry_format', $session->current_allocation[0]['data_entry_format'])
						->set(['transcription_index' => $session->current_transcription[0]['BMD_header_index']])
						->set(['allocation_index' => $session->current_transcription[0]['BMD_allocation_index']])
						->set(['syndicate_index' => $session->current_transcription[0]['BMD_syndicate_index']])
						->update();
				}
			else
				{
					// record not found, so insert
					$identity_last_indexes_model
						->set(['identity_index' => $session->BMD_identity_index])
						->set(['project_index' => $session->current_project['project_index']])
						->set(['data_entry_format' => $session->current_allocation[0]['data_entry_format']])
						->set(['transcription_index' => $session->current_transcription[0]['BMD_header_index']])
						->set(['allocation_index' => $session->current_transcription[0]['BMD_allocation_index']])
						->set(['syndicate_index' => $session->current_transcription[0]['BMD_syndicate_index']])
						->insert();
				}
		}