<?php
class Write extends MX_Controller
{
	function __construct()
	{
		$this->load->library('template');
		$this->load->library('Parser');
		$this->load->library('form');
		$this->load->model('site_settings');
		$this->load->model('page_m');
		$this->load->helper('url');
		$this->load->helper('quick_escape');
	}

	function index()
	{
		$valid_types = array('none', 'html', 'php');
		
		$this->load->library('form');
		
		$this->form
			->open('page/write')
			->text('uri', lang_string('write_form_path', 'path to your page'), 'trim|xss_clean')#TODO:prefix with username for non-admins
			->text('title', lang_string('write_form_title', 'title of your page'), 'trim|min_lenght[10]|xss_clean')
			->textarea('content', lang_string('write_form_content', 'the content of your page'), 'trim|min_lenght[10]')
			->select('makeup', $valid_types, lang_string('write_form_markup', 'allow markup'), 'none')
			
			->submit(lang_string('write_form_save', 'save'), 'submit_post')
			->submit(lang_string('write_form_previeuw','previeuw'), 'submit')
			->model('page_write_m', 'newpage', array('valid_types' => $valid_types));

		$data['form'] = $this->form->get(); // this returns the validated form as a string
		$data['errors'] = $this->form->errors;  // this returns validation errors as a string
		
		$this->template
			->set_title(lang_string('write_title_new', 'New page'))
			->add_head('<link href="'.base_url('static/form.css').'" rel="stylesheet" type="text/css" />')
			->build('contact/contact_view', $data);
	}
	
	function edit_page($id, $post = 'vieuw')
	{

		$valid_types = array('none', 'html', 'php');
		
		$this->load->library('form');
		
		$id = (int)$id;
		
		$this->form
			->open('page/write/edit_page/'.$id.'/post');
			
		if($post != 'post')
		{
			$page = $this->page_m->get_page_by_id($id);
			
			$this->form
				->text('uri', lang_string('write_form_path', 'path to your page'), 'trim|xss_clean', $page->uri)#TODO:prefix with username for non-admins
				->text('title', lang_string('write_form_title', 'title of your page'), 'trim|min_lenght[10]|xss_clean', $page->title)
				->textarea('content', lang_string('write_form_content', 'the content of your page'), 'trim|min_lenght[10]', $page->content)
				->select('makeup', $valid_types, lang_string('write_form_markup', 'allow markup'), 'none', array_search($page->makeup, $valid_types	));
		
		}
		else
		{
			$this->form
				->text('uri', lang_string('write_form_path', 'path to your page'), 'trim|xss_clean')#TODO:prefix with username for non-admins
				->text('title', lang_string('write_form_title', 'title of your page'), 'trim|min_lenght[10]|xss_clean')
				->textarea('content', lang_string('write_form_content', 'the content of your page') , 'trim|min_lenght[10]')
				->select('makeup', $valid_types, lang_string('write_form_markup', 'allow markup'), 'none');
		}
		$this->form
			->submit(lang_string('write_form_save', 'save'), 'submit_post')
			->submit(lang_string('write_form_previeuw','previeuw'), 'submit')
			->model('page_write_m', 'update_page', array('valid_types' => $valid_types, 'id' => $id));

		$data['form'] = $this->form->get(); // this returns the validated form as a string
		$data['errors'] = $this->form->errors;  // this returns validation errors as a string
		
		$this->template
			->set_title(lang_string('write_title_edit', 'Edit page'))
			->add_head('<link href="'.base_url('static/form.css').'" rel="stylesheet" type="text/css" />')
			->build('contact/contact_view', $data);

	}
}
