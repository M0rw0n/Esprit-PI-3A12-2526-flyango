<?php

namespace Symfony\Config\OneupFlysystem\AdapterConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AsyncAwsS3Config 
{
    private $client;
    private $bucket;
    private $prefix;
    private $visibilityConverter;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function client($value): static
    {
        $this->_usedProperties['client'] = true;
        $this->client = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function bucket($value): static
    {
        $this->_usedProperties['bucket'] = true;
        $this->bucket = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function prefix($value): static
    {
        $this->_usedProperties['prefix'] = true;
        $this->prefix = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function visibilityConverter($value): static
    {
        $this->_usedProperties['visibilityConverter'] = true;
        $this->visibilityConverter = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('client', $value)) {
            $this->_usedProperties['client'] = true;
            $this->client = $value['client'];
            unset($value['client']);
        }

        if (array_key_exists('bucket', $value)) {
            $this->_usedProperties['bucket'] = true;
            $this->bucket = $value['bucket'];
            unset($value['bucket']);
        }

        if (array_key_exists('prefix', $value)) {
            $this->_usedProperties['prefix'] = true;
            $this->prefix = $value['prefix'];
            unset($value['prefix']);
        }

        if (array_key_exists('visibilityConverter', $value)) {
            $this->_usedProperties['visibilityConverter'] = true;
            $this->visibilityConverter = $value['visibilityConverter'];
            unset($value['visibilityConverter']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['client'])) {
            $output['client'] = $this->client;
        }
        if (isset($this->_usedProperties['bucket'])) {
            $output['bucket'] = $this->bucket;
        }
        if (isset($this->_usedProperties['prefix'])) {
            $output['prefix'] = $this->prefix;
        }
        if (isset($this->_usedProperties['visibilityConverter'])) {
            $output['visibilityConverter'] = $this->visibilityConverter;
        }

        return $output;
    }

}
