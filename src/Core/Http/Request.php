<?php

declare(strict_types=1);

namespace Core\Http;

class Request implements RequestInterface
{
    /**
     * Used on POST request for identity PUT, PATCH, DELETE
     * @var string
     */
    protected $methodParam = '_method';
    /**
     * Data received from $_GET
     * @var array
     */
    private $queryParams = [];
    /**
     * Data received from $_POST
     * @var array
     */
    private $parsedBody;
    /**
     * Data received from $_SERVER
     * @var array
     */
    private $server;
    /**
     * @var array
     */
    private $headers = [];
    /**
     * Current request method
     * @var string
     */
    private $method;
    private $url;
    private $pathInfo;
    private $scriptUrl;
    private $baseUrl;
    private $attributes = [];
    /**
     * Request body
     * @var
     */
    private $content;

    public function __construct(array $queryParams = [], $parsedBody = null)
    {
        $this->queryParams = $queryParams;
        $this->parsedBody = $parsedBody;
        $this->server = $_SERVER;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function isJson(): bool
    {
        return in_array($this->getHeader('Content-Type'), ['application/json', 'application/x-json']);
    }

    public function getHeader(string $name, $default = null)
    {
        $headers = $this->getHeaders();
        $key = str_replace('-', '_', $name);

        return $headers[strtoupper($key)] ?? $default;
    }

    public function getHeaders()
    {
        if (empty($this->headers)) {

            $contentHeaders = ['CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true];

            foreach ($this->server as $key => $value) {
                if (0 === strpos($key, 'HTTP_')) {
                    $this->headers[substr($key, 5)] = $value;
                } elseif (isset($contentHeaders[$key])) {
                    $this->headers[$key] = $value;
                }
            }
        }

        return $this->headers;
    }

    public function getServerParam(string $name, $default = null)
    {

    }

    /**
     * @param array $query
     * @return Request
     */
    public function withQueryParams(array $query): self
    {
        $object = clone $this;
        $object->queryParams = $query;

        return $object;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @param $data
     * @return Request
     */
    public function withParsedBody($data): self
    {
        $object = clone $this;
        $object->parsedBody = $data;

        return $object;
    }

    public function getMethod(): string
    {
        if (!$this->method) {
            if (isset($_POST[$this->methodParam])) {
                $this->method = strtoupper($_POST[$this->methodParam]);
            } elseif (isset($_SERVER['REQUEST_METHOD'])) {
                $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
            } else {
                $this->method = 'GET';
            }
        }

        return $this->method;
    }

    /**
     * Requested path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->getPathInfo();
    }

    public function getPathInfo()
    {
        if ($this->pathInfo === null) {
            $this->pathInfo = $this->resolvePathInfo();
        }

        return $this->pathInfo;
    }

    protected function resolvePathInfo()
    {
        $pathInfo = $this->getUrl();

        if (($pos = strpos($pathInfo, '?')) !== false) {
            $pathInfo = substr($pathInfo, 0, $pos);
        }

        $pathInfo = urldecode($pathInfo);

        $scriptUrl = $this->getScriptUrl();
        $baseUrl = $this->getBaseUrl();

        if (strpos($pathInfo, $scriptUrl) === 0) {
            $pathInfo = substr($pathInfo, strlen($scriptUrl));
        } elseif ($baseUrl === '' || strpos($pathInfo, $baseUrl) === 0) {
            $pathInfo = substr($pathInfo, strlen($baseUrl));
        } elseif (isset($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0) {
            $pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
        } else {
            throw new \InvalidArgumentException('Unable to determine the path info of the current request.');
        }

        return (string)$pathInfo;
    }

    /**
     * @return null|string|string[]
     */
    public function getUrl()
    {
        if ($this->url === null) {
            $this->url = $this->resolveRequestUri();
        }

        return $this->url;
    }

    protected function resolveRequestUri()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if ($requestUri !== '' && $requestUri[0] !== '/') {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }
        } else {
            throw new \InvalidArgumentException('Unable to determine the request URI.');
        }

        return $requestUri;
    }

    public function getScriptUrl()
    {
        if ($this->scriptUrl === null) {
            $scriptFile = $this->getScriptFile();
            $scriptName = basename($scriptFile);
            if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
                $this->scriptUrl = $_SERVER['SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $scriptName) {
                $this->scriptUrl = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
                $this->scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && ($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
                $this->scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
            } elseif (!empty($_SERVER['DOCUMENT_ROOT']) && strpos($scriptFile, $_SERVER['DOCUMENT_ROOT']) === 0) {
                $this->scriptUrl = str_replace([$_SERVER['DOCUMENT_ROOT'], '\\'], ['', '/'], $scriptFile);
            } else {
                throw new \InvalidArgumentException('Unable to determine the entry script URL.');
            }
        }

        return $this->scriptUrl;
    }

    public function getScriptFile()
    {
        if (isset($this->_scriptFile)) {
            return $this->_scriptFile;
        }

        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            return $_SERVER['SCRIPT_FILENAME'];
        }

        throw new \InvalidArgumentException('Unable to determine the entry script file path.');
    }

    public function getBaseUrl()
    {
        if ($this->baseUrl === null) {
            $this->baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');
        }

        return $this->baseUrl;
    }

    /**
     * @param $attribute
     * @param $value
     * @return self
     */
    public function withAttribute($attribute, $value)
    {
        $new = clone $this;
        $new->attributes[$attribute] = $value;

        return $new;
    }

    /**
     * Find value in requested params by attribute name
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getContent()
    {
        if (null === $this->content || false === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }
}