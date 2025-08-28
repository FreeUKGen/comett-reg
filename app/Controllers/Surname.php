<?php namespace App\Controllers;

use App\Models\Surname_Model;
use App\Models\Transcription_Cycle_Model;

class Surname extends BaseController
{
	
	public function manage_surnames($start_message)
	{		
		// initialise method
		$session = session();
		$surname_model = new Surname_Model();
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Surnames - first 100 names shown. Use search to find the name you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get all surnames in surname name sequence - limit to 100
					$session->surnames = $surname_model	->orderby('Surname') ->findAll(100);
					if (  ! $session->surnames )
						{
							$session->set('message_2',  'No surnames found. Have you synced your names database with master?');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('surname/manage_surnames/2') );
						}
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Surnames - first 100 names shown. Use search to find the name you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show surnames
		echo view('templates/header');
		echo view('linBMD2/manage_surnames');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$surname_model = new Surname_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		// get inputs
		$session->set('surname', $this->request->getPost('Surname'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'SURNA')
			->find());
		
		
		// get surname from DB
		$surname_record = $surname_model->where('Surname',  $session->surname)->find();
		if ( ! $surname_record )
			{
				$session->set('message_2', 'Invalid surname, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('surname/manage_surnames/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NONSN': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('surname/manage_surnames/2') );
					break;
				case 'SURCH': // Correct surname
					return redirect()->to( base_url('surname/correct_surname_step1/0') );	
					break;
				case 'SURDL': // Delete surname
					$surname_model->delete($session->surname);
					$session->surnames = $surname_model	->orderby('Surname') ->findAll(100);
					$session->set('message_2', 'Surname, '.$session->surname.', was deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('surname/manage_surnames/2') );
					break;	
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('surname/manage_surnames/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$surname_model = new Surname_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find surnames.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('surname/manage_surnames/2') );
		}
		
		// get results
		$session->surnames = $surname_model	->like('Surname', $session->search, 'after')
																	->findAll();
		// anthing found?
		if (  ! $session->surnames )
		{
			$session->set('message_2',  'No surnames starting with '.$session->search.' were found. Try again.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('surname/manage_surnames/2') );
		}
		
		// show results
		$session->set('message_2', 'Surnames starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		return redirect()->to( base_url('surname/manage_surnames/2') );				
	}
	
	public function correct_surname_step1($start_message)
	{
		// initialise method
		$session = session();
		$surname_model = new Surname_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct Surname.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get surname from DB
					$session->surname_to_corrected = $surname_model->find($session->surname);
					$session->corrected_surname = $session->surname_to_corrected['Surname'];
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct Surname.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show surnames
		echo view('templates/header');
		echo view('linBMD2/correct_surname');
		echo view('templates/footer');		
	}
	
	public function correct_surname_step2()
	{
		// initialise method
		$session = session();
		$surname_model = new Surname_Model();
		
		// get input
		$session->set('corrected_surname', $this->request->getPost('corrected_surname'));
		
		// is corrected surname in the DB
		$session->set('corrected_surname', strtoupper($session->corrected_surname));
		$surname_in_DB = $surname_model	->find($session->corrected_surname);
		if ( $surname_in_DB )
		{
			$session->set('message_2', 'The corrected name is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('surname/correct_surname_step1/2') );	
		}
		
		// update record
		$surname_model->delete($session->surname_to_corrected['Surname']);
		$data =	[
						'Surname' => $session->corrected_surname,
						'Surname_popularity' =>$session->surname_to_corrected['Surname_popularity'],
					];		
		$surname_model->insert($data);
		
		// reload names
		$session->surnames = $surname_model	->orderby('Surname')
																	->findAll(100);
		
		// go round again
		$session->set('message_2', 'The surname has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('surname/manage_surnames/2') );	
	}
}
