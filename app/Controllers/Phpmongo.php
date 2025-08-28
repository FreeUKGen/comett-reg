<?php namespace App\Controllers;
class Phpmongo extends BaseController
{
	public function index() 
	{
		// Configuration
		$session = session();
		
		// does this project use MongoDB?
		if ( $session->current_project[0]['DB_driver'] == 'mongodb://' )
			{
				$client = new \MongoDB\Client($session->project_DB['DBDriver'].$session->project_DB['hostname'].':'.$session->project_DB['port']);
				print_r($client);
			}
		else
			{
				$session->message_2 = 'Your current project does not use MongoDB';
				$session->message_class_2 = 'alert alert-danger';
				return redirect()->to( base_url('database/database_step1/1') );
			}
	}
}

?>
