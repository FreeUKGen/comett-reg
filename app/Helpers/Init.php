<?php

namespace App\Helpers;

use App\Models\Freeukgen_Sources_Model;
use App\Models\Projects_Model;

class Init
{
	/**
	 * @throws \ReflectionException
	 */
	public function start(): void
	{
		// $this->load_freereg($project_name);
		$this->setup();
		echo view('templates/header-no-nav');
		echo view('linBMD2/new_signin');
	}

	/**
	 * AKA Identity::signin_step1
	 *
	 * @return void
	 */
	public function setup()
	{
		// initialise method
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
		$session->set('projects', $projects_model->findAll());

		// were any found? if not, this is first use of the system
		if ( ! $session->projects )
		{
			var_dump('first_use');
		}

		// I need to detect if javascript is enabled in the browser.
		// set a php session variable to disabled
		// add some script to the project select page changing the php session variable to enabled
		// check the variable in identity - if disabled, send user a message.
		$session->javascript = 'disabled';
	}


	/**
	 * Formerly Projects::load_project())
	 * @param string $project
	 * @return void
	 * @throws \ReflectionException
	 */
	public function load_freereg(string $project)
	{
		// initialise method
		$session = session();
		$projects_model = new Projects_Model();
		$sources_model = new Freeukgen_Sources_Model();

		// get project details
		$session->current_project = $projects_model->where('project_index', $project)->find();

		// set the project environment - see Identity controller for using the environment parameter
		$session->environment_project = $session->current_project[0]['environment'];

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
		$source_records = $sources_model->where('project_index', $project)->findALL();

		// read source records
		$freeukgen_source_values = array();
		foreach ( $source_records as $source_info ) {
			// get source value
			$source_data = get_source_data($source_info);

			if ( $source_data != 'error' ) $source_value = get_source_value($source_data, $source_info);
			// load source value to array
			$freeukgen_source_values[$source_info['source_key']] = $source_value;
		}

		// load session array
		$session->freeukgen_source_values = $freeukgen_source_values;
		// set the project DB depending on project selected
		switch ( $session->current_project[0]['project_name']) {
			case 'FreeBMD':
				// The FreeBMD database name changes on each Freebmd update.
				// In order to set the FreeBMD DB up, I have to get the latest (like bmd_12345678) name from a source file.
				// http://ginseng.internal.freeukgen.org.uk:8000/freebmd/status/current_db

				// get database variable from session
				if ($session->freeukgen_source_values['database'] == 'error') {
					// this should never happen but, if it does, use last known DB
					$session->freeukgen_source_values['database'] = $session->current_project[0]['DB_last_known'];
				}

				// update project with last known
				$projects_model
					->where('project_index', $session->current_project[0]['project_index'])
					->set(['DB_last_known' => $session->freeukgen_source_values['database']])
					->update();

				// setup the DB definition
				// project DB
				$session->project_DB = [
					'DSN' => '',
					'hostname' => $session->current_project[0]['DB_hostname'],
					'username' => $session->current_project[0]['DB_username'],
					'password' => $session->current_project[0]['DB_password'],
					'database' => $session->freeukgen_source_values['database'],
					'DBDriver' => $session->current_project[0]['DB_driver'],
					'DBPrefix' => '',
					'pConnect' => false,
					'DBDebug' => (ENVIRONMENT !== 'production'),
					'cacheOn' => false,
					'cacheDir' => '',
					'charset' => 'utf8',
					'DBCollat' => 'utf8_general_ci',
					'swapPre' => '',
					'encrypt' => false,
					'compress' => false,
					'strictOn' => false,
					'failover' => [],
					'port' => $session->current_project[0]['DB_hostport'],
				];
				// syndicate DB
				$session->syndicate_DB = [
					'DSN' => '',
					'hostname' => $session->current_project[0]['DB_hostname'],
					'username' => $session->current_project[0]['DB_username'],
					'password' => $session->current_project[0]['DB_password'],
					'database' => 'syndicate',
					'DBDriver' => $session->current_project[0]['DB_driver'],
					'DBPrefix' => '',
					'pConnect' => false,
					'DBDebug' => (ENVIRONMENT !== 'production'),
					'cacheOn' => false,
					'cacheDir' => '',
					'charset' => 'utf8',
					'DBCollat' => 'utf8_general_ci',
					'swapPre' => '',
					'encrypt' => false,
					'compress' => false,
					'strictOn' => false,
					'failover' => [],
					'port' => $session->current_project[0]['DB_hostport'],
				];
				break;
			case 'FreeREG':
				// FreeREG uses a MongoDB database
				// see here for set up details https://www.mongodb.com/compatibility/mongodb-and-codeigniter
				// project DB
				$session->project_DB = [
					'hostname' => $session->current_project[0]['DB_hostname'],
					'database' => $session->current_project[0]['DB_database'],
					'port' => $session->current_project[0]['DB_hostport'],
					'DBDriver' => $session->current_project[0]['DB_driver'],
				];
				break;
		}

	}
}