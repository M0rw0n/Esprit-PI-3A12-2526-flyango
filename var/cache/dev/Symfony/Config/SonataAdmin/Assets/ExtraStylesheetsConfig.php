<?php

namespace Symfony\Config\SonataAdmin\Assets;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ExtraStylesheetsConfig 
{
    private $path;
    private $packageName;
    private $_usedProperties = [];

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

    /**
     * @default 'sonata_admin'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function packageName($value): static
    {
        $this->_usedProperties['packageName'] = true;
        $this->packageName = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('path', $value)) {
            $this->_usedProperties['path'] = true;
            $this->path = $value['path'];
            unset($value['path']);
        }

        if (array_key_exists('package_name', $value)) {
            $this->_usedProperties['packageName'] = true;
            $this->packageName = $value['package_name'];
            unset($value['package_name']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['path'])) {
            $output['path'] = $this->path;
        }
        if (isset($this->_usedProperties['packageName'])) {
            $output['package_name'] = $this->packageName;
        }

        return $output;
    }

}
