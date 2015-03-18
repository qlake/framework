<?php

namespace Qlake\Http;

use InvalidArgumentException;

class Cookie
{
	protected $name;


	protected $value;


	protected $domain;


	protected $expire;


	protected $path;


	protected $secure;


	protected $httpOnly;



	public function __construct($name, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = false)
	{
		if (empty($name))
		{
			throw new InvalidArgumentException('The Cookie Name Can Not Be Empty.');
		}

		// PHP setcookie() warning
		if (preg_match('#[=,; \t\r\n\013\014]#', $name))
		{
			throw new InvalidArgumentException('Cookie Names Can Not Contain Any Of The Following [=,; \t\r\n\013\014] Characters.');
		}

		$this->name     = $name;
		$this->value    = $value;
		$this->domain   = $domain;
		$this->expire   = $expire;
		$this->path     = empty($path) ? '/' : $path;
		$this->secure   = $secure;
		$this->httpOnly = $httpOnly;
	}



	public function getName()
	{
		return $this->name;
	}



	public function getValue()
	{
		return $this->value;
	}



	public function getDomain()
	{
		return $this->domain;
	}



	public function getExpiresTime()
	{
		return $this->expire;
	}



	public function getPath()
	{
		return $this->path;
	}



	public function isSecure()
	{
		return $this->secure;
	}



	public function isHttpOnly()
	{
		return $this->httpOnly;
	}
}
