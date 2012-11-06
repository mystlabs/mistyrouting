<?php

namespace MistyRouting;

/**
 * Transform a path into a relative or absolute url
 */
class PathDecorator
{
    private $defaultOptions;

    /**
     * Create a new PathDecotorator
     *
     * @param array $defaultOptions The default options for this decorator
     * @throws \InvalidArgumentException
     */
    public function __construct(array $defaultOptions = array())
    {
        if (!isset($defaultOptions['hostname'])) {
            throw new \InvalidArgumentException('The PathDecorator requires a hostname');
        }

        $this->defaultOptions = array_merge(
            array(
                'absolute' => false,
                'protocol' => 'http',
                'secure' => false,
                'port' => 80,
            ),
            $defaultOptions
        );
    }

    /**
     * Transform a path into a url
     *
     * @param string $path The path to decorate
     * @param array $options The options for this
     * @return string
     */
    public function decorate($path, array $options = array())
    {
        $url = '';

        // Create an absolute url if requested or
        if ($this->useAbsolutePath($options)) {
            $url .= $this->generateBaseUrl($options);
        }

        if ($this->option('entryPoint', $options)) {
            $url .= '/';
            $url .= $this->option('entryPoint', $options);
        }

        $url .= $path;

        if (isset($options['anchor'])) {
            $url .= '#'.$options['anchor'];
        }

        return $url;
    }

    /**
     * Read the given property, giving priority to the options passed to the method call,
     * and falling back to the default options of the key is not present
     *
     * @param string $key The property name
     * @param array $options The options for this url
     * @return string The value for this option
     */
    private function option($key, $options)
    {
        if (isset($options[$key])) {
            return $options[$key];
        }

        if (isset($this->defaultOptions[$key])) {
            return $this->defaultOptions[$key];
        }

        return null;
    }

    /**
     * Generate the base url. If protocol and secure are not provided it uses '//',
     * which uses the protocol of the page
     *
     * @param array $options The options for this url
     * @return string The base url
     */
    private function generateBaseUrl(array $options)
    {
        $baseUrl = '';
        if ($this->option('protocol', $options) !== 'http' || $this->option('secure', $options)) {
            $baseUrl .= $this->option('protocol', $options);
            if ($this->option('secure', $options)) {
                $baseUrl .= 's';
            }
            $baseUrl .= ':';
        }
        $baseUrl .= '//';

        $baseUrl .= $this->option('hostname', $options);

        // Only adding the port if it's different from 80
        if ($this->usePort($options)) {
            $baseUrl .= ':' . $this->option('port', $options);
        }

        return $baseUrl;
    }

    private function usePort($options)
    {
        return $this->option('port', $options) && $this->option('port', $options) != 80;
    }


    private function useAbsolutePath($options)
    {
        if ($this->option('absolute', $options) ||
            $this->usePort($options) ||
            (isset($options['hostname']) && $options['hostname'] !== $this->defaultOptions['hostname']) ||
            $this->option('secure', $options)
        ) {
            return true;
        }

        return false;
    }
}
