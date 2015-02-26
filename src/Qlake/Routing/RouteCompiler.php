<?php

namespace Qlake\Routing;

use Qlake\Routing\Route;

class RouteCompiler
{

	/**
	 * Instans of route that will be compile.
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

		$this->route->uri = ltrim($this->route->uri, '/');

		// match patterns {param:pattern}
		$regex = preg_replace_callback(
			'#(\/\{((?:[^{}]++|(?1))*+)\})#',
			array($this, 'createRegex'),
			$this->route->uri
		);

		if (substr($this->route->uri, -1) === '/')
		{
			$regex .= '?';
		}

		$regex = '#^' . $regex . '$#';

		if ($this->route->caseSensitive === false)
		{
			$regex .= 'i';
		}

		//$regex = preg_replace('#(?<!\\\\)([{}])#', '\\\\\1', $regex);
//echo $regex ."\n";exit;
		$this->route->pattern = $regex;
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
//print_r($param);


		$pattern = $sections[1];

		$pattern = $this->route->conditions[$param] ?: $pattern ?: '[^/]+';

		$this->route->paramNames[] = $param;

		$regex = ($optional ? '(/' : '') .'(?P<' . $param . '>' . $pattern . ')'. ($optional ? ')?' : '');

		// for optional params! its hard! no problem,i will do it next time
		//$regex .= substr($matched[0], -2, 1) === '?' ? '?' : '';

		//$regex .= substr($matched[0], -2, 1) === '*' ? '[^/]*' : '';

		return $regex;
	}
}