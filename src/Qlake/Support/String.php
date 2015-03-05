<?php

namespace Qlake\Support;

use Stringy\Stringy;

class String
{
	public function __construct()
	{
		//$this->driver = $foo;
	}



	public function __call($method, $args)
	{
		if (method_exists('Stringy', $method))
		{
			new Stringy();
		}
	}
}