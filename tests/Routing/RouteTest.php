<?php

use Qlake\Routing\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
	public function testSetNameKeyConfig()
	{
		$route = new Route(['GET'], '/', null);

		$this->assertEquals(['GET'], $route->getMethods());
	}
}