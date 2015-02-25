<?php

namespace Qlake\Database;

class Operator
{
	public function __construct($operator)
	{
		$this->operator = $operator;
	}

	public function getType()
	{
		return $this->operator;
	}
}