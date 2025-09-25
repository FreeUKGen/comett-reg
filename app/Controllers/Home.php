<?php namespace App\Controllers;

use App\Models\Identity_Model;
use App\Models\Parameter_Model;
use App\Models\Projects_Model;
use App\Models\Help_Model;
use App\Models\Speedtest_Results_Model;
use App\Config\Session;

class Home extends BaseController
{
	function __construct() 
	{
        helper('common');
        helper('backup');
    }
	
	public function index()
	{		
		// initialise method
		$session = session();
		$projects_model = new Projects_Model();
		$parameter_model = new Parameter_Model();
		
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
			
		// show view to select project
		echo view('linBMD2/project_select');
	}
	
	public function signout()
	{
		// declare session
		$session = session();
		
		// destroy the session
		$session->destroy();
		
		// clean session files
		// get the session save path
		$sessionSavePath = $session->sessionSavePath;

		// find session files
		foreach( glob($sessionSavePath.'/ci_session*') as $file )
			{
				// check if it is a file
				if( is_file($file) )
					{
						// delete file - not sure I want to do this since the app is no multi user.
						// unlink($file);
					}
			}
		
		// return
		return redirect()->to( base_url('/') );
	}
	
	public function close()
	{
		// declare session
		$session = session();
		
		// destroy the session
		$session->destroy();
		
		// tell user to exit using ALT+F4
		echo view('linBMD2/close');
	}
	
	public function session_exists()
	{
		// declare session
		$session = session();
		
		$session_status = '';
		
		// If realname is not set, it must mean that the session has expired or was never intialised.
		if ( ! $session->has('realname') )
			{
				$session_status = 'session_expired';
				return  json_encode($session_status);
			}
		else
			{
				$session_status = 'session_active';
				return  json_encode($session_status);
			}
	}
	
	public function update_in_progress()
	{
		// declare session
		$session = session();
		
		// show update in progress message
		echo view('linBMD2/update_in_progress');
	}
	
	public function test_javascript()
	{
		// declare session
		$session = session();
		
		// set javascript session variable
		$session->javascript = 'enabled';
		
		return;
	}
	
	public function no_javascript()
	{
		// declare session
		$session = session();
		
		// show no javascript message 
		echo view('linBMD2/no_javascript');
		
		return;
	}
	
