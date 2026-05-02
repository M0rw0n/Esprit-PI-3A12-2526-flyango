<?php

namespace Symfony\Config\FosElastica\IndexesConfig\IndexPrototype\Persistence;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ListenerConfig 
{
    private $enabled;
    private $insert;
    private $update;
    private $delete;
    private $flush;
    private $defer;
    private $logger;
    private $service;
    private $_usedProperties = [];

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enabled($value): static
    {
        $this->_usedProperties['enabled'] = true;
        $this->enabled = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function insert($value): static
    {
        $this->_usedProperties['insert'] = true;
        $this->insert = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function update($value): static
    {
        $this->_usedProperties['update'] = true;
        $this->update = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function delete($value): static
    {
        $this->_usedProperties['delete'] = true;
        $this->delete = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function flush($value): static
    {
        $this->_usedProperties['flush'] = true;
        $this->flush = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function defer($value): static
    {
        $this->_usedProperties['defer'] = true;
        $this->defer = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function logger($value): static
    {
        $this->_usedProperties['logger'] = true;
        $this->logger = $value;

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
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('insert', $value)) {
            $this->_usedProperties['insert'] = true;
            $this->insert = $value['insert'];
            unset($value['insert']);
        }

        if (array_key_exists('update', $value)) {
            $this->_usedProperties['update'] = true;
            $this->update = $value['update'];
            unset($value['update']);
        }

        if (array_key_exists('delete', $value)) {
            $this->_usedProperties['delete'] = true;
            $this->delete = $value['delete'];
            unset($value['delete']);
        }

        if (array_key_exists('flush', $value)) {
            $this->_usedProperties['flush'] = true;
            $this->flush = $value['flush'];
            unset($value['flush']);
        }

        if (array_key_exists('defer', $value)) {
            $this->_usedProperties['defer'] = true;
            $this->defer = $value['defer'];
            unset($value['defer']);
        }

        if (array_key_exists('logger', $value)) {
            $this->_usedProperties['logger'] = true;
            $this->logger = $value['logger'];
            unset($value['logger']);
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
        if (isset($this->_usedProperties['enabled'])) {
            $output['enabled'] = $this->enabled;
        }
        if (isset($this->_usedProperties['insert'])) {
            $output['insert'] = $this->insert;
        }
        if (isset($this->_usedProperties['update'])) {
            $output['update'] = $this->update;
        }
        if (isset($this->_usedProperties['delete'])) {
            $output['delete'] = $this->delete;
        }
        if (isset($this->_usedProperties['flush'])) {
            $output['flush'] = $this->flush;
        }
        if (isset($this->_usedProperties['defer'])) {
            $output['defer'] = $this->defer;
        }
        if (isset($this->_usedProperties['logger'])) {
            $output['logger'] = $this->logger;
        }
        if (isset($this->_usedProperties['service'])) {
            $output['service'] = $this->service;
        }

        return $output;
    }

}
