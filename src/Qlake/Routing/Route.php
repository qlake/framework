<?php

namespace Qlake\Routing;

use Qlake\Routing\RouteCompiler;
use Qlake\Http\Request;
use Qlake\Exception\ClearException;
use Qlake\View\View;

class Route
{
	public $uri;

	public $pattern;

	public $action;

	public $actionType;

	public $methods = [];

	public $name;

	public $filters = [];

	public $conditions = [];

	public $params = [];
	
	public $paramNames = [];

	public $compiler;

	public $compiled = false;

	public $caseSensitive = true;


	public function __construct($methods, $uri, $action, $compiler = null)
	{
		$this->methods = (array) $methods;

		$this->uri = trim($uri, '/');

		$this->action = $action;

		$this->compiler = $compiler ?: new RouteCompiler;
	}


	public function name($name)
	{
		$this->setName($name);

		return $this;
	}


	public function setName($name)
	{
		$this->name = (string)$name;
	}


	public function getName()
	{
		return $this->name;
	}


	public function conditions($conditions)
	{
		$this->setConditions($conditions);

		return $this;
	}


	public function setConditions($conditions)
	{
		$this->conditions = array_merge($this->conditions, $conditions);
	}


	public function getConditions()
	{
		return $this->conditions;
	}


	public function setPrefixUri($prefix)
	{
		$prefix = trim($prefix, '/') .'/';

		$this->uri = $prefix . $this->uri;

		return $this;
	}


	public function compile()
	{
		if ($this->compiled)
		{
			return;
		}

		$this->compiler->compile($this);

		$this->compiled = true;
	}


	public function checkMatching($pathInfo)
	{
		$this->compile();

		$pathInfo = trim($pathInfo, '/');

		if (!preg_match($this->pattern, $pathInfo, $paramValues))
		{
            return false;
        }

        foreach ($this->paramNames as $name)
        {
            if (isset($paramValues[$name]))
            {
                if (isset($this->paramNamesPath[$name]))
                {
                    $this->params[$name] = explode('/', urldecode($paramValues[$name]));
                }
                else
                {
                    $this->params[$name] = urldecode($paramValues[$name]);
                }
            }
        }

        return true;
	}


	public function dispatch(Request $request)
	{
		$call_user_func_array = function($callable, array $args = [])
		{
			if (!is_callable($callable))
			{
				throw new ClearException("A Real Action Not Found", 1);	
			}

			if (is_string($callable))
			{
				if (function_exists($callable))
				{
					$function = new \ReflectionFunction($callable);
				}
				else
				{
					$function = new \ReflectionMethod($callable);
				}
			}
			elseif (is_object($callable) && ($callable instanceof \Closure))
			{
				$function = new \ReflectionFunction($callable);
			}
			elseif (is_array($callable))
			{
				$function = (new \ReflectionClass($callable[0]))->getMethod($callable[1]);
			}

			$functionParams = $function->getParameters();

			$params = [];

			foreach ($functionParams as $param)
			{
				if ($args[$param->name])
				{
					$params[$param->name] = $args[$param->name];
				}
				elseif($param->isDefaultValueAvailable()){
					$params[$param->name] = $param->getDefaultValue();
				}
			}

			return call_user_func_array($callable, $params);
		};

		$callable = $this->action;

		if (is_string($callable))
		{
			// if $callable was like App\Controllers\ControllerClass or App\Controllers\ControllerClass::actionMethod
			if (preg_match("/^\\w+(\\\\\\w*)*(::\\w+)?$/", $callable))
			{
				$callable = explode('::', $callable);

				$callable[1] = $callable[1] ?: 'index';

				list($class, $method) = $callable;

				if (class_exists($class))
				{
					$controller = new $class();

					if (method_exists($controller, $method))
					{
						$call_user_func_array([$controller, $method], $this->params);
					}
					else
					{
						call_user_func([$controller, '__missing'], [$method, $this->params]);
					}
				}
				else
				{
					throw new ClearException("Controller [{$callable[0]}] Not Found", 4);
				}
			}
			else
			{
				throw new ClearException("Invalid Controller Name [{$callable}]", 4);	
			}
		}
		elseif (is_object($callable) && ($callable instanceof \Closure))
		{
			$res = $call_user_func_array($callable, $this->params);

			if ($res instanceof View)
			{
				echo $res->getContent();
			}
			elseif (is_string($res))
			{
				echo $res;
			}
		}
		else
		{
			throw new ClearException("Route Handler For URL [{$this->uri}] Not Found", 4);
		}
	}
}