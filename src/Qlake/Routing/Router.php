<?php

namespace Qlake\Routing;

use Qlake\Http\Request;
use Qlake\Support\Queue;
use Qlake\Exception\ClearException;

class Router
{
	/**
	 * Instance of RouteCollection
	 *
	 * @var Qlake\Routing\Collection
	 */
	protected $routes;

	/**
	 * Instance of SplStack for manage route groups
	 *
	 * @var \SplStack
	 */
	protected $groups;



	/**
	 * Create an instance of Router class.
	 *
	 */
	public function __construct()
	{
		$this->routes = new Collection;

		$this->groups = new \SplStack;
	}



	/**
	 * Create and register a route by GET method.
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Qlake\Routing\Route
	 */
	public function get($uri, $action)
	{
		return $this->addRoute(['GET'], $uri, $action);
	}



	/**
	 * Create and register a route by HEAD method.
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Qlake\Routing\Route
	 */
	public function head($uri, $action)
	{
		return $this->addRoute(['HEAD'], $uri, $action);
	}



	/**
	 * Create and register a route by POST method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Qlake\Routing\Route
	 */
	public function post($uri, $action)
	{
		return $this->addRoute(['POST'], $uri, $action);
	}



	/**
	 * Create and register a route by PUT method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Qlake\Routing\Route
	 */
	public function put($uri, $action)
	{
		return $this->addRoute(['PUT'], $uri, $action);
	}



	/**
	 * Create and register a route by PATCH method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Qlake\Routing\Route
	 */
	public function patch($uri, $action)
	{
		return $this->addRoute(['PATCH'], $uri, $action);
	}



	/**
	 * Create and register a route by DELETE method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Qlake\Routing\Route
	 */
	public function delete($uri, $action)
	{
		return $this->addRoute(['DELETE'], $uri, $action);
	}



	/**
	 * Create and register a route by OPTIONS method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Qlake\Routing\Route
	 */
	public function options($uri, $action)
	{
		return $this->addRoute(['OPTIONS'], $uri, $action);
	}



	/**
	 * Create and register a route by ANY method
	 *
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Qlake\Routing\Route
	 */
	public function any($uri, $action)
	{
		return $this->addRoute(['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $uri, $action);
	}



	public function group()
	{
		$args = func_get_args();

		$argc = count($args);

		$uri = '';
		$closure;
		$options = [];

		if ($argc >= 3)
		{
			$uri = array_shift($args);

			$closure = array_pop($args);

			$options = array_shift($args);
		}
		elseif ($argc == 2)
		{
			$uri = array_shift($args);

			$closure = array_pop($args);

			$options = [];
		}
		elseif ($argc == 1)
		{
			$uri = '';

			$closure = array_pop($args);

			$options = [];
		}
		elseif ($argc == 0)
		{
			throw new ClearException("Calling Whitout Argument", 1);
		}
		

		$this->groups->push([
			'uri'     => $uri,
			'options' => $options,
		]);

		if (is_callable($closure))
		{
			call_user_func($closure);
		}

		$this->groups->pop();

		return $this;
	}



	protected function processGroupsUri()
	{
		$uri = '';

		foreach ($this->groups as $group)
		{
			$uri = trim($group['uri'], '/') . '/' . $uri;
		}

		return trim($uri, '/');
	}



	/**
	 * Create and register a route
	 *
	 * @param array $methods
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Qlake\Routing\Route
	 */
	protected function addRoute($methods, $uri, $action)
	{
		$route = $this->createRoute($methods, $uri, $action);

		$route->setPrefixUri($this->processGroupsUri());

		$this->routes->addRoute($route);

		return $route;
	}



	/**
	 * Create and return a route instance
	 *
	 * @param array $methods
	 * @param string $uri
	 * @param Closure|string $action
	 * @return Qlake\Routing\Route
	 */
	protected function createRoute($methods, $uri, $action)
	{
		return new Route($methods, $uri, $action);
	}



	/**
	 * Description
	 * @return type
	 */
	public function current()
	{
		
	}



	/**
	 * Description
	 * @return type
	 */
	public function matches()
	{
		
	}



	/**
	 * Chack matching requested uri by registered routes.
	 * 
	 * @param Qlake\Http\Request $request 
	 * @return Qlake\Routing\Route|null
	 */
	public function match(Request $request)
	{
		foreach ($this->routes->filterByMethod($request->getMethod()) as $route)
		{
			if ($route->isMatch($request->getPathInfo()))
			{
				return $route;
			}
		}

		return null;
	}



	public function handel(Request $request)
	{
		$route = $this->match($request);

		if ($route)
		{
			$route->dispatch($request);
		}
		else
		{
			throw new ClearException("Not Found Any Route For [". urldecode($request->getUri()) ."] Uri.");
		}
	}
	
	/*
	public string getRewriteUri ()
	public Router removeExtraSlashes (boolean $remove)
	public string getDefaultController ()
	public Router setDefaultAction (string $actionName)
	public string getDefaultAction ()
	public handle ([string $uri])
	public Router notFound (array|string $paths)


	public string getControllerName ()
	public string getActionName ()


	public Route getMatchedRoute ()
	public array getMatches ()
	public bool wasMatched ()
	public Route | false getRouteById (string $id)
	public Route getRouteByName (string $name)

	// For Route Or Router???
	public Router controller()
	public Router controllers()
	public Route via(array $methods)
	public restful()
	public Route as/setName/byName(atring $name)
	public beSecure()
	public Router notFound()
	public string getParam($name)
	public array getParams()
-	public Route | null getRouteByUrl()
	public getMatches()
	public bool isMatch($uri)
	*/

	public function getRoutes()
	{
		return $this->routes->getRoutes();
	}
}
