<?php

class Game extends MX_Controller
{

	function index()
	{
		$this->load->model('map_m');
		echo '
			<html>
				<head>
					<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
					'.$this->load->view('css', array(), true).'
					'.$this->load->view('js', array(), true).'
				</head>
				<body><div class="map">';
		echo $this->map_m->render();
		echo '<div class="hash">'.md5(serialize($this->map_m->get_map())).'</div></div><div id="log" /></body></html>';
	}
	
	function v2()
	{
		$this->load->model('map_m2', 'map_m'); #load v2
		
		echo '
			<html>
				<head>
					<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
					'.$this->load->view('css', array(), true).'
					'.$this->load->view('js', array(), true).'
				</head>
				<body><div class="map"></div><div id="log" /></body></html>'.$this->map_m->print_r();;	
	}
	
	function ajax($action = '', $x = -1, $y = -1)
	{
		$this->load->model('map_m');
		$hash = md5(serialize($this->map_m->get_map()));
		switch($action)
		{
			case 'click':
				if(!$this->map_m->valid_block($x, $y))
					echo json_encode(array('error' => 'could not find that block', 'result' => '', 'map_hash' => $hash));
				else
				{
					$result = $this->map_m->get_block($x, $y)->click($this->map_m, func_get_args());
					if($result !== false)
						echo json_encode(array('error' => false, 'result' => $result, 'map_hash' => $hash));
					else
						echo json_encode(array('error' => 'internal error', 'result' => $result, 'map_hash' => $hash));
				
				}
				break;
			
			default:
				echo json_encode(array('error' => 'invalid action', 'result' => ''));
				break;
		}
	}
	
	function ajax_v2()
	{
		$messages = $this->input->post('msg');
		if(!$messages) show_404();

		$this->load->library('js_protocol', array(), 'protocol');
		$this->load->model('map_m2', 'map_m'); #load v2
		
		foreach($messages as $message) switch($message['name'])
		{
			case 'reload':
				switch($message['arguments']['type'])
				{
					case 'script':
						$this->protocol->simple_push_message('script_reload', array('alert("scripts were reloaded!")') );
						break;
					
					case 'textures':
						$this->protocol->simple_push_message('texture_reload', array($this->map_m->get_textures()));
						break;
				}
				break;
		
		}
		

		
//		$this->protocol->simple_push_message('DEBUG', $messages);
		
		$this->protocol->send();
	}
}
