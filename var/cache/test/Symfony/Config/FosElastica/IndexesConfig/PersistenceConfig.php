<?php

namespace Symfony\Config\FosElastica\IndexesConfig;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Persistence'.\DIRECTORY_SEPARATOR.'ProviderConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Persistence'.\DIRECTORY_SEPARATOR.'ListenerConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Persistence'.\DIRECTORY_SEPARATOR.'FinderConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Persistence'.\DIRECTORY_SEPARATOR.'ElasticaToModelTransformerConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Persistence'.\DIRECTORY_SEPARATOR.'ModelToElasticaTransformerConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Persistence'.\DIRECTORY_SEPARATOR.'PersisterConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PersistenceConfig 
{
    private $driver;
    private $model;
    private $repository;
    private $identifier;
    private $provider;
    private $listener;
    private $finder;
    private $elasticaToModelTransformer;
    private $modelToElasticaTransformer;
    private $persister;
    private $_usedProperties = [];

    /**
     * @default 'orm'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function driver($value): static
    {
        $this->_usedProperties['driver'] = true;
        $this->driver = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function model($value): static
    {
        $this->_usedProperties['model'] = true;
        $this->model = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function repository($value): static
    {
        $this->_usedProperties['repository'] = true;
        $this->repository = $value;

        return $this;
    }

    /**
     * @default 'id'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function identifier($value): static
    {
        $this->_usedProperties['identifier'] = true;
        $this->identifier = $value;

        return $this;
    }

    /**
     * @default {"batch_size":100,"clear_object_manager":true,"debug_logging":true,"query_builder_method":"createQueryBuilder"}
    */
    public function provider(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\Persistence\ProviderConfig
    {
        if (null === $this->provider) {
            $this->_usedProperties['provider'] = true;
            $this->provider = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\ProviderConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "provider()" has already been initialized. You cannot pass values the second time you call provider().');
        }

        return $this->provider;
    }

    /**
     * @default {"enabled":true,"insert":true,"update":true,"delete":true,"flush":true,"defer":false,"logger":false}
    */
    public function listener(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\Persistence\ListenerConfig
    {
        if (null === $this->listener) {
            $this->_usedProperties['listener'] = true;
            $this->listener = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\ListenerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "listener()" has already been initialized. You cannot pass values the second time you call listener().');
        }

        return $this->listener;
    }

    public function finder(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\Persistence\FinderConfig
    {
        if (null === $this->finder) {
            $this->_usedProperties['finder'] = true;
            $this->finder = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\FinderConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "finder()" has already been initialized. You cannot pass values the second time you call finder().');
        }

        return $this->finder;
    }

    /**
     * @default {"hints":[],"hydrate":true,"ignore_missing":false,"query_builder_method":"createQueryBuilder"}
    */
    public function elasticaToModelTransformer(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\Persistence\ElasticaToModelTransformerConfig
    {
        if (null === $this->elasticaToModelTransformer) {
            $this->_usedProperties['elasticaToModelTransformer'] = true;
            $this->elasticaToModelTransformer = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\ElasticaToModelTransformerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "elasticaToModelTransformer()" has already been initialized. You cannot pass values the second time you call elasticaToModelTransformer().');
        }

        return $this->elasticaToModelTransformer;
    }

    public function modelToElasticaTransformer(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\Persistence\ModelToElasticaTransformerConfig
    {
        if (null === $this->modelToElasticaTransformer) {
            $this->_usedProperties['modelToElasticaTransformer'] = true;
            $this->modelToElasticaTransformer = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\ModelToElasticaTransformerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "modelToElasticaTransformer()" has already been initialized. You cannot pass values the second time you call modelToElasticaTransformer().');
        }

        return $this->modelToElasticaTransformer;
    }

    public function persister(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\Persistence\PersisterConfig
    {
        if (null === $this->persister) {
            $this->_usedProperties['persister'] = true;
            $this->persister = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\PersisterConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "persister()" has already been initialized. You cannot pass values the second time you call persister().');
        }

        return $this->persister;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('driver', $value)) {
            $this->_usedProperties['driver'] = true;
            $this->driver = $value['driver'];
            unset($value['driver']);
        }

        if (array_key_exists('model', $value)) {
            $this->_usedProperties['model'] = true;
            $this->model = $value['model'];
            unset($value['model']);
        }

        if (array_key_exists('repository', $value)) {
            $this->_usedProperties['repository'] = true;
            $this->repository = $value['repository'];
            unset($value['repository']);
        }

        if (array_key_exists('identifier', $value)) {
            $this->_usedProperties['identifier'] = true;
            $this->identifier = $value['identifier'];
            unset($value['identifier']);
        }

        if (array_key_exists('provider', $value)) {
            $this->_usedProperties['provider'] = true;
            $this->provider = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\ProviderConfig($value['provider']);
            unset($value['provider']);
        }

        if (array_key_exists('listener', $value)) {
            $this->_usedProperties['listener'] = true;
            $this->listener = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\ListenerConfig($value['listener']);
            unset($value['listener']);
        }

        if (array_key_exists('finder', $value)) {
            $this->_usedProperties['finder'] = true;
            $this->finder = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\FinderConfig($value['finder']);
            unset($value['finder']);
        }

        if (array_key_exists('elastica_to_model_transformer', $value)) {
            $this->_usedProperties['elasticaToModelTransformer'] = true;
            $this->elasticaToModelTransformer = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\ElasticaToModelTransformerConfig($value['elastica_to_model_transformer']);
            unset($value['elastica_to_model_transformer']);
        }

        if (array_key_exists('model_to_elastica_transformer', $value)) {
            $this->_usedProperties['modelToElasticaTransformer'] = true;
            $this->modelToElasticaTransformer = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\ModelToElasticaTransformerConfig($value['model_to_elastica_transformer']);
            unset($value['model_to_elastica_transformer']);
        }

        if (array_key_exists('persister', $value)) {
            $this->_usedProperties['persister'] = true;
            $this->persister = new \Symfony\Config\FosElastica\IndexesConfig\Persistence\PersisterConfig($value['persister']);
            unset($value['persister']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['driver'])) {
            $output['driver'] = $this->driver;
        }
        if (isset($this->_usedProperties['model'])) {
            $output['model'] = $this->model;
        }
        if (isset($this->_usedProperties['repository'])) {
            $output['repository'] = $this->repository;
        }
        if (isset($this->_usedProperties['identifier'])) {
            $output['identifier'] = $this->identifier;
        }
        if (isset($this->_usedProperties['provider'])) {
            $output['provider'] = $this->provider->toArray();
        }
        if (isset($this->_usedProperties['listener'])) {
            $output['listener'] = $this->listener->toArray();
        }
        if (isset($this->_usedProperties['finder'])) {
            $output['finder'] = $this->finder->toArray();
        }
        if (isset($this->_usedProperties['elasticaToModelTransformer'])) {
            $output['elastica_to_model_transformer'] = $this->elasticaToModelTransformer->toArray();
        }
        if (isset($this->_usedProperties['modelToElasticaTransformer'])) {
            $output['model_to_elastica_transformer'] = $this->modelToElasticaTransformer->toArray();
        }
        if (isset($this->_usedProperties['persister'])) {
            $output['persister'] = $this->persister->toArray();
        }

        return $output;
    }

}
