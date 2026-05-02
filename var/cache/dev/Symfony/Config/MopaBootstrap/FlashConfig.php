<?php

namespace Symfony\Config\MopaBootstrap;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Flash'.\DIRECTORY_SEPARATOR.'MappingConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class FlashConfig 
{
    private $mapping;
    private $_usedProperties = [];

    /**
     * @default {"success":["success"],"danger":["error","danger"],"warning":["warning","warn"],"info":["info","notice"]}
    */
    public function mapping(array $value = []): \Symfony\Config\MopaBootstrap\Flash\MappingConfig
    {
        if (null === $this->mapping) {
            $this->_usedProperties['mapping'] = true;
            $this->mapping = new \Symfony\Config\MopaBootstrap\Flash\MappingConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "mapping()" has already been initialized. You cannot pass values the second time you call mapping().');
        }

        return $this->mapping;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('mapping', $value)) {
            $this->_usedProperties['mapping'] = true;
            $this->mapping = new \Symfony\Config\MopaBootstrap\Flash\MappingConfig($value['mapping']);
            unset($value['mapping']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['mapping'])) {
            $output['mapping'] = $this->mapping->toArray();
        }

        return $output;
    }

}
