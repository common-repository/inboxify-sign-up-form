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
 * Cache for Inboxify API Client
 *
 * @package Inboxify\Api
 */
class Cache
{
    const HTACCESS = '.htacess';
    const HTACCESS_CONTENT = 'Deny from all';
    const SUFFIX = '.cache';
    
    /**
     * @var string Cache Directory (Must be writeable)
     */
    protected $dir;
    /**
     * @var boolean Enabled Flag
     */
    protected $enabled;
    /**
     * @var integer Time to live for cached Data
     */
    protected $ttl;
    
    /**
     * Create new Instance of Cache
     *
     * @param Config $config Configuration
     */
    public function __construct(Config $config)
    {
        $this->setEnabled($config->getCache());
        $this->setDir($config->getCacheDir());
        $this->setTtl($config->getTtl());
    }
    
    /**
     * Get Enabled Flag
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    /**
     * Set Cache Directory
     *
     * @param  boolean $enabled Cache Directory (Must be writeable)
     * @return \Inboxify\Api\Cache self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }
    
    /**
     * Get Cache Directory (Absolute Path)
     *
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }
    
    /**
     * Set Cache Directory
     *
     * @param  string $dir Cache Directory (Must be writeable)
     * @return \Inboxify\Api\Cache self
     * @throws \InvalidArgumentException In Case the Directory is not writeable, or .htaccess File can't be created
     */
    public function setDir($dir)
    {
        if (( !is_dir($dir) && !mkdir($dir) ) || !is_writable($dir)) {
            throw new \InvalidArgumentException('Cache directory is not writeable.');
        }
        
        $this->checkHtaccess($dir);
        $this->dir = $dir;
        
        return $this;
    }
    
    /**
     * Set Cache Time to live
     *
     * @param  integer $ttl Time to live
     * @return \Inboxify\Api\Cache self
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
        return $this;
    }
    
    /**
     * Get Cache Time to live
     *
     * @return integer
     */
    public function getTtl()
    {
        return $this->ttl;
    }
    
    /**
     * Check and if not exist create .htaccess File protecting Cache Files
     *
     * @param  string $dir Cache Directory (Must be writeable)
     * @return boolean
     * @throws \RuntimeException In case the .htaccess File cannot be written
     */
    protected function checkHtaccess($dir)
    {
        $htaccess = $dir . '.htaccess';
        
        if (!is_file($htaccess)) {
            if (!file_put_contents($htaccess, self::HTACCESS_CONTENT)) {
                throw new \RuntimeException('Couldn\'t create .htaccess file in cache directory.');
            }
        }
        
        return true;
    }
    
    /**
     * Sanitize File Name
     *
     * @param  string $filename Un-sanitized File Name
     * @return string Sanitized File Name
     */
    protected function sanitizeFileName($filename)
    {
        $specialChars = array(
            "?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"",
            "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0)
        );
        
        $filename = preg_replace("#\x{00a0}#siu", ' ', $filename);
        $filename = str_replace($specialChars, '', $filename);
        $filename = str_replace(array( '%20', '+' ), '-', $filename);
        $filename = preg_replace('/[\r\n\t -]+/', '-', $filename);
        $filename = trim($filename, '.-_');
        
        return $this->getDir() . $filename;
    }
    
    /**
     * Get all Cache Files and delete them.
     *
     * @return boolean
     */
    public function purge()
    {
        $files = scandir($this->getDir());
        
        if (is_array($files) && count($files)) {
            foreach ($files as $file) {
                if (!in_array($file, array('.', '..', '.htaccess'))) {
                    unlink($this->getDir() . $file);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Remove Cached data identified by Key $key from Cache
     *
     * @param  string $key Cache ID
     * @return boolean
     * @throws \RuntimeException In case the Cache File cannot be deleted.
     */
    public function remove($key)
    {
        $filename = $this->sanitizeFileName($key);
        
        if (is_file($filename)) {
            if (!unlink($filename)) {
                throw new \RuntimeException('Couldn\'t remove the cache key.');
            }
        }
        
        return true;
    }
    
    /**
     * Set Cache Key
     *
     * @param  string $key   Cache ID
     * @param  mixed  $value Any value except false obviously
     * @return boolean
     * @throws \RuntimeException In case the Key cannot be stored
     */
    public function set($key, $value)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        
        $filename = $this->sanitizeFileName($key);
        
        if (!file_put_contents($filename, serialize($value))) {
            throw new \RuntimeException('Couldn\t write key to cache.');
        }
        
        return true;
    }
    
    /**
     * Get Cache Key
     *
     * @param  string $key Cache ID
     * @return mixed Cached Data or false
     * @throws \RuntimeException In case the file cannot be open, or Content unserialized
     */
    public function get($key)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        
        $filename = $this->sanitizeFileName($key);
        $value = false;
        
        if ($this->has($key)) {
            $content = file_get_contents($filename);

            if (!$content) {
                throw new \RuntimeException('Couldn\'t read cache key.');
            }
            
            $value = unserialize($content);
            
            if (!$value) {
                throw new \RuntimeException('Couldn\'t unserialize cache key.');
            }
        }
        
        return $value;
    }
    
    /**
     * Check if Key is cached and not too old
     *
     * @param  string $key Cache ID
     * @return boolean yes / no
     */
    public function has($key)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        
        $filename = $this->sanitizeFileName($key);
        
        if (!is_file($filename)) {
            return false;
        }
        
        $ttl = time() - $this->getTtl();
        
        if ($ttl >= filemtime($filename)) {
            return false;
        }
        
        return true;
    }
}
