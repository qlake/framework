<?php

namespace Qlake\Architecture;

use Qlake\Architecture\Application;

class Iwan
{
	/**
	 * Application instans that is running.
	 *
	 * @var Qlake\Application
	 */
	protected static $app;



	public static $provider;

	/**
	 * Get instans of application service by name. The name specified in
	 * each Iwan class by static property named $provider.
	 *
	 * @return mixed Application service that is Object
	 */
	public final static function getInstans()
	{
		$app = static::$app;

		return $app[static::$provider];
	}

	/**
	 * Get application instans.
	 *
	 * @return Qlake\Application
	 */
	public static function getApplication()
	{
		return static::$app;
	}

	/**
	 * Set application instans.
	 * 
	 * @param Qlake\Application $app
	 * @return void
	 */
	public static function setApplication(Application $app)
	{
		static::$app = $app;
	}

	/**
	 * Magic method!
	 *
	 * This method provides static method interface for objective method of
	 * applicatin services. This is heart of Iwans;
	 */
	public final static function __callStatic($name, $arguments)
	{
		$instans = static::getInstans();

		return call_user_func_array(array($instans, $name), $arguments);
	}
}