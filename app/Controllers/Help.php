<?php namespace App\Controllers;

use App\Models\Help_Model;
use App\Models\Transcription_Cycle_Model;

class Help extends BaseController
{
	public function help_show($start_message)
	{
		// initialise
		$session = session();
		$help_model = new Help_Model();
		$session->help_categories = array('HELP' => 'HELP', 'HOWTO' => 'HOWTO');
		
		// Manage start
		switch ($start_message) 
			{
				case '0':
					// message defaults
					$session->set('message_1', 'Choose the help you wish to see. The document will be opened in a new window.');
					$session->set('message_class_1', 'alert alert-primary');
					//$session->set('message_2', '');
					//$session->set('message_class_2', '');
					
					// get help
					$session->help = $help_model	
						->where('help_project', $session->current_project[0]['project_index'])
						->orderby('help_category', 'ASC')
						->orderby('help_title', 'ASC')
						->findAll();
			
					// any found?
					if ( ! $session->help )
						{
							$session->set('message_2', 'No Help has been defined for your project. Contact your co-ordinator.');
							$session->set('message_class_2', 'alert alert-danger');
						}
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Choose the help you wish to see. The document will be opened in a new window.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
					break;
			}
		
		// show views																
		echo view('templates/header');
		echo view('linBMD2/help_show');
		echo view('templates/footer');
	}
	
	public function help_manage($start_message)
	{		
		// initialise
		$session = session();
		$help_model = new Help_Model();
		$session->help_categories = array('HELP' => 'HELP', 'HOWTO' => 'HOWTO');
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Help.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Help.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// get all help in help sequence
		$session->help = $help_model
			->where('help_project', $session->current_project[0]['project_index'])
			->orderby('help_category')
			->orderby('help_title', 'ASC')
			->findAll();
						
		// any found
		if (  ! $session->help )
			{
				$session->set('message_2',  'No help or howto found. Use add help to add a help entry.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/manage_help/1') );
			}
			
