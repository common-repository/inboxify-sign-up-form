<?php

/**
 * @package Inboxify\Wordpress
 */
namespace Inboxify\Wordpress;

use Inboxify\Api\Cache;
use Inboxify\Api\Client as InboxifyApiClient;
use Inboxify\Api\Config;

/**
 * Inboxify PHP API Client Wrapper
 * @package Inboxify\Wordpress
 */
class Client
{
    /**
     * Singleton instance
     * @var Client self
     */
    protected static $instance;
    
    protected $cache;
    protected $client;
    protected $config;
    
    /**
     * Create singleton instance
     * @param array $configData client config data
     * @return Client self 
     * @throws \RuntimeException In case the instance is not initialized with config data
     */
    public static function getInstance(array $configData = array())
    {
        if (!self::$instance) {
            if (!count($configData)) {
                throw new \RuntimeException('Config data missing.');
            }
            
            self::$instance = new self($configData);
        }
        
        return self::$instance;
    }
    
    /**
     * Get temporary client instance.
     * @param array $configData associative array with configuration
     * @return Inboxify\Api\Client
     */
    public static function getTemporaryClientInstance(array $configData)
    {
        $instance = new self($configData);
        
        return $instance->getClient();
    }
    
    /**
     * Singleton constructor
     * @param array $configData assoc. array of config data for client
     */
    protected function __construct(array $configData)
    {
        $this->getConfigInstance($configData);
        $this->getCacheInstance();
        $this->getClientInstance();
    }
    
    /**
     * Create Inboxify API PHP Client Cache Instance
     */
    protected function getCacheInstance()
    {
        $this->cache = new Cache($this->config);
    }
    
    /**
     * Create Inboxify API PHP Client Config Instance
     */
    protected function getConfigInstance($configData)
    {
        $this->config = new Config($configData);
    }
    
    /**
     * Create Inboxify API PHP Client Instance
     */
    protected function getClientInstance()
    {
        $this->client = new InboxifyApiClient($this->cache, $this->config);
    }
    
    /**
     * Return current api client instance
     * @return Inboxify\Api\Client
     */
    public function getClient()
    {
        return $this->client;
    }
    
    /**
     * Act as a method call wrapper for client... 
     * @param string $name method name
     * @param array $args method args
     * @return mixed call result
     */
    public function __call($name, $args)
    {
        $c = array($this->client, $name);
        
        if (is_callable($c)) {
            return call_user_func_array($c, $args);
        }
        
        return false;
    }
}
