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
 * @package Inboxify\Api\Client
 */
namespace Inboxify\Api\Client;

use Inboxify\Api\Config;

/**
 * Simplified Socket based HTTP Client
 *
 * @method  array delete(string $url, array $headers = array(), string $body = null)
 * @method  array get(string $url, array $headers = array(), string $body = null)
 * @method  array post(string $url, array $headers = array(), string $body = null)
 * @method  array put(string $url, array $headers = array(), string $body = null)
 * @package Inboxify\Api\Client
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Http
{
    const HEAD_KV_SEP = ':';
    const EOL = "\r\n";
    const EOL2 = "\r\n\r\n";
    const SPACE = ' ';
    
    const PORT_HTTP = 80;
    const PORT_HTTPS = 443;
    
    const V_10 = 'HTTP/1.0';
    const V_11 = 'HTTP/1.1';
    
    const HTTP = 'http';
    const HTTPS = 'https';
    const HOST = 'host';
    const PATH = 'path';
    const PORT = 'port';
    const PROTOCOL = 'scheme';
    const QUERY = 'query';
    
    const DELETE = 'DELETE';
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    
    const OK = 200;
    const CREATED = 201;
    const NO_CONTENT = 204;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const NOT_FOUND = 404;
    
    const USE_GZIP = true;
    
    /**
     * @var array Allowed HTTP Methods
     */
    protected $allowedMethods = array(
        self::DELETE, self::GET, self::POST, self::PUT
    );
    
    /**
     * @var integer Socket Error Number
     */
    protected $errorCode;
    
    /**
     * @var string Socket Error Message
     */
    protected $errorMessage;
    
    /**
     * @var boolean Print HTTP Request and Response
     */
    protected $printRr;
    
    /**
     * @var string Current HTTP Request
     */
    protected $request;
    
    /**
     * @var string Last HTTP Response
     */
    protected $response;
    
    /**
     * @var string Last HTTP Response Body
     */
    protected $responseBody;
    
    /**
     * @var array Last HTTP Response Status Headers
     */
    protected $responseHeaders;
    
    /**
     * @var integer Last HTTP Response Status Code
     */
    protected $responseCode;
    
    /**
     * @var string Last HTTP Response Status Message
     */
    protected $responseStatus;
    
    /**
     * @var resource Socket
     */
    protected $socket;
    
    /**
     * @var array Parsed URL as an associative Array
     */
    protected $urlParsed;
    
    /**
     * Create new instance of HTTP Client
     *
     * @param Config $config Config Instance
     */
    public function __construct(Config $config)
    {
        $this->setConfig($config);
    }
    
    /**
     * Set Configuration
     *
     * @param \Inboxify\Api\Config $config Configuration
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }
    
    /**
     * Get Configuration
     *
     * @return \Inboxify\Api\Config
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Magic Method Implementation
     *
     * Allows short-hand Methods for HTTP Calls: delete(), get(), post(), put()
     * by wrapping-up the request() Method.
     *
     * @param  string $method Method Name
     * @param  array  $args   Method Arguments
     * @return mixed Configuration Value or null
     * @see    \Inboxify\Api\Config::__get()
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if ($this->isAllowedMethod($method)) {
            $args[0] = isset($args[0]) ? $args[0] : '';
            $args[1] = isset($args[1]) ? $args[1] : array();
            $args[2] = isset($args[2]) ? $args[2] : null;
            
            return $this->request($args[0], $method, $args[1], $args[2]);
        }
        
        throw new \BadMethodCallException('Call to undefined method ' . __CLASS__ . '::' . $method . '().');
    }
    
    /**
     * Check if the Method is allowed.
     *
     * @param  string $method Method Name
     * @return boolean
     */
    public function isAllowedMethod($method)
    {
        $method = strtoupper($method);
        return in_array($method, $this->allowedMethods);
    }
    
    /**
     * Do HTTP Request.
     *
     * This Method do HTTP Request, and stores parsed HTTP Response
     * as this Object Properties. You can do both simple Requests, and or send
     * HTTP Headers and or Body.
     * Workflow: reset Object Properties (e.g. Response), parse URL,
     * normalize URL, open Socket, create HTTP Request, send HTTP Request,
     * read HTTP Response, close Socket, and finally parse HTTP Response.
     *
     * @param  string $url     Request URL
     * @param  string $method  HTTP Method
     * @param  array  $headers HTTP Headers
     * @param  string $body    HTTP Body
     * @return boolean True in Case the Method get to the End
     * @throws \InvalidArgumentException If the URL is invalid
     * @throws \RunTimeException If there is unexpected value returned in function calls
     */
    public function request($url, $method = self::GET, array $headers = array(), $body = null)
    {
        $this->reset();
        $this->parseUrl($url);
        $this->normalizeUrl();
        $this->openSocket();
        $this->createRequest($method, $headers, $body);
        
        if ($this->printRr) {
            echo PHP_EOL . 'HTTP Request: ' . PHP_EOL . $this->request . PHP_EOL;
        }
        
        $this->sendRequest($method, $headers, $body);
        $this->readResponse();
        
        if ($this->printRr) {
            echo PHP_EOL . 'HTTP Response: ' . PHP_EOL . $this->response . PHP_EOL;
        }
        
        $this->closeSocket();
        $this->parseResponse();
        
        return true;
    }
    
    /**
     * Reset all Client's properties and set them to null.
     */
    protected function reset()
    {
        $this->errorCode = $this->errorMessage = $this->request =
        $this->response = $this->responseBody = $this->responseCode =
        $this->responseHeaders = $this->socket = $this->urlParsed = null;
    }
    
    /**
     * Parse given URL and stores the associative Array.
     *
     * @param  string $url URL to parse
     * @throws \InvalidArgumentException In case the URL is not parsable.
     */
    protected function parseUrl($url)
    {
        $this->urlParsed = parse_url($url);
    
        if (!is_array($this->urlParsed)) {
            throw new \InvalidArgumentException('Unparsable URL.');
        }
    }
    
    /**
     * Check the parsed URL, and prepare it for HTTP Request.
     *
     * Workflow: check if Hostname is not missing in parsed URL,
     * check if Port is not missing in parsed URL, check URL Protocol
     * and set the Port accordingly, and prefix URL Query with "?" or set
     * it to null.
     *
     * @throws \InvalidArgumentException In case any URL validation test fails
     */
    protected function normalizeUrl()
    {
        if (!$this->getUrlParsedKey(self::HOST)) {
            throw new \InvalidArgumentException('URL is missing Hostname.');
        }
        
        if (!$this->getUrlParsedKey(self::PATH)) {
            $this->urlParsed[self::PATH] = DIRECTORY_SEPARATOR;
        }
        
        if (!$this->getUrlParsedKey(self::PROTOCOL)) {
            throw new \InvalidArgumentException('URL is missing Protocol.');
        }
        
        if (!$this->getUrlParsedKey(self::PORT)) {
            $this->normalizeUrlPort();
        }
        
        $this->urlParsed[self::QUERY] = isset($this->urlParsed[self::QUERY])
            ? '?' . $this->urlParsed[self::QUERY] : null;
    }
    
    /**
     * Normalize parsed URL port
     *
     * @throws \InvalidArgumentException if requested protocol is not http(s)
     */
    protected function normalizeUrlPort()
    {
        switch ($this->urlParsed[self::PROTOCOL]) {
            case self::HTTP:
                $this->urlParsed[self::PORT] = self::PORT_HTTP;
                break;
            case self::HTTPS:
                $this->urlParsed[self::PORT] = self::PORT_HTTPS;
                break;
            default:
                throw new \InvalidArgumentException('Allowed URL Protocols are "http" and "https".');
        }
    }

    /**
     * Open Socket for HTTP Connection.
     *
     * @return resource The newly created Socket
     * @throws \RuntimeException In Case the Socket or its Configuration fails
     */
    protected function openSocket()
    {
        $host = (self::HTTPS == $this->urlParsed[self::PROTOCOL])
            ? 'tls://' . $this->urlParsed[self::HOST]
            : $this->urlParsed[self::HOST];
        
        $this->socket = @fsockopen(
            $host,
            $this->urlParsed[self::PORT],
            $this->errorCode,
            $this->errorMessage,
            $this->getConfig()->getTimeOutSocket()
        );
        
        if (!$this->socket) {
            throw new \RuntimeException(
                sprintf(
                    'Couldn\'t open Socket for HTTP Connection (Socket Error: %d - %s).',
                    $this->errorCode,
                    $this->errorMessage
                )
            );
        }
        
        if (!stream_set_timeout($this->socket, $this->getConfig()->getTimeOutStream())) {
            throw new \RuntimeException('Couldn\'t set Stream Time-out for HTTP Connection Socket.');
        }
        
        return $this->socket;
    }
    
    /**
     * Close HTTP Connection Socket.
     *
     * @throws \RuntimeException In Case the Sockect cannot be closed
     */
    protected function closeSocket()
    {
        if (!fclose($this->socket)) {
            throw new \RuntimeException('Couldn\'t close HTTP Connection Socket.');
        }
    }
    
    /**
     * Create HTTP Request String from given Arguments.
     *
     * @param  string $method  HTTP Method
     * @param  array  $headers HTTP Headers
     * @param  string $body    HTTP Body
     * @return string HTTP Request
     */
    protected function createRequest($method = self::GET, array &$headers = array(), &$body = null)
    {
        $this->request = strtoupper($method) . self::SPACE . $this->urlParsed['path']
            . $this->urlParsed[self::QUERY] . self::SPACE . self::V_11 . self::EOL
            . 'Host' . self::HEAD_KV_SEP . self::SPACE . $this->urlParsed['host'] . self::EOL;
        
        // INFO not supported at the moment
//        if (self::USE_GZIP) {
//            $this->request .= 'Accept-Encoding' . self::HEAD_KV_SEP . self::SPACE . 'gzip' . self::EOL;
//            $this->request .= 'Content-Encoding' . self::HEAD_KV_SEP . self::SPACE . 'gzip' . self::EOL;
//            $body = gzencode($body);
//        }
        
        $this->request .= 'Connection' . self::HEAD_KV_SEP . self::SPACE . 'Close' . self::EOL;
        
        if (in_array(strtoupper($method), array(self::POST, self::PUT))) {
            $this->request .= 'Content-Length' . self::HEAD_KV_SEP
                . self::SPACE . strlen($body) . self::EOL;
        }
        
        if (count($headers)) {
            foreach ($headers as $k => $v) {
                $this->request .= $k . self::HEAD_KV_SEP . self::SPACE . $v . self::EOL;
            }
        }
        
        $this->request .= self::EOL;
        
        if (!is_null($body)) {
            $this->request .= $body;
        }

        return $this->request;
    }
    
    /**
     * Send HTTP Request through openned Socket.
     *
     * @throws \RuntimeException In Case the HTTP Request Send fails
     */
    protected function sendRequest()
    {
        $res = fwrite($this->socket, $this->request);

        if (!$res) {
            throw new \RuntimeException('Couldn\'t send HTTP Request.');
        }
    }
    
    /**
     * Read HTTP Response from Socket
     *
     * @throws \RuntimeException In case the Read fails, or Time-out
     */
    protected function readResponse()
    {
        $this->response = null;

        // INFO this should only read up to content-length...
        while (!feof($this->socket)) {
            if (!( $res = fgets($this->socket, 128) ) && empty($this->response)) {
                throw new \RuntimeException('Couldn\'t read from HTTP Socket.');
            }
            
            if ($res) {
                $this->response .= $res;
            }
        }
        
        if (!$this->response) {
            throw new \RuntimeException('Empty HTTP Response.');
        }
    }
    
    /**
     * Parse HTTP Response string
     *
     * @throws \RuntimeException In Case the Headers or Body is empty or HTTP Status Code or Message cannot be parsed.
     */
    public function parseResponse()
    {
        list($headers, $this->responseBody) = explode(self::EOL2, $this->response, 2);

        if (!$headers && !$this->responseBody) {
            throw new \RuntimeException('Invalid HTTP Response: empty Headers and Body.');
        }
        
        $headersLines = explode(self::EOL, $headers);
        $this->responseHeaders = array();

        foreach ($headersLines as $i => $headerLine) {
            if (0 == $i) {
                list(/*$protocol*/, $this->responseCode, $this->responseStatus) =
                    explode(self::SPACE, $headerLine, 3);
                
                if (!$this->responseCode || !$this->responseStatus) {
                    throw new \RuntimeException('Invalid HTTP Response: unknown Status Code and or Message.');
                }
                
                continue;
            }

            list($name, $value) = explode(':', $headerLine, 2);
            $this->responseHeaders[strtolower(trim($name))] = strtolower(trim($value));
        }
        
        $this->parseResponseTransferEncoding();
        $this->parseResponseContentEncoding();
    }
    
    /**
     * Decode response transfer encoding
     * @throws \UnexpectedValueException
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected function parseResponseTransferEncoding()
    {
        if (isset($this->responseHeaders['transfer-encoding'])) {
            if ('chunked' == $this->responseHeaders['transfer-encoding']) {
                $this->responseBody = $this->unchunkHttpResponse($this->responseBody);
                
                if (!$this->responseBody) {
                    throw new \UnexpectedValueException('Decoding chunked transfer encoding failed.');
                }
            } else {
                throw new \UnexpectedValueException(
                    sprintf(
                        'transfer-encoding "%s" is not supported.',
                        $this->responseHeaders['transfer-encoding']
                    )
                );
            }
        }
    }
    
    /**
     * Decode response content encoding
     * @throws \UnexpectedValueException
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected function parseResponseContentEncoding()
    {
        if (isset($this->responseHeaders['content-encoding'])) {
            if ('gzip' == $this->responseHeaders['content-encoding']) {
                $this->responseBody = gzdecode($this->responseBody);
                
                if (!$this->responseBody) {
                    throw new \UnexpectedValueException('Decoding gzip content encoding failed.');
                }
            } else {
                throw new \UnexpectedValueException(
                    sprintf(
                        'content-encoding "%s" is not supported.',
                        $this->responseHeaders['transfer-encoding']
                    )
                );
            }
        }
    }
    
    /**
     * Decode transfer encoding "chunked".
     *
     * @param string $str http chunked body
     * @return mixed false or decoded string
     */
    protected function unchunkHttpResponse($str = null)
    {
        if (!is_string($str) or strlen($str) < 1) {
            return false;
        }
        
        $add = strlen(self::EOL);
        $tmp = $str;
        $str = '';
        
        do {
            $tmp = ltrim($tmp);
            $pos = strpos($tmp, self::EOL);
            
            if ($pos === false) {
                return false;
            }
            
            $len = hexdec(substr($tmp, 0, $pos));
            
            if (!is_numeric($len) or $len < 0) {
                return false;
            }
            
            $str .= substr($tmp, ($pos + $add), $len);
            $tmp  = substr($tmp, ($len + $pos + $add));
            $check = trim($tmp);
        } while (!empty($check));

        return $str;
    }
    
    /**
     * Get last Response Body
     *
     * @return string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }
    
    /**
     * Get last Response Status Code
     *
     * @return string|integer
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }
    
    /**
     * Get last Response Header by key
     *
     * @param  string $header header to get
     * @return null|string
     */
    public function getResponseHeader($header)
    {
        $header = strtolower($header);
        return isset($this->responseHeaders[$header]) ? $this->responseHeaders[$header] : null;
    }
    
    /**
     * Get multiple response headers
     *
     * @param  array|null $headers array of headers to get or null for all
     * @return array
     */
    public function getResponseHeaders($headers = null)
    {
        $res = array();
        
        if (!$headers) {
            return $this->responseHeaders;
        }
        
        foreach ($headers as $header) {
            $header = strtolower($header);
            $res[$header] = $this->getResponseHeader($header);
        }
        
        return $res;
    }
    
    /**
     * Get last Reponse Status Message
     *
     * @return string
     */
    public function getResponseMessage()
    {
        return $this->responseStatus;
    }
    
    /**
     * Get key from parsed URL.
     *
     * @param  string $key key to get
     * @return mixed value or false
     */
    protected function getUrlParsedKey($key)
    {
        return isset($this->urlParsed[$key]) && $this->urlParsed[$key]
            ? $this->urlParsed[$key] : false;
    }
    
    /**
     * Set Print HTTP Requests and Responses Flag
     *
     * @param type $value
     */
    public function setPrintRequestResponse($value)
    {
        $this->printRr = $value;
    }
}
