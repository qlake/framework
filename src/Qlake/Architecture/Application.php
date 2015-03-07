<?php

namespace Qlake\Architecture;

use Qlake\Architecture\Extensible;
use Qlake\Architecture\Container;
use Qlake\Architecture\Iwan;
use Qlake\Routing\Router;
use Qlake\Http\Request;

class Application extends Container
{


	const VERSION = '0.1-dev';



	public static $instance;



	public function __construct()
	{

		static::$instance = $this;

		$self = $this;

		$this->singleton('app', function() use ($self)
		{
			return $self;
		});
	}



	public function handle(Request $request = null)
	{

		//$this['config']->set('module::app.a', 'reza');

		//trace($this['config']->get('module::app.title'));

		//set_exception_handler(array($this, 'handleExceptions'));
		

		$this->singleton('log', function()
		{
			$whoops = new \Whoops\Run;
			

			return $whoops;
		});

		$this['log']->pushHandler(new \Whoops\Handler\PrettyPageHandler);
		$this['log']->register();

		$request = $this['request'];


		//require '../app/routes.php';
		$response = $this['router']->handel($request);

		
		//$response->send();

		//$this->terminate();

		restore_exception_handler();

		register_shutdown_function(function()
		{
			trace('===' . memory_get_usage()/1024/1024  . '===');
			//print_r(get_included_files());
		});
	}



	public function getRequest()
	{
		//return $this['request'] ?: $this['request'] = $this->createRequest();
	}



	public function createRequest()
	{
		//return Request::createFromGlobals();
	}



	public static function instance()
	{
		return static::$instance;
	}



	public function terminate()
	{
	}



	public function setPaths(array $paths)
	{
	}
}