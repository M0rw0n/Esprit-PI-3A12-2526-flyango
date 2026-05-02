<?php

namespace Symfony\Config\KnpGaufrette;

require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'InMemoryConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'ServiceConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'LocalConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'SafeLocalConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'AsyncAwsS3Config.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'AwsS3Config.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'DoctrineDbalConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'AzureBlobStorageConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'GoogleCloudStorageConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'GridfsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'FtpConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'AdapterConfig'.\DIRECTORY_SEPARATOR.'PhpseclibSftpConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AdapterConfig 
{
    private $inMemory;
    private $service;
    private $local;
    private $safeLocal;
    private $asyncAwsS3;
    private $awsS3;
    private $doctrineDbal;
    private $azureBlobStorage;
    private $googleCloudStorage;
    private $gridfs;
    private $ftp;
    private $phpseclibSftp;
    private $_usedProperties = [];

    public function inMemory(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\InMemoryConfig
    {
        if (null === $this->inMemory) {
            $this->_usedProperties['inMemory'] = true;
            $this->inMemory = new \Symfony\Config\KnpGaufrette\AdapterConfig\InMemoryConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "inMemory()" has already been initialized. You cannot pass values the second time you call inMemory().');
        }

        return $this->inMemory;
    }

    public function service(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\ServiceConfig
    {
        if (null === $this->service) {
            $this->_usedProperties['service'] = true;
            $this->service = new \Symfony\Config\KnpGaufrette\AdapterConfig\ServiceConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "service()" has already been initialized. You cannot pass values the second time you call service().');
        }

        return $this->service;
    }

    public function local(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\LocalConfig
    {
        if (null === $this->local) {
            $this->_usedProperties['local'] = true;
            $this->local = new \Symfony\Config\KnpGaufrette\AdapterConfig\LocalConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "local()" has already been initialized. You cannot pass values the second time you call local().');
        }

        return $this->local;
    }

    public function safeLocal(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\SafeLocalConfig
    {
        if (null === $this->safeLocal) {
            $this->_usedProperties['safeLocal'] = true;
            $this->safeLocal = new \Symfony\Config\KnpGaufrette\AdapterConfig\SafeLocalConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "safeLocal()" has already been initialized. You cannot pass values the second time you call safeLocal().');
        }

        return $this->safeLocal;
    }

    public function asyncAwsS3(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\AsyncAwsS3Config
    {
        if (null === $this->asyncAwsS3) {
            $this->_usedProperties['asyncAwsS3'] = true;
            $this->asyncAwsS3 = new \Symfony\Config\KnpGaufrette\AdapterConfig\AsyncAwsS3Config($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "asyncAwsS3()" has already been initialized. You cannot pass values the second time you call asyncAwsS3().');
        }

        return $this->asyncAwsS3;
    }

    public function awsS3(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\AwsS3Config
    {
        if (null === $this->awsS3) {
            $this->_usedProperties['awsS3'] = true;
            $this->awsS3 = new \Symfony\Config\KnpGaufrette\AdapterConfig\AwsS3Config($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "awsS3()" has already been initialized. You cannot pass values the second time you call awsS3().');
        }

        return $this->awsS3;
    }

    public function doctrineDbal(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\DoctrineDbalConfig
    {
        if (null === $this->doctrineDbal) {
            $this->_usedProperties['doctrineDbal'] = true;
            $this->doctrineDbal = new \Symfony\Config\KnpGaufrette\AdapterConfig\DoctrineDbalConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "doctrineDbal()" has already been initialized. You cannot pass values the second time you call doctrineDbal().');
        }

        return $this->doctrineDbal;
    }

    public function azureBlobStorage(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\AzureBlobStorageConfig
    {
        if (null === $this->azureBlobStorage) {
            $this->_usedProperties['azureBlobStorage'] = true;
            $this->azureBlobStorage = new \Symfony\Config\KnpGaufrette\AdapterConfig\AzureBlobStorageConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "azureBlobStorage()" has already been initialized. You cannot pass values the second time you call azureBlobStorage().');
        }

        return $this->azureBlobStorage;
    }

    public function googleCloudStorage(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\GoogleCloudStorageConfig
    {
        if (null === $this->googleCloudStorage) {
            $this->_usedProperties['googleCloudStorage'] = true;
            $this->googleCloudStorage = new \Symfony\Config\KnpGaufrette\AdapterConfig\GoogleCloudStorageConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "googleCloudStorage()" has already been initialized. You cannot pass values the second time you call googleCloudStorage().');
        }

        return $this->googleCloudStorage;
    }

    public function gridfs(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\GridfsConfig
    {
        if (null === $this->gridfs) {
            $this->_usedProperties['gridfs'] = true;
            $this->gridfs = new \Symfony\Config\KnpGaufrette\AdapterConfig\GridfsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "gridfs()" has already been initialized. You cannot pass values the second time you call gridfs().');
        }

        return $this->gridfs;
    }

    public function ftp(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\FtpConfig
    {
        if (null === $this->ftp) {
            $this->_usedProperties['ftp'] = true;
            $this->ftp = new \Symfony\Config\KnpGaufrette\AdapterConfig\FtpConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "ftp()" has already been initialized. You cannot pass values the second time you call ftp().');
        }

        return $this->ftp;
    }

    public function phpseclibSftp(array $value = []): \Symfony\Config\KnpGaufrette\AdapterConfig\PhpseclibSftpConfig
    {
        if (null === $this->phpseclibSftp) {
            $this->_usedProperties['phpseclibSftp'] = true;
            $this->phpseclibSftp = new \Symfony\Config\KnpGaufrette\AdapterConfig\PhpseclibSftpConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "phpseclibSftp()" has already been initialized. You cannot pass values the second time you call phpseclibSftp().');
        }

        return $this->phpseclibSftp;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('in_memory', $value)) {
            $this->_usedProperties['inMemory'] = true;
            $this->inMemory = new \Symfony\Config\KnpGaufrette\AdapterConfig\InMemoryConfig($value['in_memory']);
            unset($value['in_memory']);
        }

        if (array_key_exists('service', $value)) {
            $this->_usedProperties['service'] = true;
            $this->service = new \Symfony\Config\KnpGaufrette\AdapterConfig\ServiceConfig($value['service']);
            unset($value['service']);
        }

        if (array_key_exists('local', $value)) {
            $this->_usedProperties['local'] = true;
            $this->local = new \Symfony\Config\KnpGaufrette\AdapterConfig\LocalConfig($value['local']);
            unset($value['local']);
        }

        if (array_key_exists('safe_local', $value)) {
            $this->_usedProperties['safeLocal'] = true;
            $this->safeLocal = new \Symfony\Config\KnpGaufrette\AdapterConfig\SafeLocalConfig($value['safe_local']);
            unset($value['safe_local']);
        }

        if (array_key_exists('async_aws_s3', $value)) {
            $this->_usedProperties['asyncAwsS3'] = true;
            $this->asyncAwsS3 = new \Symfony\Config\KnpGaufrette\AdapterConfig\AsyncAwsS3Config($value['async_aws_s3']);
            unset($value['async_aws_s3']);
        }

        if (array_key_exists('aws_s3', $value)) {
            $this->_usedProperties['awsS3'] = true;
            $this->awsS3 = new \Symfony\Config\KnpGaufrette\AdapterConfig\AwsS3Config($value['aws_s3']);
            unset($value['aws_s3']);
        }

        if (array_key_exists('doctrine_dbal', $value)) {
            $this->_usedProperties['doctrineDbal'] = true;
            $this->doctrineDbal = new \Symfony\Config\KnpGaufrette\AdapterConfig\DoctrineDbalConfig($value['doctrine_dbal']);
            unset($value['doctrine_dbal']);
        }

        if (array_key_exists('azure_blob_storage', $value)) {
            $this->_usedProperties['azureBlobStorage'] = true;
            $this->azureBlobStorage = new \Symfony\Config\KnpGaufrette\AdapterConfig\AzureBlobStorageConfig($value['azure_blob_storage']);
            unset($value['azure_blob_storage']);
        }

        if (array_key_exists('google_cloud_storage', $value)) {
            $this->_usedProperties['googleCloudStorage'] = true;
            $this->googleCloudStorage = new \Symfony\Config\KnpGaufrette\AdapterConfig\GoogleCloudStorageConfig($value['google_cloud_storage']);
            unset($value['google_cloud_storage']);
        }

        if (array_key_exists('gridfs', $value)) {
            $this->_usedProperties['gridfs'] = true;
            $this->gridfs = new \Symfony\Config\KnpGaufrette\AdapterConfig\GridfsConfig($value['gridfs']);
            unset($value['gridfs']);
        }

        if (array_key_exists('ftp', $value)) {
            $this->_usedProperties['ftp'] = true;
            $this->ftp = new \Symfony\Config\KnpGaufrette\AdapterConfig\FtpConfig($value['ftp']);
            unset($value['ftp']);
        }

        if (array_key_exists('phpseclib_sftp', $value)) {
            $this->_usedProperties['phpseclibSftp'] = true;
            $this->phpseclibSftp = new \Symfony\Config\KnpGaufrette\AdapterConfig\PhpseclibSftpConfig($value['phpseclib_sftp']);
            unset($value['phpseclib_sftp']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['inMemory'])) {
            $output['in_memory'] = $this->inMemory->toArray();
        }
        if (isset($this->_usedProperties['service'])) {
            $output['service'] = $this->service->toArray();
        }
        if (isset($this->_usedProperties['local'])) {
            $output['local'] = $this->local->toArray();
        }
        if (isset($this->_usedProperties['safeLocal'])) {
            $output['safe_local'] = $this->safeLocal->toArray();
        }
        if (isset($this->_usedProperties['asyncAwsS3'])) {
            $output['async_aws_s3'] = $this->asyncAwsS3->toArray();
        }
        if (isset($this->_usedProperties['awsS3'])) {
            $output['aws_s3'] = $this->awsS3->toArray();
        }
        if (isset($this->_usedProperties['doctrineDbal'])) {
            $output['doctrine_dbal'] = $this->doctrineDbal->toArray();
        }
        if (isset($this->_usedProperties['azureBlobStorage'])) {
            $output['azure_blob_storage'] = $this->azureBlobStorage->toArray();
        }
        if (isset($this->_usedProperties['googleCloudStorage'])) {
            $output['google_cloud_storage'] = $this->googleCloudStorage->toArray();
        }
        if (isset($this->_usedProperties['gridfs'])) {
            $output['gridfs'] = $this->gridfs->toArray();
        }
        if (isset($this->_usedProperties['ftp'])) {
            $output['ftp'] = $this->ftp->toArray();
        }
        if (isset($this->_usedProperties['phpseclibSftp'])) {
            $output['phpseclib_sftp'] = $this->phpseclibSftp->toArray();
        }

        return $output;
    }

}
