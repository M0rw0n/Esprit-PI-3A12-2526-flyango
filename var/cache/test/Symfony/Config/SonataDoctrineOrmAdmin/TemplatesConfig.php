<?php

namespace Symfony\Config\SonataDoctrineOrmAdmin;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Templates'.\DIRECTORY_SEPARATOR.'TypesConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class TemplatesConfig 
{
    private $types;
    private $_usedProperties = [];

    public function types(array $value = []): \Symfony\Config\SonataDoctrineOrmAdmin\Templates\TypesConfig
    {
        if (null === $this->types) {
            $this->_usedProperties['types'] = true;
            $this->types = new \Symfony\Config\SonataDoctrineOrmAdmin\Templates\TypesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "types()" has already been initialized. You cannot pass values the second time you call types().');
        }

        return $this->types;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('types', $value)) {
            $this->_usedProperties['types'] = true;
            $this->types = new \Symfony\Config\SonataDoctrineOrmAdmin\Templates\TypesConfig($value['types']);
            unset($value['types']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['types'])) {
            $output['types'] = $this->types->toArray();
        }

        return $output;
    }

}
