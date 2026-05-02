<?php

namespace Symfony\Config\SonataExporter\Writers;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class XlsConfig 
{
    private $filename;
    private $showHeaders;
    private $_usedProperties = [];

    /**
     * path to the output file
     * @default 'php://output'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function filename($value): static
    {
        $this->_usedProperties['filename'] = true;
        $this->filename = $value;

        return $this;
    }

    /**
     * add column names as the first line
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function showHeaders($value): static
    {
        $this->_usedProperties['showHeaders'] = true;
        $this->showHeaders = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('filename', $value)) {
            $this->_usedProperties['filename'] = true;
            $this->filename = $value['filename'];
            unset($value['filename']);
        }

        if (array_key_exists('show_headers', $value)) {
            $this->_usedProperties['showHeaders'] = true;
            $this->showHeaders = $value['show_headers'];
            unset($value['show_headers']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['filename'])) {
            $output['filename'] = $this->filename;
        }
        if (isset($this->_usedProperties['showHeaders'])) {
            $output['show_headers'] = $this->showHeaders;
        }

        return $output;
    }

}
