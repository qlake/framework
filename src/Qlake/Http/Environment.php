<?php

namespace Qlake\Http;

use ArrayAccess;
use ArrayIterator;

class Environment implements ArrayAccess
{
	protected $data = [];



	public function __construct()
	{
        $this->getScriptName();
        $this->getRequestUri();
		$this->getPathInfo();
	}



	protected function getScriptName()
	{
        $scriptName = $_SERVER['SCRIPT_NAME']; // <-- "/foo/index.php"
        $requestUri = $_SERVER['REQUEST_URI']; // <-- "/foo/bar?test=abc" or "/foo/index.php/bar?test=abc"
        $queryString = $_SERVER['QUERY_STRING'] ?: ''; // <-- "test=abc" or ""

        // Physical path
        if (strpos($requestUri, $scriptName) !== false) {
            $physicalPath = $scriptName; // <-- Without rewriting
        } else {
            $physicalPath = str_replace('\\', '', dirname($scriptName)); // <-- With rewriting
        }
        return $this['SCRIPT_NAME'] = rtrim($physicalPath, '/'); // <-- Remove trailing slashes
	}



	protected function getRequestUri()
	{
		return $this['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
	}



	protected function getPathInfo()
	{
		$pathInfo = $_GET['_url'];

		$pathInfo = trim($pathInfo, '/');

		return $this['PATH_INFO'] = urldecode($pathInfo);
	}



	protected function runMethod($name)
	{
		// this line should be replaced by Str Class methods
		$method = 'get' . str_replace(' ', '', ucwords(strtolower(str_replace(['-', '_'], ' ', $name))));

		if (method_exists($this, $method))
		{
			return call_user_func_array([$this, $method], []);
		}

		return null;
	}



    public function offsetExists($name)
    {
        return isset($this->data[$name]);
    }



    public function offsetGet($name)
    {
        if (isset($this->data[$name]))
        {
            return $this->data[$name];
        }

        return $this->data[$name] = $this->runMethod($name) ?: $_SERVER[$name];
    }



    public function offsetSet($name, $value)
    {
        $this->data[$name] = $value;
    }



    public function offsetUnset($name)
    {
        unset($this->data[$name]);
    }



    public function getIterator()
	{
		return new ArrayIterator($this->data);
	}
}