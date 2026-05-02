<?php

namespace Symfony\Config\FosElastica\IndexesConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class RoutingConfig 
{
    private $required;
    private $path;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function required($value): static
    {
        $this->_usedProperties['required'] = true;
        $this->required = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function path($value): static
    {
        $this->_usedProperties['path'] = true;
        $this->path = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('required', $value)) {
            $this->_usedProperties['required'] = true;
            $this->required = $value['required'];
            unset($value['required']);
        }

        if (array_key_exists('path', $value)) {
            $this->_usedProperties['path'] = true;
            $this->path = $value['path'];
            unset($value['path']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['required'])) {
            $output['required'] = $this->required;
        }
        if (isset($this->_usedProperties['path'])) {
            $output['path'] = $this->path;
        }

        return $output;
    }

}
