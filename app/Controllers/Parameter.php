<?php namespace App\Controllers;

use App\Models\Parameter_Model;

class Parameter extends BaseController
{	
	public function manage_parameters_step1($start_message)
	{		
		// initialise method
		$session = session();
		$parameter_model = new Parameter_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage FreeComETT Global Parameters.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');				
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage FreeComETT Global Parameters');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
			
		// get all parameters
		$session->parameters =	$parameter_model
								->findAll();
		
		// any found								
		if (  ! $session->parameters )
			{
				$session->set('message_2',  'No FreeComETT Global Parameters found. Please report to '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('parameters/manage_parameters_step1/2') );
			}			
			
		// show parameters
		echo view('templates/header');
		echo view('linBMD2/manage_parameters');
		echo view('templates/footer');
	}
	
	public function manage_parameters_step2($parameter_key)
	{		
		// initialise method
		$session = session();
		$parameter_model = new Parameter_Model();

		// get parameter value
		$parameter_value =	$parameter_model
							->where('Parameter_key', $parameter_key)
							->find();
		// found?
		if ( ! $parameter_value )
			{
				$session->set('message_2', 'Sorry I cannot find the parameter you selected. Please report to '.$session->linbmd2_email);
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('parameter/manage_parameters_step1/2') );
			}
		
		// set fields
		$session->parameter_key = $parameter_key;
		$session->current_parameter_value = $parameter_value[0]['Parameter_value'];
		$session->new_parameter_value = $parameter_value[0]['Parameter_value'];
		$session->allowed_parameter_values = $parameter_value[0]['Parameter_allowed_values'];

		// show districts
		$session->set('message_2', '');
		echo view('templates/header');
		echo view('linBMD2/change_parameter_step1');
		echo view('templates/footer');
	}
	
	public function manage_parameters_step3()
	{		
		// initialise method
		$session = session();
		$parameter_model = new Parameter_Model();

		// get input
		$session->new_parameter_value = $this->request->getPost('new_parameter_value');
		
		// blank?
		if ( $session->new_parameter_value == '' )
			{
				$session->set('message_2', 'Parameter cannot be blank. Are you sure you know what you are doing? If you are wise, back out NOW!');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('parameter/manage_parameters_step1/2') );
			}		
		
		// update parameter
		$parameter_model->where('Parameter_key', $session->parameter_key)
						->set(['Parameter_value' => $session->new_parameter_value])
						->update();

		// go round again
		return redirect()->to( base_url('parameter/manage_parameters_step1/0') );
	}
}
