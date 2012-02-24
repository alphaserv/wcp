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
		$this->load->helper('quick_escape');
		
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
					#->add_head('<link href="'.base_url('static/css/caption.css').'" rel="stylesheet" type="text/css" />')
					->build('gallery_view', array('images' => quick_html_escape($this->gallery_m->get_db_images())));
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
			$img = $this->gallery_m->get_db_image($id);
			switch($action)
			{
				default:
				case 'vieuw':
					$this->template
						->set_title('image TITLE')
						->build('single_img', array('img' => array(
							'id' => (int)$img->id,
							'url' => site_url('gallery/img/'.(int)$img->id.'/thumb/raw'),
							'full_img' => site_url('gallery/img/'.(int)$img->id.'/full/raw'),
							'title' => htmlentities($img->name),
							'description' => auto_link(htmlentities($img->description)),
							'date_added' => htmlentities($img->date_added),
							'rating' => (int)$img->rating
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
	}
	
	function img_info($id)
	{
		try
		{
			$info = $this->gallery_m->get_db_image($id);
			$info = (object)quick_html_escape($info);
			switch($this->router->content_type)
			{
				case 'json':
					$this->output->set_output(json_encode($info));
				break;
			
				case 'xml':
					$xml_img = new SimpleXMLElement('<images><image /></images>');
				
					$xml_img->image->id = $info->id;
					$xml_img->image->name = $info->name;
					$xml_img->image->description = $info->description;
					$xml_img->image->rating = $info->rating;
					$xml_img->image->date_added = $info->date_added;
				
					$this->output->set_output($xml_img->asXML());
				break;
			}
		}
		catch(Exception $e)
		{
			print_r($e);
		}
	
	}
	
	function rate($id, $way)
	{
		try
		{
			if($way == 'up')
				$way_ = '+ 1';
			elseif($way == 'down')
				$way_ = '- 1';
			else
				throw new exception('unvalid way');
			
			$this->load->library('form');
			
			$this->form
				->open('gallery/rate/'.(int)$id.'/'.rawurlencode($way))
				->label('are you shure you want to change '.(int)$id.' '.htmlentities($way));
		
			if(1 == (int)$this->site_settings->get_setting('rate_use_captcha', '0'))
				$this->form->recaptcha('Please enter the captcha code');
			
			$this->form
				->model('gallery_m', 'submit_rate', array('id' => (int)$id, 'way' => $way_))
				->onsuccess('redirect', 'gallery/img/'.(int)$id)
				->submit();
			
			
			$data['form'] = $this->form->get(); // this returns the validated form as a string
			$data['errors'] = $this->form->errors;  // this returns validation errors as a string
			
			$this->template
				->set_title('confirm')
				->add_head('<link href="'.base_url('static/form.css').'" rel="stylesheet" type="text/css" />')
				->build('contact/contact_view', $data);
			#$this->gallery_m->update_rate($id, ($way == 'up') ? '+ 1' : ($way == 'down') ? '- 1' : throw new excpetion('unvalid way')); #example of bad coding
			
			
		}
		catch(Exception $e)
		{
			print_r($e);
		}
	}
	
	function upload()
	{
		try
		{
			$this->load->library('form');
			
			$this->form
				->open('gallery/upload')
				->text('title', 'the name of your picture', 'trim|required|min_length[6]|xss_clean')
				->iupload('img', 'the image file', 'required')
				->textarea('description', 'the description of the file', 'trim')
				->checkbox ('public', 1, 'only visible for me');
		
			if(1 == (int)$this->site_settings->get_setting('rate_upload_captcha', '1'))
				$this->form->recaptcha('Please enter the captcha code');
			
			$this->form
				->model('gallery_m', 'upload')
				->onsuccess(array($this, '_lastaddedimg'))
				->submit();
			
			
			$data['form'] = $this->form->get(); // this returns the validated form as a string
			$data['errors'] = $this->form->errors;  // this returns validation errors as a string
			
			$this->template
				->set_title('confirm')
				->add_head('<link href="'.base_url('static/form.css').'" rel="stylesheet" type="text/css" />')
				->build('contact/contact_view', $data);
			#$this->gallery_m->update_rate($id, ($way == 'up') ? '+ 1' : ($way == 'down') ? '- 1' : throw new excpetion('unvalid way')); #example of bad coding
			
			
		}
		catch(Exception $e)
		{
			print_r($e);
		}	
	}
	
	function _lastaddedimg()
	{
		redirect('gallery/img/'.(int)$this->gallery_m->just_uploaded_id);
	}
}
