<?php

namespace Symfony\Config\FosElastica\IndexesConfig\IndexPrototype\Persistence;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ProviderConfig 
{
    private $batchSize;
    private $clearObjectManager;
    private $debugLogging;
    private $queryBuilderMethod;
    private $locale;
    private $service;
    private $_usedProperties = [];

    /**
     * @default 100
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function batchSize($value): static
    {
        $this->_usedProperties['batchSize'] = true;
        $this->batchSize = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function clearObjectManager($value): static
    {
        $this->_usedProperties['clearObjectManager'] = true;
        $this->clearObjectManager = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function debugLogging($value): static
    {
        $this->_usedProperties['debugLogging'] = true;
        $this->debugLogging = $value;

        return $this;
    }

    /**
     * @default 'createQueryBuilder'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function queryBuilderMethod($value): static
    {
        $this->_usedProperties['queryBuilderMethod'] = true;
        $this->queryBuilderMethod = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function locale($value): static
    {
        $this->_usedProperties['locale'] = true;
        $this->locale = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function service($value): static
    {
        $this->_usedProperties['service'] = true;
        $this->service = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('batch_size', $value)) {
            $this->_usedProperties['batchSize'] = true;
            $this->batchSize = $value['batch_size'];
            unset($value['batch_size']);
        }

        if (array_key_exists('clear_object_manager', $value)) {
            $this->_usedProperties['clearObjectManager'] = true;
            $this->clearObjectManager = $value['clear_object_manager'];
            unset($value['clear_object_manager']);
        }

        if (array_key_exists('debug_logging', $value)) {
            $this->_usedProperties['debugLogging'] = true;
            $this->debugLogging = $value['debug_logging'];
            unset($value['debug_logging']);
        }

        if (array_key_exists('query_builder_method', $value)) {
            $this->_usedProperties['queryBuilderMethod'] = true;
            $this->queryBuilderMethod = $value['query_builder_method'];
            unset($value['query_builder_method']);
        }

        if (array_key_exists('locale', $value)) {
            $this->_usedProperties['locale'] = true;
            $this->locale = $value['locale'];
            unset($value['locale']);
        }

        if (array_key_exists('service', $value)) {
            $this->_usedProperties['service'] = true;
            $this->service = $value['service'];
            unset($value['service']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['batchSize'])) {
            $output['batch_size'] = $this->batchSize;
        }
        if (isset($this->_usedProperties['clearObjectManager'])) {
            $output['clear_object_manager'] = $this->clearObjectManager;
        }
        if (isset($this->_usedProperties['debugLogging'])) {
            $output['debug_logging'] = $this->debugLogging;
        }
        if (isset($this->_usedProperties['queryBuilderMethod'])) {
            $output['query_builder_method'] = $this->queryBuilderMethod;
        }
        if (isset($this->_usedProperties['locale'])) {
            $output['locale'] = $this->locale;
        }
        if (isset($this->_usedProperties['service'])) {
            $output['service'] = $this->service;
        }

        return $output;
    }

}
