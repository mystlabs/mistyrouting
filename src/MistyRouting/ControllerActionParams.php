<?php

namespace MistyRouting;

class ControllerActionParams
{
	public $controller;
	public $action;
	public $params;

	public function __construct($controller, $action, array $params=array())
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->params = $params;
	}
}
