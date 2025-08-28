<?php namespace App\Controllers;

use App\Models\Title_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Transcription_Cycle_Model;

class Title extends BaseController
{
	public function manage_titles($start_message)
	{		
		// initialise method
		$session = session();
		$title_model = new Title_Model();
		$def_ranges_model = new Def_Ranges_Model();
		
		// only for FreeREG
		if ( $session->current_project[0]['project_index'] != 2 )
			{
				$session->set('message_2',  'Manage Titles is for FreeREG only.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1') );
			}
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Titles - first 100 titles shown. Use search to find the title you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get all titles in title name sequence - limit to 100
					$session->titles = $title_model
						->orderby('Title') 
						->findAll(100);
						
					if (  ! $session->titles )
						{
							$session->set('message_2',  'No titles found.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('title/manage_titles/2') );
						}
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Titles - first 100 titles shown. Use search to find the title you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show titles
		echo view('templates/header');
		echo view('linBMD2/manage_titles');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$title_model = new Title_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		// get inputs
		$session->set('title', $this->request->getPost('Title'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model	
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'TITNA')
			->find());
		
		// get title from DB
		$title_record = $title_model
			->where('Title',  $session->title)
			->find();
		if ( ! $title_record )
			{
				$session->set('message_2', 'Invalid title, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('title/manage_titles/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NOTIT': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('title/manage_titles/2') );
					break;
				case 'TITCH': // Correct title
					return redirect()->to( base_url('title/correct_title_step1/0') );	
					break;
				case 'TITDL': // Delete title
					$title_model->delete($session->title);
					$session->titles = $title_model	
						->orderby('Title') 
						->findAll(100);
					$session->set('message_2', 'Title, '.$session->title.', was deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('title/manage_titles/2') );
					break;	
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('title/manage_titles/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$title_model = new Title_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find titles.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('title/manage_titles/2') );
		}
		
		// get results
		$session->titles = $title_model	
			->like('Title', $session->search, 'after')
			->findAll();
		// anthing found?
		if (  ! $session->titles )
		{
			$session->set('message_2',  'No titles starting with '.$session->search.' were found. Try again.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('title/manage_titles/2') );
		}
		
		// show results
		$session->set('message_2', 'Titles starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		return redirect()->to( base_url('title/manage_titles/2') );				
	}
	
	public function correct_title_step1($start_message)
	{
		// initialise method
		$session = session();
		$title_model = new Title_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct Title.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get title from DB
					$session->title_to_corrected = $title_model
					->find($session->title);
					$session->corrected_title = $session->title_to_corrected['Title'];
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct Title.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show titles
		echo view('templates/header');
		echo view('linBMD2/correct_title');
		echo view('templates/footer');		
	}
	
	public function correct_title_step2()
	{
		// initialise method
		$session = session();
		$title_model = new Title_Model();
		
		// get input
		$session->set('corrected_title', $this->request->getPost('corrected_title'));
		
		// is corrected title in the DB
		$session->set('corrected_title', $session->corrected_title);
		$title_in_DB = $title_model	
		->find($session->corrected_title);
		if ( $title_in_DB )
		{
			$session->set('message_2', 'The corrected title is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('title/correct_title_step1/2') );	
		}
		
		// update record
		$title_model
			->delete($session->title_to_corrected['Title']);
		$data =	[
						'Title' => $session->corrected_title,
						'Title_popularity' =>$session->title_to_corrected['Title_popularity'],
					];		
		$title_model->insert($data);
		
		// reload titles
		$session->titles = $title_model	
			->orderby('Title')
			->findAll(100);
		
		// go round again
		$session->set('message_2', 'The title has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('title/manage_titles/2') );	
	}
	
	public function add_title()
	{
		// initialise method
		$session = session();
		$title_model = new Title_Model();
		
		// get input
		$session->set('add_title', ucfirst($this->request->getPost('add_title')));
		
		// blank?
		if ( $session->add_title == '' )
			{
				$session->set('message_2', 'The entry cannot be blank');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('title/manage_titles/2') );
			}
		
		// is add title in the DB
		$title_in_DB = $title_model	
		->find($session->add_title);
		if ( $title_in_DB )
		{
			$session->set('message_2', 'The title is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('title/manage_titles/2') );	
		}
		
		// add record
		$data =	[
						'Title' => $session->add_title,
						'Title_popularity' => 0,
					];		
		$title_model->insert($data);
		
		// reload titles
		$session->titles = $title_model	
			->orderby('Title')
			->findAll(100);
		
		// go round again
		$session->add_title = '';
		$session->set('message_2', 'The title has been added.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('title/manage_titles/2') );	
	}
}
