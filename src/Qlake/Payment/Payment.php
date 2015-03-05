<?php

namespace Qlake\Payment;

use Qlake\Payment\Gateway\GatewayInterface;

class Payment
{
	protected $gateway;


	public function __construct(GatewayInterface $gateway)
	{
		$this->gateway = $gateway;
	}



	public function request($amount, $receiptId)
	{
		return $this->gateway->request($amount, $receiptId);
	}
}