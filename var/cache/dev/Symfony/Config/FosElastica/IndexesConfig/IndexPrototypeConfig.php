<?php

namespace Symfony\Config\FosElastica\IndexesConfig;

require_once __DIR__.\DIRECTORY_SEPARATOR.'IndexPrototype'.\DIRECTORY_SEPARATOR.'PersistenceConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'IndexPrototype'.\DIRECTORY_SEPARATOR.'SerializerConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class IndexPrototypeConfig 
{
    private $analyzer;
    private $persistence;
    private $serializer;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function analyzer($value): static
    {
        $this->_usedProperties['analyzer'] = true;
        $this->analyzer = $value;

        return $this;
    }

    public function persistence(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\IndexPrototype\PersistenceConfig
    {
        if (null === $this->persistence) {
            $this->_usedProperties['persistence'] = true;
            $this->persistence = new \Symfony\Config\FosElastica\IndexesConfig\IndexPrototype\PersistenceConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "persistence()" has already been initialized. You cannot pass values the second time you call persistence().');
        }

        return $this->persistence;
    }

    /**
     * @default {"groups":[],"serialize_null":false}
    */
    public function serializer(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\IndexPrototype\SerializerConfig
    {
        if (null === $this->serializer) {
            $this->_usedProperties['serializer'] = true;
            $this->serializer = new \Symfony\Config\FosElastica\IndexesConfig\IndexPrototype\SerializerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "serializer()" has already been initialized. You cannot pass values the second time you call serializer().');
        }

        return $this->serializer;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('analyzer', $value)) {
            $this->_usedProperties['analyzer'] = true;
            $this->analyzer = $value['analyzer'];
            unset($value['analyzer']);
        }

        if (array_key_exists('persistence', $value)) {
            $this->_usedProperties['persistence'] = true;
            $this->persistence = new \Symfony\Config\FosElastica\IndexesConfig\IndexPrototype\PersistenceConfig($value['persistence']);
            unset($value['persistence']);
        }

        if (array_key_exists('serializer', $value)) {
            $this->_usedProperties['serializer'] = true;
            $this->serializer = new \Symfony\Config\FosElastica\IndexesConfig\IndexPrototype\SerializerConfig($value['serializer']);
            unset($value['serializer']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['analyzer'])) {
            $output['analyzer'] = $this->analyzer;
        }
        if (isset($this->_usedProperties['persistence'])) {
            $output['persistence'] = $this->persistence->toArray();
        }
        if (isset($this->_usedProperties['serializer'])) {
            $output['serializer'] = $this->serializer->toArray();
        }

        return $output;
    }

}
