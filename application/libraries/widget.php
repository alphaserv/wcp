<?php

namespace alphaserv;

abstract class Widget extends \CI_Model #CI_Model -> __get()
{
	abstract function call($arguments);
	
	abstract function install();
	abstract function uninstall();
	
}
