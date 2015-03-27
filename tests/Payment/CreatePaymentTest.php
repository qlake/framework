<?php

use Qlake\Payment\Payment;
use Qlake\Payment\Saman\Saman;
use Qlake\Payment\Mellat\Mellat;

class CreatePaymentTest extends PHPUnit_Framework_TestCase
{
	public function testCreateWithoutGatewayName()
	{
		$this->setExpectedException('InvalidArgumentException');

		$gateway = Payment::create('some-name');
	}



	public function testCreateSamanGateway()
	{
		$gateway = Payment::create('Saman');

		$this->assertTrue($gateway instanceof Saman);
	}



	public function testCreateMellatGateway()
	{
		$gateway = Payment::create('Mellat');

		$this->assertTrue($gateway instanceof Mellat);
	}



	public function testCreateGatewayByEmptyParams()
	{
		$gateway = Payment::create('Saman', []);

		$this->assertTrue($gateway instanceof Saman);
	}



}
