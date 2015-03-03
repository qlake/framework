<?php

namespace Qlake\Http;

use Qlake\Http\Request;
use Qlake\Http\Response;

class Dispatcher
{
	public function dispatch(Request $request, Response $response)
	{
		
	}


	public function dispatchController($callable)
	{
		$callable = explode('::', $callable);

		$callable[1] = $callable[1] ?: 'index';

		list($class, $method) = $callable;

		$controller = new $class();

		if (method_exists($controller, $method))
		{
			$this->actionCall([$controller, $method], $this->params);
		}
		elseif (method_exists($controller, '__missing'))
		{
			call_user_func([$controller, '__missing'], [$method, $this->params]);
		}
		else
		{
			throw new ClearException("Action [{$method}] From Controller [{$class}] Not Found", 4);
		}
	}


	public function isController($callable)
	{
		if (is_string($callable))
		{
			// if $callable was like App\Controllers\ControllerClass or App\Controllers\ControllerClass::actionMethod
			if (preg_match("/^\\w+(\\\\\\w*)*(::\\w+)?$/", $callable))
			{
				$callable = explode('::', $callable);

				$callable[1] = $callable[1] ?: 'index';

				return class_exists($callable[0]) ? true : false;
			}
			else
			{
				return false;
			}
		}

		return false;
	}


	protected function actionCall($callable, array $args = [])
	{
		if (!is_callable($callable))
		{
			throw new ClearException("A Real Action Not Found", 1);	
		}

		if (is_string($callable))
		{
			if (function_exists($callable))
			{
				$function = new \ReflectionFunction($callable);
			}
			else
			{
				$function = new \ReflectionMethod($callable);
			}
		}
		elseif (is_object($callable) && ($callable instanceof \Closure))
		{
			$function = new \ReflectionFunction($callable);
		}
		elseif (is_array($callable))
		{
			$function = (new \ReflectionClass($callable[0]))->getMethod($callable[1]);
		}

		$functionParams = $function->getParameters();

		$params = [];

		foreach ($functionParams as $param)
		{
			if ($args[$param->name])
			{
				$params[$param->name] = $args[$param->name];
			}
			elseif($param->isDefaultValueAvailable()){
				$params[$param->name] = $param->getDefaultValue();
			}
		}

		return call_user_func_array($callable, $params);
	}
}