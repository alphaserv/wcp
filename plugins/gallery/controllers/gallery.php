<?php

class Gallery extends MX_Controller
{
	function __construct()
	{
		$this->load->model('site_settings');
		$this->load->library('template');
		$this->load->library('Parser');
#		$this->load->library('form');
		#$this->load->model('site_settings');
		$this->load->helper('url');
		$this->load->model('gallery_m');
		
	}
	
	function index()
	{
		#$this->gallery_m->save_img_file(1, dirname(dirname(__file__)).'/test.jpg');
		#$this->gallery_m->save_img_file(2, dirname(dirname(__file__)).'/test.png');
		
		switch($this->router->content_type)
		{
			default:
			case 'html':
				$this->template
					->set_title('Galery overvieuw')
					->add_head('<link href="'.base_url('static/css/caption.css').'" rel="stylesheet" type="text/css" />')
					->build('gallery_view', array('images' => $this->gallery_m->get_db_images()));
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
	
	function img($id = -1, $format = 'full', $action = '')
	{
		try
		{
			switch($action)
			{
				default:
				case 'vieuw':
					$this->template
						->set_title('image TITLE')
						->build('single_img', array('img' => array( 
							'url' => site_url('gallery/img/'.(int)$id.'/thumb/raw'),
							'full_img' => site_url('gallery/img/'.(int)$id.'/full/raw'),
							'title' => 'the IMG!',
							'description' => 'my description'
						)));
					break;
					
				case 'raw':
					if($format == 'thumb')
						$this->output->set_output($this->gallery_m->get_img_file($id, true));
					else
						$this->output->set_output($this->gallery_m->get_img_file($id));
					header('Content-Type: image/jpg');
					break;
			}
		}
		catch(Exception $e)
		{
			print_r($e);
		}
		
#		exit;
	}
}
