<?php

namespace Symfony\Config\DoctrineDoctor\Analyzers;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class EagerLoadingConfig 
{
    private $enabled;
    private $joinThreshold;
    private $criticalJoinThreshold;
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
     * Maximum number of JOINs before warning
     * @default 4
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function joinThreshold($value): static
    {
        $this->_usedProperties['joinThreshold'] = true;
        $this->joinThreshold = $value;

        return $this;
    }

    /**
     * Number of JOINs to mark as critical
     * @default 7
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function criticalJoinThreshold($value): static
    {
        $this->_usedProperties['criticalJoinThreshold'] = true;
        $this->criticalJoinThreshold = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('join_threshold', $value)) {
            $this->_usedProperties['joinThreshold'] = true;
            $this->joinThreshold = $value['join_threshold'];
            unset($value['join_threshold']);
        }

        if (array_key_exists('critical_join_threshold', $value)) {
            $this->_usedProperties['criticalJoinThreshold'] = true;
            $this->criticalJoinThreshold = $value['critical_join_threshold'];
            unset($value['critical_join_threshold']);
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
        if (isset($this->_usedProperties['joinThreshold'])) {
            $output['join_threshold'] = $this->joinThreshold;
        }
        if (isset($this->_usedProperties['criticalJoinThreshold'])) {
            $output['critical_join_threshold'] = $this->criticalJoinThreshold;
        }

        return $output;
    }

}
