<?php

namespace MistyRouting;

use MistyRouting\Exception\DuplicateRouteException;
use MistyRouting\Exception\InvalidParamException;
use MistyRouting\Exception\InvalidPathException;
use MistyRouting\Exception\MalformedPathException;
use MistyRouting\Exception\MissingParamException;
use MistyRouting\Exception\UnknownRouteException;
use MistyRouting\Route\IRoute;

use MistyUtils\StringUtil;

class Router
{
	private $routes;

	/**
	 * @param array $routes Array of IRoute objects
	 */
	public function __construct(array $routes)
	{
		$this->routes = array();

		foreach ($routes as $route) {
			$this->addRoute($route);
		}
	}

	/**
	 * Decode the path and return a ControllerActionParams representing it
	 *
	 * @param string $path The path to decode
	 * @return ControllerActionParams The Controller::action to execute
	 * @throws InvalidPathException If there is no route for this path
	 * @throws MalformedPathException If the path is malformed
	 */
	public function decode($path)
	{
		// Making sure that the path starts with /
		if (!StringUtil::startsWith($path, '/')) {
			throw new MalformedPathException(sprintf(
				'The path must begin with /, instead it was: %s',
				$path
			));
		}

		$controllerActionPairParams = null;
		foreach ($this->routes as $route) {

            /** @var $route IRoute  */
			$controllerActionParams = $route->decode($path);
			if( $controllerActionParams !== null )
			{
				return $controllerActionParams;
			}
		}

		throw new InvalidPathException(sprintf(
			'Count not find a valid route for %s',
			$path
		));
	}

	/**
	 * Use the route defined by $routeName to encode $params and return a valid path
	 *
	 * @param string $routeName The name of the route to use
	 * @param array $params The params to use in the path
	 * @return string The path
	 * @throws InvalidParamException If there is something wrong with the params
	 * @throws MalformedPathException If the generated path is malformed
	 * @throws MissingParamException If one or more required params are missing
	 * @throws UnknownRouteException If there's no route for $routeName
	 */
	public function encode($routeName, array $params = array())
	{
		if (!isset($this->routes[$routeName])) {
			throw new UnknownRouteException(sprintf(
				'Unknown route %s',
				$routeName
			));
		}

        /** @var $route IRoute */
		$route = $this->routes[$routeName];
		$path = $route->encode($params);

		// Making sure that the path starts with /
		if (!StringUtil::startsWith($path, '/')) {
			throw new MalformedPathException(sprintf(
				'The generated path must start with /, instead it was: %s',
				$path
			));
		}

		return $path;
	}

	/**
	 * Add a new Route
	 *
	 * @param IRoute $route The route to add to this router
	 * @throws DuplicateRouteException If the route name is already in use
	 */
	private function addRoute(IRoute $route)
	{
		$routeName = $route->getRouteName();
		if (isset($this->routes[$routeName])) {
			throw new DuplicateRouteException(sprintf(
				"Duplicate route name '%s'. Please make sure every route name is unique.",
				$routeName
			));
		}

		$this->routes[$routeName] = $route;
	}
}