	public function issue_step1($start_message)
	{
		// for help see here
		// https://docs.github.com/en/rest/issues/issues?apiVersion=2022-11-28#create-an-issue
		
		// declare session
		$session = session();
		
		// set defaults
		switch ($start_message) 
			{
				case 0:
					// message defaults
					$session->set('message_1', 'You wish to report a problem or make a suggestion: please fill in this form.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->subject1 = '';
					$session->body = '';
					break;
				case 1:
					break;
				case 2:
					break;
				default:
			}
			
		// show view
		echo view('templates/header');
		echo view('linBMD2/issue');
		echo view('templates/footer');	
		return;
	}
	
	public function issue_step2()
	{
		// declare session
		$session = session();
		
		// get inputs
		$session->set('subject1', $this->request->getPost('subject1'));
		$session->set('body', $this->request->getPost('body'));
		$session->set('label', $this->request->getPost('label'));

		// test inputs
		// subject
		if ( $session->subject1 == '' )
			{
				$session->set('message_2', 'You must enter a subject.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('home/issue_step1/1'));
			}
			
		//	request details, removing slashes and sanitize content
		$session->subject1 = trim(htmlspecialchars(stripslashes($session->subject1), ENT_QUOTES));
		$session->body = trim(htmlspecialchars(stripslashes($session->body), ENT_QUOTES));

		// build headers
		$headers = 	array(
						'Accept: application/vnd.github+json',
						'X-GitHub-Api-Version: 2022-11-28',
						'Authorization: Bearer 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',
					);
		
		// build postfields			
		$postfields = json_encode(array(
					'title' => $session->subject1,
					'body' => $session->body,
					'labels' => array($session->current_project[0]['project_name'], $session->environment, $session->identity_userid, $session->label),
					));

		// build cURL
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/FreeUKGen/ComETT/issues");
		curl_setopt($ch, CURLOPT_USERAGENT, "dreams-togo");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		//curl_setopt($ch, CURLOPT_VERBOSE, true);
		//curl_setopt($ch, CURLOPT_STDERR, fopen(getcwd()."/curl.log", 'a+'));
					
		// run the curl
		$curl_result = curl_exec($ch);
		curl_close($ch);
		//$info = curl_getinfo($ch);
	
		// decode response to array
		$curl_result = json_decode($curl_result, true);	

		// get issue number and return
		$session->subject1 = '';
		$session->body = '';
		$session->set('message_2', 'Your report has been registered under reference number FreeUKGen/ComETT/'.$curl_result['number']);
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('home/issue_step1/1'));
	}
	
	public function issue_see($state)
	{
		// declare session
		$session = session();
		$session->issue_state = $state;

		// build headers
		$headers = 	array(
						'Accept: application/vnd.github+json',
						'X-GitHub-Api-Version: 2022-11-28',
						'Authorization: Bearer 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',
					);
					
		// build getfields		
		$getfields = http_build_query(array(
					'per_page' => 100,
					'state' => $state,
					));

		// build cURL
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/FreeUKGen/ComETT/issues?".$getfields);
		curl_setopt($ch, CURLOPT_USERAGENT, "dreams-togo");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($ch, CURLOPT_VERBOSE, true);
		//curl_setopt($ch, CURLOPT_STDERR, fopen(getcwd()."/curl.log", 'a+'));
					
		// run the curl
		$curl_result = curl_exec($ch);
		curl_close($ch);
		
		// decode response to array
		$curl_result = json_decode($curl_result, true);
		
		// for open issues find related issues
		if ( $state = 'open' )
			{					
				// read results to find related issues.
				$related_issues = array();
				foreach ( $curl_result as $issue )
					{						
						// read labels
						foreach ( $issue['labels'] as $label )
							{
								// does this label relate to issue
								if ( substr($label['name'], 0, 1) == '#' )
									{
										// get issue number
										$mother_number = trim(substr($label['name'], 1));
										
										// add issue number to related issues array
										if ( ! array_key_exists($mother_number, $related_issues) )
											{
												$related_issues[$mother_number] = $issue['number'].', ';
											}
										else
											{
												$related_issues[$mother_number] = $related_issues[$mother_number].$issue['number'].', ';
											}
									}
							}
					}
			}
							
		// add to session
		$session->curl_result = $curl_result;
		$session->related_issues = $related_issues;
		$count = count($session->curl_result);			

		// show view
		$session->set('message_1', 'GitHUB feedback for FreeComETT = '.$count.' '.$state.' issues. A max of 100 issues can be shown.');
		$session->set('message_class_1', 'alert alert-primary');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		echo view('templates/header');
		echo view('linBMD2/issue_see');
		echo view('linBMD2/searchTableNew');
		echo view('linBMD2/sortTableNew');
		echo view('templates/footer');	
	}
	
	public function issue_comments_see($issue_number, $issue_title)
	{
		// declare session
		$session = session();
		$session->issue_number = $issue_number;
		$session->issue_title = $issue_title;

		// build headers
		$headers = 	array(
						'Accept: application/vnd.github+json',
						'X-GitHub-Api-Version: 2022-11-28',
						'Authorization: Bearer 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',
					);
					
		// build getfields		
		$getfields = http_build_query(array(
					'per_page' => 100,
					));

		// build cURL
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/FreeUKGen/ComETT/issues/'.$issue_number.'/comments?'.$getfields);
		curl_setopt($ch, CURLOPT_USERAGENT, "dreams-togo");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($ch, CURLOPT_VERBOSE, true);
		//curl_setopt($ch, CURLOPT_STDERR, fopen(getcwd()."/curl.log", 'a+'));
					
		// run the curl
		$curl_result = curl_exec($ch);
		curl_close($ch);

		// decode response to array
		$session->comments_result = json_decode($curl_result, true);
		$count = count($session->comments_result);	

		// show view
		$session->set('message_1', 'GitHUB comments for issue '.$issue_number.':'.$issue_title.' for FreeComETT = '.$count.' comments. A max of 100 comments can be shown.');
		$session->set('message_class_1', 'alert alert-primary');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		echo view('templates/header');
		echo view('linBMD2/issue_comments_see');
		echo view('linBMD2/searchTableNew');
		echo view('linBMD2/sortTableNew');
		echo view('templates/footer');	
	}
	
	public function issue_comment_step1($start_message)
	{
		// for help see here
		// https://docs.github.com/en/rest/issues/issues?apiVersion=2022-11-28#create-an-issue
		
		// declare session
		$session = session();
		
		// set defaults
		switch ($start_message) 
			{
				case 0:
					// message defaults
					$session->set('message_1', 'You wish to add a comment to an existing issue, please fill in this form.');
					$session->set('message_class_1', 'alert alert-primary');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					$session->subject1 = '';
					$session->body = '';
					break;
				case 1:
					break;
				case 2:
					break;
				default:
			}
			
		// show view
		echo view('templates/header');
		echo view('linBMD2/issue_comment');
		echo view('templates/footer');	
		return;
	}
	
	public function issue_comment_step2()
	{
		// declare session
		$session = session();
		
		// get inputs
		$session->set('comment1', $this->request->getPost('comment1'));

		// test inputs
		// subject
		if ( $session->comment1 == '' )
			{
				$session->set('message_2', 'You must enter a comment or return to previous screen.');
				$session->set('message_class_2', 'alert alert-danger');
				return redirect()->to( base_url('/home/issue_comment_step1/1'));
			}
		
		//	request details, removing slashes and sanitize content
		$session->comment1 = trim(htmlspecialchars(stripslashes($session->comment1), ENT_QUOTES));

		// build headers
		$headers = 	array(
						'Accept: application/vnd.github+json',
						'X-GitHub-Api-Version: 2022-11-28',
						'Authorization: Bearer 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',
					);
		
		// build postfields			
		$postfields = json_encode(array(
					'body' => $session->identity_userid.' said: '.$session->comment1,
					));

		// build cURL
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/FreeUKGen/ComETT/issues/'.$session->issue_number.'/comments');
		curl_setopt($ch, CURLOPT_USERAGENT, "dreams-togo");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		//curl_setopt($ch, CURLOPT_VERBOSE, true);
		//curl_setopt($ch, CURLOPT_STDERR, fopen(getcwd()."/curl.log", 'a+'));
					
		// run the curl
		$curl_result = curl_exec($ch);
		curl_close($ch);
		//$info = curl_getinfo($ch);
	
		// decode response to array
		$curl_result = json_decode($curl_result, true);	

		// get issue number and return
		$session->comment1 = '';
		$session->set('message_2', 'Your comment has been registered against issue '.$session->issue_number.'.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('/home/issue_comments_see/'.$session->issue_number.'/'.$session->issue_title));
	}
	
	public function issue_comments_delete($id)
	{
		// declare session
		$session = session();

		// build headers
		$headers = 	array(
						'Accept: application/vnd.github+json',
						'X-GitHub-Api-Version: 2022-11-28',
						'Authorization: Bearer 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',
					);

		// build cURL
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/FreeUKGen/ComETT/issues/comments/".$id);
		curl_setopt($ch, CURLOPT_USERAGENT, "dreams-togo");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");  
		//curl_setopt($ch, CURLOPT_VERBOSE, true);
		//curl_setopt($ch, CURLOPT_STDERR, fopen(getcwd()."/curl.log", 'a+'));
					
		// run the curl
		$curl_result = curl_exec($ch);
		curl_close($ch);

		// get issue number and return
		$session->set('message_2', 'Your comment has been deleted.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('/home/issue_comments_see/'.$session->issue_number.'/'.$session->issue_title));
	}
	
	public function speedtest($start_message)
	{
		// declare session
		$session = session();
		$speedtest_results_model = new Speedtest_Results_Model();
		
		// set defaults
		switch ($start_message) 
			{
				case 0:
					// message defaults
					$session->set('message_1', '');
					$session->set('message_class_1', '');
					$session->set('message_2', '');
					$session->set('message_class_2', '');
					break;
				case 1:
					break;
				case 2:
					break;
				default:
			}
		
		// load stored speedtest this user
		$session->speedtests = $speedtest_results_model
			->where('project_index', $session->current_project[0]['project_index'])
			->where('identity_index', $session->BMD_identity_index)
			->orderby('timestamp', 'DESC')
			->findAll();
		
		// calculate averages
		if ( $session->speedtests )
			{
				$count = count($session->speedtests);
				$total_dl = 0;
				$total_ul = 0;
				$total_ping = 0;
				$total_jitter = 0;
				$total_distance = 0;
				foreach ( $session->speedtests as $speedtest )
					{
						$total_dl = $total_dl + $speedtest['dl'];
						$total_ul = $total_ul + $speedtest['ul'];
						$total_ping = $total_ping + $speedtest['ping'];
						$total_jitter = $total_jitter + $speedtest['jitter'];
						$total_distance = $total_distance + $speedtest['distance'];
					}
				$session->average_dl = $total_dl / $count;
				$session->average_ul = $total_ul / $count;
				$session->average_ping = $total_ping / $count;
				$session->average_jitter = $total_jitter / $count;
				$session->average_distance = $total_distance / $count;
			}

		// show view
		echo view('templates/header');
		echo view('linBMD2/speedtest');
		echo view('templates/footer');	
		return;
	}
	
	public function speedtest_results()
	{
		// store speedtest results
		// initialise
		$session = session();
		$speedtest_results_model = new Speedtest_Results_Model();
				
		// get selected field index and action
		$speedtest_results = json_decode($this->request->getPost('result_data'), true);
	
		// create db fields
		$ispinfo_array = explode('-', $speedtest_results['clientIp']);
		$isp_array = explode('(', $ispinfo_array[1]);
			
		// insert to DB
		$speedtest_results_model
			->set(['project_index' => $session->current_project[0]['project_index']])
			->set(['identity_index' => $session->BMD_identity_index])
			->set(['ip' => trim($ispinfo_array[0])])
			->set(['ispinfo' => trim($isp_array[0])])
			->set(['distance' => trim($isp_array[1], ' km)')])
			->set(['dl' => $speedtest_results['dlStatus']])
			->set(['ul' => $speedtest_results['ulStatus']])
			->set(['ping' => $speedtest_results['pingStatus']])
			->set(['jitter' => $speedtest_results['jitterStatus']])
			->insert();

		$session->set('message_1', 'Speedtest successfully performed. See test history below.');
		$session->set('message_class_1', 'alert alert-success');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		return redirect()->to( base_url('home/speedtest/1') );
	}
}


