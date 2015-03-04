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


	public function getScriptName()
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


	public function getRequestUri()
	{
		return $this['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
	}


	public function getPathInfo()
	{
		// This method not stable and must be edited!
		$pathInfo = '';

		if ($_SERVER['PATH_INFO'])
		{
			$pathInfo = $_SERVER['PATH_INFO'];
		}
		elseif ($_SERVER['REDIRECT_REDIRECT_STATUS'])
		{
			// for request whitout vitual host and path info and query strings
			if ($_SERVER['REDIRECT_URL'] == $_SERVER['REQUEST_URI'])
			{
				$pathInfo = '/';
			}
			elseif ($_SERVER['REDIRECT_STATUS'])
			{
				$pathInfo = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['REDIRECT_URL']);
			}
		}
		elseif ($_SERVER['REDIRECT_STATUS'])
		{
			if ($_SERVER['REDIRECT_URL'] == $_SERVER['REQUEST_URI'])
			{
				$pathInfo = '/';
			}
			elseif ($_SERVER['REDIRECT_QUERY_STRING'] && ($_SERVER['REDIRECT_URL'] .'?'. $_SERVER['REDIRECT_QUERY_STRING'] == $_SERVER['REQUEST_URI']))
			{
				$pathInfo = '/';
			}
			else
			{
				$pathInfo = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['REDIRECT_URL']);
			}
		}
		else
		{
			// Wow! What is this line?
		}

		$pathInfo = trim($pathInfo, '/');

		return $this['PATH_INFO'] = urldecode($pathInfo);
	}



	public function runMethod($name)
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