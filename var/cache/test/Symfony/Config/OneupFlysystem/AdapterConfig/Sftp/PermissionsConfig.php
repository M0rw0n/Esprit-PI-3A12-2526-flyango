<?php

namespace Symfony\Config\OneupFlysystem\AdapterConfig\Sftp;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Permissions'.\DIRECTORY_SEPARATOR.'FileConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Permissions'.\DIRECTORY_SEPARATOR.'DirConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PermissionsConfig 
{
    private $file;
    private $dir;
    private $_usedProperties = [];

    public function file(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\Permissions\FileConfig
    {
        if (null === $this->file) {
            $this->_usedProperties['file'] = true;
            $this->file = new \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\Permissions\FileConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "file()" has already been initialized. You cannot pass values the second time you call file().');
        }

        return $this->file;
    }

    public function dir(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\Permissions\DirConfig
    {
        if (null === $this->dir) {
            $this->_usedProperties['dir'] = true;
            $this->dir = new \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\Permissions\DirConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "dir()" has already been initialized. You cannot pass values the second time you call dir().');
        }

        return $this->dir;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('file', $value)) {
            $this->_usedProperties['file'] = true;
            $this->file = new \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\Permissions\FileConfig($value['file']);
            unset($value['file']);
        }

        if (array_key_exists('dir', $value)) {
            $this->_usedProperties['dir'] = true;
            $this->dir = new \Symfony\Config\OneupFlysystem\AdapterConfig\Sftp\Permissions\DirConfig($value['dir']);
            unset($value['dir']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['file'])) {
            $output['file'] = $this->file->toArray();
        }
        if (isset($this->_usedProperties['dir'])) {
            $output['dir'] = $this->dir->toArray();
        }

        return $output;
    }

}
