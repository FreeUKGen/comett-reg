<?php namespace App\Controllers;

use App\Models\Messaging_Model;
use App\Models\Transcription_Cycle_Model;

class Messaging extends BaseController
{	
	public function manage_messages($start_message)
	{		
		// initialise method
		$session = session();
		$messaging_model = new Messaging_Model();
		
		// set messages
		switch ($start_message) 
			{
				case 0:
					$session->set('message_1', 'Manage Messages.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Manage Messages.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
			}
		
		// get all messages in message from date sequence
		// get today date
		$today = date("Y-m-d");
		// get messages from today
		$session->messages =	$messaging_model
								->where('project_index', $session->current_project[0]['project_index'])
								->where('from_date <=', $today)
								->where('to_date >=', $today)
								->orderby('from_date')
								->findAll();
		// any found?
		if (  ! $session->messages )
			{
				$session->set('message_2',  'No messages found. Create a new one.');
				$session->set('message_class_2', 'alert alert-info');
			}
		// show messages
		echo view('templates/header');
		echo view('linBMD2/manage_messages');
		echo view('templates/footer');
	}
	
	public function next_action()
	{
		// initialise method
		$session = session();
		$messaging_model = new Messaging_Model();
		$transcription_cycle_model = new Transcription_Cycle_Model();
		
		// get inputs
		$message_index = $this->request->getPost('message_index');	
		$session->set('cycle_code', $this->request->getPost('message_next_action'));
		$session->set('cycle_text', $transcription_cycle_model	
									->where('BMD_cycle_code', $session->cycle_code)
									->where('BMD_cycle_type', 'MESSA')
									->find());
		
		// get message
		$session->current_message = $messaging_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('message_index', $message_index)
			->where('project_index', $session->current_project[0]['project_index'])
			->find();
									
		if ( ! $session->current_message )
			{
				$session->set('message_2', 'Invalid message, please select again.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/manage_messages/1') );
			}
			
		// perform action selected
		switch ($session->cycle_code) 
			{
				case 'NONME': // nothing was selected
					$session->set('message_2', 'Please select an action to perform from the dropdown.');
					$session->set('message_class_2', 'alert alert-danger');
					return redirect()->to( base_url('messaging/manage_messages/1') );
					break;
				case 'DELME': // delete message
					$messaging_model->delete($message_index);
					$session->set('message_2', 'Message deleted.');
					$session->set('message_class_2', 'alert alert-success');
					return redirect()->to( base_url('messaging/manage_messages/0') );
					break;
			}
		// no action found
		$session->set('message_2', 'No action performed. Selected action not recognised. Report to '.$session->linbmd2_email);
		$session->set('message_class_2', 'alert alert-warning');
		return redirect()->to( base_url('messaging/manage_messages/1') );			
	}
	
	public function create_message_step1($start_message)
	{		
		// initialise method
		$session = session();		
		
		switch ($start_message) 
			{
				case 0:
					// input values defaults for first time
					$session->set('from_date', '');
					$session->set('to_date', '');
					$session->set('message', '');
					$session->set('colour', '');
					
					// message defaults
					$session->set('message_1', 'Please enter the data required to create your message.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					$session->set('message_1', 'Please enter the data required to create your message.');
					$session->set('message_class_1', 'alert alert-primary');
					break;
				default:
			}
			
		echo view('templates/header');
		echo view('linBMD2/create_message_step1');
		echo view('templates/footer');
	}
	
	public function create_message_step2()
	{
		// initialise method
		$session = session();
		$messaging_model = new Messaging_Model();
		
		// get user data
		$session->set('from_date', $this->request->getPost('from_date'));
		$session->set('to_date', $this->request->getPost('to_date'));
		$session->set('colour', $this->request->getPost('colour'));
		$session->set('message', $this->request->getPost('message'));
	
		// get today
		$today = date("Y-m-d");
		
		// test for from date
		if ( $session->from_date == '' )
			{
				$session->set('message_2', 'From Date cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// explode from date
		$date = explode('-', $session->from_date);
		
		// test for valid from date
		if ( $session->from_date < $today )
			{
				$session->set('message_2', 'From Date not valid. Must be greater than or equal to today\'s date.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// test for enough elements
		if ( count($date) != 3 )
			{
				$session->set('message_2', 'From Date not valid. Must be in yyyy-mm-dd format.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// month OK?
		if ( $date[1] < 01 or $date[1] > 12 )
			{
				$session->set('message_2', 'From Date not valid. Month not valid.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// day OK?
		if ( $date[2] < 01 or $date[2] > 31 )
			{
				$session->set('message_2', 'From Date not valid. Day not valid.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// test for to date
		if ( $session->to_date == '' )
			{
				$session->set('message_2', 'To Date cannot be blank.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// explode to date
		$date = explode('-', $session->to_date);

		// test for valid to date
		if ( $session->to_date <= $today )
			{
				$session->set('message_2', 'To Date not valid. Must be greater than today\'s date.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// test for enough elements
		if ( count($date) != 3 )
			{
				$session->set('message_2', 'To Date not valid. Must be in yyyy-mm-dd format.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// month OK?
		if ( $date[1] < 01 or $date[1] > 12 )
			{
				$session->set('message_2', 'To Date not valid. Month not valid.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// day OK?
		if ( $date[2] < 01 or $date[2] > 31 )
			{
				$session->set('message_2', 'To Date not valid. Day not valid.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// to date greater than from date
		if ( $session->to_date <= $session->from_date )
			{
				$session->set('message_2', 'To Date not valid. Must be greater than From Date.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
		
		// test message text exists
		$session->message = trim($session->message);
		if ( $session->message == '' )
			{
				$session->set('message_2', 'Please enter some text for the message.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// test message length
		if ( strlen($session->message) > 200 )
			{
				$session->set('message_2', 'Message text is too long including line return. Be concise!');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('messaging/create_message_step1/1') );
			}
			
		// All good so write to database
		$data = [
					'project_index' => $session->current_project[0]['project_index'],
					'from_date' => $session->from_date,
					'to_date' => $session->to_date,
					'colour' => $session->colour,
					'message' => $session->message,
				];
		$messaging_model->insert($data);
		
		// go back to sign in
		$session->set('message_2', 'Your Message has been created.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('messaging/manage_messages/0') );
	}
}
