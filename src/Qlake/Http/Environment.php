<?php

namespace Qlake\Http;

use ArrayAccess;
use ArrayIterator;

class Environment implements ArrayAccess
{
	protected $data = [];



	public function __construct()
	{
	}




	protected function getRequestUri()
	{
		return $this['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
	}



	protected function getPathInfo()
	{
		$pathInfo = $_GET['_url'];

		return $this['PATH_INFO'] = urldecode($pathInfo);
	}



	protected function runMethod($name)
	{
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
