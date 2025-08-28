<?php namespace App\Controllers;

use App\Models\Condition_Model;
use App\Models\Def_Ranges_Model;
use App\Models\Transcription_Cycle_Model;

class Condition extends BaseController
{
	public function manage_conditions($start_message)
	{		
		// initialise method
		$session = session();
		$condition_model = new Condition_Model();
		$def_ranges_model = new Def_Ranges_Model();
		
		// only for FreeREG
		if ( $session->current_project[0]['project_index'] != 2 )
			{
				$session->set('message_2',  'Manage Conditions is for FreeREG only.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('database/database_step1/1') );
			}
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Conditions - first 100 conditions shown. Use search to find the condition you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get all conditions in condition name sequence - limit to 100
					$session->conditions = $condition_model
						->orderby('Condition') 
						->findAll(100);
						
					if (  ! $session->conditions )
						{
							$session->set('message_2',  'No conditions found.');
							$session->set('message_class_2', 'alert alert-danger');
							return redirect()->to( base_url('condition/manage_conditions/2') );
						}
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Conditions - first 100 conditions shown. Use search to find the condition you are looking for.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show conditions
		echo view('templates/header');
		echo view('linBMD2/manage_conditions');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$condition_model = new Condition_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		// get inputs
		$session->set('condition', $this->request->getPost('Condition'));
		$session->set('BMD_cycle_code', $this->request->getPost('BMD_next_action'));
		// get cycle text
		$session->set('BMD_cycle_text', $transcription_cycle_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('BMD_cycle_code', $session->BMD_cycle_code)
			->where('BMD_cycle_type', 'CONNA')
			->find());
		
		// get condition from DB
		$condition_record = $condition_model
			->where('Condition',  $session->condition)
			->find();
		if ( ! $condition_record )
			{
				$session->set('message_2', 'Invalid condition, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('condition/manage_conditions/2') );
			}
		
		// perform action selected
		switch ($session->BMD_cycle_code) 
			{
				case 'NOCON': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('condition/manage_conditions/2') );
					break;
				case 'CONCH': // Correct condition
					return redirect()->to( base_url('condition/correct_condition_step1/0') );	
					break;
				case 'CONDL': // Delete condition
					$condition_model->delete($session->condition);
					$session->conditions = $condition_model	
						->orderby('Condition') 
						->findAll(100);
					$session->set('message_2', 'Condition, '.$session->condition.', was deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('condition/manage_conditions/2') );
					break;	
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised.');
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('condition/manage_conditions/2') );			
	}
	
	public function search()
	{
		// initialise method
		$session = session();
		$condition_model = new Condition_Model();
		
		// get input
		$session->set('search', $this->request->getPost('search'));
		
		// test not empty
		if ( empty($session->search) )
		{
			$session->set('message_2',  'No search entered. Please enter a search to find conditions.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('condition/manage_conditions/2') );
		}
		
		// get results
		$session->conditions = $condition_model	
			->like('Condition', $session->search, 'after')
			->findAll();
		// anthing found?
		if (  ! $session->conditions )
		{
			$session->set('message_2',  'No conditions starting with '.$session->search.' were found. Try again.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('condition/manage_conditions/2') );
		}
		
		// show results
		$session->set('message_2', 'Conditions starting with the search, '.$session->search);
		$session->set('message_class_2', 'alert alert-warning');
		$session->set('search', '');
		return redirect()->to( base_url('condition/manage_conditions/2') );				
	}
	
	public function correct_condition_step1($start_message)
	{
		// initialise method
		$session = session();
		$condition_model = new Condition_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Correct Condition.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->set('search', '');
					// get condition from DB
					$session->condition_to_corrected = $condition_model
						->find($session->condition);
					$session->corrected_condition = $session->condition_to_corrected['Condition'];
					$session->corrected_condition_sex = $session->condition_to_corrected['condition_sex'];
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Correct Condition.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// show conditions
		echo view('templates/header');
		echo view('linBMD2/correct_condition');
		echo view('templates/footer');		
	}
	
	public function correct_condition_step2()
	{
		// initialise method
		$session = session();
		$condition_model = new Condition_Model();
		
		// get input
		$session->set('corrected_condition', $this->request->getPost('corrected_condition'));
		$session->set('corrected_condition_sex', $this->request->getPost('corrected_condition_sex'));
		
		// is corrected sex valid
		$list = ['m', 'f', 'b'];
		if ( ! in_array($session->corrected_condition_sex, $list) )
			{
				$session->set('message_2', 'The corrected Applies to is not valid. Must be m/f/b.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('condition/correct_condition_step1/2') );	
		}
		
		// delete and insert record
		$condition_model
			->delete($session->condition_to_corrected['Condition']);
		$data =	[
					'Condition' => $session->corrected_condition,
					'Condition_popularity' =>$session->condition_to_corrected['Condition_popularity'],
					'condition_sex' => $session->corrected_condition_sex,
				];		
		$condition_model->insert($data);
		
		// reload conditions
		$session->conditions = $condition_model	
			->orderby('Condition')
			->findAll(100);
		
		// go round again
		$session->set('message_2', 'The condition has been corrected.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('condition/manage_conditions/2') );	
	}
	
	public function add_condition()
	{
		// initialise method
		$session = session();
		$condition_model = new Condition_Model();
		
		// get input
		$session->set('add_condition', ucfirst($this->request->getPost('add_condition')));
		$session->set('add_condition_sex', $this->request->getPost('add_condition_sex'));
		
		// blank?
		if ( $session->add_condition == '' )
			{
				$session->set('message_2', 'The entry cannot be blank');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('condition/manage_conditions/2') );
			}
		
		// is add condition in the DB
		$condition_in_DB = $condition_model	
		->find($session->add_condition);
		if ( $condition_in_DB )
		{
			$session->set('message_2', 'The condition is already in the Database');
			$session->set('message_class_2', 'alert alert-warning');
			return redirect()->to( base_url('condition/manage_conditions/2') );	
		}
		
		// is corrected sex valid
		$list = ['m', 'f', 'b'];
		if ( ! in_array($session->add_condition_sex, $list) )
			{
				$session->set('message_2', 'The Applies to is not valid. Must be m/f/b.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('condition/manage_conditions/2') );	
		}
		
		// add record
		$data =	[
						'Condition' => $session->add_condition,
						'condition_sex' => $session->add_condition_sex,
						'Condition_popularity' => 0,
					];		
		$condition_model->insert($data);
		
		// reload conditions
		$session->conditions = $condition_model	
			->orderby('Condition')
			->findAll(100);
		
		// go round again
		$session->add_condition = '';
		$session->add_condition_sex = '';
		$session->set('message_2', 'The condition has been added.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('condition/manage_conditions/2') );	
	}
}
