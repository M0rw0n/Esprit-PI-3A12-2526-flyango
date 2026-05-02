<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'FosElastica'.\DIRECTORY_SEPARATOR.'ClientConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'FosElastica'.\DIRECTORY_SEPARATOR.'IndexesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'FosElastica'.\DIRECTORY_SEPARATOR.'IndexTemplateConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'FosElastica'.\DIRECTORY_SEPARATOR.'MessengerConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'FosElastica'.\DIRECTORY_SEPARATOR.'SerializerConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class FosElasticaConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $clients;
    private $indexes;
    private $indexTemplates;
    private $defaultClient;
    private $defaultIndex;
    private $defaultManager;
    private $messenger;
    private $serializer;
    private $_usedProperties = [];

    public function client(string $id, array $value = []): \Symfony\Config\FosElastica\ClientConfig
    {
        if (!isset($this->clients[$id])) {
            $this->_usedProperties['clients'] = true;
            $this->clients[$id] = new \Symfony\Config\FosElastica\ClientConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "client()" has already been initialized. You cannot pass values the second time you call client().');
        }

        return $this->clients[$id];
    }

    public function indexes(string $name, array $value = []): \Symfony\Config\FosElastica\IndexesConfig
    {
        if (!isset($this->indexes[$name])) {
            $this->_usedProperties['indexes'] = true;
            $this->indexes[$name] = new \Symfony\Config\FosElastica\IndexesConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "indexes()" has already been initialized. You cannot pass values the second time you call indexes().');
        }

        return $this->indexes[$name];
    }

    /**
     * @template TValue of mixed
     * @param TValue $value
     * @return \Symfony\Config\FosElastica\IndexTemplateConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\FosElastica\IndexTemplateConfig : static)
     */
    public function indexTemplate(string $name, mixed $value = []): \Symfony\Config\FosElastica\IndexTemplateConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['indexTemplates'] = true;
            $this->indexTemplates[$name] = $value;

            return $this;
        }

        if (!isset($this->indexTemplates[$name]) || !$this->indexTemplates[$name] instanceof \Symfony\Config\FosElastica\IndexTemplateConfig) {
            $this->_usedProperties['indexTemplates'] = true;
            $this->indexTemplates[$name] = new \Symfony\Config\FosElastica\IndexTemplateConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "indexTemplate()" has already been initialized. You cannot pass values the second time you call indexTemplate().');
        }

        return $this->indexTemplates[$name];
    }

    /**
     * Defaults to the first client defined
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultClient($value): static
    {
        $this->_usedProperties['defaultClient'] = true;
        $this->defaultClient = $value;

        return $this;
    }

    /**
     * Defaults to the first index defined
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultIndex($value): static
    {
        $this->_usedProperties['defaultIndex'] = true;
        $this->defaultIndex = $value;

        return $this;
    }

    /**
     * @default 'orm'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultManager($value): static
    {
        $this->_usedProperties['defaultManager'] = true;
        $this->defaultManager = $value;

        return $this;
    }

    /**
     * @default {"enabled":false,"bus":"messenger.default_bus"}
    */
    public function messenger(array $value = []): \Symfony\Config\FosElastica\MessengerConfig
    {
        if (null === $this->messenger) {
            $this->_usedProperties['messenger'] = true;
            $this->messenger = new \Symfony\Config\FosElastica\MessengerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "messenger()" has already been initialized. You cannot pass values the second time you call messenger().');
        }

        return $this->messenger;
    }

    public function serializer(array $value = []): \Symfony\Config\FosElastica\SerializerConfig
    {
        if (null === $this->serializer) {
            $this->_usedProperties['serializer'] = true;
            $this->serializer = new \Symfony\Config\FosElastica\SerializerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "serializer()" has already been initialized. You cannot pass values the second time you call serializer().');
        }

        return $this->serializer;
    }

    public function getExtensionAlias(): string
    {
        return 'fos_elastica';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('clients', $value)) {
            $this->_usedProperties['clients'] = true;
            $this->clients = array_map(fn ($v) => new \Symfony\Config\FosElastica\ClientConfig($v), $value['clients']);
            unset($value['clients']);
        }

        if (array_key_exists('indexes', $value)) {
            $this->_usedProperties['indexes'] = true;
            $this->indexes = array_map(fn ($v) => new \Symfony\Config\FosElastica\IndexesConfig($v), $value['indexes']);
            unset($value['indexes']);
        }

        if (array_key_exists('index_templates', $value)) {
            $this->_usedProperties['indexTemplates'] = true;
            $this->indexTemplates = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\FosElastica\IndexTemplateConfig($v) : $v, $value['index_templates']);
            unset($value['index_templates']);
        }

        if (array_key_exists('default_client', $value)) {
            $this->_usedProperties['defaultClient'] = true;
            $this->defaultClient = $value['default_client'];
            unset($value['default_client']);
        }

        if (array_key_exists('default_index', $value)) {
            $this->_usedProperties['defaultIndex'] = true;
            $this->defaultIndex = $value['default_index'];
            unset($value['default_index']);
        }

        if (array_key_exists('default_manager', $value)) {
            $this->_usedProperties['defaultManager'] = true;
            $this->defaultManager = $value['default_manager'];
            unset($value['default_manager']);
        }

        if (array_key_exists('messenger', $value)) {
            $this->_usedProperties['messenger'] = true;
            $this->messenger = \is_array($value['messenger']) ? new \Symfony\Config\FosElastica\MessengerConfig($value['messenger']) : $value['messenger'];
            unset($value['messenger']);
        }

        if (array_key_exists('serializer', $value)) {
            $this->_usedProperties['serializer'] = true;
            $this->serializer = new \Symfony\Config\FosElastica\SerializerConfig($value['serializer']);
            unset($value['serializer']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['clients'])) {
            $output['clients'] = array_map(fn ($v) => $v->toArray(), $this->clients);
        }
        if (isset($this->_usedProperties['indexes'])) {
            $output['indexes'] = array_map(fn ($v) => $v->toArray(), $this->indexes);
        }
        if (isset($this->_usedProperties['indexTemplates'])) {
            $output['index_templates'] = array_map(fn ($v) => $v instanceof \Symfony\Config\FosElastica\IndexTemplateConfig ? $v->toArray() : $v, $this->indexTemplates);
        }
        if (isset($this->_usedProperties['defaultClient'])) {
            $output['default_client'] = $this->defaultClient;
        }
        if (isset($this->_usedProperties['defaultIndex'])) {
            $output['default_index'] = $this->defaultIndex;
        }
        if (isset($this->_usedProperties['defaultManager'])) {
            $output['default_manager'] = $this->defaultManager;
        }
        if (isset($this->_usedProperties['messenger'])) {
            $output['messenger'] = $this->messenger instanceof \Symfony\Config\FosElastica\MessengerConfig ? $this->messenger->toArray() : $this->messenger;
        }
        if (isset($this->_usedProperties['serializer'])) {
            $output['serializer'] = $this->serializer->toArray();
        }

        return $output;
    }

}
