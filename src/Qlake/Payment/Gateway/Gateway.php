<?php

namespace Qlake\Payment\Gateway;

class Gateway
{
	protected function redirectByForm($url, array $params)
	{
		$elements = '';

		foreach ($params as $name => $value)
		{
			$elements .= "<input type=\"hidden\" value=\"{$value}\" name=\"{$name}\" />";
		}

		include __DIR__ . '/form.html';
	}
}
