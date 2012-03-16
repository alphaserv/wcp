<?php

class Login extends MX_Controller
{

	function _go()
	{
		$scopes = array();
		
		$allow =  $this->input->post('allow_usr');
		if(is_array($allow) && isset($allow[0]) && $allow[0] == 'user_access')
			$scopes[] = 'user';
		unset($allow);

		$allow =  $this->input->post('allow_pub_repo');
		if(is_array($allow) && isset($allow[0]) && $allow[0] == 'pub_repo')
			$scopes[] = 'public_repo';
		unset($allow);
		
		$allow =  $this->input->post('allow_repo');
		if(is_array($allow) && isset($allow[0]) && $allow[0] == 'repo')
			$scopes[] = 'repo';
		unset($allow);		
		
		$allow =  $this->input->post('allow_gist');
		if(is_array($allow) && isset($allow[0]) && $allow[0] == 'gist')
			$scopes[] = 'gist';
		unset($allow);
		
		header('Location: https://accounts.google.com/o/oauth2/auth?response_type=code&client_id='.$this->key.'&redirect_uri='.rawurlencode($this->url).'&scope=https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email');
		#.'&scope='.implode($scopes, ','));
	}
	
	function index()
	{
		require_once dirname(__file__).'/_config.php';
		$this->load->library('form');
		
		$this->form
			->open('auth/google/login')
			->label('auth with github?')
			->checkbox('auth', 'user_access', 'allow this site to auth you with your github account', true, 'required')
			->checkbox('allow_usr', 'user_access', 'see profile information', true)
			->checkbox('allow_pub_repo', 'pub_repo', 'see public repos')
			->checkbox('allow_repo', 'repo', 'see all your repos')
			->checkbox('allow_gist', 'gist', 'allow access to your gists')
			->onsuccess(array($this, '_go'), '')

			->submit('proceed');
		
		echo $this->form->errors;
		echo $this->form->get();
	}
}
