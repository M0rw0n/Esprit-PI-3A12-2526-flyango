<?php

namespace Symfony\Config\OneupFlysystem\AdapterConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class GitlabConfig 
{
    private $client;
    private $prefix;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function client($value): static
    {
        $this->_usedProperties['client'] = true;
        $this->client = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function prefix($value): static
    {
        $this->_usedProperties['prefix'] = true;
        $this->prefix = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('client', $value)) {
            $this->_usedProperties['client'] = true;
            $this->client = $value['client'];
            unset($value['client']);
        }

        if (array_key_exists('prefix', $value)) {
            $this->_usedProperties['prefix'] = true;
            $this->prefix = $value['prefix'];
            unset($value['prefix']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['client'])) {
            $output['client'] = $this->client;
        }
        if (isset($this->_usedProperties['prefix'])) {
            $output['prefix'] = $this->prefix;
        }

        return $output;
    }

}
