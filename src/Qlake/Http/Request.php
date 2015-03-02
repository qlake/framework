<?php

namespace Qlake\Http;

use Qlake\Http\Header;

class Request
{

	protected $header;


	protected $method;


	protected $query = [];


	protected $data = [];


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
}