<?php

namespace Qlake\View\Engine;

use Qlake\View\Finder;

class Twig implements EngineInterface
{
	private $data = [];


	private $finder;


	public function __construct(Finder $finder)
	{
		$this->finder = $finder;
	}


	public function by($name, $value)
	{
		$this->data[$name] = $value;

		return $this;
	}


	public function render($name, array $data = [])
	{
		$this->name = $name;

		$this->data = array_merge($this->data, $data)

		return $this;
	}


	public function content()
	{
		return (string)$this;
	}


	public function compile()
	{
		$viewFile = pathinfo($this->parseName($this->name), PATHINFO_BASENAME);

		$path = pathinfo($this->parseName($this->name), PATHINFO_DIRNAME);

		$loader = new \Twig_Loader_Filesystem($path);

		$twig = new \Twig_Environment($loader, [
			'cache' => 'cache',
		]);

		$data =  $this->data;

		$f = function() use ($path, $viewFile, $data)
		{
			foreach ($data as $key => $value) {
				${$key} = $value;
			}

			ob_start();
			require $path . '/' . $viewFile;
			return ob_get_clean();

		};

		return $f();

		return $twig->render($viewFile, $data);
	}
}