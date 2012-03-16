<?php

class Contact extends MX_Controller
{
	function index()
	{
		$this->load->library('template');
		$this->load->library('Parser');
		$this->load->library('form');
		$this->load->model('site_settings');
		$this->load->helper('url');
		
		#create form
		$this->form
			->open('contact/')
			->text('name', 'your name', 'trim|max_length[50]|xss_clean')
			->text('email', 'your email adress <sub>(won&#39;t be published)</sub>', 'email|trim|max_length[50]|xss_clean')
			
			->text('subject', 'the subject of your message', 'trim|max_length[50]|xss_clean')
			->textarea('message', 'Your message', 'trim|xss_clean')
			->indent(200);
			
		if(1 == (int)$this->site_settings->get_setting('contact_use_captcha', '1'))
			$this->form->recaptcha('Please enter the captcha code');
		
		$this->form
			->submit()
			->reset()
			
			->model('contact_m', 'contact');
			#->onsuccess('redirect', 'contacted');
		
		$data['form'] = $this->form->get(); // this returns the validated form as a string
		$data['errors'] = $this->form->errors;  // this returns validation errors as a string
		
		switch($this->router->content_type)
		{
			default:
			case 'html':
				$this->template
					->set_title('contact us')
					->add_head('<link href="'.base_url('static/form.css').'" rel="stylesheet" type="text/css" />')
					->build('contact_view', $data);
				break;
				
			case 'json':
				$this->output->set_status_header(400);
				$this->output->set_output(json_encode(array('error' => array('code' => 400, 'message' => 'this page is not available for ajax (yet?)'))));
				break;
			
			case 'xml':
				$this->output->set_status_header(400);
				$this->output->set_output('<pages><page status="400" message="this page is not available for ajax (yet?)" /> </pages>');
				break;
		}
	}
}
