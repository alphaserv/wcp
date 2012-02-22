<?php
class Page extends MX_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('page_m', 'page');
		$this->load->library('template');
		$this->load->library('parser');
		$this->load->helper('url');
		$this->load->helper('quick_escape');
	}
	
	function _remap()
	{
		try
		{
			$uri = $this->router->pathstring; #uri->uri_string();
			
			if(!isset($uri[0]) || trim($uri) == '' || $uri[0] != '/')
				$uri = '/'.$uri;
			
			if($uri == '/page' || $uri == '/page/index')
				$uri = '/';
			
			$page = $this->page->get_page($uri);
			
			switch($page->makeup)
			{
				default:
				case 'none' :
					$page->content = htmlentities($page->content);
					break;
				
				case 'html' :
					break;
			
				case 'php':
					ob_start();
					echo eval('?>'.$page->content.'<?php ');
					$page->content = ob_get_contents();

					ob_end_clean();
				break;
			}
			
			switch($this->router->content_type)
			{
				default:
				case 'html':
					$this->template->build('page', array('page' => quick_html_escape($page, array('content'))));
					break;
				
				#ajax!! <3
				case 'json':
					$this->output->set_output(json_encode(quick_html_escape($page, array('content'))));
					break;
				
				case 'xml':
					$xml_pages = new SimpleXMLElement('<pages><page></page></pages>');

					foreach(array('id', 'title', 'date', 'uri', 'content') as $key)
						$xml_pages->page->$key = $page->$key;
					
					$this->output->set_output($xml_pages->asXML());
					break;
			}
		}
		catch(page_not_found_exception $e)
		{
			$this->_404($uri);
		}
	}
	
	private function _404()
	{
		try
		{
			$uri = '/404';
		
			$page = $this->page->get_page($uri);
			
			switch($page->makeup)
			{
				default:
				case 'none' :
					$page->content = htmlentities($page->content);
					break;
				
				case 'html' :
					break;
			
				case 'php':
					ob_start();
					echo eval('?>'.$page->content.'<?php ');
					$page->content = ob_get_contents();

					ob_end_clean();
				break;
			}
			
			switch($this->router->content_type)
			{
				default:
				case 'html':
					$this->template->build('page', array('page' => quick_html_escape($page, array('content'))));
					break;
				
				#ajax!! <3
				case 'json':
					$this->output->set_output(json_encode(array_merge(quick_html_escape($page, array('content')), array('status' => 404))));
					break;
				
				case 'xml':
					$xml_pages = new SimpleXMLElement('<pages><page></page></pages>');

					foreach(array('id', 'title', 'date', 'uri', 'content') as $key)
						$xml_pages->page->$key = $page->$key;
					
					$xml_pages->page['status'] = 404;
					$this->output->set_output($xml_pages->asXML());
					break;
			}
		}
		catch(page_not_found_exception $e)
		{
			show_404();
		}
	}
	

}