		// show help
		echo view('templates/header');
		echo view('linBMD2/help_manage');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$help_model = new Help_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		
		// get inputs
		$help_index = $this->request->getPost('help_index');
		$session->help_cycle_code = $this->request->getPost('help_next_action');
		$session->help_cycle_text = $transcription_cycle_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->help_cycle_code)
			->where('BMD_cycle_type', 'HELP')
			->find();
			
		// get help line
		$session->current_help = $help_model
			->where('help_index',  $help_index)
			->where('help_project',  $session->current_project[0]['project_index'])
			->find();
			
		// any found
		if ( ! $session->current_help )
			{
				$session->set('message_2', 'Invalid help, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/help_manage/2') );
			}
			
		// perform action selected
		switch ($session->help_cycle_code) 
			{
				case 'NONHE': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('help/help_manage/2') );
					break;
				case 'CHGHE': // change help line
					return redirect()->to( base_url('help/help_change_step1/0') );
					break;
				case 'DELHE': // delete help line
					if ( $session->current_help[0]['help_permanent'] == 'NO' )
						{
							$help_model->delete($help_index);
							$session->set('message_2', 'Help/Howto has been deleted.');
							$session->set('message_class_2', 'alert alert-success');
							return redirect()->to( base_url('help/help_manage/2') );
						}
					else
						{
							$session->set('message_2', 'Help/Howto cannot be deleted. It is marked as a permanent entry.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('help/help_manage/2') );
						}			
					break;
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised. Report to '.$session->linbmd2_email);
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('help/help_manage/2') );			
	}
	
	public function help_create_step1($start_message)
	{
		// initialise method
		$session = session();
		$help_model = new Help_Model();	
		$session->help_categories = array('HELP' => 'HELP', 'HOWTO' => 'HOWTO');
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					// set messages
					$session->set('message_1', 'Create Help/Howto. You can point to any type of document/web page/video etc.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					
					// set fields
					$session->help_category = '';
					$session->help_title = '';
					$session->help_url = '';
					$session->help_permenent = 'NO';
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Create Help/Howto. You can point to any type of document/web page/video etc.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show data-entry 
		echo view('templates/header');
		echo view('linBMD2/help_create');
		echo view('templates/footer');
	}
	
	public function help_create_step2()
	{
		// initialise method
		$session = session();
		$help_model = new Help_Model();
		
		// get entries
		$session->help_category = $this->request->getPost('help_category');
		$session->help_title = $this->request->getPost('help_title');
		$session->help_url = $this->request->getPost('help_url');
		$session->help_permanent = $this->request->getPost('help_permanent');
		
		// test title
		if ( $session->help_title == '' )
			{
				$session->set('message_2', 'Help.Howto title cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/help_create_step1/1') );
			}
		if ( strlen($session->help_title) > 60 )
			{
				$session->set('message_2', 'Help.Howto title cannot be greater than 60 characters.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/help_create_step1/1') );
			}
			
		// test URL
		if ( $session->help_url == '' )
			{
				$session->set('message_2', 'Help.Howto URL cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/help_create_step1/1') );
			}
		if ( strlen($session->help_url) > 200 )
			{
				$session->set('message_2', 'Help.Howto URL cannot be greater than 200 characters.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/help_create_step1/1') );
			}
			
		// test permanent
		$test_array = array("YES", "NO");
		if ( ! in_array($session->help_permanent, $test_array) )
			{
				$session->set('message_2', 'Permanent must be YES or NO. You entered '.$session->help_permanent);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/help_create_step1/1'));
			}
			
		// ALL OK
		// Add to DB
		$help_model
			->set(['help_category' => $session->help_category])
			->set(['help_sequence' => 10])
			->set(['help_project' => $session->current_project[0]['project_index']])
			->set(['help_title' => $session->help_title])
			->set(['help_url' => $session->help_url])
			->set(['help_permanent' => $session->help_permanent])
			->insert();
				
		// return
		$session->set('message_2', 'Help.Howto created successfully. Now test it by clicking on the URL.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('help/help_manage/1') );
	}
	
	public function help_change_step1($start_message)
	{
		// initialise method
		$session = session();
		$help_model = new Help_Model();
		$session->help_categories = array('HELP' => 'HELP', 'HOWTO' => 'HOWTO');
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					// set messages
					$session->set('message_1', 'Change Help/Howto.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Change Help/Howto.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// load fields
		$session->help_category = $session->current_help[0]['help_category'];
		$session->help_title = $session->current_help[0]['help_title'];
		$session->help_url = $session->current_help[0]['help_url'];
		$session->help_permanent = $session->current_help[0]['help_permanent'];
		
		// show data and invite change
		echo view('templates/header');
		echo view('linBMD2/help_change');
		echo view('templates/footer');
	}
	
	public function help_change_step2()
	{
		// initialise method
		$session = session();
		$help_model = new Help_Model();
		
		// get entries
		$session->help_category = $this->request->getPost('help_category');
		$session->help_title = $this->request->getPost('help_title');
		$session->help_url = $this->request->getPost('help_url');
		$session->help_permanent = $this->request->getPost('help_permanent');
		
		// test title
		if ( $session->help_title == '' )
			{
				$session->set('message_2', 'Help.Howto title cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/help_create_step1/1') );
			}
		if ( strlen($session->help_title) > 60 )
			{
				$session->set('message_2', 'Help.Howto title cannot be greater than 60 characters.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/help_create_step1/1') );
			}
			
		// test URL
		if ( $session->help_url == '' )
			{
				$session->set('message_2', 'Help.Howto URL cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/help_create_step1/1') );
			}
		if ( strlen($session->help_url) > 200 )
			{
				$session->set('message_2', 'Help.Howto URL cannot be greater than 200 characters.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/help_create_step1/1') );
			}
			
		// test permanent
		$test_array = array("YES", "NO");
		if ( ! in_array($session->help_permanent, $test_array) )
			{
				$session->set('message_2', 'Permanent must be YES or NO. You entered '.$session->help_permanent);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('help/help_create_step1/1'));
			}
			
		// ALL OK
		// update to DB
		$help_model
			->set(['help_category' => $session->help_category])
			->set(['help_sequence' => 10])
			->set(['help_project' => $session->current_project[0]['project_index']])
			->set(['help_title' => $session->help_title])
			->set(['help_url' => $session->help_url])
			->set(['help_permanent' => $session->help_permanent])
			->update($session->current_help[0]['help_index']);
				
		// return
		$session->set('message_2', 'Help.Howto changed successfully. Now test it by clicking on the URL.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('help/help_manage/1') );
	}
}
