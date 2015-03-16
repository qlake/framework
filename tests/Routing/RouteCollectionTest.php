<?php

use Qlake\Routing\Route;
use Qlake\Routing\Collection;

class RouteCollectionTest extends PHPUnit_Framework_TestCase
{
	public function testGetRoutes()
	{
		$c = $this->getCollection();

		$this->assertEquals([], $c->getRoutes());
	}



	public function testAddRouteBySingleMethod()
	{
		$route = new Route(['GET'], '/', null);

		$c = $this->getCollection();

		$c->addRoute($route);

		$this->assertEquals([$route], $c->getRoutes());
	}



	public function testAddRouteByMultipleMethod()
	{
		$route = new Route(['GET', 'POST'], '/', null);

		$c = $this->getCollection();

		$c->addRoute($route);

		$this->assertEquals([$route], $c->getRoutes());
	}



	public function testGetRoutesBySeveralRoute()
	{
		$route1 = new Route(['GET'], '/', null);
		$route2 = new Route(['GET'], '/', null);

		$c = $this->getCollection();

		$c->addRoute($route1);
		$c->addRoute($route2);

		$this->assertEquals([$route1, $route2], $c->getRoutes());
	}



	public function testfilterByMethod()
	{
		$route = new Route(['GET'], '/', null);

		$c = $this->getCollection();

		$c->addRoute($route);

		$this->assertEquals([$route], $c->filterByMethod('GET'));
		$this->assertNotEquals([$route], $c->filterByMethod('POST'));
		$this->assertEquals([], $c->filterByMethod('POST'));
	}



	public function testfilterByMethodBySeveralRoute()
	{
		$route1 = new Route(['GET'], '/', null);
		$route2 = new Route(['GET', 'POST', 'HEAD'], '/', null);
		$route3 = new Route(['POST'], '/', null);

		$c = $this->getCollection();

		$c->addRoute($route1);
		$c->addRoute($route2);
		$c->addRoute($route3);

		$this->assertEquals([$route1, $route2], $c->filterByMethod('GET'));
		$this->assertEquals([$route2], $c->filterByMethod('HEAD'));
		$this->assertEquals([$route2, $route3], $c->filterByMethod('POST'));
	}



	public function getCollection()
	{
		return new Collection;
	}
}