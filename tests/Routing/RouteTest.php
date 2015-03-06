<?php

use Qlake\Routing\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
	public function testGetRouteMethods()
	{
		$route = new Route(['GET'], '/', null);

		$this->assertEquals(['GET'], $route->getMethods());


		$route = new Route(['GET', 'POST'], '/', null);

		$this->assertEquals(['GET', 'POST'], $route->getMethods());


		$route = new Route(['POST', 'GET'], '/', null);

		$this->assertEquals(['POST', 'GET'], $route->getMethods());


		$route = new Route(['GET', 'POST', 'PUT', 'PATCH', 'HEAD', 'DELETE', 'OPTIONS'], '/', null);

		$this->assertEquals(['GET', 'POST', 'PUT', 'PATCH', 'HEAD', 'DELETE', 'OPTIONS'], $route->getMethods());
	}


	/*public function testGetRouteMethods()
	{
	}*/
}