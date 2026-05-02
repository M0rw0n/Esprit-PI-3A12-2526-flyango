<?php

namespace Symfony\Config\OneupFlysystem\AdapterConfig;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Local'.\DIRECTORY_SEPARATOR.'PermissionsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class LocalConfig 
{
    private $lazy;
    private $location;
    private $permissions;
    private $writeFlags;
    private $linkHandling;
    private $mimeTypeDetector;
    private $lazyRootCreation;
    private $_usedProperties = [];

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function lazy($value): static
    {
        $this->_usedProperties['lazy'] = true;
        $this->lazy = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function location($value): static
    {
        $this->_usedProperties['location'] = true;
        $this->location = $value;

        return $this;
    }

    public function permissions(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\Local\PermissionsConfig
    {
        if (null === $this->permissions) {
            $this->_usedProperties['permissions'] = true;
            $this->permissions = new \Symfony\Config\OneupFlysystem\AdapterConfig\Local\PermissionsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "permissions()" has already been initialized. You cannot pass values the second time you call permissions().');
        }

        return $this->permissions;
    }

    /**
     * @default 2
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function writeFlags($value): static
    {
        $this->_usedProperties['writeFlags'] = true;
        $this->writeFlags = $value;

        return $this;
    }

    /**
     * @default 2
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function linkHandling($value): static
    {
        $this->_usedProperties['linkHandling'] = true;
        $this->linkHandling = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function mimeTypeDetector($value): static
    {
        $this->_usedProperties['mimeTypeDetector'] = true;
        $this->mimeTypeDetector = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function lazyRootCreation($value): static
    {
        $this->_usedProperties['lazyRootCreation'] = true;
        $this->lazyRootCreation = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('lazy', $value)) {
            $this->_usedProperties['lazy'] = true;
            $this->lazy = $value['lazy'];
            unset($value['lazy']);
        }

        if (array_key_exists('location', $value)) {
            $this->_usedProperties['location'] = true;
            $this->location = $value['location'];
            unset($value['location']);
        }

        if (array_key_exists('permissions', $value)) {
            $this->_usedProperties['permissions'] = true;
            $this->permissions = new \Symfony\Config\OneupFlysystem\AdapterConfig\Local\PermissionsConfig($value['permissions']);
            unset($value['permissions']);
        }

        if (array_key_exists('writeFlags', $value)) {
            $this->_usedProperties['writeFlags'] = true;
            $this->writeFlags = $value['writeFlags'];
            unset($value['writeFlags']);
        }

        if (array_key_exists('linkHandling', $value)) {
            $this->_usedProperties['linkHandling'] = true;
            $this->linkHandling = $value['linkHandling'];
            unset($value['linkHandling']);
        }

        if (array_key_exists('mimeTypeDetector', $value)) {
            $this->_usedProperties['mimeTypeDetector'] = true;
            $this->mimeTypeDetector = $value['mimeTypeDetector'];
            unset($value['mimeTypeDetector']);
        }

        if (array_key_exists('lazyRootCreation', $value)) {
            $this->_usedProperties['lazyRootCreation'] = true;
            $this->lazyRootCreation = $value['lazyRootCreation'];
            unset($value['lazyRootCreation']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['lazy'])) {
            $output['lazy'] = $this->lazy;
        }
        if (isset($this->_usedProperties['location'])) {
            $output['location'] = $this->location;
        }
        if (isset($this->_usedProperties['permissions'])) {
            $output['permissions'] = $this->permissions->toArray();
        }
        if (isset($this->_usedProperties['writeFlags'])) {
            $output['writeFlags'] = $this->writeFlags;
        }
        if (isset($this->_usedProperties['linkHandling'])) {
            $output['linkHandling'] = $this->linkHandling;
        }
        if (isset($this->_usedProperties['mimeTypeDetector'])) {
            $output['mimeTypeDetector'] = $this->mimeTypeDetector;
        }
        if (isset($this->_usedProperties['lazyRootCreation'])) {
            $output['lazyRootCreation'] = $this->lazyRootCreation;
        }

        return $output;
    }

}
