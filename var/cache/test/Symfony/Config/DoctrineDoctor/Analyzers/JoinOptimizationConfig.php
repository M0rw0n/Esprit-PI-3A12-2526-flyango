<?php

namespace Symfony\Config\DoctrineDoctor\Analyzers;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class JoinOptimizationConfig 
{
    private $enabled;
    private $maxJoinsRecommended;
    private $maxJoinsCritical;
    private $_usedProperties = [];

    /**
     * Detect suboptimal JOIN usage (LEFT JOIN on NOT NULL, too many JOINs, unused JOINs)
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
     * Maximum recommended number of JOINs in a single query
     * @default 5
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function maxJoinsRecommended($value): static
    {
        $this->_usedProperties['maxJoinsRecommended'] = true;
        $this->maxJoinsRecommended = $value;

        return $this;
    }

    /**
     * Number of JOINs to mark as critical
     * @default 8
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function maxJoinsCritical($value): static
    {
        $this->_usedProperties['maxJoinsCritical'] = true;
        $this->maxJoinsCritical = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('max_joins_recommended', $value)) {
            $this->_usedProperties['maxJoinsRecommended'] = true;
            $this->maxJoinsRecommended = $value['max_joins_recommended'];
            unset($value['max_joins_recommended']);
        }

        if (array_key_exists('max_joins_critical', $value)) {
            $this->_usedProperties['maxJoinsCritical'] = true;
            $this->maxJoinsCritical = $value['max_joins_critical'];
            unset($value['max_joins_critical']);
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
        if (isset($this->_usedProperties['maxJoinsRecommended'])) {
            $output['max_joins_recommended'] = $this->maxJoinsRecommended;
        }
        if (isset($this->_usedProperties['maxJoinsCritical'])) {
            $output['max_joins_critical'] = $this->maxJoinsCritical;
        }

        return $output;
    }

}
