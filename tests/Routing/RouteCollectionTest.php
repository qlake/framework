<?php

use Qlake\Routing\Route;
use Qlake\Routing\Collection;

class RouteCollectionTest extends PHPUnit_Framework_TestCase
{
	public function testGetRoutes()
	{
		$c = $this->getCollection();

		$this->assertEquals([], $route->getRoutes());
	}

	public function testAddRouteBySingleMethod()
	{
		$route = new Route(['GET'], '/', null);
		$c = $this->getCollection();
		$c->addRoute($route);

		$this->assertEquals(['GET'], $route->getMethods());

		$route = new Route(['GET', 'POST'], '/', null);
		$this->assertEquals(['GET', 'POST'], $route->getMethods());

		$route = new Route(['POST', 'GET'], '/', null);
		$this->assertEquals(['POST', 'GET'], $route->getMethods());

		$route = new Route(['GET', 'POST', 'PUT', 'PATCH', 'HEAD', 'DELETE', 'OPTIONS'], '/', null);
		$this->assertEquals(['GET', 'POST', 'PUT', 'PATCH', 'HEAD', 'DELETE', 'OPTIONS'], $route->getMethods());
	}



	public function getCollection()
	{
		return new Collection;
	}
}