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



	public function testIsRouteMethod()
	{
		$route = new Route(['GET', 'HEAD'], '/', null);

		$this->assertTrue($route->isMethod('GET'));
		$this->assertTrue($route->isMethod('Get'));
		$this->assertTrue($route->isMethod('get'));
		$this->assertTrue($route->isMethod('HEAD'));
		$this->assertTrue($route->isMethod('Head'));
		$this->assertTrue($route->isMethod('head'));

		$this->assertFalse($route->isMethod('some'));
		$this->assertFalse($route->isMethod('post'));
		$this->assertFalse($route->isMethod('HEADD'));
		$this->assertFalse($route->isMethod('POST'));
		$this->assertFalse($route->isMethod('PUT'));
	}



	public function testSetAndGetRouteUri()
	{
		$route = new Route(['GET'], '', null);
		$this->assertEquals('', $route->getUri());

		$route->setUri('/');
		$this->assertEquals('/', $route->getUri());

		$route->setUri('/path/to/{var1}/{var2?}/{var3?:\d{2}}');
		$this->assertEquals('/path/to/{var1}/{var2?}/{var3?:\d{2}}', $route->getUri());
	}



	public function testSetAndGetRouteName()
	{
		$route = new Route(['GET'], '', null);

		$this->assertEquals(null, $route->getName());

		$route->setName('routeName');
		
		$this->assertEquals('routeName', $route->getName());
	}


	public function testSetPrefixUriRoute()
	{
		$route = new Route(['GET'], 'to/{param}', null);

		$route->setUri('to/{param}');
		$route->setPrefixUri('path');
		$this->assertEquals('path/to/{param}', $route->getUri());

		$route->setUri('to/{param}');
		$route->setPrefixUri('path2');
		$this->assertEquals('path2/path/to/{param}', $route->getUri());

		$route->setUri('/path/to/{param}/');
		$route->setPrefixUri('/path3');
		$this->assertEquals('/path3//path/to/{param}/', $route->getUri());

		$route->setUri('/path/to/{param}/');
		$route->setPrefixUri('/path4/');
		$this->assertEquals('/path4//path/to/{param}/', $route->getUri());

		$route->setUri('/path/to/{param}/');
		$route->setPrefixUri('');
		$this->assertEquals('/path/to/{param}/', $route->getUri());
	}
}