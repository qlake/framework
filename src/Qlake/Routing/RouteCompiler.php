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

		$uri = ltrim($this->route->getUri(), '/');

		// match patterns like /{param?:regex}
		$regex = preg_replace_callback(
			'#(\/\{((?:[^{}]++|(?1))*+)\})#',
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
		$sections = explode(':', $matched[2], 2);

		if (mb_substr($sections[0], -1) == '?')
		{
			$param = mb_substr($sections[0], 0, mb_strlen($sections[0])-1);

			$optional = true;
		}
		else
		{
			$param = $sections[0];
		}

		$pattern = $sections[1];

		$pattern = $this->route->getCondition($param) ?: $pattern ?: '[^/]+';

		$this->route->setParam($param);

		$regex = ($optional ? '(/' : '') .'(?P<' . $param . '>' . $pattern . ')'. ($optional ? ')?' : '');

		return $regex;
	}
}