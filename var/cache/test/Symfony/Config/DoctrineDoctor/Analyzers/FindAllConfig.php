<?php

namespace Symfony\Config\DoctrineDoctor\Analyzers;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class FindAllConfig 
{
    private $enabled;
    private $threshold;
    private $_usedProperties = [];

    /**
     * Enable findAll() detection
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
     * Maximum number of rows before flagging as issue
     * @default 99
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function threshold($value): static
    {
        $this->_usedProperties['threshold'] = true;
        $this->threshold = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('threshold', $value)) {
            $this->_usedProperties['threshold'] = true;
            $this->threshold = $value['threshold'];
            unset($value['threshold']);
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
        if (isset($this->_usedProperties['threshold'])) {
            $output['threshold'] = $this->threshold;
        }

        return $output;
    }

}
