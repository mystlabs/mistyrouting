<?php

namespace MistyRouting\Route;

use MistyRouting\Exception\InvalidParamException;
use MistyRouting\Exception\MalformedPathException;

class VarargsHelper
{
	/**
	 * Transform a name => value array into /name/value varargs
	 *
	 * @param array $params The params to be serialized
	 * @return string The serialized varargs
	 * @throws \MistyRouting\Exception\InvalidParamException If there's something wrong with a param
	 */
	public static function serialize(array $params)
	{
		$path = '';
		foreach ($params as $name => $value) {
			if (strlen($name) == 0) {
				throw new InvalidParamException(
					'Received a param without a name'
				);
			}

			if (!is_string($name) && !is_numeric($name)) {
				throw new InvalidParamException(
					'The name must be a string or a number, was a: ' . gettype($value)
				);
			}

			if (!is_string($value) && !is_numeric($value)) {
				throw new InvalidParamException(
					'The value must be a string or a number, was a: ' . gettype($value)
				);
			}

			$path .= '/' . urlencode($name) . '/' . urlencode($value);
		}

		return $path;
	}

	/**
	 * Transform /name/value path into a name => value array
	 *
	 * @param string $path The path to deserialize
	 * @return array Associative array of arams
	 * @throws MistyRouting\Exception\MalformedPathException If the path cannot be deserialized
	 */
	public static function deserialize($path)
	{
		$varargs = array();
		$params = array_slice(
			explode('/', $path),
			1
		);

		if (count($params) % 2 !== 0) {
			throw new MalformedPathException(sprintf(
				'Varargs must contain an even number of params, found %s instead.',
				count($params)
			));
		}

		for ($i=0; $i<count($params); $i=$i+2) {
			$name = $params[$i];
			$value = $params[$i+1];

			$varargs[$name] = $value;
		}

		return $varargs;
	}
}
