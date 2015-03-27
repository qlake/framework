<?php

namespace Qlake\Payment;

interface GatewayInterface
{
	public function __construct(array $config);



	public function purchase($amount, $receiptId);



	public function send();



	//public function redirect();



	//public function handle();



	//public function verify();
}
