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

		//$route->setUri('to/{param}');
		$route->setPrefixUri('path2');
		$this->assertEquals('path2/path/to/{param}', $route->getUri());

		$route->setUri('/path/to/{param}/');
		$route->setPrefixUri('/path3');
		$this->assertEquals('/path3//path/to/{param}/', $route->getUri());

		$route->setUri('/path/to/{param}/');
		$route->setPrefixUri('/path4/');
		$this->assertEquals('/path4///path/to/{param}/', $route->getUri());

		$route->setUri('/path/to/{param}/');
		$route->setPrefixUri('');
		$this->assertEquals('/path/to/{param}/', $route->getUri());
	}


	public function testCompileRoute()
	{
		$route = new Route(['GET'], '', null);

		$route->setUri('path');
		$route->compile();
		$this->assertEquals('#^path/?$#', $route->getPattern());

		$route->setUri('path/to');
		$route->compile();
		$this->assertEquals('#^path/to/?$#', $route->getPattern());

		$route->setUri('path/to/');
		$route->compile();
		$this->assertEquals('#^path/to/?$#', $route->getPattern());

		$route->setUri('/path/to');
		$route->compile();
		$this->assertEquals('#^path/to/?$#', $route->getPattern());

		$route->setUri('/path/to/');
		$route->compile();
		$this->assertEquals('#^path/to/?$#', $route->getPattern());

		$route->setUri('//path///to//');
		$route->compile();
		$this->assertEquals('#^path/to/?$#', $route->getPattern());

		/*$route->setUri('path\\to');
		$route->compile();
		$this->assertEquals('#^path/to/?$#', $route->getPattern());

		$route->setUri('//path\\to//');
		$route->compile();
		$this->assertEquals('#^path/to/?$#', $route->getPattern());*/

		$route->setUri('path/{id}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>[^/]+)/?$#', $route->getPattern());

		$route->setUri('path/to{id}');
		$route->compile();
		$this->assertEquals('#^path/to(?P<id>[^/]+)/?$#', $route->getPattern());

		$route->setUri('path/{id}/{name}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>[^/]+)/(?P<name>[^/]+)/?$#', $route->getPattern());


		$route->setUri('path/{id:\d}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>\d)/?$#', $route->getPattern());

		$route->setUri('path/{id:\d{2}}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>\d{2})/?$#', $route->getPattern());

		$route->setUri('path/{id:\d{2,}}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>\d{2,})/?$#', $route->getPattern());

		$route->setUri('path/{id:\d{2,3}}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>\d{2,3})/?$#', $route->getPattern());

		$route->setUri('path/{id:\}}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>\})/?$#', $route->getPattern());

		$route->setUri('path/{id:\}\{}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>\}\{)/?$#', $route->getPattern());

		$route->setUri('path/{id:\d}/{name}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>\d)/(?P<name>[^/]+)/?$#', $route->getPattern());

		$route->setUri('path/{id}/{name:\w+}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>[^/]+)/(?P<name>\w+)/?$#', $route->getPattern());


		$route->setUri('path/{id?}');
		$route->compile();
		$this->assertEquals('#^path(/(?P<id>[^/]+))?/?$#', $route->getPattern());

		$route->setUri('path/{id?}/{name}');
		$route->compile();
		$this->assertEquals('#^path(/(?P<id>[^/]+))?/(?P<name>[^/]+)/?$#', $route->getPattern());

		$route->setUri('path/{id}/{name?}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>[^/]+)(/(?P<name>[^/]+))?/?$#', $route->getPattern());

		$route->setUri('path/{id?}/{name?}');
		$route->compile();
		$this->assertEquals('#^path(/(?P<id>[^/]+))?(/(?P<name>[^/]+))?/?$#', $route->getPattern());

		$route->setUri('path/{id:\d{2}}/{name?:\w+}');
		$route->compile();
		$this->assertEquals('#^path/(?P<id>\d{2})(/(?P<name>\w+))?/?$#', $route->getPattern());



	}
}