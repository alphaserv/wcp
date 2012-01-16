<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
#it's data so model :)

class AS_Priv {
#ported from alphaserv
	public $banned = 0;
	public $default_ = 1;
	public $user = 50;
	public $master = 300;
	public $admin = 600;
	public $owner = 1000;
	public $dev = 1000000;
}

#helper class
class Priv
{
	const Banned = 0;
	const Default_ = 1;
	const User = 50;
	const Master = 300;
	const Admin = 600;
	const Owner = 1000;
	const Dev = 1000000;

}
