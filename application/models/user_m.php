<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class User_m extends CI_Model
{
	/*
		the way the user should be activated:
			1, needs admin approval and user should activate his email
			2, admin apruval only
			3, email activation only
			4, no approval 
	
	*/
	const ACTIVATION_TYPE_BOTH = 3;
	const ACTIVATION_TYPE_ADMIN = 2;
	const ACTIVATION_TYPE_EMAIL = 1;
	const ACTIVATION_TYPE_NONE = 0;
	
	const ACTIVATION_STATUS_COMPLETE = 3;
	const ACTIVATION_STATUS_EMAIL = 2;
	const ACTIVATION_STATUS_ADMIN = 1;
	const ACTIVATION_STATUS_NONE = 0;
	
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		
		$this->load->model('clan_m');
	}
	
	public function get_acces_to(string $actionname, int $user_id)
	{
		$return |= Auth::access_read;
		$return |= Auth::access_write;
		$return |= Auth::access_delete;
		$return |= Auth::access_create;
		$return |= Auth::access_manage;
		
		return $return;
	}
	

	public function try_login ($username, $password)
	{
	/*
		description: function to let users login
		return bool:
			return true on success
			return false if login fails (username/pass incorrect)
		Arguments:
		- $username username of the user
		- $password the password of the user
	*/
	
		#query the database TODO:implement new hashing method
		$result = $this->db->query('SELECT
										users.id,
										web_users.pass,
										users.email
									FROM
										users,
										web_users
									WHERE
										users.name = ?
									AND
										users.id = web_users.user_id;', array($username));

		if (!$result)
			throw new exception('could not retreive user from database.');
		elseif($result->num_rows() == 1)
		{
			#get only one row
			$row = $result->row();
		
			#only load if needed
			$this->load->library('hash');
		
			#backwards compatible hash TODO:implement new hashing method
#			if(isset($this->hashing_type) && $this->hashing_type == 'keyhash')
#				$pass = $this->hash->checkuserpassword($pass, $row->key);
#			else
				$pass = $this->hash->hash($password);
			echo $pass;
			if($pass == $row->pass)
				return array(true, $row->id);
		}

		#username or password error
		return array(false);
	}
	
	public function set_activation($key, $is_admin = false, $change_to = 1)
	{
		$res = $this->db->query('SELECT `id`, `activation_id` FROM `web_activation` WHERE `activation_id` = ?;', array($key));
		
		if(!$res || $res->num_rows() != 1)
			throw new exception('could not find activation key'); #TODO: make user exception
		
		
		if ($is_admin)
			$sql = 'UPDATE web_activation SET `admin_activated` = ? WHERE `activation_id` = ?;';
		else
			$sql = 'UPDATE web_activation SET `user_activated` = ? WHERE `activation_id` = ?;';
		
		return (bool) $this->db->query($sql, array($change_to, $key));
	}
	
	public function activation_status(string $name)
	{
		$result = $this->db->query('SELECT 
										user_activated,
										admin_activated,
									FROM
										web_activation
									WHERE
										username = ?
									LIMIT 1', array($name));	
		
		if(!$result or $result->num_rows() !== 1)
			throw new exception('could not find unactivated user');
		
		$row = $result->row();
		if((int)$row->admin_activated === 1 && (int)$row->user_activated === 1)
			return self::ACTIVATION_STATUS_COMPLETE;
		elseif((int)$row->admin_activated == 1)
			return self::ACTIVATION_STATUS_EMAIL;
		elseif((int)$row->admin_activated == 1)
			return self::ACTIVATION_STATUS_ADMIN;
		else
			return self::ACTIVATION_STATUS_NONE;
	}
	
	public function check_activations(/*int error*/ $max = 10)
	{
		$result = $this->db->query('SELECT 
										id,
										activation_id,
										username,
										email,
										password,
										ingame_pass,
										user_activated,
										admin_activated,
										priv'.#TODO:add key field
									'FROM
										web_activation
									WHERE
										user_activated = 1
									AND
										admin_activated = 1;
									LIMIT '.(int)$max);
		
		#the query failed
		if(!$result)
			throw new exception('could not update activation list');
		
		#no results so nothing to do
		if($result->num_rows() < 1)
			return true;
		
		foreach ($result->result() as $row)
		{
			#safe transactions
			$this->db->trans_start();
			
				$this->db->query('INSERT INTO `users` (`name`, `email`, `pass`, `priv`) VALUES (?, ?, ?, ?);', array($row->username, $row->email, $row->ingame_pass, $row->priv));

				#get new user id
				$id = $this->db->insert_id();

				#TODO:add new hashing method field in here			
				$this->db->query('INSERT INTO `web_users` (`user_id`, `pass`) VALUES (?, ?);', array($id, $row->password));
				$this->db->query('INSERT INTO `stats_totals` (`user_id`) VALUES (?);', array($id));
				
				#extra, probebly unneeded check
				if ($this->db->trans_status() === TRUE)
				{
					$this->db->query('DELETE FROM web_activation WHERE id = ?', array($row['id']));
					$this->db->trans_commit();
				}
				else
					$this->db->trans_rollback();
			
			#end of safe transaction
			$this->db->trans_complete();

		};
		$result->free_result();
		return true;
	}
	function register ($email, $pass, $as_pass, $username, $activation_type = 0, $priv = 50)
	{
	/*
		description: function to register users
		return array:
			return assiocative array on success
		Arguments:
		- $email email of the user
		- $pass password to use on the website
		- $as_pass password to use ingame should not be the same as $pass (not encrypted)
		- $username username of the user
		- $activation_type the way the user is registered
	*/
		if($pass == $as_pass) #insecure we require different passwords
			throw new exception ('your ingame password is currently the same as your online one, please use two different ones');#TODO:use user error
		
		#encode the password
		$pass = $this->hash->hash($pass);
		
		#chek if an user with that password does't already exist and not already is activated
		$result = $this->db->query('SELECT `name` FROM `users` WHERE `name` = ? OR `email` = ?;', array($username, $email));

		if(!$result)
			throw new exception('could not check if the name is already in use');

				
		if($result->num_rows() > 0)
		{
			#user already registered
			$result->free_result();
			throw new exception('The username / the email-address is already in use, please choose another email address / username.');#TODO:use user error
		}
		
		#clear up
		$result->free_result();
		unset($result);
		
		#did the user register but not activate?
		$result = $this->db->query('SELECT activation_id FROM web_activation WHERE `username` = ? OR `email` = ?', array($username, $email));
		
		if(!$result)
			throw new exception('could not check if the name is already in use');
		
		if($result->num_rows() > 0)
		{
			#user already registered but just not activated
			$result->free_result();
			throw new exception('The username / the email-address is already in use, please choose another email address / username or finish its activation.');#TODO:use user error
		}
		
		#clean up
		$result->free_result();
		unset($result);
		
		#generate an unique register id
		$code = $this->hash->hash(mt_rand().$this->config->item('salt').$pass.$username.$email.$priv.$as_pass.$this->config->item('pepper').microtime().unique_id()); #compex enough? ;-)
		
		$a = 0;
		$b = 0;

		switch($activation_type)
		{
			#4, no appruval
			case self::ACTIVATION_TYPE_NONE:
				$a = 1;
				$b = 1;
			break;
			
			#3, email activation only
			case self::ACTIVATION_TYPE_EMAIL:
				#$this->send_activation_mail($email, $code);#TODO:send email
				$a = 0;
				$b = 1;
			break;
			
			#2, admin apruval only
			case self::ACTIVATION_TYPE_ADMIN:
				$a = 1;
				$b = 0;
			break;
			
			#1, needs admin appruval and user should activate his email
			default:
			case self::ACTIVATION_TYPE_BOTH:
				#$this->send_activation_mail($email, $code);#TODO:send email
				#do nothing a and b are 0 by default
			break;
		}
		
		#safe transaction
		$this->db->trans_begin();

			$this->db->query('INSERT INTO
								web_activation
								(
									id,
									activation_id,
									username,
									email,
									password,
									ingame_pass,
									user_activated,
									admin_activated,
									priv
								)
								VALUES
								(
									NULL,
									?,
									?,
									?,
									?,
									?,
									?,
									?,
									?
								);', array($code, $username, $email, $pass, $as_pass, $a, $b, $priv));
			
			
		#end of safe transaction
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			throw new exception('could not insert user into database');
		}
		else
			#3, 2, 1, GO!
			$this->db->trans_commit();

		#return activation id
		return array('code' => $code, 'activation_type' => $activation_type);
	}
}

/*
	function send_activation_mail($email, $key)
	{
		/ *
			description: function to send an email with an activation key to an user
			return boolean:
				true on success
				false on error
			arguments:
				- $email email adress to send the mail to
				- $key the activation key to use
		* /
		
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
	*/
