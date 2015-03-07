<?php

namespace Qlake\Routing;

use Qlake\Http\Request;
use Qlake\Exception\ClearException;
use Qlake\View\View;

class Route
{
	/**
	 * Route uri.
	 * 
	 * @var string
	 */
	protected $uri;


	/**
	 * Route uri compiled pattren.
	 * 
	 * @var string
	 */
	protected $pattern;


	/**
	 * Route handler.
	 * 
	 * @var string|Closure
	 */
	protected $handler;


	/**
	 * Route methods like POST and GET.
	 * 
	 * @var array
	 */
	protected $methods = [];


	/**
	 * Route name.
	 * 
	 * @var string
	 */
	protected $name;


	/**
	 * Route uri params by there values.
	 * 
	 * @var array
	 */
	protected $params = [];


	/**
	 * Route uri param names only.
	 * 
	 * @var array
	 */
	protected $paramNames = [];


	/**
	 * Route uri compile status.
	 * 
	 * @var bool
	 */
	protected $compiled = false;


	/**
	 * Route uri case sensitive status.
	 * 
	 * @var bool
	 */
	protected $caseSensitive = true;



	/**
	 * Create a route instance.
	 * 
	 * @param array $methods
	 * @param string $uri
	 * @param string|closure $handler
	 */
	public function __construct(array $methods, $uri, $handler)
	{
		$this->methods = (array)$methods;

		$this->uri = $uri;

		$this->handler = $handler;
	}



	/**
	 * Get all route's methods as array.
	 * 
	 * @return array
	 */
	public function getMethods()
	{
		return $this->methods;
	}



	/**
	 * Determine that route's method is it.
	 * 
	 * @param string $method
	 * @return bool
	 */
	public function isMethod($method)
	{
		return in_array(strtoupper($method), $this->methods);
	}



	/**
	 * Get the route uri.
	 * 
	 * @return string
	 */
	public function getUri()
	{
		return $this->uri;
	}



	/**
	 * Set the route uri.
	 * 
	 * @param string $uri
	 * @return Qlake\Routing\Route
	 */
	public function setUri($uri)
	{
		$this->uri = $uri;

		return $this;
	}



	/**
	 * Determine that route param exists.
	 * 
	 * @param string $param
	 * @return bool
	 */
	public function hasParam($param)
	{
		$param = (string)$this->params[$param];
		
		return strlen($param) > 0 ? true : false;
	}



	/**
	 * Get a param value by given name's or null if it's not be sets;
	 * 
	 * @param string $param
	 * @return string|null
	 */
	public function getParam($param)
	{
		return $this->params[$param] ?: null;
	}



	/**
	 * Get all route's named parameters as array.
	 * 
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}



	/**
	 * Get compiled route's uri pattren.
	 * 
	 * @return string
	 */
	public function getPattern()
	{
		return $this->pattern;
	}



	/**
	 * Set route's name.
	 * 
	 * @param string $name
	 * @return Qlake\Routing\Route
	 */
	public function setName($name)
	{
		$this->name = (string)$name;

		return $this;
	}



	/**
	 * Get route's name.
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}



	/**
	 * Set prefix to current route's uri. use by Route::group method.
	 * 
	 * @param string $prefix
	 * @return Qlake\Routing\Route
	 */
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



	/**
	 * Determine matching route by given uri.
	 * 
	 * @param string $pathInfo
	 * @return bool
	 */
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
			else{
				$this->params[$name] = null;
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



	/**
	 * Compile route uri pattern regex
	 * 
	 * @return Qlake\Routing\Route
	 */
	public function compile()
	{
		//reset arrays
		$this->params     = [];
		$this->paramNames = [];
		$this->conditions = [];

		$this->compiled = true;

		$uri = $this->normalizeUri($this->getUri());

		// match patterns like /{param?:regex}
		// tested in https://regex101.com/r/gP6yH7
		$regex = preg_replace_callback(
			'#(?:(\\/)?\\{(\\w+)(\\?)?(?::((?:\\\\\\{|\\\\\\}|[^{}]|\\{\\d(?:\\,(?:\\d)?)?\\})+))?\\})#',
			[$this, 'createRegex'],
			$uri
		);

		$regex .= "/?";

		$regex = '#^' . $regex . '$#';

		if ($this->caseSensitive === false)
		{
			$regex .= 'i';
		}

		$this->pattern = $regex;

		return $this;
	}



	/**
	 * Callback for creating route param names regex
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
			$regex = ($optional ? "(/" : '/') .'(?P<' . $param . '>' . $pattern . ')'. ($optional ? ')?' : '');
		}
		else
		{
			$regex = '(?P<' . $param . '>' . $pattern . ')'. ($optional ? '?' : '');
		}

		return $regex;
	}


	/**
	 * Convert multiple slashes to single.
	 * 
	 * @param string $uri 
	 * @return string
	 */
	protected function normalizeUri($uri)
	{
		$uri = preg_replace("#([\\/]{2,})#", '/', $uri);

		return trim($uri, '/');
	}
}