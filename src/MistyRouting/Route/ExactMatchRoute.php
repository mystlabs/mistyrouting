<?php

namespace MistyRouting\Route;

use MistyRouting\ControllerActionParams;
use MistyRouting\Exception\InvalidParamException;
use MistyRouting\Route\IRoute;

/**
 * Match a controller to an exact path
 */
class ExactMatchRoute implements IRoute
{
	private $routeName;
	private $path;
	private $controller;
	private $action;

	public function __construct( $routeName, $path, $controller, $action )
	{
		$this->routeName = $routeName;
		$this->path = $path;
		$this->controller = $controller;
		$this->action = $action;
	}

	/**
	 * @see IRoute
	 */
	public function encode( array $params )
	{
		if (!empty($params)) {
			throw new InvalidParamException(sprintf(
				'%s cannot accept additional parameters, but got %s',
				__CLASS__,
				count($params)
			));
		}

		return $this->path;
	}

	/**
	 * @see IRoute
	 */
	public function decode($path)
	{
		if ($path === $this->path) {
			return new ControllerActionParams($this->controller, $this->action);
		} else {
			return null;
		}
	}

	/**
	 * @see IRoute
	 */
	public function getRouteName()
	{
		return $this->routeName;
	}
}
