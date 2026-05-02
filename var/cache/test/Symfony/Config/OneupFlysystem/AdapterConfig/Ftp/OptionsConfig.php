<?php

namespace Symfony\Config\OneupFlysystem\AdapterConfig\Ftp;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class OptionsConfig 
{
    private $host;
    private $root;
    private $username;
    private $password;
    private $port;
    private $ssl;
    private $timeout;
    private $utf8;
    private $passive;
    private $transferMode;
    private $systemType;
    private $ignorePassiveAddress;
    private $timestampsOnUnixListingsEnabled;
    private $recurseManually;
    private $useRawListOptions;
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
    public function root($value): static
    {
        $this->_usedProperties['root'] = true;
        $this->root = $value;

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
     * @default 21
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
    public function ssl($value): static
    {
        $this->_usedProperties['ssl'] = true;
        $this->ssl = $value;

        return $this;
    }

    /**
     * @default 90
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
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function utf8($value): static
    {
        $this->_usedProperties['utf8'] = true;
        $this->utf8 = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function passive($value): static
    {
        $this->_usedProperties['passive'] = true;
        $this->passive = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function transferMode($value): static
    {
        $this->_usedProperties['transferMode'] = true;
        $this->transferMode = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function systemType($value): static
    {
        $this->_usedProperties['systemType'] = true;
        $this->systemType = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function ignorePassiveAddress($value): static
    {
        $this->_usedProperties['ignorePassiveAddress'] = true;
        $this->ignorePassiveAddress = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function timestampsOnUnixListingsEnabled($value): static
    {
        $this->_usedProperties['timestampsOnUnixListingsEnabled'] = true;
        $this->timestampsOnUnixListingsEnabled = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function recurseManually($value): static
    {
        $this->_usedProperties['recurseManually'] = true;
        $this->recurseManually = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function useRawListOptions($value): static
    {
        $this->_usedProperties['useRawListOptions'] = true;
        $this->useRawListOptions = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('host', $value)) {
            $this->_usedProperties['host'] = true;
            $this->host = $value['host'];
            unset($value['host']);
        }

        if (array_key_exists('root', $value)) {
            $this->_usedProperties['root'] = true;
            $this->root = $value['root'];
            unset($value['root']);
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

        if (array_key_exists('port', $value)) {
            $this->_usedProperties['port'] = true;
            $this->port = $value['port'];
            unset($value['port']);
        }

        if (array_key_exists('ssl', $value)) {
            $this->_usedProperties['ssl'] = true;
            $this->ssl = $value['ssl'];
            unset($value['ssl']);
        }

        if (array_key_exists('timeout', $value)) {
            $this->_usedProperties['timeout'] = true;
            $this->timeout = $value['timeout'];
            unset($value['timeout']);
        }

        if (array_key_exists('utf8', $value)) {
            $this->_usedProperties['utf8'] = true;
            $this->utf8 = $value['utf8'];
            unset($value['utf8']);
        }

        if (array_key_exists('passive', $value)) {
            $this->_usedProperties['passive'] = true;
            $this->passive = $value['passive'];
            unset($value['passive']);
        }

        if (array_key_exists('transferMode', $value)) {
            $this->_usedProperties['transferMode'] = true;
            $this->transferMode = $value['transferMode'];
            unset($value['transferMode']);
        }

        if (array_key_exists('systemType', $value)) {
            $this->_usedProperties['systemType'] = true;
            $this->systemType = $value['systemType'];
            unset($value['systemType']);
        }

        if (array_key_exists('ignorePassiveAddress', $value)) {
            $this->_usedProperties['ignorePassiveAddress'] = true;
            $this->ignorePassiveAddress = $value['ignorePassiveAddress'];
            unset($value['ignorePassiveAddress']);
        }

        if (array_key_exists('timestampsOnUnixListingsEnabled', $value)) {
            $this->_usedProperties['timestampsOnUnixListingsEnabled'] = true;
            $this->timestampsOnUnixListingsEnabled = $value['timestampsOnUnixListingsEnabled'];
            unset($value['timestampsOnUnixListingsEnabled']);
        }

        if (array_key_exists('recurseManually', $value)) {
            $this->_usedProperties['recurseManually'] = true;
            $this->recurseManually = $value['recurseManually'];
            unset($value['recurseManually']);
        }

        if (array_key_exists('useRawListOptions', $value)) {
            $this->_usedProperties['useRawListOptions'] = true;
            $this->useRawListOptions = $value['useRawListOptions'];
            unset($value['useRawListOptions']);
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
        if (isset($this->_usedProperties['root'])) {
            $output['root'] = $this->root;
        }
        if (isset($this->_usedProperties['username'])) {
            $output['username'] = $this->username;
        }
        if (isset($this->_usedProperties['password'])) {
            $output['password'] = $this->password;
        }
        if (isset($this->_usedProperties['port'])) {
            $output['port'] = $this->port;
        }
        if (isset($this->_usedProperties['ssl'])) {
            $output['ssl'] = $this->ssl;
        }
        if (isset($this->_usedProperties['timeout'])) {
            $output['timeout'] = $this->timeout;
        }
        if (isset($this->_usedProperties['utf8'])) {
            $output['utf8'] = $this->utf8;
        }
        if (isset($this->_usedProperties['passive'])) {
            $output['passive'] = $this->passive;
        }
        if (isset($this->_usedProperties['transferMode'])) {
            $output['transferMode'] = $this->transferMode;
        }
        if (isset($this->_usedProperties['systemType'])) {
            $output['systemType'] = $this->systemType;
        }
        if (isset($this->_usedProperties['ignorePassiveAddress'])) {
            $output['ignorePassiveAddress'] = $this->ignorePassiveAddress;
        }
        if (isset($this->_usedProperties['timestampsOnUnixListingsEnabled'])) {
            $output['timestampsOnUnixListingsEnabled'] = $this->timestampsOnUnixListingsEnabled;
        }
        if (isset($this->_usedProperties['recurseManually'])) {
            $output['recurseManually'] = $this->recurseManually;
        }
        if (isset($this->_usedProperties['useRawListOptions'])) {
            $output['useRawListOptions'] = $this->useRawListOptions;
        }

        return $output;
    }

}
