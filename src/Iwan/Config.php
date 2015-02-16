<?php

namespace Qlake\Iwan;

use Qlake\Architecture\Iwan;

abstract class Config
{
	/**
	 * Use Iwan trait for this class
	 */
	use Iwan;

	/**
	 * Determine the application service name
	 * 
	 * @var string
	 */
	private static $provider = 'config';
}