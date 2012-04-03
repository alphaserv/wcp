<?php

namespace alphaserv;

abstract class Widget extends \CI_Model #CI_Model -> __get()
{
	abstract function call($arguments, $inner);
	
	abstract function install();
	abstract function uninstall();
}
