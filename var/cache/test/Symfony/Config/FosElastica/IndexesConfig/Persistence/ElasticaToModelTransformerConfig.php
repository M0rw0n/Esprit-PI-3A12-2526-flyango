<?php

namespace Symfony\Config\FosElastica\IndexesConfig\Persistence;

require_once __DIR__.\DIRECTORY_SEPARATOR.'ElasticaToModelTransformer'.\DIRECTORY_SEPARATOR.'HintsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ElasticaToModelTransformerConfig 
{
    private $hints;
    private $hydrate;
    private $ignoreMissing;
    private $queryBuilderMethod;
    private $service;
    private $_usedProperties = [];

    public function hints(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\Persistence\ElasticaToModelTransformer\HintsConfig
    {
        $this->_usedProperties['hints'] = true;

        return $this->hints[] = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\ElasticaToModelTransformer\HintsConfig($value);
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function hydrate($value): static
    {
        $this->_usedProperties['hydrate'] = true;
        $this->hydrate = $value;

        return $this;
    }

    /**
     * Silently ignore results returned from Elasticsearch without corresponding persistent object.
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function ignoreMissing($value): static
    {
        $this->_usedProperties['ignoreMissing'] = true;
        $this->ignoreMissing = $value;

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
    public function service($value): static
    {
        $this->_usedProperties['service'] = true;
        $this->service = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('hints', $value)) {
            $this->_usedProperties['hints'] = true;
            $this->hints = array_map(fn ($v) => new \Symfony\Config\FosElastica\IndexesConfig\Persistence\ElasticaToModelTransformer\HintsConfig($v), $value['hints']);
            unset($value['hints']);
        }

        if (array_key_exists('hydrate', $value)) {
            $this->_usedProperties['hydrate'] = true;
            $this->hydrate = $value['hydrate'];
            unset($value['hydrate']);
        }

        if (array_key_exists('ignore_missing', $value)) {
            $this->_usedProperties['ignoreMissing'] = true;
            $this->ignoreMissing = $value['ignore_missing'];
            unset($value['ignore_missing']);
        }

        if (array_key_exists('query_builder_method', $value)) {
            $this->_usedProperties['queryBuilderMethod'] = true;
            $this->queryBuilderMethod = $value['query_builder_method'];
            unset($value['query_builder_method']);
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
        if (isset($this->_usedProperties['hints'])) {
            $output['hints'] = array_map(fn ($v) => $v->toArray(), $this->hints);
        }
        if (isset($this->_usedProperties['hydrate'])) {
            $output['hydrate'] = $this->hydrate;
        }
        if (isset($this->_usedProperties['ignoreMissing'])) {
            $output['ignore_missing'] = $this->ignoreMissing;
        }
        if (isset($this->_usedProperties['queryBuilderMethod'])) {
            $output['query_builder_method'] = $this->queryBuilderMethod;
        }
        if (isset($this->_usedProperties['service'])) {
            $output['service'] = $this->service;
        }

        return $output;
    }

}
