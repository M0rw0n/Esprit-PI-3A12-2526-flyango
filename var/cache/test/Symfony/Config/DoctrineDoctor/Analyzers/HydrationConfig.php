<?php

namespace Symfony\Config\DoctrineDoctor\Analyzers;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class HydrationConfig 
{
    private $enabled;
    private $rowThreshold;
    private $criticalThreshold;
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
     * Number of rows to consider for hydration analysis
     * @default 99
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function rowThreshold($value): static
    {
        $this->_usedProperties['rowThreshold'] = true;
        $this->rowThreshold = $value;

        return $this;
    }

    /**
     * Number of rows to mark as critical
     * @default 999
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function criticalThreshold($value): static
    {
        $this->_usedProperties['criticalThreshold'] = true;
        $this->criticalThreshold = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('row_threshold', $value)) {
            $this->_usedProperties['rowThreshold'] = true;
            $this->rowThreshold = $value['row_threshold'];
            unset($value['row_threshold']);
        }

        if (array_key_exists('critical_threshold', $value)) {
            $this->_usedProperties['criticalThreshold'] = true;
            $this->criticalThreshold = $value['critical_threshold'];
            unset($value['critical_threshold']);
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
        if (isset($this->_usedProperties['rowThreshold'])) {
            $output['row_threshold'] = $this->rowThreshold;
        }
        if (isset($this->_usedProperties['criticalThreshold'])) {
            $output['critical_threshold'] = $this->criticalThreshold;
        }

        return $output;
    }

}
