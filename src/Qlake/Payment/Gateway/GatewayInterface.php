<?php

namespace Qlake\Payment\Gateway;

interface GatewayInterface
{
	public function __construct(array $config);



	public function request($amount, $receipt);



	public function redirect();



	public function handle();



	public function verify();
}