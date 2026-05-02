<?php

namespace Symfony\Config\DoctrineDoctor\Analyzers;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class FlushInLoopConfig 
{
    private $enabled;
    private $flushCountThreshold;
    private $timeWindowMs;
    private $_usedProperties = [];

    /**
     * Enable flush() in loop detection (anti-pattern)
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
     * Minimum number of flush calls to trigger detection
     * @default 5
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function flushCountThreshold($value): static
    {
        $this->_usedProperties['flushCountThreshold'] = true;
        $this->flushCountThreshold = $value;

        return $this;
    }

    /**
     * Time window in milliseconds to consider flushes as being in a loop
     * @default 1000
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function timeWindowMs($value): static
    {
        $this->_usedProperties['timeWindowMs'] = true;
        $this->timeWindowMs = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('flush_count_threshold', $value)) {
            $this->_usedProperties['flushCountThreshold'] = true;
            $this->flushCountThreshold = $value['flush_count_threshold'];
            unset($value['flush_count_threshold']);
        }

        if (array_key_exists('time_window_ms', $value)) {
            $this->_usedProperties['timeWindowMs'] = true;
            $this->timeWindowMs = $value['time_window_ms'];
            unset($value['time_window_ms']);
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
        if (isset($this->_usedProperties['flushCountThreshold'])) {
            $output['flush_count_threshold'] = $this->flushCountThreshold;
        }
        if (isset($this->_usedProperties['timeWindowMs'])) {
            $output['time_window_ms'] = $this->timeWindowMs;
        }

        return $output;
    }

}
