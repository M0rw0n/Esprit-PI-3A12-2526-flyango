<?php

namespace Symfony\Config\OneupFlysystem\AdapterConfig;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Ftp'.\DIRECTORY_SEPARATOR.'OptionsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class FtpConfig 
{
    private $options;
    private $connectionProvider;
    private $connectivityChecker;
    private $visibilityConverter;
    private $mimeTypeDetector;
    private $_usedProperties = [];

    public function options(array $value = []): \Symfony\Config\OneupFlysystem\AdapterConfig\Ftp\OptionsConfig
    {
        if (null === $this->options) {
            $this->_usedProperties['options'] = true;
            $this->options = new \Symfony\Config\OneupFlysystem\AdapterConfig\Ftp\OptionsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "options()" has already been initialized. You cannot pass values the second time you call options().');
        }

        return $this->options;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function connectionProvider($value): static
    {
        $this->_usedProperties['connectionProvider'] = true;
        $this->connectionProvider = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function connectivityChecker($value): static
    {
        $this->_usedProperties['connectivityChecker'] = true;
        $this->connectivityChecker = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function visibilityConverter($value): static
    {
        $this->_usedProperties['visibilityConverter'] = true;
        $this->visibilityConverter = $value;

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

    public function __construct(array $value = [])
    {
        if (array_key_exists('options', $value)) {
            $this->_usedProperties['options'] = true;
            $this->options = new \Symfony\Config\OneupFlysystem\AdapterConfig\Ftp\OptionsConfig($value['options']);
            unset($value['options']);
        }

        if (array_key_exists('connectionProvider', $value)) {
            $this->_usedProperties['connectionProvider'] = true;
            $this->connectionProvider = $value['connectionProvider'];
            unset($value['connectionProvider']);
        }

        if (array_key_exists('connectivityChecker', $value)) {
            $this->_usedProperties['connectivityChecker'] = true;
            $this->connectivityChecker = $value['connectivityChecker'];
            unset($value['connectivityChecker']);
        }

        if (array_key_exists('visibilityConverter', $value)) {
            $this->_usedProperties['visibilityConverter'] = true;
            $this->visibilityConverter = $value['visibilityConverter'];
            unset($value['visibilityConverter']);
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
        if (isset($this->_usedProperties['connectionProvider'])) {
            $output['connectionProvider'] = $this->connectionProvider;
        }
        if (isset($this->_usedProperties['connectivityChecker'])) {
            $output['connectivityChecker'] = $this->connectivityChecker;
        }
        if (isset($this->_usedProperties['visibilityConverter'])) {
            $output['visibilityConverter'] = $this->visibilityConverter;
        }
        if (isset($this->_usedProperties['mimeTypeDetector'])) {
            $output['mimeTypeDetector'] = $this->mimeTypeDetector;
        }

        return $output;
    }

}
