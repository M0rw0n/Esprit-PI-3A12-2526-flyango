<?php

namespace Symfony\Config\SonataExporter;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Writers'.\DIRECTORY_SEPARATOR.'CsvConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Writers'.\DIRECTORY_SEPARATOR.'JsonConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Writers'.\DIRECTORY_SEPARATOR.'XlsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Writers'.\DIRECTORY_SEPARATOR.'XlsxConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Writers'.\DIRECTORY_SEPARATOR.'XmlConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class WritersConfig 
{
    private $csv;
    private $json;
    private $xls;
    private $xlsx;
    private $xml;
    private $_usedProperties = [];

    /**
     * @default {"filename":"php:\/\/output","delimiter":",","enclosure":"\"","escape":"\\","show_headers":true,"with_bom":false}
    */
    public function csv(array $value = []): \Symfony\Config\SonataExporter\Writers\CsvConfig
    {
        if (null === $this->csv) {
            $this->_usedProperties['csv'] = true;
            $this->csv = new \Symfony\Config\SonataExporter\Writers\CsvConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "csv()" has already been initialized. You cannot pass values the second time you call csv().');
        }

        return $this->csv;
    }

    /**
     * @default {"filename":"php:\/\/output"}
    */
    public function json(array $value = []): \Symfony\Config\SonataExporter\Writers\JsonConfig
    {
        if (null === $this->json) {
            $this->_usedProperties['json'] = true;
            $this->json = new \Symfony\Config\SonataExporter\Writers\JsonConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "json()" has already been initialized. You cannot pass values the second time you call json().');
        }

        return $this->json;
    }

    /**
     * @default {"filename":"php:\/\/output","show_headers":true}
    */
    public function xls(array $value = []): \Symfony\Config\SonataExporter\Writers\XlsConfig
    {
        if (null === $this->xls) {
            $this->_usedProperties['xls'] = true;
            $this->xls = new \Symfony\Config\SonataExporter\Writers\XlsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "xls()" has already been initialized. You cannot pass values the second time you call xls().');
        }

        return $this->xls;
    }

    /**
     * @default {"filename":"php:\/\/output","show_headers":true,"show_filters":true}
    */
    public function xlsx(array $value = []): \Symfony\Config\SonataExporter\Writers\XlsxConfig
    {
        if (null === $this->xlsx) {
            $this->_usedProperties['xlsx'] = true;
            $this->xlsx = new \Symfony\Config\SonataExporter\Writers\XlsxConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "xlsx()" has already been initialized. You cannot pass values the second time you call xlsx().');
        }

        return $this->xlsx;
    }

    /**
     * @default {"filename":"php:\/\/output","show_headers":true,"main_element":"datas","child_element":"data"}
    */
    public function xml(array $value = []): \Symfony\Config\SonataExporter\Writers\XmlConfig
    {
        if (null === $this->xml) {
            $this->_usedProperties['xml'] = true;
            $this->xml = new \Symfony\Config\SonataExporter\Writers\XmlConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "xml()" has already been initialized. You cannot pass values the second time you call xml().');
        }

        return $this->xml;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('csv', $value)) {
            $this->_usedProperties['csv'] = true;
            $this->csv = new \Symfony\Config\SonataExporter\Writers\CsvConfig($value['csv']);
            unset($value['csv']);
        }

        if (array_key_exists('json', $value)) {
            $this->_usedProperties['json'] = true;
            $this->json = new \Symfony\Config\SonataExporter\Writers\JsonConfig($value['json']);
            unset($value['json']);
        }

        if (array_key_exists('xls', $value)) {
            $this->_usedProperties['xls'] = true;
            $this->xls = new \Symfony\Config\SonataExporter\Writers\XlsConfig($value['xls']);
            unset($value['xls']);
        }

        if (array_key_exists('xlsx', $value)) {
            $this->_usedProperties['xlsx'] = true;
            $this->xlsx = new \Symfony\Config\SonataExporter\Writers\XlsxConfig($value['xlsx']);
            unset($value['xlsx']);
        }

        if (array_key_exists('xml', $value)) {
            $this->_usedProperties['xml'] = true;
            $this->xml = new \Symfony\Config\SonataExporter\Writers\XmlConfig($value['xml']);
            unset($value['xml']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['csv'])) {
            $output['csv'] = $this->csv->toArray();
        }
        if (isset($this->_usedProperties['json'])) {
            $output['json'] = $this->json->toArray();
        }
        if (isset($this->_usedProperties['xls'])) {
            $output['xls'] = $this->xls->toArray();
        }
        if (isset($this->_usedProperties['xlsx'])) {
            $output['xlsx'] = $this->xlsx->toArray();
        }
        if (isset($this->_usedProperties['xml'])) {
            $output['xml'] = $this->xml->toArray();
        }

        return $output;
    }

}
