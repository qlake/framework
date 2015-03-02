<?php

namespace Qlake\Http;

use Qlake\Http\Header;

class Request
{

	/**
	 * An instance of Qlake\Http\Header.
	 * 
	 * @var Qlake\Http\Header
	 */
	protected $header;


	/**
	 * Current request method.
	 * 
	 * @var srting
	 */
	protected $method;


	/**
	 * Array of GET params.
	 * 
	 * @var array
	 */
	protected $query = [];


	/**
	 * Array of POST params.
	 * 
	 * @var array
	 */
	protected $data = [];


	/**
	 * Array of special params that use in framework core services.
	 * 
	 * @var array
	 */
	protected $specialInputs =
	[
		'__method' => 'GET',
		'__csrf'   => '',
		'__url'    => '/',
	];


	/**
	 * An instance of Qlake\Http\Environment.
	 * 
	 * @var Qlake\Http\Environment
	 */
	public $env;


	/**
	 * An instance of Qlake\Http\Cookie.
	 * 
	 * @var Qlake\Http\Cookie
	 */
	protected $cookie;


	/**
	 * An array of instances of Qlake\Http\File.
	 * 
	 * @var array
	 */
	protected $files;


	/**
	 * Constructor of Qlake\Http\Request class.
	 * 
	 * @param array $query 
	 * @param array $data 
	 * @param $server 
	 * @param $cookies 
	 * @param $files 
	 * @param $content 
	 */
	public function __construct(array $query = [], array $data = [], $server = null, $cookies = [], $files = null, $content = null)
	{
		$this->header = new Header;
		$this->env    = new Environment;
		$this->query  = $this->parseInputs($query);
		$this->data   = $this->parseInputs($data);
	}


	/**
	 * Capture current request by current environment variables and return a new instance from Qlake\Http\Request class.
	 * 
	 * @return Qlake\Http\Request
	 */
	public static function capture()
	{
		return new static($_GET, $_POST);
	}


	/**
	 * Parse and filter GET and POST params from special params.
	 * 
	 * @param array $inputs 
	 * @return array
	 */
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


	/**
	 * Detect and return current request method.
	 * 
	 * @return string
	 */
	protected function detectMethod()
	{
		$method = $this->env['REQUEST_METHOD'];
		
		return strtoupper($this->getSpecialInput('__method', $method));
	}


	/**
	 * Return current request method.
	 * 
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method = $this->method ?: $this->detectMethod();
	}


	/**
	 * Return Header object from current request.
	 * 
	 * @return Qlake\Http\Header
	 */
	public function getHeader()
	{
		return $this->header;
	}


	/**
	 * Return GET param by given name.
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function getQuery($name, $default = null)
	{
		return $this->query[$name] ?: $default;
	}


	/**
	 * Return all GET params as array.
	 * 
	 * @return array
	 */
	public function getAllQuery()
	{
		return $this->query;
	}


	/**
	 * Return POST param by given name.
	 * 
	 * @param string $name 
	 * @param mixed $default 
	 * @return mixed
	 */
	public function getData($name, $default = null)
	{
		return $this->data[$name] ?: $default;
	}


	/**
	 * Return all POST params as array.
	 * 
	 * @return array
	 */
	public function getAllData()
	{
		return $this->data;
	}


	/**
	 * Return GET or POST param by given name.
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @return mixed|null
	 */
	public function getInput($name, $default = null)
	{
		return $this->query[$name] ?: $this->data[$name] ?: $default;
	}


	/**
	 * Return spacial input value like __method and __csrf.
	 * 
	 * @param string $name 
	 * @return string|null
	 */
	public function getSpecialInput($name)
	{
		return $this->specialInputs[$name] ?: null;
	}
}