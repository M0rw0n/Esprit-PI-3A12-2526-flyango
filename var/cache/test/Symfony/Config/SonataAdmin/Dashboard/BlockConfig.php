<?php

namespace Symfony\Config\SonataAdmin\Dashboard;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class BlockConfig 
{
    private $type;
    private $roles;
    private $settings;
    private $position;
    private $class;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function type($value): static
    {
        $this->_usedProperties['type'] = true;
        $this->type = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function roles(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['roles'] = true;
        $this->roles = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function setting(string $id, mixed $value): static
    {
        $this->_usedProperties['settings'] = true;
        $this->settings[$id] = $value;

        return $this;
    }

    /**
     * @default 'right'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function position($value): static
    {
        $this->_usedProperties['position'] = true;
        $this->position = $value;

        return $this;
    }

    /**
     * @default 'col-md-4'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function class($value): static
    {
        $this->_usedProperties['class'] = true;
        $this->class = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('type', $value)) {
            $this->_usedProperties['type'] = true;
            $this->type = $value['type'];
            unset($value['type']);
        }

        if (array_key_exists('roles', $value)) {
            $this->_usedProperties['roles'] = true;
            $this->roles = $value['roles'];
            unset($value['roles']);
        }

        if (array_key_exists('settings', $value)) {
            $this->_usedProperties['settings'] = true;
            $this->settings = $value['settings'];
            unset($value['settings']);
        }

        if (array_key_exists('position', $value)) {
            $this->_usedProperties['position'] = true;
            $this->position = $value['position'];
            unset($value['position']);
        }

        if (array_key_exists('class', $value)) {
            $this->_usedProperties['class'] = true;
            $this->class = $value['class'];
            unset($value['class']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['type'])) {
            $output['type'] = $this->type;
        }
        if (isset($this->_usedProperties['roles'])) {
            $output['roles'] = $this->roles;
        }
        if (isset($this->_usedProperties['settings'])) {
            $output['settings'] = $this->settings;
        }
        if (isset($this->_usedProperties['position'])) {
            $output['position'] = $this->position;
        }
        if (isset($this->_usedProperties['class'])) {
            $output['class'] = $this->class;
        }

        return $output;
    }

}
