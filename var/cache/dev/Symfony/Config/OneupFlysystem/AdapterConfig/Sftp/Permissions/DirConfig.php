<?php

namespace Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\Permissions;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DirConfig 
{
    private $public;
    private $private;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function public($value): static
    {
        $this->_usedProperties['public'] = true;
        $this->public = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function private($value): static
    {
        $this->_usedProperties['private'] = true;
        $this->private = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('public', $value)) {
            $this->_usedProperties['public'] = true;
            $this->public = $value['public'];
            unset($value['public']);
        }

        if (array_key_exists('private', $value)) {
            $this->_usedProperties['private'] = true;
            $this->private = $value['private'];
            unset($value['private']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['public'])) {
            $output['public'] = $this->public;
        }
        if (isset($this->_usedProperties['private'])) {
            $output['private'] = $this->private;
        }

        return $output;
    }

}
