<?php

namespace MistyRouting\Route;

use MistyRouting\ControllerActionParams;
use MistyRouting\Exception\InvalidParamException;
use MistyRouting\Exception\MissingParamException;

use MistyUtils\StringUtil;

/**
 */
class Route implements IRoute
{
	protected $routeName;
	protected $rule;
	protected $controller;
	protected $action;

	protected $parsedRule;

	public function __construct($routeName, $rule, $controller, $action)
	{
		$this->routeName = $routeName;
		$this->rule = $rule;
		$this->controller = $controller;
		$this->action = $action;

		$this->parsedRule = RuleParser::parseRule($rule);
	}

	/**
	 * @see IRoute
	 */
	public function encode(array $params)
	{
		$pieces = array();
		foreach ($this->parsedRule['components'] as $component) {
			$name = $component['name'];
			if ($component['isVar']) {

				if (!isset($params[$name])) {
					throw new MissingParamException(sprintf(
						"Missing parameter '%s' in route '%s'",
						$name,
						$this->routeName
					));
				}

				if ($component['choices'] && !in_array($params[$name], $component['choices']))
				{
					throw new InvalidParamException(sprintf(
						"Invalid value '%s' for parameter '%s' in route '%s'",
						$params[$name],
						$name,
						$this->routeName
					));
				}

				$pieces[] = urlencode($params[$name]);
			} else {
				$pieces[] = urlencode($name);
			}
			unset($params[$name]);
		}

		$path = "/" . implode( "/", $pieces );

		if ($this->parsedRule['varargs']) {
			$path .= VarargsHelper::serialize( $params );
		} else {
			if (!empty($params)) {
				throw new InvalidParamException(sprintf(
					'The route %s cannot accept additional parameters, but found other %s',
					$this->routeName,
					count($params)
				));
			}
		}

		return $path;
	}

	/**
	 * @see IRoute
	 */
	public function decode($path)
	{
		// Removing the first token, which is always empty
		$tokens = array_slice(explode('/', $path), 1);
		$controller = $this->controller;
		$action = $this->action;
		$params = array();

		foreach (array_values($this->parsedRule['components']) as $i => $component) {

			if ($component['isVar']) {
				if (!isset($tokens[$i])) {
					return null;
				}

				if ($component['choices'] && !in_array($tokens[$i], $component['choices'])) {
					return null;
				}

				$paramName = $component['name'];
				$paramValue = $tokens[$i];
				$params[$paramName] = urldecode($paramValue);

				$controller = str_replace( ":$paramName", ucfirst( $paramValue ), $controller );
				$action = str_replace( ":$paramName", $paramValue, $action );
			} else {
				if ($component['name'] !== $tokens[$i]) {
					return null;
				}
			}
			unset($tokens[$i]);
		}

		if (!empty($tokens)) {
			if ($this->parsedRule['varargs']) {
				$varargsStartsAt = StringUtil::findNthOccurence($path, '/', count($this->parsedRule['components']) + 1);
				$params = array_merge(
					$params,
					VarargsHelper::deserialize(substr($path, $varargsStartsAt))
				);
			} else {
				return null;
			}
		}

		return new ControllerActionParams($controller, $action, $params);
	}

	/**
	 * @see IRoute
	 */
	public function getRouteName()
	{
		return $this->routeName;
	}
}
