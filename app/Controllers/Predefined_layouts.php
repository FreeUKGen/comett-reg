<?php namespace App\Controllers;

use App\Models\Def_Fields_Model;
use App\Models\User_Data_Entry_Layouts_Model;
use App\Models\User_Data_Entry_Layout_Fields_Model;
use App\Models\Transcription_Current_Layout_Model;
use App\Models\Transcription_Cycle_Model;
use App\Models\Project_Types_Model;

class Predefined_layouts extends BaseController
{
	public function manage_predefined_layouts($start_message)
	{		
		// initialise method
		$session = session();
		$user_data_entry_layouts_model = new User_Data_Entry_Layouts_Model();
		$project_types_model = new Project_Types_Model();
		
		// only for FreeREG
		if ( $session->current_project[0]['project_index'] != 2 )
			{
				$session->set('message_2',  'Manage Predefined Layouts is for FreeREG only.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1') );
			}
			
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Predefined Layouts. Use search to find the layout you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('add_event_type', '');
					$session->set('add_predefined_layout', '');
					// get all predefined layouts in layout name within event type sequence
					$session->predefined_layouts = $user_data_entry_layouts_model
						->where('identity_index', 999999)
						->orderby('event_type')
						->orderby('layout_name')
						->findAll();	
						if (  ! $session->predefined_layouts )
							{
								$session->set('message_2',  'No pre-defined layouts found.');
								$session->set('message_class_2', 'alert alert-danger');
								return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );
							}
					// load event types
					$session->event_types = $project_types_model
						->where('project_index', $session->current_project[0]['project_index'])
						->where('type_controller !=', null)
						->findAll();
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Predefined Layouts. Use search to find the layout you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show predefined layouts
		echo view('templates/header');
		echo view('linBMD2/manage_predefined_layouts');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$user_data_entry_layouts_model = new User_Data_Entry_Layouts_Model();
		$user_data_entry_layout_fields_model = new User_Data_Entry_Layout_Fields_Model();
		$transcription_current_layout_model = new Transcription_Current_Layout_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		
		// get inputs
		$session->set('layout_index', $this->request->getPost('layout_index'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'PRELA')
			->find());
		
		// get predefined layout from DB
		$predefined_layout = $user_data_entry_layouts_model
			->where('layout_index',  $session->layout_index)
			->find();
		if ( ! $predefined_layout )
			{
				$session->set('message_2', 'Invalid predefined layout, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'PREDO': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );
					break;
				case 'PRECH': // Change predefined layout
					return redirect()->to( base_url('predefined_layouts/change_predefined_layout_step1/0') );	
					break;
				case 'PREDL': // Delete predefined layout
					// is this predefined layout used?
					$inuse = $transcription_current_layout_model
						->where('current_layout_index', $session->layout_index)
						->find();
						if ( $inuse )
							{
								$session->set('message_2',  'This Predefined layout is currently being used on a transcription, so it cannot be deleted.');
								$session->set('message_class_2', 'alert alert-danger');
								return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );
							}
					// ok to delete
					$user_data_entry_layouts_model->delete($session->layout_index);
					$user_data_entry_layout_fields_model->delete($session->layout_index);
					// reload predefined layouts
					$session->predefined_layouts = $user_data_entry_layouts_model
						->where('identity_index', 999999)
						->orderby('event_type')
						->orderby('layout_name')
						->findAll();
					// return
					$session->set('message_2', 'Predefined Layout, '.$predefined_layout[0]['layout_name'].', has been deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );
					break;	
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$user_data_entry_layouts_model = new User_Data_Entry_Layouts_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find predefined layouts.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );
		}
		
		// get results
		$session->predefined_layouts = $user_data_entry_layouts_model
			->like('layout_name', $session->search, 'after')
			->findAll();
			// anthing found?
			if (  ! $session->predefined_layouts )
			{
				$session->set('message_2',  'No predefined layouts starting with '.$session->search.' were found. Try again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );
			}
		
		// show results
		$session->set('message_2', 'Predefined layouts starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );				
	}
	
	public function change_predefined_layout_step1($start_message)
	{
		// initialise method
		$session = session();
		$user_data_entry_layouts_model = new User_Data_Entry_Layouts_Model();
		$user_data_entry_layout_fields_model = new User_Data_Entry_Layout_Fields_Model();
		$def_fields_model = new Def_Fields_Model();
			
		// is predefined layout already in the DB
		$exists = $user_data_entry_layouts_model
			->where('layout_index', $session->layout_index)
			->find();
			if ( ! $exists )
			{
				$session->set('message_2', 'This predefind layout does not exist.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );	
			}
		
		// get standard defs
		$standard_def = $def_fields_model	
			->where('project_index', $session->current_project[0]['project_index'])
			->where('syndicate_index', null)
			->where('data_entry_format', $exists[0]['event_type'])
			->where('field_line >', 0)
			->orderby('field_order','ASC')
			->find();
					
		// 999999 field order.
		$elements = count($standard_def);
		for ($i = 0; $i < $elements; $i++) 
			{
				$standard_def[$i]['field_order'] = 999999;
			}
			
		// get predefined layout fields
		$fields = $user_data_entry_layout_fields_model
			->where('layout_index', $session->layout_index)
			->find();
		// and apply field order to standard def
		foreach ( $fields as $field )
			{
				$id = array_search($field['field_name'], array_column($standard_def, 'table_fieldname'));
				$standard_def[$id]['field_order'] = $field['field_order'];
			}
		// now sort array on field order
		usort($standard_def, fn($a, $b) => $a['field_order'] <=> $b['field_order']);
		// now remove 999999
		for ($i = 0; $i < $elements; $i++) 
			{
				if ( $standard_def[$i]['field_order'] == 999999 )
					{
						$standard_def[$i]['field_order'] = 0;
					}
			}
		
		$session->standard_def = $standard_def;
		$session->add_event_type = $exists[0]['event_type'];
		$session->add_predefined_layout = $exists[0]['layout_name'];
		
		// choose fields
		$session->set('message_1', '');
		$session->set('message_class_1', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		echo view('templates/header');
		echo view('linBMD2/predefined_layout_select_fields');
		echo view('templates/footer');	
	}
	
	public function change_predefined_layout_step2()
	{
		// initialise method
		$session = session();
		$user_data_entry_layouts_model = new User_Data_Entry_Layouts_Model();
		$user_data_entry_layout_fields_model = new User_Data_Entry_Layout_Fields_Model();
		
		// get data array
		$data_array = json_decode($this->request->getPost('data_object'), true);

		// anything selected?
		if ( count($data_array) > 0 )
			{
				// 1) delete layout fields
				$user_data_entry_layout_fields_model
					->where('layout_index', $session->layout_index)
					->delete();
				// 2) create them
				foreach ( $data_array as $field => $order )
					{
						$user_data_entry_layout_fields_model
							->set(['layout_index' => $session->layout_index])
							->set(['field_name' => $field])
							->set(['field_width' => 0])
							->set(['field_order' => $order])
							->insert();
					}
			}
		// all done
		return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/0') );	
	}
	
	public function add_predefined_layout_step1()
	{
		// initialise method
		$session = session();
		$user_data_entry_layouts_model = new User_Data_Entry_Layouts_Model();
		$def_fields_model = new Def_Fields_Model();
		
		// get input
		$session->set('add_predefined_layout', ucfirst($this->request->getPost('add_predefined_layout')));
		$session->set('add_event_type', $this->request->getPost('add_event_type'));
		$session->set('BMD_cycle_code', '');
		
		// blank?
		if ( $session->add_predefined_layout == '' )
			{
				$session->set('message_2', 'The layout name cannot be blank');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );
			}
			
		// valid event type?
		if ( $session->add_event_type == 'na' )
			{
				$session->set('message_2', 'Please select an event type for this layout.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );
			}
			
		// is predefined layout already in the DB
		$exists = $user_data_entry_layouts_model
			->where('layout_name', $session->add_predefined_layout)
			->where('event_type', $session->add_event_type)
			->where('identity_index', 999999)
			->find();
			if ( $exists )
			{
				$session->set('message_2', 'This predefind layout is already defined for this event type.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/2') );	
			}
		
		// get standard defs
		$standard_def = $def_fields_model	
			->where('project_index', $session->current_project[0]['project_index'])
			->where('syndicate_index', null)
			->where('data_entry_format', $session->add_event_type)
			->where('field_line >', 0)
			->orderby('field_order','ASC')
			->find();
					
		// zero field order. The user will enter the order he wants
		$elements = count($standard_def);
		for ($i = 0; $i < $elements; $i++) 
			{
				$standard_def[$i]['field_order'] = 0;
			}
			
		$session->standard_def = $standard_def;
		
		// choose fields
		$session->set('message_1', '');
		$session->set('message_class_1', '');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		echo view('templates/header');
		echo view('linBMD2/predefined_layout_select_fields');
		echo view('templates/footer');	
	}
	
	public function add_predefined_layout_step2()
	{
		// initialise method
		$session = session();
		$user_data_entry_layouts_model = new User_Data_Entry_Layouts_Model();
		$user_data_entry_layout_fields_model = new User_Data_Entry_Layout_Fields_Model();
		
		// get data array
		$data_array = json_decode($this->request->getPost('data_object'), true);
	
		// anything selected?
		if ( count($data_array) > 0 )
			{
				// 1) create the layout			
				$user_data_entry_layouts_model
					->set(['project_index' => $session->current_project[0]['project_index']])
					->set(['identity_index' => 999999])
					->set(['event_type' => $session->add_event_type])
					->set(['layout_name' => $session->add_predefined_layout])
					->insert();
				$layout_index = $user_data_entry_layouts_model->getInsertID();
				// 2) create layout fields
				foreach ( $data_array as $field => $order )
					{
						$user_data_entry_layout_fields_model
							->set(['layout_index' => $layout_index])
							->set(['field_name' => $field])
							->set(['field_width' => 0])
							->set(['field_order' => $order])
							->insert();
					}
			}
		// all done
		return redirect()->to( base_url('predefined_layouts/manage_predefined_layouts/0') );
	}
}
