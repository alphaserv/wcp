<?php

class Helpers extends MX_Controller
{
	function __construct()
	{
		$this->load->database();
	}
	function _remap()
	{
		show_404();
	}
	function _tagcall($parse)
	{
		#remove segment #0
		$parse['segments'][0] = null;
		unset($parse['segments'][0]);
		
		#let array start @ 0
		$parse['segments'] = array_values($parse['segments']);
		
		switch($parse['segments'][0])
		{
			case 'lang':
					if(defined('OMG_DEBUG'))
						print_r($parse['attributes']);
					
					#closure
					$handler = $this->config->item('lang_handler');
					return $handler($parse['attributes']['string'], ((isset($parse['attributes']['forcelang']) && $parse['attributes']['forcelang'] != 0) ? $parse['attributes']['forcelang'] : null), ((isset($parse['attributes']['nodboverride']) && $parse['attributes']['nodboverride'] == 1) ? true : false));
					/*
					#cache!!!!!
					$this->db->cache_on();
					
					$result = $this->db->query('SELECT id, name, language, value FROM web_lang WHERE name = ? AND language = ?', array($parse['attributes']['string'], 'en'));
						
					if(!$result || $result->num_rows() != 1)
						throw new exception('could not receive lang: "'.$parse['attributes']['string'].'"');
		
					$setting = $result->result_object();
					$setting = $setting[0]->value;
					
					$this->db->cache_off();
					
					return $setting;*/
				break;
			
			case 'url':
				$this->load->helper('url');
				
				if(!isset($parse['segments'][1]))
					$parse['segments'][1] = 'base';
				
				switch($parse['segments'][1])
				{
					default:
					case 'base':
						return base_url($parse['attributes']['url']);
						break;
					case 'site':
						return site_url($parse['attributes']['url']);
						break;
					
					case 'current':
						return current_url();
						break;
						
					case 'string':
						return uri_string();
						break;
						
					case 'index_page':
						return index_page();
						break;
					
					case 'encode':
						return urlencode($parse['attributes']['url']);
						break;
					
					case 'anchor':
					case 'anchor_popup':
					case 'mailto':
					case 'safe_mailto':
					case 'auto_link':
					case 'url_title':
					case 'prep_url':
						throw new exception($parse['segments']['1'].' is not supported yet: TOO_LAZY_TO_IMPLEMENT error');
						break;
					
					case 'redirect':
						throw new exception('Template redirect blocked.');
						break;
					
				}
				
				break;
			
			#TODO: database!!!!!!!
			case 'banner':
				switch($parse['attributes']['id'])
				{
					case 'noclan-template-banner':
						$this->load->helper('url');
						
						$banners = array(
							array(
								'url' => '/templates/noclan/img/dott/ed.gif',
								'text' => array(
									'', #chanse for no text
									'Soon we\'ll all be speaking... Well, English I guess',
									'Get me out of here! I feel like I\'m pupating!',
									'Cheap-Mail-Ordered Jewel! I\'d knew I should\'ve bought a real diamond.',
									'Step one. Find plans. Step two. Save world. Step three. Get out of my house! Let\'s get cracking.'
								)
							),
							array(
								'url' => '/templates/noclan/images/dott/bernard.gif',
								'text' => array(
									'', #chanse for no text
									'You know what they say: "To save the world, you have to push a few old ladies down the stairs."',
									
									'Boy, I haven\'t seen you since I was here five years ago.'.
										' You know, I bet you\'d really like my friend Hoagie.'.
										' He\'s a roadie for a heavy metal band.'.
										' You could hit him over the head with a bowling ball and it wouldn\'t faze him.'.
										'He can pass out standing up and not drop anything." (pause) "The two of you have a lot in common.',
									
									'Boy, I wish I had as little on my mind as you do. No offense intended, of course.',
									
									'Bernard: I\'m sure Dr. Fred wouldn\'t do this if it weren\'t safe!<br />'.
										'<img src="'.base_url('/templates/noclan/img/dott/laverne.gif').'"><br />After all, he IS a doctor.',
									
									'Look behind you, a three-headed monkey!<br />'.
										'<img src="'.base_url('/templates/noclan/img/dott/pt.gif').'" />'.
										'<br />The only three-headed monkey here is in FRONT of us.',

									'Look behind you, a three-headed monkey!'
								)
							),
							
							array(
								'url' => '/templates/noclan/img/dott/pt.gif',
								'text' => array(
									'', #chanse for no text
									'I feel like I could... like I could... like I could... TAKE ON THE WORLD!!',
									
									'The only three-headed monkey here is in FRONT of us.',
								)
							),

							array(
								'url' => '/templates/noclan/img/dott/hoagie.gif',
								'text' => array(
									'', #chanse for no text
									'We may not live to see yesterday...',
									
									'Bernard, float over here so I can punch you.',
									
									'Dude! You\'re, like, George Washington, man!'
								)
							),							

							array(
								'url' => '/templates/noclan/img/dott/laverne.gif',
								'text' => array(
									'', #chanse for no text
									'This must be that Woodstock place Mom and Dad always talk about.',
									
									'Bernard, float over here so I can punch you.',
									
									'Dude! You\'re, like, George Washington, man!',
									
									'Gosh, I hope this isn\'t like the primitive, dangerous microwave ovens of my century. Those things could really pop a hamster good.'
								)
							),		
						);
						
						$element = $banners[rand(0, count($banners) - 1)];
						if(is_array($element['url'])) $element['url'] = $element['url'][rand(0, count($element['url']) - 1)];
						if(is_array($element['text'])) $element['text'] = $element['text'][rand(0, count($element['text']) - 1)];
						$finfo = pathinfo($element['url']);
						$name = $finfo['filename'];
						
						return '<img src="'.base_url($element['url']).'" alt="'.$name.'" title="'.$name.'" /> '.$element['text'];

/*
TODO:
"<img src=\"$laverne\"><br/>This is all your fault, Bernard.<br/><img src=\"$fred\"><br/>Behold, children! The Chron-O-John!<br/><img src=\"$hoagie\"><br/>Doc, can't you just send Bernard?<br/><img src=\"$fred\"><br/>No, you must all go to increase the odds that one of you will make it there alive.<br/><img src=\"$bernard\"><br/>Has anyone ever been hurt in this thing?<br/><img src=\"$fred\"><br/Of course not!<br/>This is the first time I've ever tried it on people.",
"<img src=\"$fred\"><br/>Leaping labrats!<br/><img src=\"$bernard\"><br/>Dr. Fred!<br/><img src=\"$fred\"><br/>What have you done this time, you meddling milquetoast? Now Purple Tentacle is free to use his evil mutant powers to take over the world, and ENSLAVE ALL HUMANITY!<br/><img src=\"$bernard\"><br/>Whoops.",
"Is that a W-390/B Frivolous Spending Report?<br/><br/>No, it's another 561-AB Negative Attention Statement.",
*/
					break;
				}
				
			break;
			
			case 'menu':
				$this->load->helper('url');
				#TODO: DATABASE!!!!
				switch($parse['attributes']['id'])
				{
					case 'main':
						$menu_items = array(
							array('/', 'HOME'),
							array('/page/about', 'ETHICS'),
							#array('/forum', 'FORUM'),
							array('/page/members', 'MEMBERS'),
							array('/gallery/', 'GALLERY'),
							array('/contact', 'CONTACT'),
						);

						$current = uri_string();

						$menu_html = '';
						foreach ($menu_items as $item)
							if($item[0] == $current)
								$menu_html .= '<li class="select">
									<a href="'.site_url($item[0]).'">'.$item[1].'</a>
								</li>';
							else
								$menu_html .= '<li><a href="'.site_url($item[0]).'">'.$item[1].'</a></li>';

						return $menu_html;
						break;
				}
				break;
				
			default:
				throw new exception('could not find helper type');
				break;
		
		}
	}
}
