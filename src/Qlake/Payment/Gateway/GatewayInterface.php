<?php

namespace Qlake\Payment\Gateway;

interface GatewayInterface
{
	public function __construct(array $config);



	public function purchase($amount, $receipt);



	public function send();



	public function redirect();



	public function handle();



	public function verify();
}
