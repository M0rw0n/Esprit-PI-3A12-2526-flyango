<?php

namespace Symfony\Config\FosElastica\IndexesConfig\Persistence;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PersisterConfig 
{
    private $refresh;
    private $service;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|'true'|'wait_for'|'false' $value
     * @return $this
     */
    public function refresh($value): static
    {
        $this->_usedProperties['refresh'] = true;
        $this->refresh = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function service($value): static
    {
        $this->_usedProperties['service'] = true;
        $this->service = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('refresh', $value)) {
            $this->_usedProperties['refresh'] = true;
            $this->refresh = $value['refresh'];
            unset($value['refresh']);
        }

        if (array_key_exists('service', $value)) {
            $this->_usedProperties['service'] = true;
            $this->service = $value['service'];
            unset($value['service']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['refresh'])) {
            $output['refresh'] = $this->refresh;
        }
        if (isset($this->_usedProperties['service'])) {
            $output['service'] = $this->service;
        }

        return $output;
    }

}
