<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class as_user extends CI_Model
{
	/*
		TODO: make this correct oop ;-)
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('hash');
	}
	function clan_exists ($clantag)
	{
		if($clantag != NULL && $clanag[1])
		{
			$result = $this->db->query('SELECT `tag` FROM `clans` WHERE `tag` = ?', array($clantag));
			if($result->num_rows() > 0)
			{
				$result->free_result();
				return true;
			}
		}
		return false;
	}
	function remember_user($id)
	{
		#DON'T just copy the user pasword to an cookie! = UNsafe
		
		$this->db->query('DELETE FROM web_cookies WHERE `user_id` = ?;', array($id)); #deletes unusefull remain/ nothing so we don't really care :)
		
		unset($result); #clean var so we can reuse the name

		$code = $this->hash->hash(mt_rand().$id.$this->CI->config->item('salt').$this->CI->config->item('pepper').mt_rand().microtime());		
		$result = $this->db->query('INSERT INTO web_cookies (`user_id`, `pass`) VALUES (?, ?);', array($id, $code));

		#check for errors
		if (!$result)
		{
			$result->free_result();#clean up
			return false;
		}
		
		#copy the var in the cookie
		setcookie ('remember_me', $code , time()+3600*24*30);
		
		$result->free_result();#clean up
		return true;
	}
	
	function check_remember()
	{
		if(isset($_COOKIE['remember_me']) && $_COOKIE['remember_me'] != 0)
		{
			$result = $this->db->query('SELECT `user_id` FROM web_cookies WHERE `pass` = ?;', array($_COOKIE['remember_me']));
			
			if ($query->num_rows() <= 0)
			{
				$_COOKIE['remember_me'] = 0;
				return -2; #invalid cookie
			}
			$row = $result->result();
			$id = $row->user_id;
			unset($row);
			$result->free_result();#clean up
			return $id;
		}
		return -1; #nothing found
	}
	function login_remember($id)
	{
		#query the database
		$result = $this->db->query('SELECT `users`.`id`, `web_users`.`pass`, `users`.`priv`, `users`.`email`, `web_users`.`activated` FROM users, web_users WHERE `users`.`id` = ? AND `users`.`id` = `web_users`.`user_id`;', array($id));

		if(!$result)
			return -1;
	
		if ($result->num_rows() > 0) {
			$row = $result->row_array(); 
			return $row;
		}
		else
			#username or password error
			return -2;
	}
	function fetch_userdata($id)
	{
		/*
			description: function to receive userdata from the database and cache it local
			return mixed:
				array on success
				negative errorcode on failure
			arguments:
				- $id the user id of the user
		*/

		$result = $this->db->query('SELECT `module`, `name`, `data` FROM user_data WHERE `user_id` = ?;', array($id));
		
		#check if errors have occured
		if(!$result)
			return -1;
		
		if ($query->num_rows() < 1)
				return -2; #no results
		
		#initialise the return array
		$return = array();
		
		#loop trough values
		foreach($result->result() as $i => $row)
		{
			#return the data formatted in two ways
			$return[$row->module][$row->name] = $row->data;
			$return['raw'][] = array(
				'module' => $row->module,
				'name' => $row->name,
				'data' => $row->data,
				'i' => $i
			);
		}
		
		#clean up
		$query->free_result();
		
		#return the return array
		return $return;
	}
	function update_userdata($id, $module, $name, $data)
	{
		#description: pretty simple function to update an user field
		return $this->db->query('UPDATE user_data SET data = ? WHERE user_id = ? AND module = ? AND name = ?', array($data, $id, $module, $name));
	}	
	function send_activation_mail($email, $key)
	{
		/*
			description: function to send an email with an activation key to an user
			return boolean:
				true on success
				false on error
			arguments:
				- $email email adress to send the mail to
				- $key the activation key to use
		*/
		
		#load the email library
		$this->load->library('email');
		
		#clear everything to make shure no conflicts will be generated
		$this->email->clear(true);

		#set where the email is from
		$this->email->from($this->config->item('mail_from'), $this->config->item('mail_from_name'));
		
		#set the reply to adress if not the same
		if ($this->config->item('mail_drom') != $this->config->item('mail_reply_to'))
			$this->email->reply_to($this->config->item('mail_reply_to'), $this->config->item('mail_reply_to_name'));
		
		#set where to send the email to
		$this->email->to($email);
		
		#set the subject of the message
		$this->email->subject($this->config->item('mail_activation_subject'));
		
		#load the message from an view
		$this->email->message($this->load->view('mail/activation', array( 'key' => $key), true));
		
		#load an alternative message for email clients wich don't support html formated email
		$this->email->set_alt_message($this->load->view('mail/alt_activation', array( 'key' => $key), true));
		
		#send the email
		$result = $this->email->send();
		
		#clear up
		$this->email->clear(true);

		if(!$result)
		{
			#log($this->email->print_debugger()); #TODO: make this working
			return false;
		}
		else
			return true;
	}
	function login ($username, $password)
	{
	/*
		description: function to let users login
		return mixed:
			negative error code on errors
			return true on success
		Arguments:
		- $username username of the user
		- $password the password of the user
	*/
	
		#hash the password
		$password = $this->hash->hash($password);
	
		#query the database
		$result = $this->db->query('SELECT `users`.`id`, `web_users`.`pass`, `users`.`priv`, `users`.`email`, `web_users`.`activated` FROM users, web_users WHERE `users`.`name` = ? AND `users`.`id` = `web_users`.`user_id`;', array($username));

		if(!$result)
			return -1;
	
		if ($result->num_rows() > 0) {
			$row = $result->row_array(); 
		     
			#check for an match
			if($password == $row['pass'])
			{
				return $row;
			}
		}
	
		#username or password error
		return -2;
	
	}
	function activate($key, $is_admin = false, $change_to = 1)
	{
		$res = $this->db->query('SELECT `id`, `activation_id` FROM `web_activation` WHERE `activation_id` = ?;', array($key));
		if(!($res && ($res->num_rows() >= 1)))
			#not found or error
			return -1;
		
		if ($is_admin)
			$sql = 'UPDATE web_activation SET `admin_activated` = ? WHERE `activation_id` = ?;';
		else
			$sql = 'UPDATE web_activation SET `user_activated` = ? WHERE `activation_id` = ?;';
		
		return (bool) $this->db->query($sql, array($change_to, $key));
	}
	function check_activations()
	{
		$result = $this->db->query('SELECT `id`, `activation_id`, `username`, `email`, `password`, `ingame_pass`, `user_activated`, `admin_activated`, `priv` FROM `web_activation` WHERE `user_activated` = 1 AND `admin_activated` = 1;');
		
		#the query failed
		if(!$result)
			return false;
		
		#no results so nothing to do
		if($result->num_rows() < 1)
			return true;
		
		foreach ($result->result() as $row)
		{
			$activate_result = $this->db->query('INSERT INTO `users` (`name`, `email`, `pass`, `priv`) VALUES (?, ?, ?, ?);', array($row->username, $row->email, $row->ingame_pass, $row->priv));
			if(!$activate_result)
				return false;
			$id = $this->db->insert_id();
			unset($activate_result);
			$activate_result = $this->db->query('INSERT INTO `web_users` (`user_id`, `pass`) VALUES (?, ?);', array($id, $row->password));
			if(!$activate_result)
				return false;
			unset($activate_result);
			$activate_result = $this->db->query('INSERT INTO `stats_totals` (`user_id`) VALUES (?);', array($id));
			if(!$activate_result)
				return false;
			
			$this->db->query('DELETE FROM web_activation WHERE id = ?', array($row['id']));
			
			unset($activate_result);
			unset($id);

		};
		$result->free_result();
		return true;
	}
	function register ($email, $pass, $as_pass, $username, $activation_type = 0, $priv = 50)
	{
	/*
		description: function to register users
		return mixed:
			negative error code on errors
			return assiocative array on success
		Arguments:
		- $email email of the user
		- $pass password to use on the website
		- $as_pass password to use ingame should not be the same as $pass (not encrypted)
		- $username username of the user
		- $activation_type the way the user is registered:
			1, needs admin approval and user should activate his email
			2, admin apruval only
			3, email activation only
			4, no approval 
	*/
		if($pass == $as_pass) #insecure we require different passwords
			return -1;
		
		#encode the password
		$pass = $this->hash->hash($pass);
		
		#chek if an user with that password does't already exist and not already is activated
		$result = $this->db->query('SELECT `name` FROM `users` WHERE `name` = ? OR `email` = ?;', array($username, $email));

		if(!$result)
			return -4;

				
		if($result->num_rows() > 0)
		{
			#user already registered
			$result->free_result();
			return -2;
		}
		
		#clear up
		$result->free_result();
		unset($result); #so we can reuse the result varname
		
		#did the user register but not activate?
		$result = $this->db->query('SELECT activation_id FROM web_activation WHERE `username` = ? OR `email` = ?', array($username, $email));
		
		if(!$result)
			return -4;
		
		if($result->num_rows() > 0)
		{
			#user already registered but just not activated
			$result->free_result();
			return -3;		
		}
		
		#clear up
		$result->free_result();
		unset($result);
		
		#generate an unique register id
		$code = $this->hash->hash(mt_rand().$this->config->item('salt').$pass.$username.$email.$priv.$as_pass.$this->config->item('pepper').microtime()); #compex enough? ;-)
		
		$a = 0;
		$b = 0;
		switch($activation_type)
		{
			#4, no appruval
			case 4:
				$a = 1;
				$b = 1;
			break;
			
			#3, email activation only
			case 3:
				$this->send_activation_mail($email, $code);
				$a = 0;
				$b = 1;
			break;
			
			#2, admin apruval only
			case 2:
				$a = 1;
				$b = 0;
			break;
			
			#1, needs admin appruval and user should activate his email
			default:
			case 1:
				$this->send_activation_mail($email, $code);
				#do nothing a and b are 0 by default
			break;
		}
		
		#insert the user to the activation table
		$result = $this->db->query('INSERT INTO `web_activation` (`id`, `activation_id`, `username`, `email`, `password`, `ingame_pass`, `user_activated`, `admin_activated`, `priv`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?);', array($code, $username, $email, $pass, $as_pass, $a, $b, $priv));
		
		if(!$result)
		{
			return -4;
		}
		
		#clean up no clean up needed, insert query
		#$result->free_result();
		
		#don't mind for now
		$this->check_activations();
		
		#return activation id
		return array('code' => $code);
	}
}
