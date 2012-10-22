<?php

namespace MistyRouting\Route;

use MistyRouting\Exception\MalformedRuleException;
use MistyUtils\ArrayUtil;

class RuleParser
{
    /**
     * Transform a rule into an array of components
     *
     * @param string $rule The rule to be parsed
     * @return array Associative array, with components and varargs settings
     * @throws \MistyRouting\Exception\MalformedRuleException If the rule is not valid
     */
    public static function parseRule($rule)
    {
        $components = array();
        $tokens = array_filter(explode('/', $rule));
        $varargs = false;

        foreach ($tokens as $key => $token) {
            if ($token === ':*') {
                $varargs = true;
                if ($key < count($tokens)) {
                    throw new MalformedRuleException(
                        'The magic token :* must be at the end of the rule, instead it was: ' . $rule
                    );
                }
            } else {
                $components[] = self::parseToken($token);
            }
        }

        return array(
            'components' => $components,
            'varargs' => $varargs,
        );
    }

    /**
     * Take a rule roke (e.g. news from /news/) and parse it
     *
     * @param string $token A token from the rule
     * @return array An array describing the token (name, isVar, choices, default)
     * @throws \MistyRouting\Exception\MalformedRuleException If the token is not valid
     */
    public static function parseToken($token)
    {
        $matches = array();
        $variableTokenPattern = "/^:([a-zA-Z0-9-_]+)(?:\\[([a-zA-Z0-9-_|]+)\\])?$/";
        preg_match($variableTokenPattern, $token, $matches);

        $isVar = $choices = null;

        if (!empty($matches)) {
            // It matched the pattern, this means it's a variable token
            $name = $matches[1];
            $isVar = true;
            $choices = ArrayUtil::issetAndNotBlank($matches, 2) ? explode('|', $matches[2]) : null;
        } else {
            // It didn't matched the pattern, this means it's a fixed token
            if (strpos($token, ':') !== false) {
                throw new MalformedRuleException(sprintf(
                    'Invalid url token: %s',
                    $token
                ));
            }
            $name = $token;
        }

        return array(
            'name' => $name,
            'isVar' => (bool)$isVar,
            'choices' => $choices,
        );
    }
}
