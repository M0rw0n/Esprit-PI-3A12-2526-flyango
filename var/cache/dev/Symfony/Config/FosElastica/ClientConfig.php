<?php

namespace Symfony\Config\FosElastica;

require_once __DIR__.\DIRECTORY_SEPARATOR.'ClientConfig'.\DIRECTORY_SEPARATOR.'ClientConfigConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ClientConfig 
{
    private $hosts;
    private $username;
    private $password;
    private $httpClient;
    private $cloudId;
    private $retries;
    private $apiKey;
    private $httpErrorCodes;
    private $logger;
    private $clientConfig;
    private $clientOptions;
    private $headers;
    private $timeout;
    private $retryOnConflict;
    private $connectionStrategy;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function hosts(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['hosts'] = true;
        $this->hosts = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function username($value): static
    {
        $this->_usedProperties['username'] = true;
        $this->username = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function password($value): static
    {
        $this->_usedProperties['password'] = true;
        $this->password = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function httpClient($value): static
    {
        $this->_usedProperties['httpClient'] = true;
        $this->httpClient = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function cloudId($value): static
    {
        $this->_usedProperties['cloudId'] = true;
        $this->cloudId = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function retries($value): static
    {
        $this->_usedProperties['retries'] = true;
        $this->retries = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function apiKey($value): static
    {
        $this->_usedProperties['apiKey'] = true;
        $this->apiKey = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|mixed $value
     *
     * @return $this
     */
    public function httpErrorCodes(mixed $value): static
    {
        $this->_usedProperties['httpErrorCodes'] = true;
        $this->httpErrorCodes = $value;

        return $this;
    }

    /**
     * @default 'fos_elastica.logger'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function logger($value): static
    {
        $this->_usedProperties['logger'] = true;
        $this->logger = $value;

        return $this;
    }

    public function clientConfig(array $value = []): \Symfony\Config\FosElastica\ClientConfig\ClientConfigConfig
    {
        if (null === $this->clientConfig) {
            $this->_usedProperties['clientConfig'] = true;
            $this->clientConfig = new \Symfony\Config\FosElastica\ClientConfig\ClientConfigConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "clientConfig()" has already been initialized. You cannot pass values the second time you call clientConfig().');
        }

        return $this->clientConfig;
    }

    /**
     * @return $this
     */
    public function clientOptions(string $name, mixed $value): static
    {
        $this->_usedProperties['clientOptions'] = true;
        $this->clientOptions[$name] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function headers(string $name, mixed $value): static
    {
        $this->_usedProperties['headers'] = true;
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @default 30
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function timeout($value): static
    {
        $this->_usedProperties['timeout'] = true;
        $this->timeout = $value;

        return $this;
    }

    /**
     * @default 0
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function retryOnConflict($value): static
    {
        $this->_usedProperties['retryOnConflict'] = true;
        $this->retryOnConflict = $value;

        return $this;
    }

    /**
     * @default 'Simple'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function connectionStrategy($value): static
    {
        $this->_usedProperties['connectionStrategy'] = true;
        $this->connectionStrategy = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('hosts', $value)) {
            $this->_usedProperties['hosts'] = true;
            $this->hosts = $value['hosts'];
            unset($value['hosts']);
        }

        if (array_key_exists('username', $value)) {
            $this->_usedProperties['username'] = true;
            $this->username = $value['username'];
            unset($value['username']);
        }

        if (array_key_exists('password', $value)) {
            $this->_usedProperties['password'] = true;
            $this->password = $value['password'];
            unset($value['password']);
        }

        if (array_key_exists('http_client', $value)) {
            $this->_usedProperties['httpClient'] = true;
            $this->httpClient = $value['http_client'];
            unset($value['http_client']);
        }

        if (array_key_exists('cloud_id', $value)) {
            $this->_usedProperties['cloudId'] = true;
            $this->cloudId = $value['cloud_id'];
            unset($value['cloud_id']);
        }

        if (array_key_exists('retries', $value)) {
            $this->_usedProperties['retries'] = true;
            $this->retries = $value['retries'];
            unset($value['retries']);
        }

        if (array_key_exists('api_key', $value)) {
            $this->_usedProperties['apiKey'] = true;
            $this->apiKey = $value['api_key'];
            unset($value['api_key']);
        }

        if (array_key_exists('http_error_codes', $value)) {
            $this->_usedProperties['httpErrorCodes'] = true;
            $this->httpErrorCodes = $value['http_error_codes'];
            unset($value['http_error_codes']);
        }

        if (array_key_exists('logger', $value)) {
            $this->_usedProperties['logger'] = true;
            $this->logger = $value['logger'];
            unset($value['logger']);
        }

        if (array_key_exists('client_config', $value)) {
            $this->_usedProperties['clientConfig'] = true;
            $this->clientConfig = new \Symfony\Config\FosElastica\ClientConfig\ClientConfigConfig($value['client_config']);
            unset($value['client_config']);
        }

        if (array_key_exists('client_options', $value)) {
            $this->_usedProperties['clientOptions'] = true;
            $this->clientOptions = $value['client_options'];
            unset($value['client_options']);
        }

        if (array_key_exists('headers', $value)) {
            $this->_usedProperties['headers'] = true;
            $this->headers = $value['headers'];
            unset($value['headers']);
        }

        if (array_key_exists('timeout', $value)) {
            $this->_usedProperties['timeout'] = true;
            $this->timeout = $value['timeout'];
            unset($value['timeout']);
        }

        if (array_key_exists('retry_on_conflict', $value)) {
            $this->_usedProperties['retryOnConflict'] = true;
            $this->retryOnConflict = $value['retry_on_conflict'];
            unset($value['retry_on_conflict']);
        }

        if (array_key_exists('connection_strategy', $value)) {
            $this->_usedProperties['connectionStrategy'] = true;
            $this->connectionStrategy = $value['connection_strategy'];
            unset($value['connection_strategy']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['hosts'])) {
            $output['hosts'] = $this->hosts;
        }
        if (isset($this->_usedProperties['username'])) {
            $output['username'] = $this->username;
        }
        if (isset($this->_usedProperties['password'])) {
            $output['password'] = $this->password;
        }
        if (isset($this->_usedProperties['httpClient'])) {
            $output['http_client'] = $this->httpClient;
        }
        if (isset($this->_usedProperties['cloudId'])) {
            $output['cloud_id'] = $this->cloudId;
        }
        if (isset($this->_usedProperties['retries'])) {
            $output['retries'] = $this->retries;
        }
        if (isset($this->_usedProperties['apiKey'])) {
            $output['api_key'] = $this->apiKey;
        }
        if (isset($this->_usedProperties['httpErrorCodes'])) {
            $output['http_error_codes'] = $this->httpErrorCodes;
        }
        if (isset($this->_usedProperties['logger'])) {
            $output['logger'] = $this->logger;
        }
        if (isset($this->_usedProperties['clientConfig'])) {
            $output['client_config'] = $this->clientConfig->toArray();
        }
        if (isset($this->_usedProperties['clientOptions'])) {
            $output['client_options'] = $this->clientOptions;
        }
        if (isset($this->_usedProperties['headers'])) {
            $output['headers'] = $this->headers;
        }
        if (isset($this->_usedProperties['timeout'])) {
            $output['timeout'] = $this->timeout;
        }
        if (isset($this->_usedProperties['retryOnConflict'])) {
            $output['retry_on_conflict'] = $this->retryOnConflict;
        }
        if (isset($this->_usedProperties['connectionStrategy'])) {
            $output['connection_strategy'] = $this->connectionStrategy;
        }

        return $output;
    }

}
