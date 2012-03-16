<?php

class Forum extends MX_Controller
{

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('topics_m', 'topics');
		$this->load->model('posts_m', 'posts');
		
		$this->load->library('form');
	}
	
	function index()
	{
		print_r($this->topics->get_topics());
	}
	
	function category($category_id = -1)
	{
		if($category_id == -1)
			show_404();
		print_r($this->topics->get_topics_from_category($category_id));
	}
	
	function topic($id = -1)
	{
		if($id == -1)
			show_404();

		print_r($this->posts->get_messages((int)$id));
	}
	
	function newtopic($category_id)
	{
		#create form
		$this->form
			->open('forum/newtopic/'.(int)$category_id)
			->text('subject', 'the subject of your message', 'trim|max_length[50]|xss_clean')
			->textarea('message', 'Your message', 'trim|xss_clean')
			->indent(200)
			
			->submit()
			->reset()
			
			->model('topics_m', 'newtopic', array('category_id' => (int)$category_id));
		
		$data['form'] = $this->form->get(); // this returns the validated form as a string
		$data['errors'] = $this->form->errors;  // this returns validation errors as a string
		
		print_r($data);

	}
	
	function reaction($topic_id)
	{
		if($topic_id !== $this->input->post('topic_id'))
		{
			#>>failing<< hacker?
			
			isset($_SERVER['HTTP_REFER']) and header('Refresh: 5;URL='.$_SERVER['HTTP_REFER']);
			show_error('someting went wrond, please copy your message and refresh the page');
		}
		
	}
}
