<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

/**
 * Issue - specifically, GitHub issues
 * post an issue to GitHub, get issue list from GitHub
 */
class Issue extends BaseController
{
	static string $base_url = 'https://api.github.com/repos/FreeUKGen/';

	static string $githubAccount;
	static string $githubKey;
	static string $githubRepo;

	private bool $stateOk;

	public function __construct()
	{
		self::$githubAccount = getenv('github.account');
		self::$githubKey = getenv('github.key');
		self::$githubRepo = getenv('github.repo');

		//@TODO DS - check credentials during operations, report
		//@TODO      invalidity on user views
		$this->stateOk = false;
		if (self::$githubRepo && self::$githubAccount && self::$githubKey)
			$this->stateOk = true;			
	}

	public function index(): void
	{
		$session = session();
		$session->set('subject1', "");
		$session->set('body', "");
		echo view('templates/header');
		echo view('linBMD2/new_issue');
		echo view('templates/footer');
	}

	public function create(): mixed
	{
		// declare session
		$session = session();

		// get inputs
		$subject = $this->request->getPost('subject');
		if ($subject === '') {
			$session->set('message_2', 'You must enter a subject.');
			$session->set('message_class_2', 'alert alert-danger');
			return redirect()->to( base_url('issue/'));
		}

		$label =  $this->request->getPost('label');
		$subject = trim(htmlspecialchars(stripslashes($subject), ENT_QUOTES));
		$body = trim(htmlspecialchars(stripslashes($this->request->getPost('body')), ENT_QUOTES));

		$session->set('subject1', $subject);
		$session->set('issue_body', $body);

		$postParams = json_encode([
			'title' => $subject,
			'body' => $body,
			'labels' => array($session->current_project['project_name'], $session->environment, $session->identity_userid, $label)
		]);

		$result = $this->curlRequest(null, $postParams);
log_message('info', 'INFO:' . print_r($result, true));

	// get issue number and return
		if (isset($result['number'])) {
			$session->set('message_2', 'Your report has been registered under reference number FreeUKGen/ComETT/'.$result['number']);
			$session->set('message_class_2', 'alert alert-success');
			return redirect()->to( base_url('issue/index'));
		}
		else {
			echo view('templates/header');
			echo view('linBMD2/new_issue');
			echo view('templates/footer');
		}
		return false;
	}

	/**
	 * Formerly Home::issue_see($state)
	 * @param string $state
	 * @return void
	 */
	public function show(string $state): void
	{
		// declare session
		$session = session();
		$session->issue_state = $state;

		$result = $this->curlRequest(http_build_query(['per_page' => 100, 'state' => $state]));

		// for open issues find related issues
		if ($state === 'open')
		{
			// read results to find related issues.
			$related_issues = [];
			foreach ($result as $issue)
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
		$session->curl_result = $result;
		$session->related_issues = $related_issues;
		$count = count($result);

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


	public function comments_form(): void
	{
		echo view('templates/header');
		echo view('linBMD2/issue_comment');
		echo view('templates/footer');
	}

	/**
	 * Formerly issue_comments_see()
	 * @param $issue_number
	 * @param $issue_title
	 * @return void
	 */
	public function show_comments($issue_number, $issue_title): void
	{
		$session = session();

		$result = $this->curlRequest('/' . $issue_number.'/comments?'. http_build_query(['per_page' => 100]));

		// show view
		$session->set('message_1', 'GitHUB comments for issue '.$issue_number.':'.$issue_title.' for FreeComETT = '.count($result).' comments. A max of 100 comments can be shown.');
		$session->set('message_class_1', 'alert alert-primary');
		$session->set('message_2', '');
		$session->set('message_class_2', '');
		echo view('templates/header');
		echo view('linBMD2/issue_comments_see');
		echo view('linBMD2/searchTableNew');
		echo view('linBMD2/sortTableNew');
		echo view('templates/footer');
	}

	public function issue_comments_delete($id)
	{
		// declare session
		$session = session();

		//@TODO handle error return state
		$result = $this->curlRequest('/comments/' . $id);

		$session->set('message_2', 'Your comment has been deleted.');
		$session->set('message_class_2', 'alert alert-success');
		return redirect()->to( base_url('/home/issue_comments_see/'.$session->issue_number.'/'.$session->issue_title));
	}

	/**
	 * Build the CURL request, handles GET and POSTs
	 * GET requests with string $params
	 * POST request with array $params
	 * @param string $queryString
	 * @param string|null $postParams
	 * @return mixed
	 */
	private function curlRequest(?string $queryString, ?string $postParams=null): mixed
	{
		$url = self::URL();
		$ch = curl_init();

		if ($queryString)
			$url = self::$url . $queryString;
		else
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, self::$githubAccount);
		curl_setopt($ch, CURLOPT_HTTPHEADER, self::getHeader());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if (!$queryString) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
		}

		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_STDERR, fopen(getcwd()."/curl.log", 'a+'));

		$result = curl_exec($ch);
		curl_close($ch);
		// decode response to array
		return json_decode($result, true);
	}

	private static function getHeader(): array
	{
		return [
			'Accept: application/vnd.github+json',
			'X-GitHub-Api-Version: 2022-11-28',
			'Authorization: Bearer ' . self::$githubKey
		];
	}

	private static function URL() 
	{
		return self::$base_url . self::$githubRepo . '/Issues';
	}
}
