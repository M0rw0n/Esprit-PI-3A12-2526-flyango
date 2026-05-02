<?php

namespace Symfony\Config\OneupFlysystem\AdapterConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class Awss3v3Config 
{
    private $client;
    private $bucket;
    private $prefix;
    private $visibilityConverter;
    private $mimeTypeDetector;
    private $options;
    private $streamReads;
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

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function mimeTypeDetector($value): static
    {
        $this->_usedProperties['mimeTypeDetector'] = true;
        $this->mimeTypeDetector = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function options(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['options'] = true;
        $this->options = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function streamReads($value): static
    {
        $this->_usedProperties['streamReads'] = true;
        $this->streamReads = $value;

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

        if (array_key_exists('mimeTypeDetector', $value)) {
            $this->_usedProperties['mimeTypeDetector'] = true;
            $this->mimeTypeDetector = $value['mimeTypeDetector'];
            unset($value['mimeTypeDetector']);
        }

        if (array_key_exists('options', $value)) {
            $this->_usedProperties['options'] = true;
            $this->options = $value['options'];
            unset($value['options']);
        }

        if (array_key_exists('streamReads', $value)) {
            $this->_usedProperties['streamReads'] = true;
            $this->streamReads = $value['streamReads'];
            unset($value['streamReads']);
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
        if (isset($this->_usedProperties['mimeTypeDetector'])) {
            $output['mimeTypeDetector'] = $this->mimeTypeDetector;
        }
        if (isset($this->_usedProperties['options'])) {
            $output['options'] = $this->options;
        }
        if (isset($this->_usedProperties['streamReads'])) {
            $output['streamReads'] = $this->streamReads;
        }

        return $output;
    }

}
