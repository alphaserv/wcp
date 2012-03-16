<?php

class Contact_m extends CI_Model
{
	function contact(&$forum, $data)
	{
		$this->load->model('site_settings');
		$mail_adress = $this->site_settings->get_setting('contact_email', 'user@domain.tld');
		
		$this->load->library('email');

		$this->email->from($data['email'], $data['name']);
		$this->email->to($mail_adress); 

		$this->email->subject($data['subject']);
		$this->email->message($data['message']);	

		if(!$this->email->send())
			$form->add_error('email', 'A contact email could not be sent, please try again');
	}

}
