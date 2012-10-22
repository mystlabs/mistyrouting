<?php

namespace MistyRouting;

class Urlifier
{
    /** @var Router */
    private $router;

    /** @var PathDecorator */
    private $decorator;

    public function __construct(Router $router, PathDecorator $decorator)
    {
        $this->router = $router;
        $this->decorator = $decorator;
    }

    /**
     * Create a path and decorate it with the given options
     *
     * @param string $routeName The name of the route
     * @param array $params
     * @param array $options
     * @return string The decorated url/path
     */
    public function url($routeName, array $params = array(), array $options = array())
    {
        return $this->decorator->decorate(
            $this->router->encode($routeName, $params),
            $options
        );
    }
}
