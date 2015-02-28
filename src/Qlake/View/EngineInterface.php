<?php

namespace Qlake\View;

interface EngineInterface
{
	public function by($name, $value);


	public function render($name, array $valuee = []);


	public function compile();
}