<?php

namespace Symfony\Config\SonataAdmin\Dashboard\GroupConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ItemConfig 
{
    private $admin;
    private $label;
    private $route;
    private $roles;
    private $routeParams;
    private $routeAbsolute;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function admin($value): static
    {
        $this->_usedProperties['admin'] = true;
        $this->admin = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function label($value): static
    {
        $this->_usedProperties['label'] = true;
        $this->label = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function route($value): static
    {
        $this->_usedProperties['route'] = true;
        $this->route = $value;

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
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function routeParams(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['routeParams'] = true;
        $this->routeParams = $value;

        return $this;
    }

    /**
     * Whether the generated url should be absolute
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function routeAbsolute($value): static
    {
        $this->_usedProperties['routeAbsolute'] = true;
        $this->routeAbsolute = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('admin', $value)) {
            $this->_usedProperties['admin'] = true;
            $this->admin = $value['admin'];
            unset($value['admin']);
        }

        if (array_key_exists('label', $value)) {
            $this->_usedProperties['label'] = true;
            $this->label = $value['label'];
            unset($value['label']);
        }

        if (array_key_exists('route', $value)) {
            $this->_usedProperties['route'] = true;
            $this->route = $value['route'];
            unset($value['route']);
        }

        if (array_key_exists('roles', $value)) {
            $this->_usedProperties['roles'] = true;
            $this->roles = $value['roles'];
            unset($value['roles']);
        }

        if (array_key_exists('route_params', $value)) {
            $this->_usedProperties['routeParams'] = true;
            $this->routeParams = $value['route_params'];
            unset($value['route_params']);
        }

        if (array_key_exists('route_absolute', $value)) {
            $this->_usedProperties['routeAbsolute'] = true;
            $this->routeAbsolute = $value['route_absolute'];
            unset($value['route_absolute']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['admin'])) {
            $output['admin'] = $this->admin;
        }
        if (isset($this->_usedProperties['label'])) {
            $output['label'] = $this->label;
        }
        if (isset($this->_usedProperties['route'])) {
            $output['route'] = $this->route;
        }
        if (isset($this->_usedProperties['roles'])) {
            $output['roles'] = $this->roles;
        }
        if (isset($this->_usedProperties['routeParams'])) {
            $output['route_params'] = $this->routeParams;
        }
        if (isset($this->_usedProperties['routeAbsolute'])) {
            $output['route_absolute'] = $this->routeAbsolute;
        }

        return $output;
    }

}
