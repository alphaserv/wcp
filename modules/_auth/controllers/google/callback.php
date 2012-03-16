<?php


class Callback extends MX_Controller
{

	function index()
	{
		require_once dirname(__file__).'/_config.php';
		$this->load->helper('url');
		$this->load->library('session');
		
		if( (!isset($_GET['code'])) || empty($_GET['code']) || trim($_GET['code']) == '' )
			return redirect('auth/google/login');
			
		print_r($_GET['code']);
		
		$this->load->library('curl');
		
		$request = $this->curl->new_request('http://accounts.google.com/o/oauth2/token');
		
		$request->set_request_method('post');
		
		$request->set_post_var('code', $_GET['code']);
		$request->set_post_var('client_id', $this->key);
		$request->set_post_var('client_secret', $this->secret);
		$request->set_post_var('redirect_uri', $this->url);
		$request->set_post_var('rant_type', 'authorization_code');
		
		try
		{
			$response = $request->execute();
		}
		catch(Exception $e)
		{
			echo 'Error: '.$e->getMessage();
			print_r($request);
		}
		
		foreach(explode('&', $response->get_response()) as $header)
		{
			list($key, $value) = explode('=', $header);
			
			switch($key)
			{
			
				case 'error':
					show_error($value);
					break;
				
				case 'access_token':
					$this->session->set_userdata('github_logged_in', true);
					$this->session->set_userdata('github_access_token', $value);
					break;
				
				default:
					print_r($header);
					break;
			}
		}
		
		//redirect('auth/github/callback/connect');
	}
	
	function connect()
	{		
		$this->load->library('session');
		$this->load->library('curl');
		
		$this->load->model('external_user_m');
		
		if($this->session->userdata('github_logged_in'))
		{
			#https://github.com/api/v2/json OR xml/user/show?access_token=XXX
			$request = $this->curl->new_request('https://github.com/api/v2/xml/user/show?access_token='.$this->session->userdata('github_access_token'));
			$response = $request->execute();
			
			$user = new SimpleXMLElement($response->get_response());
			
			print_r($user);
		}
		else
			show_error('please auth to github first');
	}
}

