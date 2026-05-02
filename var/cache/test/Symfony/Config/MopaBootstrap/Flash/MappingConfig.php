<?php

namespace Symfony\Config\MopaBootstrap\Flash;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class MappingConfig 
{
    private $success;
    private $danger;
    private $warning;
    private $info;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|mixed $value
     *
     * @return $this
     */
    public function success(mixed $value): static
    {
        $this->_usedProperties['success'] = true;
        $this->success = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|mixed $value
     *
     * @return $this
     */
    public function danger(mixed $value): static
    {
        $this->_usedProperties['danger'] = true;
        $this->danger = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|mixed $value
     *
     * @return $this
     */
    public function warning(mixed $value): static
    {
        $this->_usedProperties['warning'] = true;
        $this->warning = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|mixed $value
     *
     * @return $this
     */
    public function info(mixed $value): static
    {
        $this->_usedProperties['info'] = true;
        $this->info = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('success', $value)) {
            $this->_usedProperties['success'] = true;
            $this->success = $value['success'];
            unset($value['success']);
        }

        if (array_key_exists('danger', $value)) {
            $this->_usedProperties['danger'] = true;
            $this->danger = $value['danger'];
            unset($value['danger']);
        }

        if (array_key_exists('warning', $value)) {
            $this->_usedProperties['warning'] = true;
            $this->warning = $value['warning'];
            unset($value['warning']);
        }

        if (array_key_exists('info', $value)) {
            $this->_usedProperties['info'] = true;
            $this->info = $value['info'];
            unset($value['info']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['success'])) {
            $output['success'] = $this->success;
        }
        if (isset($this->_usedProperties['danger'])) {
            $output['danger'] = $this->danger;
        }
        if (isset($this->_usedProperties['warning'])) {
            $output['warning'] = $this->warning;
        }
        if (isset($this->_usedProperties['info'])) {
            $output['info'] = $this->info;
        }

        return $output;
    }

}
