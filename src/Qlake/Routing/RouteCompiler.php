<?php

namespace Qlake\Routing;

use Qlake\Routing\Route;

class RouteCompiler
{

	/**
	 * The instance of route that will be compiled.
	 *
	 * @var Qlake\Routing\Route
	 */
	protected $route;



	/**
	 * Compile route URI and create pattern from its URL.
	 *
	 * @param Qlake\Routing\Route $route
	 * @return void
	 */
	public function compile($route)
	{
		$this->route = $route;

		$uri = $this->normalizeUri($this->route->getUri()) .'/';

		// match patterns like /{param?:regex}
		// tested in https://regex101.com/r/gP6yH7
		$regex = preg_replace_callback(
			'#(?:(\\/)?\\{(\\w+)(\\?)?(?::((?:\\\\\\{|\\\\\\}|[^{}]|\\{\\d(?:\\,(?:\\d)?)?\\})+))?\\})#',
			array($this, 'createRegex'),
			$uri
		);


		if (substr($uri, -1) === '/')
		{
			$regex .= '?';
		}

		$regex = '#^' . $regex . '$#';

		if ($this->route->isCaseSensitive() === false)
		{
			$regex .= 'i';
		}

		$this->route->setPattern($regex);
	}



	/**
	 * Callback from creating route param names
	 *
	 * @param array $matched
	 * @return string
	 */
	protected function createRegex($matched)
	{
		//
		$startSlash = $matched[1] ? true : false;
		$param      = $matched[2];
		$optional   = $matched[3] ? true : false;
		$pattern    = $matched[4] ?: null;

		$pattern = $this->route->getCondition($param) ?: $pattern ?: '[^/]+';

		$this->route->addParamName($param);

		if ($startSlash)
		{
			$regex = ($optional ? '(/' : '') .'(?P<' . $param . '>' . $pattern . ')'. ($optional ? ')?' : '');
		}
		else
		{
			$regex = '(?P<' . $param . '>' . $pattern . ')'. ($optional ? '?' : '');
		}

		return $regex;
	}



	protected function normalizeUri($uri)
	{
		$uri = preg_replace('#([\/\\\\]{2,}|\\\\+)#', '/', $uri);

		return trim($uri, '/');
	}
}