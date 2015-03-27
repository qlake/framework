<?php

namespace Qlake\Payment;

use Qlake\Payment\GatewayInterface;
use Qlake\Payment\Saman\Saman;
use Qlake\Payment\Mellat\Mellat;

class Payment
{
	public static function create($name, array $params = [])
	{
		switch ($name)
		{
			case 'Saman':
				$gateway = new Saman($params);
				break;

			case 'Mellat':
				$gateway = new Mellat($params);
				break;
			
			default:
				throw new \InvalidArgumentException();
				break;
		}

		return $gateway;
	}
}
