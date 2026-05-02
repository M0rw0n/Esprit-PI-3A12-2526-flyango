<?php

namespace Symfony\Config\KnpGaufrette\AdapterConfig\GoogleCloudStorage;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class OptionsConfig 
{
    private $directory;
    private $acl;
    private $projectId;
    private $bucketLocation;
    private $create;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function directory($value): static
    {
        $this->_usedProperties['directory'] = true;
        $this->directory = $value;

        return $this;
    }

    /**
     * @default 'private'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function acl($value): static
    {
        $this->_usedProperties['acl'] = true;
        $this->acl = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function projectId($value): static
    {
        $this->_usedProperties['projectId'] = true;
        $this->projectId = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function bucketLocation($value): static
    {
        $this->_usedProperties['bucketLocation'] = true;
        $this->bucketLocation = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function create($value): static
    {
        $this->_usedProperties['create'] = true;
        $this->create = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('directory', $value)) {
            $this->_usedProperties['directory'] = true;
            $this->directory = $value['directory'];
            unset($value['directory']);
        }

        if (array_key_exists('acl', $value)) {
            $this->_usedProperties['acl'] = true;
            $this->acl = $value['acl'];
            unset($value['acl']);
        }

        if (array_key_exists('project_id', $value)) {
            $this->_usedProperties['projectId'] = true;
            $this->projectId = $value['project_id'];
            unset($value['project_id']);
        }

        if (array_key_exists('bucket_location', $value)) {
            $this->_usedProperties['bucketLocation'] = true;
            $this->bucketLocation = $value['bucket_location'];
            unset($value['bucket_location']);
        }

        if (array_key_exists('create', $value)) {
            $this->_usedProperties['create'] = true;
            $this->create = $value['create'];
            unset($value['create']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['directory'])) {
            $output['directory'] = $this->directory;
        }
        if (isset($this->_usedProperties['acl'])) {
            $output['acl'] = $this->acl;
        }
        if (isset($this->_usedProperties['projectId'])) {
            $output['project_id'] = $this->projectId;
        }
        if (isset($this->_usedProperties['bucketLocation'])) {
            $output['bucket_location'] = $this->bucketLocation;
        }
        if (isset($this->_usedProperties['create'])) {
            $output['create'] = $this->create;
        }

        return $output;
    }

}
