<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Sessionexists implements FilterInterface
	{
		public function before(RequestInterface $request, $arguments = null)
			{
				$session = session();
		
				// If realname is not set, it must mean that the session has expired or was never intialised.
				if ( ! isset($session->realname) )
					{
						return redirect()->to( base_url('home/index') );
					}
			}
			
		public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
			{
			
			}
	}
