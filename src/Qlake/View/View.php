<?php

namespace Qlake\View;

use Qlake\Exception\ClearException;
use Qlake\View\Finder;

class View
{
	private $finder;

	private $engine;

	private $theme;

	private $name;

	private $data = [];

	public $paths = [];


	private $extensions = [
		'php'        => 'Qlake\View\Engine\PHP',
		/*'smarty.php' => 'Qlake\View\Engine\Smarty',
		'twig.php'   => 'Qlake\View\Engine\Twig',
		'dwoo.php'   => 'Qlake\View\Engine\Dwoo',*/
	];


	public function __construct($baseDir)
	{
		$this->finder = new Finder($baseDir, $this->extensions);
	}


	public function make($name, array $data = [])
	{
		$this->name = $name;

		$this->data = $data;

		return $this;
	}


	public function render($name, array $data = [])
	{
		return $this->make($name, $data);
	}


	public function set($name, $value)
	{
		$this->data[$name] = $value;

		return $this;
	}


	public function by($name, $value)
	{
		return $this->set($name, $value);
	}


	public function getContent()
	{
		$file = $this->finder->find($this->name);

		if (!$file)
		{
			throw new ClearException("View [$this->name] Not Found!", 4);
			
		}

		$this->engine = $this->createEngine($file);

		return $this->engine->render($file, $this->data);
	}


	public function __tostring()
	{
		return $this->getContent();
	}


	private function createEngine($file)
	{
		$fileName = pathinfo($file)['basename'];

		$ext = substr($fileName, strpos($fileName, '.')+1);

		return $this->extensions[$ext] ? new $this->extensions[$ext]($this->finder) : new $this->extensions['php']($this->finder);
	}
}