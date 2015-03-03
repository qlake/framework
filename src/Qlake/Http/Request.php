<?php

namespace Qlake\Http;

use Qlake\Http\Header;

class Request
{
	protected $content;


	protected $header;

	
	protected $method;

	
	protected $query = [];

	
	protected $data = [];


	protected $body;


	protected $specialInputs =
	[
		'__method' => 'GET',
		'__csrf'   => '',
		'__url'    => '/',
	];


	public $env;


	protected $cookie;

	
	protected $files;


	
	public function __construct(array $query = [], array $data = [], $server = null, $cookies = [], $files = null, $content = null)
	{
		$this->header = new Header;
		$this->env    = new Environment;
		$this->query  = $this->parseInputs($query);
		$this->data   = $this->parseInputs($data);
	}


	
	public static function capture()
	{
		return new static($_GET, $_POST);
	}


	
	protected function parseInputs(array $inputs)
	{
		$parsedInputs = [];

		foreach ($inputs as $key => $value)
		{
			if (array_key_exists($key = strtolower($key), $this->specialInputs))
			{
				$this->specialInputs[$key] = $value;

				continue;
			}

			$parsedInputs[$key] = $value;
		}

		return $parsedInputs;
	}


	
	protected function detectMethod()
	{
		$method = $this->env['REQUEST_METHOD'];
		
		return strtoupper($this->getSpecialInput('__method', $method));
	}

	

	public function getMethod()
	{
		return $this->method = $this->method ?: $this->detectMethod();
	}


	
	public function getHeader()
	{
		return $this->header;
	}


	
	public function getQuery($name, $default = null)
	{
		return $this->query[$name] ?: $default;
	}


	
	public function getAllQuery()
	{
		return $this->query;
	}


	
	public function getData($name, $default = null)
	{
		return $this->data[$name] ?: $default;
	}


	
	public function getAllData()
	{
		return $this->data;
	}


	
	public function getInput($name, $default = null)
	{
		return $this->query[$name] ?: $this->data[$name] ?: $default;
	}


	
	public function getSpecialInput($name)
	{
		return $this->specialInputs[$name] ?: null;
	}



	public function hasData($name)
	{
		return array_key_exists($name, $this->data) ? true : false;
	}



	public function hasQuery()
	{
		return array_key_exists($name, $this->query) ? true : false;
	}



	public function isSoap()
	{
		if (isset($_SERVER['HTTP_SOAPACTION']) || strrpos($_SERVER['CONTENT_TYPE'], 'application/soap+xml') !== false)
		{
			return true;
		}

		return false;
	}



	public function isJson()
	{
		return (json_decode($request) != null) ? true : false;
	}



	public function isAjax()
	{// must be edited
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}



	public function isPut()
	{
		return $this->getMethod() == 'PUT';
	}



	public function isPatch()
	{
		return $this->getMethod() == 'PATCH';
	}



	public function isGet()
	{
		return $this->getMethod() == 'GET';
	}



	public function isOptions()
	{
		return $this->getMethod() == 'OPTIONS';
	}



	public function isHead()
	{
		return $this->getMethod() == 'HEAD';
	}



	public function isDelete()
	{
		return $this->getMethod() == 'DELETE';
	}



	public function isPost()
	{
		return $this->getMethod() == 'POST';
	}



	public function hasFiles()
	{
		# code...
	}



	public function hasFile($name)
	{
		# code...
	}



	public function getFile($name)
	{
		# code...
	}



	public function getFiles()
	{
		# code...
	}



	public function isSecureRequest()
	{
		return (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) ? true : false;
	}



	public function isSecure()
	{
		return $this->isSecureRequest();
	}



	public function getServerAddress($value='')
	{
		# code...
	}


	public function getServerName($value='')
	{
		# code...
	}



	public function getHttpHost($value='')
	{
		# code...
	}



	public function getClientAddress($trustForwardedHeader)
	{
		//Gets most possible client IPv4 Address. This method search in $_SERVER[‘REMOTE_ADDR’] and optionally in $_SERVER[‘HTTP_X_FORWARDED_FOR’]
	}


	public function getURI()
	{
		return $_SERVER['REQUEST_URI'];
	}



	public function getUserAgent()
	{
		return $_SERVER['HTTP_USER_AGENT'];
	}



	public function getHTTPReferer($value='')
	{
		return $_SERVER['HTTP_REFERER'];
	}



	public function getContent()
	{
		if ($this->content === null)
		{
			$this->content = file_get_contents('php://input');
		}

		return $this->content;
	}
}

//http://docs.phalconphp.com/en/latest/api/Phalcon_Http_Request.html