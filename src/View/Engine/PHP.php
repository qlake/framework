<?php

namespace Qlake\View\Engine;

use Qlake\View\Finder;
use Qlake\View\EngineInterface;

class PHP implements EngineInterface
{
	private $data = [];

	private $finder;


	public function __construct(Finder $finder)
	{
		$this->finder = $finder;
	}


	public function by($file, $value)
	{
		$this->data[$file] = $value;

		return $this;
	}


	public function render($file, array $data = [])
	{
		$this->file = $file;

		$this->data = array_merge($this->data, $data);

		return $this->compile();
	}


	public function compile()
	{
		$data =  $this->data;

		$file = $this->file;

		$f = function() use ($file, $data)
		{
			foreach ($data as $key => $value) 
			{
				${$key} = $value;
			}

			ob_start();

			require $file;

			return ob_get_clean();
		};

		return $f();
	}
}