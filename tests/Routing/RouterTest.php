<?php

use Qlake\Routing\Router;

class RouterTest extends PHPUnit_Framework_TestCase
{
	public function testGetRoutesMustBeEmpty()
	{
		$router = $this->getRouter();

		$this->assertEquals([], $router->getRoutes());
	}



	public function testAddRouteByGetMethod()
	{
		$router = $this->getRouter();

		$route = $router->get(null, null);

		$this->assertEquals([$route], $router->getRoutes());
		$this->assertTrue($route->isMethod('GET'));
	}



	public function testAddRouteByPostMethod()
	{
		$router = $this->getRouter();

		$route = $router->post(null, null);

		$this->assertEquals([$route], $router->getRoutes());
		$this->assertTrue($route->isMethod('POST'));
	}



	public function testAddRouteByHeadMethod()
	{
		$router = $this->getRouter();

		$route = $router->head(null, null);

		$this->assertEquals([$route], $router->getRoutes());
		$this->assertTrue($route->isMethod('HEAD'));
	}



	public function testAddRouteByPutMethod()
	{
		$router = $this->getRouter();

		$route = $router->put(null, null);

		$this->assertEquals([$route], $router->getRoutes());
		$this->assertTrue($route->isMethod('PUT'));
	}


	public function testAddRouteByPatchMethod()
	{
		$router = $this->getRouter();

		$route = $router->patch(null, null);

		$this->assertEquals([$route], $router->getRoutes());
		$this->assertTrue($route->isMethod('PATCH'));
	}


	public function testAddRouteByDeleteMethod()
	{
		$router = $this->getRouter();

		$route = $router->delete(null, null);

		$this->assertEquals([$route], $router->getRoutes());
		$this->assertTrue($route->isMethod('DELETE'));
	}



	public function testAddRouteByOptionsMethod()
	{
		$router = $this->getRouter();

		$route = $router->options(null, null);

		$this->assertEquals([$route], $router->getRoutes());
		$this->assertTrue($route->isMethod('OPTIONS'));
	}



	public function testAddRouteByAnyMethod()
	{
		$router = $this->getRouter();

		$route = $router->any(null, null);

		$this->assertEquals([$route], $router->getRoutes());
		$this->assertTrue($route->isMethod('GET'));
		$this->assertTrue($route->isMethod('POST'));
		$this->assertTrue($route->isMethod('HEAD'));
		$this->assertTrue($route->isMethod('OPTIONS'));
		$this->assertTrue($route->isMethod('PUT'));
		$this->assertTrue($route->isMethod('PATCH'));
		$this->assertTrue($route->isMethod('DELETE'));
	}



	public function testAddSeveralRoute()
	{
		$router = $this->getRouter();

		$route1 = $router->get(null, null);
		$route2 = $router->post(null, null);
		$route3 = $router->put(null, null);
		$route4 = $router->patch(null, null);
		$route5 = $router->delete(null, null);
		$route6 = $router->head(null, null);
		$route7 = $router->options(null, null);
		$route8 = $router->post(null, null);

		$this->assertEquals([$route1, $route2, $route3, $route4, $route5, $route6, $route7, $route8], $router->getRoutes());
	}



	public function testCreateGroupWithOnlyClosure()
	{
		$router = $this->getRouter();

		$var = false;

		$router->group(function()use(&$var)
		{
			$var = true;
		});

		$this->assertEquals(true, $var);
	}



	public function testCreateGroupWithUriAndClosure()
	{
		$router = $this->getRouter();

		$route;

		$router->group('foo/bar', function()use($router, &$route)
		{
			$route = $router->get('uri', null);
		});

		$this->assertEquals('foo/bar/uri', $route->getUri());
	}




	public function getRouter()
	{
		return new Router;
	}
}
