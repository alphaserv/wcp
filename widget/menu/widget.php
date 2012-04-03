<?php

class Menu_widget extends alphaserv\Widget
{
	protected $items = array();
	
	function call($arguments, $inner)
	{
				
		$this->tags->set_trigger('as:');
		
		$this->tags->parse($inner, array(), array($this, 'parser_callback'));
		
		
		if(!isset($arguments['type']))
			$arguments['type'] = '';
		
		switch($arguments['type'])
		{
			default:
				$template = '
					<ul>
						{items}
							{if "{uri}" == "{current_uri}"}
								<li class="select">
							{else}
								<li>
							{/if}
							
								<a href="{url}">{name}</a>
							
							</li>
						{/items}
					</ul>';
				break;
			
		
		}
		
		$parser = new CI_Parser;
		
		return $parser->parse_string($template, array('current_uri' => uri_string(), 'items' => $this->items), true);
	}
	
	function parser_callback($path)
	{
		if($path['segments'][0] != 'item')
			throw new exception('menu only allows items inside');
		
		if(isset($path['attributes']['uri']))
			$this->items[] = array('url' => site_url($path['attributes']['uri']), 'uri' => $path['attributes']['uri'], 'name' => $path['content']);
		elseif(isset($path['attributes']['url']))
			$this->items[] = array('url' =>  $path['attributes']['url'], 'uri' => '', 'name' => $path['content']);
		else
			throw new exception('menuitem requires ether an uri or an url');
	}
	
	function install()
	{
		return -1;
	}
	
	function uninstall()
	{
		return -1;
	}
}
