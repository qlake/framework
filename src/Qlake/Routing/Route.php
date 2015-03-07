<?php

namespace Qlake\Routing;

use Qlake\Http\Request;
use Qlake\Exception\ClearException;
use Qlake\View\View;

class Route
{
	protected $uri;


	protected $pattern;


	protected $handler;


	protected $handlerType;


	protected $methods = [];


	protected $name;


	protected $filters = [];


	protected $conditions = [];


	protected $params = [];


	protected $paramNames = [];


	protected $compiler;


	protected $compiled = false;


	protected $caseSensitive = true;


	/**
	 * Description
	 * @param type array $methods 
	 * @param type $uri 
	 * @param type $handler 
	 * @return type
	 */
	public function __construct(array $methods, $uri, $handler)
	{
		$this->methods = (array)$methods;

		$this->uri = $uri;

		$this->handler = $handler;
	}



	/**
	 * Description
	 * @return type
	 */
	public function getMethods()
	{
		return $this->methods;
	}



	/*public function setMethods(array $methods)
	{
		$this->methods = array_merge($this->methods, $methods);

		return $this;
	}*/



	/**
	 * Description
	 * @param type $method 
	 * @return type
	 */
	public function isMethod($method)
	{
		return in_array(strtoupper($method), $this->methods);
	}



	/**
	 * Description
	 * @return type
	 */
	public function getUri()
	{
		return $this->uri;
	}



	/**
	 * Description
	 * @param type $uri 
	 * @return type
	 */
	public function setUri($uri)
	{
		$this->uri = $uri;

		return $this;
	}



	/*public function addParam($param, $value)
	{
		$this->params[$param] = $value;

		return $this;
	}*/



	public function hasParam($param)
	{
		return in_array($param, $this->params);
	}



	public function getParam($param)
	{
		return $this->params[$param] ?: null;
	}



	public function getParams()
	{
		return $this->params;
	}



	/*public function setParams($params)
	{
		$this->params = array_merge($this->params, $params);

		return $this;
	}*/



	/*public function addParamName($param)
	{
		if (in_array($param, $this->paramNames) === false)
		{
			$this->paramNames[] = $param;
		}

		return $this;
	}*/



	/*public function setPattern($pattern)
	{
		$this->pattern = $pattern;

		return $this;
	}*/



	/*public function getPattern()
	{
		return $this->pattern;
	}*/



	/*public function setCondition($param, $pattern)
	{
		$this->conditions[$param] = $pattern;

		return $this;
	}*/



	/*public function getCondition($param)
	{
		return $this->conditions[$param] ?: null;
	}*/



	/*public function isCaseSensitive()
	{
		return $this->caseSensitive;
	}*/




	/*public function name($name)
	{
		$this->setName($name);

		return $this;
	}*/


	/**
	 * Description
	 * @param type $name 
	 * @return type
	 */
	public function setName($name)
	{
		$this->name = (string)$name;

		return $this;
	}


	/**
	 * Description
	 * @return type
	 */
	public function getName()
	{
		return $this->name;
	}



	/*public function conditions($conditions)
	{
		$this->setConditions($conditions);

		return $this;
	}*/



	/*public function setConditions($conditions)
	{
		$this->conditions = array_merge($this->conditions, $conditions);

		return $this;
	}*/



	/*public function getConditions()
	{
		return $this->conditions;
	}*/



	public function setPrefixUri($prefix)
	{
		//$prefix =  $this->normalizeUri($prefix);

		if (empty($prefix))
		{
			return $this;
		}

		$this->uri = $prefix .'/'. $this->uri;

		return $this;
	}



	/*public function getPrefixUri()
	{
		return $this->prefixUri;
	}*/



	/*public function getHandlerType()
	{
		if (is_string($this->handlre))
		{
			return 'Controller';
		}
		elseif (is_object($this->handlre) && $this->handlre instanceof Closure)
		{
			return 'Closure';
		}
	}*/



	public function isMatch($pathInfo)
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
                //if (isset($this->paramNamesPath[$name]))
                //{
                //    $this->params[$name] = explode('/', urldecode($paramValues[$name]));
                //}
                //else
                //{
                    $this->params[$name] = urldecode($paramValues[$name]);
                //}
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

		$callable = $this->handler;

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



	public function compile()
	{
		//if ($this->compiled)
		//{
		//	return;
		//}

		$this->compiled = true;

		$uri = $this->normalizeUri($this->getUri());

		// match patterns like /{param?:regex}
		// tested in https://regex101.com/r/gP6yH7
		$regex = preg_replace_callback(
			'#(?:(\\/)?\\{(\\w+)(\\?)?(?::((?:\\\\\\{|\\\\\\}|[^{}]|\\{\\d(?:\\,(?:\\d)?)?\\})+))?\\})#',
			[$this, 'createRegex'],
			$uri
		);

		$regex .= "\\/?";

		$regex = '#^' . $regex . '$#';

		if ($this->caseSensitive === false)
		{
			$regex .= 'i';
		}

		$this->pattern = $regex;
	}



	/**
	 * Callback from creating route param names
	 *
	 * @param array $matched
	 * @return string
	 */
	protected function createRegex($matched)
	{
		$startSlash = $matched[1] ? true : false;
		$param      = $matched[2];
		$optional   = $matched[3] ? true : false;
		$pattern    = $matched[4] ?: null;

		$pattern = $this->conditions[$param] ?: $pattern ?: '[^/]+';

		$this->paramNames[] = $param;

		if ($startSlash)
		{
			$regex = ($optional ? "(\\/" : '') .'(?P<' . $param . '>' . $pattern . ')'. ($optional ? ')?' : '');
		}
		else
		{
			$regex = '(?P<' . $param . '>' . $pattern . ')'. ($optional ? '?' : '');
		}

		return $regex;
	}



	protected function normalizeUri($uri)
	{
		$uri = preg_replace("#([\\/]{2,})#", '/', $uri);

		return trim($uri, '/');
	}
}