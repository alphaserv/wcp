<?php

class Page_write_m extends CI_Model
{
	function __construct()
	{
		$this->load->model('page_m');
	}
	
	function newpage(&$form, $data)
	{
		try
		{
			if(isset($data['submit']) && $data['submit'] == 'previeuw')
			{
				$data['id'] = 'none';
				$data['date'] = 'NOW';
				try
				{
					switch($data['makeup'])
					{
						default:
						case 'none' :
							$data['content'] = htmlentities($data['content']);
							break;
				
						case 'html' :
							$data['content'] = $data['content'];
							break;
			
						case 'php':
							ob_start();
							echo eval('?>'.$page->content.'<?php ');
							$data['content'] = ob_get_contents();

							ob_end_clean();
						break;
					}

					$this->template->build('page', array('page' => quick_html_escape($data, array('content'))));
				}
				catch(Exception $e)
				{
					$form->adderror(lang_string('page_write_m_previeuw_fail', 'previeuw failed'));
					return;
				}

			}
			try
			{
				$this->page_m->get_page($data['uri']);
			
				#error
				$form->add_error('uri', lang_string('page_write_m_url_already_used', 'url already in use, please choose another one'));
				return;
			}
			catch(page_not_found_exception $e)
			{
				#ok
			}
		
			if(!isset($data['valid_types'][$data['makeup'][0]]))
				$form->add_error('makeup', lang_string('page_write_m_type_from_list', 'please select a type from the list'));
			else
			{
				$result = $this->db->query('
					INSERT INTO
						web_pages
						(
							uri,
							title,
							content,
							date,
							makeup,
							public
						)
					VALUES
						(
							?,
							?,
							?,
							NOW(),
							?,
							b\'1\'
						)', array($data['uri'], $data['title'], $data['content'], $data['valid_types'][$data['makeup'][0]]));
			
				if(!$result)
					throw new Exception(lang_string('page_write_m_insert_failed', 'could not insert into database'));
			}
		}
		catch(Exception	$e)
		{
			$form->add_error('', 'Internal Error: '.$e->getmessage());
		}
	}
	
	function update_page(&$form, $data)
	{
		try
		{
			if(isset($data['submit']) && $data['submit'] == 'previeuw')
			{
				$data['id'] = 'none';
				$data['date'] = 'NOW';
				try
				{
					switch($data['makeup'])
					{
						default:
						case 'none' :
							$data['content'] = htmlentities($data['content']);
							break;
				
						case 'html' :
							break;
			
						case 'php':
							ob_start();
							echo eval('?>'.$page->content.'<?php ');
							$data['content'] = ob_get_contents();

							ob_end_clean();
						break;
					}

					$this->template->build('page', array('page' => quick_html_escape($data, array('content'))));
				}
				catch(Exception $e)
				{
					$form->adderror(lang_string('page_write_m_previeuw_fail', 'previeuw failed'));
					return;
				}

			}
		
			if(!isset($data['valid_types'][$data['makeup'][0]]))
				$form->add_error('makeup', lang_string('page_write_m_type_from_list', 'please select a type from the list'));
			else
			{
				$result = $this->db->query('
					UPDATE
						web_pages
					SET
						uri = ?,
						title = ?,
						content = ?,
						date = NOW(),
						makeup = ?,
						public = b\'1\'
					WHERE
						id = ?
				', array($data['uri'], $data['title'], $data['content'], $data['valid_types'][$data['makeup'][0]], $data['id']));
			
				if(!$result)
					throw new Exception(lang_string('page_write_m_update_failed', 'could not update database'));
			}
		}
		catch(Exception	$e)
		{
			$form->add_error('', 'Internal Error: '.$e->getmessage());
		}
	}

}
