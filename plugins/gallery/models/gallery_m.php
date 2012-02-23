<?php

class gallery_m extends CI_Model
{

	function get_img_file($id, $thumb = false)
	{
		$id = (int)$id;
		
		if($thumb)
			$id .= '_thumb';
		if(($file = file_get_contents(dirname(dirname(__file__)).'/img/'.$id.'.jpg')) === false)
			throw new exception('could not read image');
		else
			return $file;
	}
	
	function save_img_file($id, $img_path)
	{

		$config['image_library'] = 'ImageMagick';
		$config['library_path'] = $this->site_settings->get_setting('img_library_path', '/usr/bin/convert');
		$config['source_image']	= $img_path;
		$config['maintain_ratio'] = TRUE;
		$config['width']	 = 1280;
		$config['height']	= 720;
		$config['new_image'] = dirname(dirname(__file__)).'/img/'.$id.'.jpg';

		$this->load->library('image_lib');
		$this->image_lib->initialize($config);
		if(!$this->image_lib->resize())
		{
			throw new exception($this->image_lib->display_errors());
		}
		
		$config['width']	 = 155;
		$config['height']	= 155;
		$config['new_image'] = dirname(dirname(__file__)).'/img/'.$id.'_thumb.jpg';

		$this->image_lib->initialize($config);
		if(!$this->image_lib->resize())
		{
			throw new exception($this->image_lib->display_errors());
		}

	}
	
	function get_db_images()
	{
		$result = $this->db->query('
			SELECT
				id,
				name,
				description,
				rating,
				date_added,
				public
			FROM
				web_gallery
			WHERE
				public = b\'1\';');
			
		if($result->num_rows() !== 1)
			throw new exception('invalid id');
		else
			return $result->result_object();
	}
	
	function get_db_image($id)
	{
		$result = $this->db->query('
			SELECT
				id,
				name,
				description,
				rating,
				date_added,
				public
			FROM
				web_gallery
			WHERE
				id = ?;', array($id));
		
		if($result->num_rows() !== 1)
			throw new exception('invalid id');
		else
			$result = $result->first_row();

		if(ord($result->{'public'}) == 0)
			throw new exception('trying to access non public page');
		else
			return $result;
	}
	
	function submit_rate(&$form, $data)
	{
		if($data['way'] !== '+ 1' && $data['way'] !== '- 1')
			$form->adderror('way', 'Unvalid way');
		
		$result = $this->db->query('
			UPDATE
				web_gallery
			SET
				rating = rating '.$data['way'].'
			WHERE
				id = ?', array($data['id']));
		
		return $result;
	}
}
