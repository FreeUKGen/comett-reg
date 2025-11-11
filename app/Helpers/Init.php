<?php

namespace App\Helpers;

use App\Models\Freeukgen_Sources_Model;
use App\Models\Messaging_Model;
use App\Models\Parameter_Model;
use App\Models\Projects_Model;

class Init
{
	/**
	 * Where everything gets started
	 * @throws \ReflectionException
	 */
	public function start($project_index = 1): void
	{
		$this->load_projects($project_index);
		$this->load_sources_and_db();
		$this->setup($project_index);
		echo view('templates/header-no-nav');
		echo view('linBMD2/new_signin');
	}

	/**
	 * project_index 0 is BMD
	 * project_index 1 is REG
	 * project_index 2 is CEN
	 *
	 * Based on Home::index()
	**/
	public function load_projects($project_index = 1) 
	{
        $session = session();
        $projects_model = new Projects_Model();

        // destroy the session variables no longer required
        $session->environment = '';
        $session->realname = '';
        $session->signon_success = 0;

        // load time stamp to session
        $session->set('login_time_stamp', time());

        // set heading
        $session->set('title', 'FreeComETT - A FreeUKGen transcription application.');
        $session->set('realname', '');

        // load projects
        $projects = $projects_model->findAll();

		// were any found? if not, this is first use of the system
		if (!$projects)
			var_dump('first_use');

		$session->current_project = $projects[$project_index];
		//@TODO DS 11 Nov 25
		// is $session->projects still useful??
		// $session->projects = $projects;

		// set the project environment - see Identity controller for using the environment parameter
		$session->environment_project = $session->current_project['environment'];

		//@TODO handle JS check - for now assume JS is enabled
        $session->javascript = 'enabled';
	}



	/**
	 * Adapted from Projects::load_projects() - with the BMD stuff removed.
	 * Load Sources into session. NOTE:Projects has already been loaded into session.
	 * @return void
	 */
	public function load_sources_and_db(): void
	{
		$session = session();
		$sources_model = new Freeukgen_Sources_Model();

		// as of V7, FreeComETT has access to the FreeUKGEN source code for each project using a username:password.
		// All source parameters are stored in a table called freeukgen_sources in the FreeComETT DB.
		// access to the source element is by project_index and source_key. This combination must be unique for a source file.
		// the same source file can be defined multiple times but each time with a unique key.
		// all information required to access the source file is in each source table row. This allows for future change in server structure and also for user:password changes per source.
		// common_helper contains 3 methods,
		// get_source_info($project_index, $source_key) = get the table row for this source key
		// get_source_data($source_info) = get the source data using cURL
		// get_source_value($source_data, $source_info[0]['source_section'], $source_info[0]['source_field']) = get the value of the field in the source section in the source data
		// note that the special source_field = $#none#$ = use the $source_data string as is.
		// note that the source_section can also contain $#none#$ = no source section

		// get the source records for this project
		$source_records = $sources_model->where('project_index', $session->current_project['project_index'])->findALL();

		// read source records
		$freeukgen_source_values = [];
		foreach ( $source_records as $source_info ) {
			// get source value
			$source_data = get_source_data($source_info);
			if ( $source_data != 'error' ) $source_value = get_source_value($source_data, $source_info);
			// load source value to array
			$freeukgen_source_values[$source_info['source_key']] = $source_value;
		}

		// load session array
		$session->freeukgen_source_values = $freeukgen_source_values;

		// FreeREG uses a MongoDB database
		// see here for set up details https://www.mongodb.com/compatibility/mongodb-and-codeigniter
		$session->project_DB = 	[
			'hostname' => $session->current_project['DB_hostname'],
			'database' => $session->current_project['DB_database'],
			'port'     => $session->current_project['DB_hostport'],
			'DBDriver' => $session->current_project['DB_driver'],
		];
	}

	/**
	 * AKA Identity::signin_step1
	 * @return void
	 */
	public function setup($start_message)
	{
		$messaging_model = new Messaging_Model();
		$parameter_model = new Parameter_Model();
		$session = session();

		if ($session->javascript == 'disabled')
			return redirect()->to( base_url('home/no_javascript') );
			
		if ($start_message == 0)  {					
			if ( $session->session_expired == 1 ) {
				$session->set('message_2', 'Your session has expired - Time out. Please sign in again to continue.');
				$session->set('message_class_2', 'alert alert-danger');
				$session->set('session_expired', 0);
			}
			else {
				$session->set('message_2', '');
				$session->set('message_class_2', '');
						
				// get today date
				$today = date("Y-m-d");
				// get message to show
				$session->current_message =	$messaging_model
					->where('project_index', $session->current_project['project_index'])
					->where('from_date <=', $today)
					->where('to_date >=', $today)
					->find();
				// set show message if any found
				if ($session->current_message)
					$session->show_message = 'show';
				else
					$session->show_message = '';
					
				// get version from parameters
				$parameter = $parameter_model->where('Parameter_key', 'version')->findAll();
				$session->set('version', $parameter[0]['Parameter_value']);
				
				// load linbmd2 email
				$parameter = $parameter_model->where('Parameter_key', 'linbmd2_email')->findAll();
				$session->set('linbmd2_email', $parameter[0]['Parameter_value']);
			}
		}
	}
}
