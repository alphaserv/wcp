<?php

function quick_html_escape($input, $ignore = array())
{
	if(is_array($input) || is_object($input))
	{
		$input = (array)$input;
		foreach($input as $key => $value)
			if(!in_array($key, $ignore))
				$input[$key] = quick_html_escape($value);

		return $input;
	}
	else
		return htmlentities($input);
}
