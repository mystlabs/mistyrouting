<?php

namespace MistyRouting;

/**
 * Transform a path into a relative or absolute url
 */
class Urlifier
{
    private $defaultOptions;

    /**
     * @param string $hostname The target hostname, required
     * @param array $defaultOptions The common options for this decorator
     */
    public function __construct($hostname, array $defaultOptions = array())
    {
        $defaultOptions['hostname'] = $hostname;
        $this->defaultOptions = array_merge(
            array(
                'absolute' => false,
                'protocol' => 'http',
                'secure' => null,
                'port' => null,
            ),
            $defaultOptions
        );
    }

    /**
     * Transform a path into a url
     *
     * @param string $path The path to decorate
     * @param array $options The options for this
     */
    public function urlify($path, array $options = array())
    {
        $url = '';

        // Create an absolute url if requested or
        if ($this->option('absolute', $options)) {
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
        $baseUrl .= $this->option('port', $options) ? ':' . $this->option('port', $options) : '';

        return $baseUrl;
    }
}
