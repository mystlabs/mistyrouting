<?php

namespace MistyRouting\Route;

interface IRoute
{
    /**
     * Encode the params into the path described by this rule
     *
     * @param array $params The params to be used to create the path
     * @throws MistyRouting\Exception\MissingParamException If a required param is missing
     * @throws MistyRouting\Exception\InvalidParamException If there is something wrong with the params
     */
	function encode(array $params);

    /**
     * Decode a path and return the ControllerActionParams for this path
     *
     * @param string $path The path to turn into a ControllerActionParams
     * @return ControllerActionParams|null The controller, action and params for this route
     *                                     or null if it can't decode it
     */
	function decode($path);

    /**
     * Return the name of this route
     *
     * @return string The route name
     */
    function getRouteName();
}
