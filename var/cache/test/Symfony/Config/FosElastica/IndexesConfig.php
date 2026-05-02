<?php

namespace Symfony\Config\FosElastica;

require_once __DIR__.\DIRECTORY_SEPARATOR.'IndexesConfig'.\DIRECTORY_SEPARATOR.'PersistenceConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'IndexesConfig'.\DIRECTORY_SEPARATOR.'SerializerConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'IndexesConfig'.\DIRECTORY_SEPARATOR.'IndexPrototypeConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'IndexesConfig'.\DIRECTORY_SEPARATOR.'IdConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'IndexesConfig'.\DIRECTORY_SEPARATOR.'DynamicTemplatesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'IndexesConfig'.\DIRECTORY_SEPARATOR.'SourceConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'IndexesConfig'.\DIRECTORY_SEPARATOR.'RoutingConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class IndexesConfig 
{
    private $indexName;
    private $indexableCallback;
    private $useAlias;
    private $client;
    private $finder;
    private $persistence;
    private $serializer;
    private $indexPrototype;
    private $settings;
    private $dateDetection;
    private $dynamicDateFormats;
    private $analyzer;
    private $numericDetection;
    private $dynamic;
    private $_id;
    private $properties;
    private $dynamicTemplates;
    private $_source;
    private $_routing;
    private $_usedProperties = [];

    /**
     * Defaults to the name of the index, but can be modified if the index name is different in ElasticSearch
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function indexName($value): static
    {
        $this->_usedProperties['indexName'] = true;
        $this->indexName = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function indexableCallback(mixed $value): static
    {
        $this->_usedProperties['indexableCallback'] = true;
        $this->indexableCallback = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function useAlias($value): static
    {
        $this->_usedProperties['useAlias'] = true;
        $this->useAlias = $value;

        return $this;
    }

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
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function finder($value): static
    {
        $this->_usedProperties['finder'] = true;
        $this->finder = $value;

        return $this;
    }

    public function persistence(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\PersistenceConfig
    {
        if (null === $this->persistence) {
            $this->_usedProperties['persistence'] = true;
            $this->persistence = new \Symfony\Config\FosElastica\IndexesConfig\PersistenceConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "persistence()" has already been initialized. You cannot pass values the second time you call persistence().');
        }

        return $this->persistence;
    }

    /**
     * @default {"groups":[],"serialize_null":false}
    */
    public function serializer(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\SerializerConfig
    {
        if (null === $this->serializer) {
            $this->_usedProperties['serializer'] = true;
            $this->serializer = new \Symfony\Config\FosElastica\IndexesConfig\SerializerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "serializer()" has already been initialized. You cannot pass values the second time you call serializer().');
        }

        return $this->serializer;
    }

    public function indexPrototype(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\IndexPrototypeConfig
    {
        if (null === $this->indexPrototype) {
            $this->_usedProperties['indexPrototype'] = true;
            $this->indexPrototype = new \Symfony\Config\FosElastica\IndexesConfig\IndexPrototypeConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "indexPrototype()" has already been initialized. You cannot pass values the second time you call indexPrototype().');
        }

        return $this->indexPrototype;
    }

    /**
     * @default array (
     * )
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function settings(mixed $value = array (
    )): static
    {
        $this->_usedProperties['settings'] = true;
        $this->settings = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function dateDetection($value): static
    {
        $this->_usedProperties['dateDetection'] = true;
        $this->dateDetection = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function dynamicDateFormats(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['dynamicDateFormats'] = true;
        $this->dynamicDateFormats = $value;

        return $this;
    }

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

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function numericDetection($value): static
    {
        $this->_usedProperties['numericDetection'] = true;
        $this->numericDetection = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function dynamic($value): static
    {
        $this->_usedProperties['dynamic'] = true;
        $this->dynamic = $value;

        return $this;
    }

    public function id(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\IdConfig
    {
        if (null === $this->_id) {
            $this->_usedProperties['_id'] = true;
            $this->_id = new \Symfony\Config\FosElastica\IndexesConfig\IdConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "id()" has already been initialized. You cannot pass values the second time you call id().');
        }

        return $this->_id;
    }

    /**
     * @return $this
     */
    public function properties(string $name, mixed $value): static
    {
        $this->_usedProperties['properties'] = true;
        $this->properties[$name] = $value;

        return $this;
    }

    public function dynamicTemplates(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\DynamicTemplatesConfig
    {
        $this->_usedProperties['dynamicTemplates'] = true;

        return $this->dynamicTemplates[] = new \Symfony\Config\FosElastica\IndexesConfig\DynamicTemplatesConfig($value);
    }

    public function source(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\SourceConfig
    {
        if (null === $this->_source) {
            $this->_usedProperties['_source'] = true;
            $this->_source = new \Symfony\Config\FosElastica\IndexesConfig\SourceConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "source()" has already been initialized. You cannot pass values the second time you call source().');
        }

        return $this->_source;
    }

    public function routing(array $value = []): \Symfony\Config\FosElastica\IndexesConfig\RoutingConfig
    {
        if (null === $this->_routing) {
            $this->_usedProperties['_routing'] = true;
            $this->_routing = new \Symfony\Config\FosElastica\IndexesConfig\RoutingConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "routing()" has already been initialized. You cannot pass values the second time you call routing().');
        }

        return $this->_routing;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('index_name', $value)) {
            $this->_usedProperties['indexName'] = true;
            $this->indexName = $value['index_name'];
            unset($value['index_name']);
        }

        if (array_key_exists('indexable_callback', $value)) {
            $this->_usedProperties['indexableCallback'] = true;
            $this->indexableCallback = $value['indexable_callback'];
            unset($value['indexable_callback']);
        }

        if (array_key_exists('use_alias', $value)) {
            $this->_usedProperties['useAlias'] = true;
            $this->useAlias = $value['use_alias'];
            unset($value['use_alias']);
        }

        if (array_key_exists('client', $value)) {
            $this->_usedProperties['client'] = true;
            $this->client = $value['client'];
            unset($value['client']);
        }

        if (array_key_exists('finder', $value)) {
            $this->_usedProperties['finder'] = true;
            $this->finder = $value['finder'];
            unset($value['finder']);
        }

        if (array_key_exists('persistence', $value)) {
            $this->_usedProperties['persistence'] = true;
            $this->persistence = new \Symfony\Config\FosElastica\IndexesConfig\PersistenceConfig($value['persistence']);
            unset($value['persistence']);
        }

        if (array_key_exists('serializer', $value)) {
            $this->_usedProperties['serializer'] = true;
            $this->serializer = new \Symfony\Config\FosElastica\IndexesConfig\SerializerConfig($value['serializer']);
            unset($value['serializer']);
        }

        if (array_key_exists('index_prototype', $value)) {
            $this->_usedProperties['indexPrototype'] = true;
            $this->indexPrototype = new \Symfony\Config\FosElastica\IndexesConfig\IndexPrototypeConfig($value['index_prototype']);
            unset($value['index_prototype']);
        }

        if (array_key_exists('settings', $value)) {
            $this->_usedProperties['settings'] = true;
            $this->settings = $value['settings'];
            unset($value['settings']);
        }

        if (array_key_exists('date_detection', $value)) {
            $this->_usedProperties['dateDetection'] = true;
            $this->dateDetection = $value['date_detection'];
            unset($value['date_detection']);
        }

        if (array_key_exists('dynamic_date_formats', $value)) {
            $this->_usedProperties['dynamicDateFormats'] = true;
            $this->dynamicDateFormats = $value['dynamic_date_formats'];
            unset($value['dynamic_date_formats']);
        }

        if (array_key_exists('analyzer', $value)) {
            $this->_usedProperties['analyzer'] = true;
            $this->analyzer = $value['analyzer'];
            unset($value['analyzer']);
        }

        if (array_key_exists('numeric_detection', $value)) {
            $this->_usedProperties['numericDetection'] = true;
            $this->numericDetection = $value['numeric_detection'];
            unset($value['numeric_detection']);
        }

        if (array_key_exists('dynamic', $value)) {
            $this->_usedProperties['dynamic'] = true;
            $this->dynamic = $value['dynamic'];
            unset($value['dynamic']);
        }

        if (array_key_exists('_id', $value)) {
            $this->_usedProperties['_id'] = true;
            $this->_id = new \Symfony\Config\FosElastica\IndexesConfig\IdConfig($value['_id']);
            unset($value['_id']);
        }

        if (array_key_exists('properties', $value)) {
            $this->_usedProperties['properties'] = true;
            $this->properties = $value['properties'];
            unset($value['properties']);
        }

        if (array_key_exists('dynamic_templates', $value)) {
            $this->_usedProperties['dynamicTemplates'] = true;
            $this->dynamicTemplates = array_map(fn ($v) => new \Symfony\Config\FosElastica\IndexesConfig\DynamicTemplatesConfig($v), $value['dynamic_templates']);
            unset($value['dynamic_templates']);
        }

        if (array_key_exists('_source', $value)) {
            $this->_usedProperties['_source'] = true;
            $this->_source = new \Symfony\Config\FosElastica\IndexesConfig\SourceConfig($value['_source']);
            unset($value['_source']);
        }

        if (array_key_exists('_routing', $value)) {
            $this->_usedProperties['_routing'] = true;
            $this->_routing = new \Symfony\Config\FosElastica\IndexesConfig\RoutingConfig($value['_routing']);
            unset($value['_routing']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['indexName'])) {
            $output['index_name'] = $this->indexName;
        }
        if (isset($this->_usedProperties['indexableCallback'])) {
            $output['indexable_callback'] = $this->indexableCallback;
        }
        if (isset($this->_usedProperties['useAlias'])) {
            $output['use_alias'] = $this->useAlias;
        }
        if (isset($this->_usedProperties['client'])) {
            $output['client'] = $this->client;
        }
        if (isset($this->_usedProperties['finder'])) {
            $output['finder'] = $this->finder;
        }
        if (isset($this->_usedProperties['persistence'])) {
            $output['persistence'] = $this->persistence->toArray();
        }
        if (isset($this->_usedProperties['serializer'])) {
            $output['serializer'] = $this->serializer->toArray();
        }
        if (isset($this->_usedProperties['indexPrototype'])) {
            $output['index_prototype'] = $this->indexPrototype->toArray();
        }
        if (isset($this->_usedProperties['settings'])) {
            $output['settings'] = $this->settings;
        }
        if (isset($this->_usedProperties['dateDetection'])) {
            $output['date_detection'] = $this->dateDetection;
        }
        if (isset($this->_usedProperties['dynamicDateFormats'])) {
            $output['dynamic_date_formats'] = $this->dynamicDateFormats;
        }
        if (isset($this->_usedProperties['analyzer'])) {
            $output['analyzer'] = $this->analyzer;
        }
        if (isset($this->_usedProperties['numericDetection'])) {
            $output['numeric_detection'] = $this->numericDetection;
        }
        if (isset($this->_usedProperties['dynamic'])) {
            $output['dynamic'] = $this->dynamic;
        }
        if (isset($this->_usedProperties['_id'])) {
            $output['_id'] = $this->_id->toArray();
        }
        if (isset($this->_usedProperties['properties'])) {
            $output['properties'] = $this->properties;
        }
        if (isset($this->_usedProperties['dynamicTemplates'])) {
            $output['dynamic_templates'] = array_map(fn ($v) => $v->toArray(), $this->dynamicTemplates);
        }
        if (isset($this->_usedProperties['_source'])) {
            $output['_source'] = $this->_source->toArray();
        }
        if (isset($this->_usedProperties['_routing'])) {
            $output['_routing'] = $this->_routing->toArray();
        }

        return $output;
    }

}
