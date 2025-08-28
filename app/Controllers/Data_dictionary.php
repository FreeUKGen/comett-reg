<?php namespace App\Controllers;

use App\Models\Project_Types_Model;
use App\Models\Projects_Model;
use App\Models\Def_Fields_Model;
use App\Models\Def_Categories_Model;
use App\Models\Def_Category_Field_Attributes_Model;

class Data_dictionary extends BaseController
{
	public function manage_data_dictionary($start_message)
	{		
		// initialise method
		$session = session();
		$project_types_model = new Project_Types_Model();
		$def_categories_model = new Def_Categories_Model();
		
		// only for FreeREG
		if ( $session->current_project[0]['project_index'] != 2 )
			{
				$session->set('message_2',  'Manage Data Dictionary is for FreeREG only.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1') );
			}
			
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', '');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', '');
					$session->set('message_class_1', '');
					break;
			}
			
		// get all project types
		$session->project_types = $project_types_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('fr_type_code !=', '')
			->orderby('type_order')
			->findAll();
			// any found
			if (  ! $session->project_types )
				{
					$session->set('message_2',  'No Event Types found; cannot load data dictionary. Please contact the FreeComETT adminstrator on '.$session->linbmd2_email.'  to report this issue => No project types found in Data_dictionary::manage_data_dictionary.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('data_dictionary/manage_data_dictionary/1') );
				}
					
		// load categories
		$session->categories = $def_categories_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('def_category_active', 'Y')
			->orderby('def_category_order')
			->findAll();
			// any found
			if ( ! $session->categories )
				{
					$session->set('message_2', 'No Data Ditionary Categories found; cannot load data dictionary. Please contact the FreeComETT adminstrator on '.$session->linbmd2_email.'  to report this issue => No categories found in Data_dictionary::load_event_fields.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('data_dictionary/manage_data_dictionary/1') );
				}
		
		// load event type fields - show Baptism fields first
		$this->set_eventtype(5);
	}
	
	public function load_event_fields()
	{
		// initialise method
		$session = session();
		$def_fields_model = new Def_Fields_Model();
		$def_category_field_attributes_model = new Def_Category_Field_Attributes_Model();
	
		// get event fields from data dictionary
		$session->event_fields = $def_fields_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('data_entry_format',  $session->eventtype)
			->orderby('field_order', 'ASC')
			->findAll();
			// any found
			if ( ! $session->event_fields )
				{
					$session->set('message_2', 'No Event Fields found for the event type you selected; cannot load data dictionary. Please contact the FreeComETT adminstrator on '.$session->linbmd2_email.'  to report this issue => No event fields found in Data_dictionary::load_event_fields.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('data_dictionary/manage_data_dictionary/1') );
				}
				
		// set category - show definiton category attributes first
		$this->set_category(1);
	}
	
	public function load_field_attributes()
	{
		// initialise method
		$session = session();
		$def_category_field_attributes_model = new Def_Category_Field_Attributes_Model();
		
		// get field attributes for this category
		$session->field_parameters = $def_category_field_attributes_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('category_index', $session->category_index)
			->orderby('attribute_order')
			->findAll();
			// any found
			if ( ! $session->field_parameters )
				{
					$session->set('message_2', 'No Data Dictionary Field Attributes found; cannot load data dictionary. Please contact the FreeComETT adminstrator on '.$session->linbmd2_email.'  to report this issue => No field attributes found in Data_dictionary::load_event_fields.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('data_dictionary/manage_data_dictionary/1') );
				}

		// show event types
		$session->field_count = count($session->event_fields);
		$session->set('message_1', '');
		$session->set('message_class_1', '');
		echo view('templates/header');
		echo view('linBMD2/manage_DD_select_event_field');
		echo view('linBMD2/searchTableNew');
		echo view('templates/footer');	
	}
	
	public function set_category($new_category)
	{
		// initialise method
		$session = session();
		$def_categories_model = new Def_Categories_Model();
		// get category record
		$category = $def_categories_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('def_category_index', $new_category)
			->find();
			// any found
			if ( ! $category )
				{
					$session->set('message_2', 'Cannot load category definition; cannot load data dictionary. Please contact the FreeComETT adminstrator on '.$session->linbmd2_email.'  to report this issue => No field attributes found in Data_dictionary::set_category.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('data_dictionary/manage_data_dictionary/1') );
				}
		// set session
		$session->category_index = $category[0]['def_category_index'];
		$session->category = $category[0]['def_category_name'];
		// load event field attributes
		$session->set('message_1', '');
		$session->set('message_class_1', '');
		$this->load_field_attributes();
	}
	
	public function set_eventtype($new_eventtype)
	{
		// initialise method
		$session = session();
		$project_types_model = new Project_Types_Model();
		// get eventtype record
		$eventtype = $project_types_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('type_index', $new_eventtype)
			->find();
			// any found
			if ( ! $eventtype )
				{
					$session->set('message_2', 'Cannot load event type definition; cannot load data dictionary. Please contact the FreeComETT adminstrator on '.$session->linbmd2_email.'  to report this issue => No field attributes found in Data_dictionary::set_eventtype.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('data_dictionary/manage_data_dictionary/1') );
				}
		// set session
		$session->eventtype_index = $eventtype[0]['type_index'];
		$session->eventtype = $eventtype[0]['type_name_lower'];
		// load event fields
		$session->set('message_1', '');
		$session->set('message_class_1', '');
		$this->load_event_fields();
	}
	
	public function set_category_index()
	{
		// initialise method
		$session = session();
		// set category
		$this->set_category($this->request->getPost('new_category'));
	}
	
	public function set_eventtype_index()
	{
		// initialise method
		$session = session();
		// set category
		$this->set_eventtype($this->request->getPost('new_eventtype'));
	}
	
	public function enter_parameters_step()
	{
		// update data dictionary
		// initialise
		$session = session();
		$def_fields_model = new Def_Fields_Model();
		//$data_group_model = new Data_Group_Model();
				
		// get data array
		$data_array = json_decode($this->request->getPost('data_object'), true);

		// read data array
		foreach ( $data_array as $key=>$row )
			{
				// update fields
				$def_fields_model->update($key, $row);
			}
			
		// reload data dictionary
		$session->event_fields = $def_fields_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('data_entry_format',  $session->eventtype)
			->orderby('field_order', 'ASC')
			->findAll();
			// any found
			if ( ! $session->event_fields )
				{
					$session->set('message_2', 'No Event Fields found for the event type you selected; cannot load data dictionary. Please contact the FreeComETT adminstrator on '.$session->linbmd2_email.'  to report this issue => No event fields found in Data_dictionary::enter_parameters_step.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('data_dictionary/manage_data_dictionary/1') );
				}
			
		// return
		$session->set('message_2', 'The Data Dictionary has been changed to reflect your choices.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('data_dictionary/load_field_attributes/' ) );
	}
	
	
}
