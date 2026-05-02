<?php

namespace Symfony\Config\FosElastica\ClientConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ClientConfigConfig 
{
    private $sslCert;
    private $sslKey;
    private $sslVerify;
    private $sslCa;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function sslCert($value): static
    {
        $this->_usedProperties['sslCert'] = true;
        $this->sslCert = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function sslKey($value): static
    {
        $this->_usedProperties['sslKey'] = true;
        $this->sslKey = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function sslVerify($value): static
    {
        $this->_usedProperties['sslVerify'] = true;
        $this->sslVerify = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function sslCa($value): static
    {
        $this->_usedProperties['sslCa'] = true;
        $this->sslCa = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('ssl_cert', $value)) {
            $this->_usedProperties['sslCert'] = true;
            $this->sslCert = $value['ssl_cert'];
            unset($value['ssl_cert']);
        }

        if (array_key_exists('ssl_key', $value)) {
            $this->_usedProperties['sslKey'] = true;
            $this->sslKey = $value['ssl_key'];
            unset($value['ssl_key']);
        }

        if (array_key_exists('ssl_verify', $value)) {
            $this->_usedProperties['sslVerify'] = true;
            $this->sslVerify = $value['ssl_verify'];
            unset($value['ssl_verify']);
        }

        if (array_key_exists('ssl_ca', $value)) {
            $this->_usedProperties['sslCa'] = true;
            $this->sslCa = $value['ssl_ca'];
            unset($value['ssl_ca']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['sslCert'])) {
            $output['ssl_cert'] = $this->sslCert;
        }
        if (isset($this->_usedProperties['sslKey'])) {
            $output['ssl_key'] = $this->sslKey;
        }
        if (isset($this->_usedProperties['sslVerify'])) {
            $output['ssl_verify'] = $this->sslVerify;
        }
        if (isset($this->_usedProperties['sslCa'])) {
            $output['ssl_ca'] = $this->sslCa;
        }

        return $output;
    }

}
