<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class User_form_m extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		$this->load->database();
		
		$this->load->model('user_m');
		$this->load->model('clan_m');
	}

	public function login (&$form, $data)
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
										users.id = web_users.user_id;', array($data['username']));

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
				$pass = $this->hash->hash($data['password']);

			if($pass == $row->pass)
			{
				$this->user_lib->change_user(new user_($row->id));
				return true;
			}
			
			
			
			$form->add_error('username', 'username or password incorrect');
		}
		
		$form->add_error('username', 'username or password incorrect');
	}
	

	function register (&$form, $data)
	{
		$data['priv'] = (int)$this->config->item('default_priv');
		$data['activation_type'] = (int)user_m::ACTIVATION_TYPE_EMAIL; #$this->config->item('activation_type');

		if($data['password'] == $data['alphaserv_password']) #insecure we require different passwords
			$form->add_error('alphaserv_password', 'your ingame password is currently the same as your online one, please use two different ones');
		
		$this->load->library('hash');
		#encode the password TODO: new encryption 
		$data['password'] = $this->hash->hash($data['password']);
		
		#chek if an user with that password does't already exist and not already is activated
		$result = $this->db->query('SELECT name FROM users WHERE name = ? OR email = ?;', array($data['username'], $data['email']));

		if(!$result)
			throw new exception('could not check if the name is already in use');

				
		if($result->num_rows() > 0)
		{
			#user already registered
			print_r($result->result_object());
			$result->free_result();
			$form->add_error('email', 'The username / the email-address is already in use, please choose another email address / username.');
		}
		
		#clear up
		$result->free_result();
		unset($result);
		
		#did the user register but not activate?
		$result = $this->db->query('SELECT activation_id FROM web_activation WHERE username = ? OR email = ?', array($data['username'], $data['email']));
		
		if(!$result)
			throw new exception('could not check if the name is already in use');
		
		if($result->num_rows() > 0)
		{
			#user already registered but just not activated
			$result->free_result();
			$form->add_error('The username / the email-address is already in use, please choose another email address / username or finish its activation.');
		}
		
		#clean up
		$result->free_result();
		unset($result);
		
		#generate an unique register id
		$code = $this->hash->hash(mt_rand().$this->config->item('salt').$data['password'].$data['username'].$data['email'].$data['priv'].$data['alphaserv_password'].$this->config->item('pepper').microtime().uniqid()); #compex enough? ;-)
		
		$a = 0;
		$b = 0;

		switch($data['activation_type'])
		{
			#4, no appruval
			case user_m::ACTIVATION_TYPE_NONE:
				$a = 1;
				$b = 1;
			break;
			
			#3, email activation only
			case user_m::ACTIVATION_TYPE_EMAIL:
				$this->auth->send_activation_email($data['email'], $code);
				$a = 0;
				$b = 1;
			break;
			
			#2, admin apruval only
			case user_m::ACTIVATION_TYPE_ADMIN:
				$a = 1;
				$b = 0;
			break;
			
			#1, needs admin appruval and user should activate his email
			default:
			case user_m::ACTIVATION_TYPE_BOTH:
				$this->auth->send_activation_email($data['email'], $code);
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
								);', array($code, $data['username'], $data['email'], $data['password'], $data['alphaserv_password'], $a, $b, $data['priv']));
			
			
		#end of safe transaction
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$form->add_error('username', 'could not insert user into database');
		}
		else
		{
			#3, 2, 1, GO!
			$this->db->trans_commit();
			
			$id = $this->db->insert_id();
			$group_id = $this->user_m->find_group('users')->id;
			
			$this->user_m->add_to_group($group_id, $id);
		}
	}
	
	function activate(&$form, $data)
	{
		try
		{
			$this->user_m->set_activation($data['key']);
			$this->user_m->check_activations();
		}
		catch(Exception $e)
		{
			$form->add_error('key', $e->getMessage());
		}
	}
	
	function delete_name(&$form, $data)
	{
		$res = $this->db->query('
			DELETE FROM
				names
			WHERE
				id = ?', array($this->uri->segments[4]));
		
		if(!$res)
			$form->add_error('submit', 'Could not delete name!');
		
	}
	
	function update_name(&$form, $data)
	{
		$res = $this->db->query('
			UPDATE
				names
			SET
				name = ?
			WHERE
				id = ?', array($data['nickname'], $this->uri->segments[4]));
		
		if(!$res)
			$form->add_error('submit', 'Could not update name!');
		
	}
	
	function new_name(&$form, $data)
	{
		$clan_id = $this->clan_m->has_reserved_clantag($data['nickname']);
		$uid = $this->auth->get_current_user()->get_user_id();
		
		if($clan_id != -1 && !$this->clan_m->is_in_clan($uid, $clan_id))
			$form->add_error('nickname', 'Your name contains a reserved clantag, please change your name.');
			
		elseif($this->db->query('SELECT id FROM names WHERE name = ?', array($data['nickname']))->num_rows() > 0)
			$form->add_error('nickname', 'Sorry, your name is already claimed. If you believe this is a faker, please contact us.');
		
		else
		{
			$this->db->trans_begin();
		
			$this->db->query('
				INSERT INTO
					names
					(
						name,
						user_id
					)
				VALUES
					(
						?,
						?
					)
				', array($data['nickname'], $uid));
				
		
			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				$form->add_error('nickname', 'could not save name in the database, please try again.');
			}
			else
			{
				$this->db->trans_commit();
			}
		}
		
		unset ($clan_id);
		unset ($uid);
		
		
	}
	
	function user_data(&$form, $data)
	{
		$id = $this->auth->get_current_user()->get_user_id();
		
		$namechanged = $data['user']->name != $data['username'];
		
		if($namechanged && $this->db->query('SELECT name FROM users WHERE name = ?', array($data['username']))->num_rows() > 0)
			$form->add_error('username', 'That username already exists! Please pick another one.');
		else
		{
		
			$this->db->trans_begin();
		
				$this->db->query('
					UPDATE
						users
					SET
						name = ?,
						email = ?,
						pass = ?
					WHERE
						id = ?
		
				', array($data['username'], $data['email'], $data['ingame_pass'], (int)$id));
		
				if(isset($data['pass']) && $data['pass'] != '')
				{
					$this->load->library('hash');
					$this->db->query('
						UPDATE
							web_users
						SET
							pass = ?
						WHERE
							user_id = ?
					', array($this->hash->hash($data['pass']), $id));
				}
			
			if($this->db->trans_status() == false)
			{
				$this->db->trans_rollback();
				$form->add_error('username', 'Could not update settings, please try again.');
			}
			else
				$this->db->trans_commit();
		
			unset ($id);
		}
	}
}
