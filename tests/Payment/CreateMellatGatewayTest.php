<?php

use Qlake\Payment\Mellat\Mellat;

class CreateMellatGatewayTest extends PHPUnit_Framework_TestCase
{
	public function testCreate()
	{
		$gateway = new Mellat([]);

		$this->assertTrue($gateway instanceof Mellat);
	}



	public function testCreate3()
	{
		//$this->setExpectedException('InvalidArgumentException');

		$gateway = new Mellat([]);
	}




}
