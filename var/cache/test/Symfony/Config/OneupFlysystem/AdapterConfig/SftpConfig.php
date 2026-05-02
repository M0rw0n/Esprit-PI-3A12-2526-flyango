<?php

namespace Symfony\Config\OneupFlysystem\AdapterConfig;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Sftp'.\DIRECTORY_SEPARATOR.'OptionsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Sftp'.\DIRECTORY_SEPARATOR.'PermissionsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class SftpConfig 
{
    private $options;
    private $permissions;
    private $mimeTypeDetector;
    private $_usedProperties = [];

    public function options(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\OptionsConfig
    {
        if (null === $this->options) {
            $this->_usedProperties['options'] = true;
            $this->options = new \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\OptionsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "options()" has already been initialized. You cannot pass values the second time you call options().');
        }

        return $this->options;
    }

    public function permissions(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\PermissionsConfig
    {
        if (null === $this->permissions) {
            $this->_usedProperties['permissions'] = true;
            $this->permissions = new \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\PermissionsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "permissions()" has already been initialized. You cannot pass values the second time you call permissions().');
        }

        return $this->permissions;
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

    public function __construct(array $value = [])
    {
        if (array_key_exists('options', $value)) {
            $this->_usedProperties['options'] = true;
            $this->options = new \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\OptionsConfig($value['options']);
            unset($value['options']);
        }

        if (array_key_exists('permissions', $value)) {
            $this->_usedProperties['permissions'] = true;
            $this->permissions = new \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\PermissionsConfig($value['permissions']);
            unset($value['permissions']);
        }

        if (array_key_exists('mimeTypeDetector', $value)) {
            $this->_usedProperties['mimeTypeDetector'] = true;
            $this->mimeTypeDetector = $value['mimeTypeDetector'];
            unset($value['mimeTypeDetector']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['options'])) {
            $output['options'] = $this->options->toArray();
        }
        if (isset($this->_usedProperties['permissions'])) {
            $output['permissions'] = $this->permissions->toArray();
        }
        if (isset($this->_usedProperties['mimeTypeDetector'])) {
            $output['mimeTypeDetector'] = $this->mimeTypeDetector;
        }

        return $output;
    }

}
