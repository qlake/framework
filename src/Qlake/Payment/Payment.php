<?php

namespace Qlake\Payment;

use Qlake\Payment\Gateway\GatewayInterface;

class Payment
{
	protected $gateway;


	protected $requestData = [];


	public function __construct(GatewayInterface $gateway)
	{
		$this->gateway = $gateway;
	}



	public function sendRequest($amount, $receiptId)
	{
		return $this->gateway->sendRequest($amount, $receiptId);
	}



	public function getRequestData()
	{
		return $this->gateway->getRequestData();
	}




	public function getRequestError()
	{
		return $this->gateway->getRequestError();
	}



	public function isReady()
	{
		return $this->gateway->isReady();
	}



	public function redirect()
	{
		return $this->gateway->redirect();
	}
}
