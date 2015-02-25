<?php

namespace Qlake\View;

use Qlake\Exception\ClearException;

class Finder
{
	private static $aliasSeparator = '::';

	private static $directorySeparator = '.';

	private $aliases = [/*'theme' => 'themes\\blue'*/];


	public function __construct($baseDir, array $extensions)
	{
		$this->baseDir = rtrim($baseDir, '/\\');

		$this->extensions = $extensions;
	}


	public function find($name)
	{			
		$name = str_replace(static::$directorySeparator, '/', $name);

		$path = $this->parseAlias($name);

		$path = $this->normalizePath($path);

		//foreach ($this->paths as $path)
		//{
			foreach ($this->extensions as $extension => $class)
			{
				$file = $this->baseDir . '/' . implode('/', [/*trim($path, '/\\'), */$path . '.' . $extension]);

				if (file_exists($file))
				{
					return $file;
				}
			}
		//}

		return false;
	}


	public function parseAlias($name)
	{
		if (strpos($name, static::$aliasSeparator ) !== false)
		{
			list($alias, $path) = explode(static::$aliasSeparator, $name);

			if (isset($this->aliases[$alias]))
			{
				return $this->aliases[$alias] .'/'. $path;
			}
		}

		return $name;
	}


	public function normalizePath($path)
	{
		$path = preg_replace("/[\\\\\\/]+/", '/', $path);

		$path = trim($path, '/');

		return $path;
	}
}