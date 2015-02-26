<?php

namespace Qlake;

use Qlake\Architecture\Extensible;
use Qlake\Architecture\Container;
use Qlake\Architecture\Iwan;
use Qlake\Routing\Router;
use Qlake\Http\Request;

class Application extends Container
{


	const VERSION = '0.0.1';


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

		$request = $this['request'];


		//require '../app/routes.php';
		$response = $this['router']->handel($request);

		
		//$response->send();

		//$this->terminate();

		restore_exception_handler();
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