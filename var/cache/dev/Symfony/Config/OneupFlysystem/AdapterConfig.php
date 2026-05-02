<?php

namespace Symfony\Config\OneupFlysystem;

require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'LocalConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'Awss3v3Config.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'FtpConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'SftpConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'MemoryConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'CustomConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'AsyncAwsS3Config.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'GooglecloudstorageConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'GitlabConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'AzureblobConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AdapterConfig 
{
    private $local;
    private $awss3v3;
    private $ftp;
    private $sftp;
    private $memory;
    private $custom;
    private $asyncAwsS3;
    private $googlecloudstorage;
    private $gitlab;
    private $azureblob;
    private $_usedProperties = [];

    public function local(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\LocalConfig
    {
        if (null === $this->local) {
            $this->_usedProperties['local'] = true;
            $this->local = new \Symfony\Config\OneupFlysystem\AdapterConfig\LocalConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "local()" has already been initialized. You cannot pass values the second time you call local().');
        }

        return $this->local;
    }

    public function awss3v3(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\Awss3v3Config
    {
        if (null === $this->awss3v3) {
            $this->_usedProperties['awss3v3'] = true;
            $this->awss3v3 = new \Symfony\Config\OneupFlysystem\AdapterConfig\Awss3v3Config($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "awss3v3()" has already been initialized. You cannot pass values the second time you call awss3v3().');
        }

        return $this->awss3v3;
    }

    public function ftp(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\FtpConfig
    {
        if (null === $this->ftp) {
            $this->_usedProperties['ftp'] = true;
            $this->ftp = new \Symfony\Config\OneupFlysystem\AdapterConfig\FtpConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "ftp()" has already been initialized. You cannot pass values the second time you call ftp().');
        }

        return $this->ftp;
    }

    public function sftp(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\SftpConfig
    {
        if (null === $this->sftp) {
            $this->_usedProperties['sftp'] = true;
            $this->sftp = new \Symfony\Config\OneupFlysystem\AdapterConfig\SftpConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "sftp()" has already been initialized. You cannot pass values the second time you call sftp().');
        }

        return $this->sftp;
    }

    public function memory(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\MemoryConfig
    {
        if (null === $this->memory) {
            $this->_usedProperties['memory'] = true;
            $this->memory = new \Symfony\Config\OneupFlysystem\AdapterConfig\MemoryConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "memory()" has already been initialized. You cannot pass values the second time you call memory().');
        }

        return $this->memory;
    }

    public function custom(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\CustomConfig
    {
        if (null === $this->custom) {
            $this->_usedProperties['custom'] = true;
            $this->custom = new \Symfony\Config\OneupFlysystem\AdapterConfig\CustomConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "custom()" has already been initialized. You cannot pass values the second time you call custom().');
        }

        return $this->custom;
    }

    public function asyncAwsS3(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\AsyncAwsS3Config
    {
        if (null === $this->asyncAwsS3) {
            $this->_usedProperties['asyncAwsS3'] = true;
            $this->asyncAwsS3 = new \Symfony\Config\OneupFlysystem\AdapterConfig\AsyncAwsS3Config($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "asyncAwsS3()" has already been initialized. You cannot pass values the second time you call asyncAwsS3().');
        }

        return $this->asyncAwsS3;
    }

    public function googlecloudstorage(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\GooglecloudstorageConfig
    {
        if (null === $this->googlecloudstorage) {
            $this->_usedProperties['googlecloudstorage'] = true;
            $this->googlecloudstorage = new \Symfony\Config\OneupFlysystem\AdapterConfig\GooglecloudstorageConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "googlecloudstorage()" has already been initialized. You cannot pass values the second time you call googlecloudstorage().');
        }

        return $this->googlecloudstorage;
    }

    public function gitlab(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\GitlabConfig
    {
        if (null === $this->gitlab) {
            $this->_usedProperties['gitlab'] = true;
            $this->gitlab = new \Symfony\Config\OneupFlysystem\AdapterConfig\GitlabConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "gitlab()" has already been initialized. You cannot pass values the second time you call gitlab().');
        }

        return $this->gitlab;
    }

    public function azureblob(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\AzureblobConfig
    {
        if (null === $this->azureblob) {
            $this->_usedProperties['azureblob'] = true;
            $this->azureblob = new \Symfony\Config\OneupFlysystem\AdapterConfig\AzureblobConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "azureblob()" has already been initialized. You cannot pass values the second time you call azureblob().');
        }

        return $this->azureblob;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('local', $value)) {
            $this->_usedProperties['local'] = true;
            $this->local = new \Symfony\Config\OneupFlysystem\AdapterConfig\LocalConfig($value['local']);
            unset($value['local']);
        }

        if (array_key_exists('awss3v3', $value)) {
            $this->_usedProperties['awss3v3'] = true;
            $this->awss3v3 = new \Symfony\Config\OneupFlysystem\AdapterConfig\Awss3v3Config($value['awss3v3']);
            unset($value['awss3v3']);
        }

        if (array_key_exists('ftp', $value)) {
            $this->_usedProperties['ftp'] = true;
            $this->ftp = new \Symfony\Config\OneupFlysystem\AdapterConfig\FtpConfig($value['ftp']);
            unset($value['ftp']);
        }

        if (array_key_exists('sftp', $value)) {
            $this->_usedProperties['sftp'] = true;
            $this->sftp = new \Symfony\Config\OneupFlysystem\AdapterConfig\SftpConfig($value['sftp']);
            unset($value['sftp']);
        }

        if (array_key_exists('memory', $value)) {
            $this->_usedProperties['memory'] = true;
            $this->memory = new \Symfony\Config\OneupFlysystem\AdapterConfig\MemoryConfig($value['memory']);
            unset($value['memory']);
        }

        if (array_key_exists('custom', $value)) {
            $this->_usedProperties['custom'] = true;
            $this->custom = new \Symfony\Config\OneupFlysystem\AdapterConfig\CustomConfig($value['custom']);
            unset($value['custom']);
        }

        if (array_key_exists('async_aws_s3', $value)) {
            $this->_usedProperties['asyncAwsS3'] = true;
            $this->asyncAwsS3 = new \Symfony\Config\OneupFlysystem\AdapterConfig\AsyncAwsS3Config($value['async_aws_s3']);
            unset($value['async_aws_s3']);
        }

        if (array_key_exists('googlecloudstorage', $value)) {
            $this->_usedProperties['googlecloudstorage'] = true;
            $this->googlecloudstorage = new \Symfony\Config\OneupFlysystem\AdapterConfig\GooglecloudstorageConfig($value['googlecloudstorage']);
            unset($value['googlecloudstorage']);
        }

        if (array_key_exists('gitlab', $value)) {
            $this->_usedProperties['gitlab'] = true;
            $this->gitlab = new \Symfony\Config\OneupFlysystem\AdapterConfig\GitlabConfig($value['gitlab']);
            unset($value['gitlab']);
        }

        if (array_key_exists('azureblob', $value)) {
            $this->_usedProperties['azureblob'] = true;
            $this->azureblob = new \Symfony\Config\OneupFlysystem\AdapterConfig\AzureblobConfig($value['azureblob']);
            unset($value['azureblob']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['local'])) {
            $output['local'] = $this->local->toArray();
        }
        if (isset($this->_usedProperties['awss3v3'])) {
            $output['awss3v3'] = $this->awss3v3->toArray();
        }
        if (isset($this->_usedProperties['ftp'])) {
            $output['ftp'] = $this->ftp->toArray();
        }
        if (isset($this->_usedProperties['sftp'])) {
            $output['sftp'] = $this->sftp->toArray();
        }
        if (isset($this->_usedProperties['memory'])) {
            $output['memory'] = $this->memory->toArray();
        }
        if (isset($this->_usedProperties['custom'])) {
            $output['custom'] = $this->custom->toArray();
        }
        if (isset($this->_usedProperties['asyncAwsS3'])) {
            $output['async_aws_s3'] = $this->asyncAwsS3->toArray();
        }
        if (isset($this->_usedProperties['googlecloudstorage'])) {
            $output['googlecloudstorage'] = $this->googlecloudstorage->toArray();
        }
        if (isset($this->_usedProperties['gitlab'])) {
            $output['gitlab'] = $this->gitlab->toArray();
        }
        if (isset($this->_usedProperties['azureblob'])) {
            $output['azureblob'] = $this->azureblob->toArray();
        }

        return $output;
    }

}
