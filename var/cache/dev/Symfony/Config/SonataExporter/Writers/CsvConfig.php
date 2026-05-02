<?php

namespace Symfony\Config\SonataExporter\Writers;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class CsvConfig 
{
    private $filename;
    private $delimiter;
    private $enclosure;
    private $escape;
    private $showHeaders;
    private $withBom;
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
     * delimits csv values
     * @default ','
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function delimiter($value): static
    {
        $this->_usedProperties['delimiter'] = true;
        $this->delimiter = $value;

        return $this;
    }

    /**
     * will be used when a value contains the delimiter
     * @default '"'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function enclosure($value): static
    {
        $this->_usedProperties['enclosure'] = true;
        $this->enclosure = $value;

        return $this;
    }

    /**
     * will be used when a value contains the enclosure
     * @default '\\'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function escape($value): static
    {
        $this->_usedProperties['escape'] = true;
        $this->escape = $value;

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

    /**
     * include the byte order mark
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function withBom($value): static
    {
        $this->_usedProperties['withBom'] = true;
        $this->withBom = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('filename', $value)) {
            $this->_usedProperties['filename'] = true;
            $this->filename = $value['filename'];
            unset($value['filename']);
        }

        if (array_key_exists('delimiter', $value)) {
            $this->_usedProperties['delimiter'] = true;
            $this->delimiter = $value['delimiter'];
            unset($value['delimiter']);
        }

        if (array_key_exists('enclosure', $value)) {
            $this->_usedProperties['enclosure'] = true;
            $this->enclosure = $value['enclosure'];
            unset($value['enclosure']);
        }

        if (array_key_exists('escape', $value)) {
            $this->_usedProperties['escape'] = true;
            $this->escape = $value['escape'];
            unset($value['escape']);
        }

        if (array_key_exists('show_headers', $value)) {
            $this->_usedProperties['showHeaders'] = true;
            $this->showHeaders = $value['show_headers'];
            unset($value['show_headers']);
        }

        if (array_key_exists('with_bom', $value)) {
            $this->_usedProperties['withBom'] = true;
            $this->withBom = $value['with_bom'];
            unset($value['with_bom']);
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
        if (isset($this->_usedProperties['delimiter'])) {
            $output['delimiter'] = $this->delimiter;
        }
        if (isset($this->_usedProperties['enclosure'])) {
            $output['enclosure'] = $this->enclosure;
        }
        if (isset($this->_usedProperties['escape'])) {
            $output['escape'] = $this->escape;
        }
        if (isset($this->_usedProperties['showHeaders'])) {
            $output['show_headers'] = $this->showHeaders;
        }
        if (isset($this->_usedProperties['withBom'])) {
            $output['with_bom'] = $this->withBom;
        }

        return $output;
    }

}
