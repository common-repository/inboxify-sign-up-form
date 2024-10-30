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

use Inboxify\Api\Client\RestJson;

/**
 * Implementation of Inboxifys REST-JSON API
 *
 * @package Inboxify\Api
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Client
{
    const CACHE_KEY_CONTACT = 'iy-contact-';
    const CACHE_KEY_LIST = 'iy-list-';
    const CONTENT_TYPE = 'application/json';
    const LIMIT = 1000;
    
    /**
     * @var array default configuration values
     */
    protected static $defaultConfig = array(
        'cache' => false,
        'cacheDir' => '',
        'endPoint' => 'https://api.inboxify.net/',
        'timeOutSocket' => 10,
        'timeOutStream' => 10,
        'ttl' => 3600,
    );

    /**
     * @var Client singleton instance
     */
    protected static $instance;
    
    /**
     * @var Cache Simple File Cache
     */
    protected $cache;
    
    /**
     * @var Config Configuration
     */
    protected $config;
    
    /**
     * @var array HTTP Headers for REST-JSON Request
     */
    protected $headers;
    
    /**
     * @var Client\RestJson REST-JSON Client
     */
    protected $restJson;
    
    /**
     * @var string Signatures Salt
     */
    protected $salt;
    
    /**
     * @var string Requests Signature
     */
    protected $signature;
    
    /**
     * @var string Encoded Signature
     */
    protected $signatureEncoded;
    
    /**
     * Create new Instance of the Inboxify API Client
     *
     * @param Cache    $cache    Cache
     * @param Config   $config   Configuration
     * @param RestJson $restJson RestJson Client or null
     */
    public function __construct(Cache $cache, Config $config, RestJson $restJson = null)
    {
        $this->setConfig($config);
        
        if (!$restJson) {
            $restJson = new RestJson($config);
        }
        
        $this->setCache($cache)
            ->setRestJson($restJson);
    }
    
    /**
     * Create new instance of the Client.
     *
     * @param  array $configData
     * @return Client
     */
    public static function getInstance(array $configData = array())
    {
        if (!self::$instance) {
            if (!isset($configData['key']) || !isset($configData['list']) || !isset($configData['secret'])) {
                throw new \BadMethodCallException('Config keys "key", "list", and "secret" must be set.');
            }
            
            $configData = array_merge(self::$defaultConfig, $configData);
            
            $config = new Config($configData);
            $cache = new Cache($config);

            self::$instance = new self($cache, $config);
        }
        
        return self::$instance;
    }
    
    /**
     * Get Salt for Request Signature. Salt is only regenerated if its not set
     * already.
     *
     * @return string
     */
    protected function getSalt()
    {
        if (!$this->salt) {
            $this->salt = md5(microtime(true));
        }
        
        return $this->salt;
    }
    
    /**
     * Get encoded Signature (base64)
     *
     * @return string
     */
    protected function getSignatureEncoded()
    {
        $this->signature = hash_hmac(
            'sha256',
            $this->getSalt(),
            $this->getConfig()->getSecret(),
            true
        );
        
        $this->signatureEncoded = rawurlencode(
            base64_encode($this->signature)
        );
        
        return $this->signatureEncoded;
    }
    
    /**
     * Get current Configuration
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Set Configuration
     *
     * @param  Config $config Configuration
     * @return Client self
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }
    
    /**
     * Get Cache
     *
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Get REST JSON Client
     *
     * @return Client\RestJson REST JSON Client
     */
    public function getRestJson()
    {
        return $this->restJson;
    }
    
    /**
     * Set REST JSON Client
     *
     * @param  Client\RestJson $restJson REST JSON Client
     * @return \Inboxify\Api\Client self
     */
    public function setRestJson(Client\RestJson $restJson)
    {
        $this->restJson = $restJson;
        return $this;
    }
    
    /**
     * Set Cache
     *
     * @param  \Inboxify\Api\Cache $cache cache
     * @return \Inboxify\Api\Client self
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
        return $this;
    }
    
    /**
     * Set HTTP Headers for next Request
     *
     * @param  array $headers HTTP Headers
     * @return Client self
     */
    protected function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }
    
    /**
     * Return HTTP Headers for next Request
     *
     * @return array HTTP Headers
     */
    protected function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * Generate Headers for next Request
     */
    protected function beforeRequest()
    {
        $this->setHeaders(
            array(
            'Accept' => self::CONTENT_TYPE,
            'apikey' => $this->getConfig()->getKey(),
            //'apisecret' => $this->getConfig()->getSecret(),
            'Content-Type' => self::CONTENT_TYPE,
            'signature' => $this->getSignatureEncoded(),
            'salt' => $this->getSalt(),
            )
        );
        $this->salt = null; // INFO will regenerate salt for next request
    }
    
    /**
     * Filter Contact List ID - if null, then use configured Contact List.
     *
     * @param mixed $list List ID or anything else
     */
    protected function normalizeList($list)
    {
        if (empty($list)) {
            $list = $this->getConfig()->getList();
        }
        
        if (empty($list)) {
            throw new \RuntimeException('List not configured and not passwed to client method.');
        }
        
        return rawurlencode($list);
    }
    
    /**
     * Filter API URL (convert relative to absolute).
     *
     * @param mixed $url relative URL
     */
    protected function normalizeUrl($url)
    {
        return $this->getConfig()->getEndPoint() . $url;
    }
    
    /**
     * Delete existing Contact from Inboxify API
     *
     * @param  string      $emailOrId Inboxify ID or Email
     * @param  null|string $list      Contact List Name or Inboxify ID, null for default
     * @return boolean|null null in Case the Record doesn't exist, otherwise true
     * @throws \RuntimeException In Case of Error other than not found.
     * @throws \InvalidArgumentException In Case the URL is unparsable
     */
    public function deleteContact($emailOrId, $list = null)
    {
        $this->beforeRequest();
        
        try {
            $this->restJson->delete(
                $this->normalizeUrl('contacts/' . $this->normalizeList($list) . '/' . rawurlencode($emailOrId)),
                $this->getHeaders(),
                null,
                Client\Http::NO_CONTENT
            );
            $this->getCache()->remove(self::CACHE_KEY_CONTACT . $emailOrId);
        } catch (\RuntimeException $e) {
            if (Client\Http::NOT_FOUND == $e->getCode()) {
                return null;
            }
            
            throw $e;
        }
        
        return true;
    }
    
    /**
     * Delete multiple Contacts from Inboxify API.
     *
     * @param  array       $contacts    array of contacts
     * @param  null|string $list        Contact List Name or Inboxify ID, null for default
     * @param  boolean     $unsubscribe unsubscribe all contacts while deleting them
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function deleteContacts(array $contacts, $list = null, $unsubscribe = false)
    {
        if (count($contacts) > self::LIMIT) {
            throw new \LengthException(
                sprintf(
                    'The maximum number of contacts for bulk methods is %d (%d given).',
                    self::LIMIT,
                    count($contacts)
                )
            );
        }
        
        $this->beforeRequest();
        
        $list = $this->normalizeList($list);
        $url = $this->normalizeUrl('contacts/' . $list . '/bulk-delete');
        
        if ($unsubscribe) {
            $url .= '?unsubscribe=true';
        }
        
        $res = $this->restJson->post($url, $this->getHeaders(), $contacts, Client\Http::OK);
        
        $http = $this->restJson->getHttp();
        $return = array(
            $http->getResponseHeader('x-Total-Count'),
            $http->getResponseHeader('x-Total-Fail'),
            $http->getResponseHeader('x-Total-Success'),
            $res
        );
        
        foreach ($contacts as $contact) {
            $this->cache->remove(self::CACHE_KEY_CONTACT . '-' . $list . '-' . $contact->email);
        }
        
        return $return;
    }
    
    //
    // ACTUAL API IMPLEMENTATION:
    //
    
    /**
     * Delete Tag from Contact Tags in Inboxify API
     *
     * @param  string      $emailOrId email or inboxify id
     * @param  string      $tag       Tag Name to delete
     * @param  null|string $list      Contact List Name or Inboxify ID, null for default
     * @return boolean|null True if tag was deleted, null if didn't exist
     * @throws \RuntimeException In Case the Request fails
     * @throws \InvalidArgumentException In Case the URL is unparsable
     */
    public function deleteTag($emailOrId, $tag, $list = null)
    {
        $this->beforeRequest();
        
        try {
            $list = $this->normalizeList($list);
            
            $this->restJson->delete(
                $this->normalizeUrl(
                    'contacts/' . $list . '/' . rawurlencode($emailOrId)
                    . '/tags/' . rawurlencode($tag)
                ),
                $this->getHeaders(),
                null,
                Client\Http::NO_CONTENT
            );
            
            $this->getCache()->remove(self::CACHE_KEY_CONTACT . '-' . $list . '-' . $emailOrId);
        } catch (\RuntimeException $e) {
            if (Client\Http::NOT_FOUND == $e->getCode()) {
                return null;
            }
            
            throw $e;
        }
        
        return true;
    }
    
    /**
     * Get single Contact from Contact List
     *
     * @param  string $emailOrId Unique Contact Identifier (both e-mail and Inboxify Contact ID is OK)
     * @param  string $list      null or list id
     * @throws \RuntimeException In Case the Request fails (except not found)
     * @throws \InvalidArgumentException In Case the URL is unparsable
     */
    public function getContact($emailOrId, $list = null)
    {
        $this->beforeRequest();
        
        $contact = null;
        
        try {
            $list = $this->normalizeList($list);
            $contact = $this->cache->get(self::CACHE_KEY_CONTACT . '-' . $list . '-' . $emailOrId);
            
            if (!$contact) {
                $contact = $this->restJson->get(
                    $this->normalizeUrl('contacts/' . $list . '/' . rawurlencode($emailOrId)),
                    $this->getHeaders(),
                    null,
                    Client\Http::OK
                );
                
                $this->getCache()->set(self::CACHE_KEY_CONTACT . '-' . $list . '-' . $emailOrId, $contact);
            }
        } catch (\RuntimeException $e) {
            if (Client\Http::NOT_FOUND == $e->getCode()) {
                return null;
            }
            
            throw $e;
        }
        
        return $contact;
    }

    /**
     * Get multiple Contacts from Contact List
     *
     * @param  integer     $offset       Starting Position
     * @param  integer     $limit        Record Limit (Max. 1000)
     * @param  string      $sort         ASC or DESC
     * @param  boolean     $unsubscribed Filter subscribed / unsubscribed Contacts
     * @param  null|string $list         list name or id or null for default
     * @return array
     * @throws \RuntimeException In Case the Request fails
     * @throws \InvalidArgumentException In Case the URL is unparsable
     */
    public function getContacts(
        $offset = 0,
        $limit = 20,
        $sort = 'ASC',
        $unsubscribed = null,
        $list = null
    ) {
        if ($limit > self::LIMIT) {
            throw new \LengthException(
                sprintf(
                    'The maximum number of contacts for bulk methods is %d (%d given).',
                    self::LIMIT,
                    $limit
                )
            );
        }
        
        $this->beforeRequest();
        
        $query = array(
            'sort' => $sort
        );
        
        if ($offset > 0) {
            $query['offset'] = $offset;
        }
        if ($limit > 0) {
            $query['limit'] = $limit;
        }
        if (!is_null($unsubscribed)) {
            $query['unsubscribed'] = $unsubscribed ? 'True' : 'False';
        }
        
        $data = $this->restJson->get(
            $this->normalizeUrl('contacts/' . $this->normalizeList($list) . '/?' . http_build_query($query)),
            $this->getHeaders(),
            null,
            Client\Http::OK
        );
        
        return $data;
    }
    
    /**
     * Get available Contact Lists
     *
     * @return array Array of available Lists (Objects)
     * @throws \RuntimeException In Case the Request fails
     * @throws \InvalidArgumentException In Case the URL is unparsable
     */
    public function getLists()
    {
        $this->beforeRequest();
        
        $lists = $this->cache->get(self::CACHE_KEY_LIST);
        
        if (!$lists) {
            $lists = $this->restJson->get(
                $this->normalizeUrl('lists'),
                $this->getHeaders(),
                null,
                Client\Http::OK
            );

            $this->cache->set(self::CACHE_KEY_LIST, $lists);
        }
        
        return $lists;
    }
    
    /**
     * Get Tags of Contact from Inboxify API
     *
     * @param  string      $emailOrId   Email or Inboxify ID
     * @param  null|string $list Contact List Name or null for default
     * @return boolean|array
     * @throws \RuntimeException In Case the Request fails
     * @throws \InvalidArgumentException In Case the URL is unparsable
     */
    public function getTags($emailOrId, $list = null)
    {
        $this->beforeRequest();
        
        $data = $this->restJson->get(
            $this->normalizeUrl('contacts/' . $this->normalizeList($list) . '/' . rawurlencode($emailOrId) . '/tags'),
            $this->getHeaders(),
            null,
            Client\Http::OK
        );
        
        return $data;
    }
    
    /**
     * Create new contact in Inboxify Contact List
     *
     * @param  object      $contact New Contact
     * @param  null|string $list    Contact List Name or ID, null for default List
     * @return object false or returned record from API
     * @throws \RuntimeException In Case the Request fails
     * @throws \InvalidArgumentException In Case the URL is unparsable
     */
    public function postContact($contact, $list = null)
    {
        $this->beforeRequest();
        
        $list = $this->normalizeList($list);
        $contact = $this->restJson->post(
            $this->normalizeUrl('contacts/' . $list),
            $this->getHeaders(),
            $contact,
            Client\Http::CREATED
        );
        
        $this->cache->set(self::CACHE_KEY_CONTACT . '-' . $list . '-' . $contact->email, $contact);
        
        return $contact;
    }
    
    /**
     * Insert multiple Contacts to Inboxify API
     *
     * @param  array       $contacts Array of Contact Objects
     * @param  null|string $list     Contact List Name or null for default
     * @return boolean
     * @throws \RuntimeException In Case the Request fails
     * @throws \InvalidArgumentException In Case the URL is unparsable
     */
    public function postContacts($contacts, $list = null)
    {
        if (count($contacts) > self::LIMIT) {
            throw new \LengthException(
                sprintf(
                    'The maximum number of contacts for bulk methods is %d (%d given).',
                    self::LIMIT,
                    count($contacts)
                )
            );
        }
        
        $this->beforeRequest();
        
        $list = $this->normalizeList($list);
        
        $this->restJson->post(
            $this->normalizeUrl('contacts/' . $list . '/bulk'),
            $this->getHeaders(),
            $contacts,
            Client\Http::OK
        );
        
        foreach ($contacts as $contact) {
            $this->cache->remove(self::CACHE_KEY_CONTACT . '-' . $list . '-' . $contact->email);
        }
        
        return true;
    }
    
    /**
     * Insert multiple Contacts to Inboxify API and optionally overwrite them (default).
     *
     * @param  array       $contacts  Array of Contact Objects
     * @param  null|string $list      Contact List Name or null for default
     * @param  boolean     $overwrite overwrite contacts if they exist
     * @return boolean
     * @throws \RuntimeException In Case the Request fails
     * @throws \InvalidArgumentException In Case the URL is unparsable
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function postContactsBulk($contacts, $list = null, $overwrite = true)
    {
        if (count($contacts) > self::LIMIT) {
            throw new \LengthException(
                sprintf(
                    'The maximum number of contacts for bulk methods is %d (%d given).',
                    self::LIMIT,
                    count($contacts)
                )
            );
        }
        
        $this->beforeRequest();
        
        $list = $this->normalizeList($list);
        $url = $this->normalizeUrl('contacts/' . $list . '/bulk-insert');
        
        if ($overwrite) {
            $url .= '?overwrite=true';
        }
        
        $res = $this->restJson->post($url, $this->getHeaders(), $contacts, Client\Http::OK);
        
        $http = $this->restJson->getHttp();
        $return = array(
            $http->getResponseHeader('x-Total-Count'),
            $http->getResponseHeader('x-Total-Fail'),
            $http->getResponseHeader('x-Total-Success'),
            $res
        );
        
        foreach ($contacts as $contact) {
            $this->cache->remove(self::CACHE_KEY_CONTACT . '-' . $list . '-' . $contact->email);
        }
        
        return $return;
    }
    
    /**
     * Unsubscribe multiple Contacts from Inboxify API.
     *
     * @param  array       $contacts Array of Contact Objects
     * @param  null|string $list     Contact List Name or null for default
     * @return boolean
     * @throws \RuntimeException In Case the Request fails
     * @throws \InvalidArgumentException In Case the URL is unparsable
     */
    public function postContactsUnsubscribe($contacts, $list = null)
    {
        if (count($contacts) > self::LIMIT) {
            throw new \LengthException(
                sprintf(
                    'The maximum number of contacts for bulk methods is %d (%d given).',
                    self::LIMIT,
                    count($contacts)
                )
            );
        }
        
        $this->beforeRequest();
        
        $list = $this->normalizeList($list);
        
        $res = $this->restJson->post(
            $this->normalizeUrl('contacts/' . $list . '/bulk-unsubscribe'),
            $this->getHeaders(),
            $contacts,
            Client\Http::OK
        );
        
        $http = $this->restJson->getHttp();
        $return = array(
            $http->getResponseHeader('x-Total-Count'),
            $http->getResponseHeader('x-Total-Fail'),
            $http->getResponseHeader('x-Total-Success'),
            $res
        );
        
        foreach ($contacts as $contact) {
            $this->cache->remove(self::CACHE_KEY_CONTACT . '-' . $list . '-' . $contact->email);
        }
        
        return $return;
    }
    
    /**
     * Add Tags to existing Contact
     *
     * @param  string      $emailOrId Email or Inboxify ID
     * @param  array       $tags      tags
     * @param  null|string $list      Contact List Name or null for default
     * @return boolean
     * @throws \RuntimeException In Case the Request fails
     * @throws \InvalidArgumentException In Case the URL is unparsable
     */
    public function postTags($emailOrId, $tags, $list = null)
    {
        $this->beforeRequest();
        
        $list = $this->normalizeList($list);
        
        $this->restJson->post(
            $this->normalizeUrl('contacts/' . $list . '/' . rawurlencode($emailOrId) . '/tags'),
            $this->getHeaders(),
            $tags,
            Client\Http::CREATED
        );
        
        $this->cache->remove(self::CACHE_KEY_CONTACT . '-' . $list . '-' . $emailOrId);
        
        return true;
    }
    
    /**
     * Update Contact in Inboxify API
     *
     * @param  string       $emailOrId Email or Inboxify ID
     * @param  object       $contact   Contact Data
     * @param  null|string  $list      Contact List Name or null for default
     * @param  null|boolean $subscribe Force Subscribe or Unsubscribe, null let API handle it
     * @return boolean true
     * @throws \RuntimeException In Case the Request fails (except not found error)
     * @throws \InvalidArgumentException In Case the URL is unparsable
     */
    public function putContact($emailOrId, $contact, $list = null, $subscribe = null)
    {
        $this->beforeRequest();
        
        $list = $this->normalizeList($list);
        $url = $this->normalizeUrl('contacts/' . $list . '/' . rawurlencode($emailOrId));
        
        if (!is_null($subscribe)) {
            $url .= '?subscribe=' . ( $subscribe ? 'True' : 'False' );
        }

        try {
            $this->restJson->put(
                $url,
                $this->getHeaders(),
                $contact,
                Client\Http::OK
            );
            
            $this->cache->remove(self::CACHE_KEY_CONTACT . '-' . $list . '-' . $emailOrId);
        } catch (\RuntimeException $e) {
            if (Client\Http::NOT_FOUND == $e->getCode()) {
                return null;
            }
            
            throw $e;
        }
        
        return true;
    }
    
    /**
     * Test current connection credentials.
     *
     * @return boolean
     */
    public function isConnected()
    {
        try {
            $this->cache->remove(self::CACHE_KEY_LIST);
            $this->getLists();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
