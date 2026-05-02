<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataExporter'.\DIRECTORY_SEPARATOR.'ExporterConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataExporter'.\DIRECTORY_SEPARATOR.'WritersConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SonataExporterConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $exporter;
    private $writers;
    private $_usedProperties = [];

    /**
     * @default {"default_writers":["csv","json","xls","xml"]}
    */
    public function exporter(array $value = []): \Symfony\Config\SonataExporter\ExporterConfig
    {
        if (null === $this->exporter) {
            $this->_usedProperties['exporter'] = true;
            $this->exporter = new \Symfony\Config\SonataExporter\ExporterConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "exporter()" has already been initialized. You cannot pass values the second time you call exporter().');
        }

        return $this->exporter;
    }

    /**
     * @default {"csv":{"filename":"php:\/\/output","delimiter":",","enclosure":"\"","escape":"\\","show_headers":true,"with_bom":false},"json":{"filename":"php:\/\/output"},"xls":{"filename":"php:\/\/output","show_headers":true},"xlsx":{"filename":"php:\/\/output","show_headers":true,"show_filters":true},"xml":{"filename":"php:\/\/output","show_headers":true,"main_element":"datas","child_element":"data"}}
    */
    public function writers(array $value = []): \Symfony\Config\SonataExporter\WritersConfig
    {
        if (null === $this->writers) {
            $this->_usedProperties['writers'] = true;
            $this->writers = new \Symfony\Config\SonataExporter\WritersConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "writers()" has already been initialized. You cannot pass values the second time you call writers().');
        }

        return $this->writers;
    }

    public function getExtensionAlias(): string
    {
        return 'sonata_exporter';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('exporter', $value)) {
            $this->_usedProperties['exporter'] = true;
            $this->exporter = new \Symfony\Config\SonataExporter\ExporterConfig($value['exporter']);
            unset($value['exporter']);
        }

        if (array_key_exists('writers', $value)) {
            $this->_usedProperties['writers'] = true;
            $this->writers = new \Symfony\Config\SonataExporter\WritersConfig($value['writers']);
            unset($value['writers']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['exporter'])) {
            $output['exporter'] = $this->exporter->toArray();
        }
        if (isset($this->_usedProperties['writers'])) {
            $output['writers'] = $this->writers->toArray();
        }

        return $output;
    }

}
