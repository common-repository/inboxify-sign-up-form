<?php

/**
 * Inboxify PHP API (https://www.inboxify.nl/index.php)
 *
 * @author    Inboxify <info@inboxify.nl>
 * @copyright (c) 2016 - 2018 - Inboxify
 * @license   https://gitlab.com/inboxify/inboxify-php-api/blob/master/LICENSE.txt
 * @package   Inboxify\API\Client
 * @version   1.0.3
 */

/**
 * Namespace
 *
 * @package Inboxify\Api
 */
namespace Inboxify\Api;

/**
 * Inboxify API Client Config Wrapper
 *
 * This Class provides simple Interface for Inboxify API Client Configuration.
 * You can use either access Configuration Values as Object Properties ($o->cache)
 * thanks to Magic Functions __get() and __set(). Or with getters, listed below.
 *
 * @method  boolean getCache()
 * @method  string getCacheDir()
 * @method  string getEndPoint()
 * @method  string getKey()
 * @method  string getList()
 * @method  string getSecret()
 * @method  integer getTtl()
 * @method  integer getTimeOutSocket()
 * @method  integer getTimeOutStream()
 * @package Inboxify\Api
 */
class Config
{
    const ENDPOINT_OLD = 'api.inboxify.nl';
    const ENDPOINT_NEW = 'api.inboxify.net';
    
    /**
     * @var array Associative Array of the Configuration Values
     */
    protected $config;

    /**
     * Create new instance of Config
     *
     * @param array $config Configuration as an Associative Array
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    public static function configFactory($filename)
    {
        if (!is_readable($filename)) {
            throw new \UnexpectedValueException(
                sprintf('Configuration file "%s" is not readable.', $filename)
            );
        }

        $config = include($filename);

        if (!is_array($config)) {
            throw new \UnexpectedValueException(
                sprintf('Configuration file "%s" did not returned an array.', $filename)
            );
        }

        return new self($config);
    }

    protected function checkConfigValueCache($cache)
    {
        if (!is_bool($cache)) {
            throw new \UnexpectedValueException('Configuration "cache" must be boolean.');
        }

        return $cache;
    }

    protected function checkConfigValueCacheDir($cacheDir)
    {
        if (empty($cacheDir)) {
            throw new \UnexpectedValueException('Configuration "cacheDir" must be set.');
        }

        if (!preg_match("/\/$/", $cacheDir)) {
            $cacheDir .= '/';
        }
        
        return $cacheDir;
    }

    protected function checkConfigValueEndPoint($endPoint)
    {
        $urlParsed = parse_url($endPoint);

        $required = ['scheme', 'host']; // , 'path'

        foreach ($required as $key) {
            if (!isset($urlParsed[$key]) || empty($urlParsed[$key])) {
                throw new \UnexpectedValueException('Configuration "endPoint" is an invalid URL.');
            }
        }

        if (!preg_match("/\/$/", $endPoint)) {
            $endPoint .= '/';
        }
        
        // @since 1.0.3 force api endpoint change from api.inboxify.nl to api.inboxify.net
        // $endPoint = str_replace(self::ENDPOINT_OLD, self::ENDPOINT_NEW, $endPoint);

        return $endPoint;
    }

    protected function checkConfigValueKey($key)
    {
        if (empty($key)) {
            throw new \UnexpectedValueException('Configuration "key" must be set.');
        }

        return $key;
    }

    protected function checkConfigValueList($list)
    {
        if (empty($list)) {
//            throw new \UnexpectedValueException('Configuration "list" must be set.');
        }

        return $list;
    }

    protected function checkConfigValueSecret($secret)
    {
        if (empty($secret)) {
            throw new \UnexpectedValueException('Configuration "secret" must be set.');
        }

        return $secret;
    }

    protected function checkConfigValueTimeOutSocket($timeOutSocket)
    {
        if (!is_int($timeOutSocket) || 0 >= $timeOutSocket) {
            throw new \UnexpectedValueException('Configuration "timeOutSocket" must be integer bigger than zero.');
        }

        return $timeOutSocket;
    }

    protected function checkConfigValueTimeOutStream($timeOutStream)
    {
        if (!is_int($timeOutStream) || 0 >= $timeOutStream) {
            throw new \UnexpectedValueException('Configuration "timeOutStream" must be integer bigger than zero.');
        }

        return $timeOutStream;
    }

    protected function checkConfigValueTtl($ttl)
    {
        if (!is_int($ttl) || 0 >= $ttl) {
            throw new \UnexpectedValueException('Configuration "ttl" must be integer bigger than zero.');
        }

        return $ttl;
    }

    /**
     * Get Configuration
     *
     * @return array|null return config as associative array
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Set Configuration
     *
     * @param array $config Configuration as an Associative Array
     */
    public function setConfig(array $config)
    {
        foreach ($config as $key => $value) {
            $method = 'checkConfigValue' . ucfirst($key);

            if (!is_callable([$this, $method])) {
                continue;
            }

            $this->config[$key] = $this->{$method}($value);
        }
    }
    
    /**
     * Magic Method Implementation
     *
     * Converts Part of called Method after "get" to Configuration Key,
     * e.g. "getCache" to "cache", and returns the Value or null.
     *
     * @param  string $method Method Name
     * @param  array  $args   Method Arguments
     * @return mixed Configuration Value or null
     * @see    \Inboxify\Api\Config::__get()
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        $get = 'get';
        
        if (preg_match('/^' . $get . '/i', $method)) {
            $property = lcfirst(str_replace($get, '', $method));
            
            return $this->$property;
        }
        
        throw new \BadMethodCallException('Call to undefined method ' . __CLASS__ . '::' . $method . '().');
    }
    
    /**
     * Magic Method Implementation - get Object Property.
     *
     * @param  string $key requested Property Name
     * @return mixed Property Value or null
     */
    public function __get($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }
    
    /**
     * Magic Method Implementation - set Object Property.
     *
     * @param string $key Property Name
     * @param mixed  $val Property Value
     */
    public function __set($key, $val)
    {
        $this->config[$key] = $val;
    }
}
