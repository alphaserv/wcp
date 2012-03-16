<?php
namespace server;

class Buffer {
	public $stack = array();

	function getc()
	{ 
		return array_shift($this->stack);
	}

	function getint()
	{  
		$c = $this->getc();
	
		if ($c == 0x80)
		{
			$n = $this->getc(); 
			$n |= $this->getc() << 8; 
			return $n;
		}
		else if ($c == 0x81)
		{
			$n = $this->getc();
			$n |= $this->getc() << 8;
			$n |= $this->getc() << 16;
			$n |= $this->getc() << 24;
			return $n;
		}
		return $c;
	}

	function getstring($len=10000)
	{
		$r = ""; $i = 0; 
		while (true)
		{ 
			$c = $this->getint();
			if ($c == 0) return $r;
			$r .= chr($c);
		} 
	}
};
