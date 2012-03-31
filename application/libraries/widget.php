<?php

namespace AS;

abstract class Widget extends \CI_Model #CI_Model -> __get()
{
	abstract function call();
	
	abstract function install();
	abstract function uninstall();
	
}
