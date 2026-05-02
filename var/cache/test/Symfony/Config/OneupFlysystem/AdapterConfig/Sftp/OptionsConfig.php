<?php

namespace Symfony\Config\OneupFlysystem\AdapterConfig\Sftp;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class OptionsConfig 
{
    private $host;
    private $username;
    private $password;
    private $privateKey;
    private $passphrase;
    private $port;
    private $useAgent;
    private $timeout;
    private $maxTries;
    private $hostFingerprint;
    private $connectivityChecker;
    private $root;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function host($value): static
    {
        $this->_usedProperties['host'] = true;
        $this->host = $value;

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
    public function privateKey($value): static
    {
        $this->_usedProperties['privateKey'] = true;
        $this->privateKey = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function passphrase($value): static
    {
        $this->_usedProperties['passphrase'] = true;
        $this->passphrase = $value;

        return $this;
    }

    /**
     * @default 22
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function port($value): static
    {
        $this->_usedProperties['port'] = true;
        $this->port = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function useAgent($value): static
    {
        $this->_usedProperties['useAgent'] = true;
        $this->useAgent = $value;

        return $this;
    }

    /**
     * @default 10
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
     * @default 4
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function maxTries($value): static
    {
        $this->_usedProperties['maxTries'] = true;
        $this->maxTries = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function hostFingerprint($value): static
    {
        $this->_usedProperties['hostFingerprint'] = true;
        $this->hostFingerprint = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function connectivityChecker($value): static
    {
        $this->_usedProperties['connectivityChecker'] = true;
        $this->connectivityChecker = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function root($value): static
    {
        $this->_usedProperties['root'] = true;
        $this->root = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('host', $value)) {
            $this->_usedProperties['host'] = true;
            $this->host = $value['host'];
            unset($value['host']);
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

        if (array_key_exists('privateKey', $value)) {
            $this->_usedProperties['privateKey'] = true;
            $this->privateKey = $value['privateKey'];
            unset($value['privateKey']);
        }

        if (array_key_exists('passphrase', $value)) {
            $this->_usedProperties['passphrase'] = true;
            $this->passphrase = $value['passphrase'];
            unset($value['passphrase']);
        }

        if (array_key_exists('port', $value)) {
            $this->_usedProperties['port'] = true;
            $this->port = $value['port'];
            unset($value['port']);
        }

        if (array_key_exists('useAgent', $value)) {
            $this->_usedProperties['useAgent'] = true;
            $this->useAgent = $value['useAgent'];
            unset($value['useAgent']);
        }

        if (array_key_exists('timeout', $value)) {
            $this->_usedProperties['timeout'] = true;
            $this->timeout = $value['timeout'];
            unset($value['timeout']);
        }

        if (array_key_exists('maxTries', $value)) {
            $this->_usedProperties['maxTries'] = true;
            $this->maxTries = $value['maxTries'];
            unset($value['maxTries']);
        }

        if (array_key_exists('hostFingerprint', $value)) {
            $this->_usedProperties['hostFingerprint'] = true;
            $this->hostFingerprint = $value['hostFingerprint'];
            unset($value['hostFingerprint']);
        }

        if (array_key_exists('connectivityChecker', $value)) {
            $this->_usedProperties['connectivityChecker'] = true;
            $this->connectivityChecker = $value['connectivityChecker'];
            unset($value['connectivityChecker']);
        }

        if (array_key_exists('root', $value)) {
            $this->_usedProperties['root'] = true;
            $this->root = $value['root'];
            unset($value['root']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['host'])) {
            $output['host'] = $this->host;
        }
        if (isset($this->_usedProperties['username'])) {
            $output['username'] = $this->username;
        }
        if (isset($this->_usedProperties['password'])) {
            $output['password'] = $this->password;
        }
        if (isset($this->_usedProperties['privateKey'])) {
            $output['privateKey'] = $this->privateKey;
        }
        if (isset($this->_usedProperties['passphrase'])) {
            $output['passphrase'] = $this->passphrase;
        }
        if (isset($this->_usedProperties['port'])) {
            $output['port'] = $this->port;
        }
        if (isset($this->_usedProperties['useAgent'])) {
            $output['useAgent'] = $this->useAgent;
        }
        if (isset($this->_usedProperties['timeout'])) {
            $output['timeout'] = $this->timeout;
        }
        if (isset($this->_usedProperties['maxTries'])) {
            $output['maxTries'] = $this->maxTries;
        }
        if (isset($this->_usedProperties['hostFingerprint'])) {
            $output['hostFingerprint'] = $this->hostFingerprint;
        }
        if (isset($this->_usedProperties['connectivityChecker'])) {
            $output['connectivityChecker'] = $this->connectivityChecker;
        }
        if (isset($this->_usedProperties['root'])) {
            $output['root'] = $this->root;
        }

        return $output;
    }

}
