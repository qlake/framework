<?php

namespace Qlake\Architecture;

use ArrayAccess;
use Closure;
use Qlake\Exception\ClearException;

class Container implements ArrayAccess
{
	/**
     * Set of application providers.
     *
     * @var Qlake\Application
     */
	protected $providers = [];
	
	/**
	 * Description
	 * 
	 * @param string $name 
	 * @param Closure $provider 
	 * @return null
	 */
	public function singleton($name, $provider)
	{
		$this->providers[$name] = ['provider' => $provider, 'type' => 'singleton'];

		return $this;
	}


	public function bind($name, $provider)
	{
		$this->providers[$name] = ['provider' => $provider, 'type' => 'instance'];

		return $this;
	}


	public function offsetSet($name, $provider)
	{
		throw new ClearException("Use [singleton] or [bind] method for adding a service provider to Application.", 0);
	}


	public function offsetGet($name)
	{
		if (isset($this->providers[$name]))
		{
			if ($this->providers[$name]['type'] == 'singleton')
			{
				if ($this->providers[$name]['provider'] instanceof Closure)
				{
					return $this->providers[$name]['provider'] = call_user_func_array($this->providers[$name]['provider'], [$this]);
				}
				
				return $this->providers[$name]['provider'];
			}
			elseif ($this->providers[$name]['type'] == 'instance')
			{
				return call_user_func_array($this->providers[$name]['provider'], [$this]);
			}
		}

		throw new ClearException("Service Provider [$name] Not Found.", 1);
	}

	public function offsetExists($name)
	{
		return isset($this->providers[$name]);
	}

	public function offsetUnset($name)
	{
		unset($this->providers[$name]);
	}
}

