<?php
	
define('BASEPATH', dirname(__FILE__).'/includes/');
	
function log_message($_, $message)
{
	if(strtoupper($_) != 'DEBUG')
		throw new exception($message);
	else
		echo $message;
}

if ( ! function_exists('is_php'))
{
	function is_php($version = '5.0.0')
	{
		static $_is_php;
		$version = (string)$version;

		if ( ! isset($_is_php[$version]))
		{
			$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
		}

		return $_is_php[$version];
	}
}
	
include BASEPATH.'database/DB.php';

try
{
	$db = DB('ms://root:Wachtwoord1@localhost/alphaserv?pdo=true');
	#print_r($db);
	print_r($db->query('SELECT * FROM users')->result_object());
}
catch(Exception $e)
{
	print_r($e->getMessage());
}
