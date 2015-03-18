<?php

use Qlake\Http\Cookie;

class CookieTest extends PHPUnit_Framework_TestCase
{
	public function testCreateWithInvalidName()
	{
		$this->setExpectedException('InvalidArgumentException');

		$cookie = new Cookie(false);
	}



	public function testCreateWithEmptyName()
	{
		$this->setExpectedException('InvalidArgumentException');

		$cookie = new Cookie('');
	}



	public function testCreateWithName()
	{
		$cookie = new Cookie('name');

		$this->assertEquals('name', $cookie->getName());
	}



	public function testCreateWithNameAndDefaultValue()
	{
		$cookie = new Cookie('name');

		$this->assertEquals(null, $cookie->getValue());
	}



	public function testCreateWithNameAndValue()
	{
		$cookie = new Cookie('name', 'value');

		$this->assertEquals('value', $cookie->getValue());
	}



	public function testCreateWithNameAndValueAndDefaultExpire()
	{
		$cookie = new Cookie('name', 'value');

		$this->assertEquals(0, $cookie->getLifeTime());
	}



	public function testCreateWithNameAndValueAndExpire()
	{
		$cookie = new Cookie('name', 'value', 10);

		$this->assertEquals(10, $cookie->getLifeTime());
	}



	public function testCreateWithNameAndValueAndExpireByUTCTime()
	{
		$time =  time() + 10;

		$cookie = new Cookie('name', 'value', $time);

		$this->assertEquals($time, $cookie->getLifeTime());
	}



	public function testCreateWithDefaultPath()
	{
		$cookie = new Cookie('name', 'value', 0);

		$this->assertEquals('/', $cookie->getPath());
	}



	public function testCreateWithNullPath()
	{
		$cookie = new Cookie('name', 'value', 0, null);

		$this->assertEquals('/', $cookie->getPath());
	}



	public function testCreateWithGivenPath()
	{
		$cookie = new Cookie('name', 'value', 0, '/path');

		$this->assertEquals('/path', $cookie->getPath());
	}



	public function testCreateWithDefaultDomain()
	{
		$cookie = new Cookie('name', 'value', 0, null);

		$this->assertEquals(null, $cookie->getDomain());
	}



	public function testCreateWithGivenDomain()
	{
		$cookie = new Cookie('name', 'value', 0, null, 'domain.com');

		$this->assertEquals('domain.com', $cookie->getDomain());
	}



	public function testCreateWithDefaultSecureType()
	{
		$cookie = new Cookie('name', 'value', 0, null, null);

		$this->assertEquals(false, $cookie->isSecure());
	}



	public function testCreateWithGivenSecureType()
	{
		$cookie = new Cookie('name', 'value', 0, null, null, true);

		$this->assertEquals(true, $cookie->isSecure());
	}



	public function testCreateWithDefaultHttpProtocolType()
	{
		$cookie = new Cookie('name', 'value', 0, null, null, false);

		$this->assertEquals(false, $cookie->isHttpOnly());
	}



	public function testCreateWithGivenHttpProtocolType()
	{
		$cookie = new Cookie('name', 'value', 0, null, null, false, true);

		$this->assertEquals(true, $cookie->isHttpOnly());
	}


}
