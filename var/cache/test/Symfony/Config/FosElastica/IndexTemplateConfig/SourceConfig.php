<?php

namespace Symfony\Config\FosElastica\IndexTemplateConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SourceConfig 
{
    private $excludes;
    private $includes;
    private $compress;
    private $compressThreshold;
    private $enabled;
    private $_usedProperties = [];

    /**
     * @return $this
     */
    public function excludes(string $name, mixed $value): static
    {
        $this->_usedProperties['excludes'] = true;
        $this->excludes[$name] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function includes(string $name, mixed $value): static
    {
        $this->_usedProperties['includes'] = true;
        $this->includes[$name] = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function compress($value): static
    {
        $this->_usedProperties['compress'] = true;
        $this->compress = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function compressThreshold($value): static
    {
        $this->_usedProperties['compressThreshold'] = true;
        $this->compressThreshold = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function enabled($value): static
    {
        $this->_usedProperties['enabled'] = true;
        $this->enabled = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('excludes', $value)) {
            $this->_usedProperties['excludes'] = true;
            $this->excludes = $value['excludes'];
            unset($value['excludes']);
        }

        if (array_key_exists('includes', $value)) {
            $this->_usedProperties['includes'] = true;
            $this->includes = $value['includes'];
            unset($value['includes']);
        }

        if (array_key_exists('compress', $value)) {
            $this->_usedProperties['compress'] = true;
            $this->compress = $value['compress'];
            unset($value['compress']);
        }

        if (array_key_exists('compress_threshold', $value)) {
            $this->_usedProperties['compressThreshold'] = true;
            $this->compressThreshold = $value['compress_threshold'];
            unset($value['compress_threshold']);
        }

        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['excludes'])) {
            $output['excludes'] = $this->excludes;
        }
        if (isset($this->_usedProperties['includes'])) {
            $output['includes'] = $this->includes;
        }
        if (isset($this->_usedProperties['compress'])) {
            $output['compress'] = $this->compress;
        }
        if (isset($this->_usedProperties['compressThreshold'])) {
            $output['compress_threshold'] = $this->compressThreshold;
        }
        if (isset($this->_usedProperties['enabled'])) {
            $output['enabled'] = $this->enabled;
        }

        return $output;
    }

}
