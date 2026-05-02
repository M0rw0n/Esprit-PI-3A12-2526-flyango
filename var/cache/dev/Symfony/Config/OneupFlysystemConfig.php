<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'OneupFlysystem'.\DIRECTORY_SEPARATOR.'AdapterConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'OneupFlysystem'.\DIRECTORY_SEPARATOR.'FilesystemConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class OneupFlysystemConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $adapters;
    private $filesystems;
    private $_usedProperties = [];

    public function adapter(string $name, array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig
    {
        if (!isset($this->adapters[$name])) {
            $this->_usedProperties['adapters'] = true;
            $this->adapters[$name] = new \Symfony\Config\OneupFlysystem\AdapterConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "adapter()" has already been initialized. You cannot pass values the second time you call adapter().');
        }

        return $this->adapters[$name];
    }

    public function filesystem(string $name, array $value = []): \Symfony\Config\OneupFlysystem\FilesystemConfig
    {
        if (!isset($this->filesystems[$name])) {
            $this->_usedProperties['filesystems'] = true;
            $this->filesystems[$name] = new \Symfony\Config\OneupFlysystem\FilesystemConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "filesystem()" has already been initialized. You cannot pass values the second time you call filesystem().');
        }

        return $this->filesystems[$name];
    }

    public function getExtensionAlias(): string
    {
        return 'oneup_flysystem';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('adapters', $value)) {
            $this->_usedProperties['adapters'] = true;
            $this->adapters = array_map(fn ($v) => new \Symfony\Config\OneupFlysystem\AdapterConfig($v), $value['adapters']);
            unset($value['adapters']);
        }

        if (array_key_exists('filesystems', $value)) {
            $this->_usedProperties['filesystems'] = true;
            $this->filesystems = array_map(fn ($v) => new \Symfony\Config\OneupFlysystem\FilesystemConfig($v), $value['filesystems']);
            unset($value['filesystems']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['adapters'])) {
            $output['adapters'] = array_map(fn ($v) => $v->toArray(), $this->adapters);
        }
        if (isset($this->_usedProperties['filesystems'])) {
            $output['filesystems'] = array_map(fn ($v) => $v->toArray(), $this->filesystems);
        }

        return $output;
    }

}
