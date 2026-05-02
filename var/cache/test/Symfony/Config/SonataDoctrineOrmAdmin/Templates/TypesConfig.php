<?php

namespace Symfony\Config\SonataDoctrineOrmAdmin\Templates;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class TypesConfig 
{
    private $list;
    private $show;
    private $_usedProperties = [];

    /**
     * @return $this
     */
    public function list(string $name, mixed $value): static
    {
        $this->_usedProperties['list'] = true;
        $this->list[$name] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function show(string $name, mixed $value): static
    {
        $this->_usedProperties['show'] = true;
        $this->show[$name] = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('list', $value)) {
            $this->_usedProperties['list'] = true;
            $this->list = $value['list'];
            unset($value['list']);
        }

        if (array_key_exists('show', $value)) {
            $this->_usedProperties['show'] = true;
            $this->show = $value['show'];
            unset($value['show']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['list'])) {
            $output['list'] = $this->list;
        }
        if (isset($this->_usedProperties['show'])) {
            $output['show'] = $this->show;
        }

        return $output;
    }

}
