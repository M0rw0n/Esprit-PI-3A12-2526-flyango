<?php

namespace Symfony\Config\SonataExporter;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ExporterConfig 
{
    private $defaultWriters;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function defaultWriters(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['defaultWriters'] = true;
        $this->defaultWriters = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('default_writers', $value)) {
            $this->_usedProperties['defaultWriters'] = true;
            $this->defaultWriters = $value['default_writers'];
            unset($value['default_writers']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['defaultWriters'])) {
            $output['default_writers'] = $this->defaultWriters;
        }

        return $output;
    }

}
