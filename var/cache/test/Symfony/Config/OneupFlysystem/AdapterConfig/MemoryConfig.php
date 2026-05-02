<?php

namespace Symfony\Config\OneupFlysystem\AdapterConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class MemoryConfig 
{
    private $defaultVisibility;
    private $_usedProperties = [];

    /**
     * @default 'public'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultVisibility($value): static
    {
        $this->_usedProperties['defaultVisibility'] = true;
        $this->defaultVisibility = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('defaultVisibility', $value)) {
            $this->_usedProperties['defaultVisibility'] = true;
            $this->defaultVisibility = $value['defaultVisibility'];
            unset($value['defaultVisibility']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['defaultVisibility'])) {
            $output['defaultVisibility'] = $this->defaultVisibility;
        }

        return $output;
    }

}
