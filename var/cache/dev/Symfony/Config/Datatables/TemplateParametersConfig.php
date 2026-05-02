<?php

namespace Symfony\Config\Datatables;

use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class TemplateParametersConfig 
{
    private $className;
    private $columnFilter;
    private $_usedProperties = [];
    private $_extraKeys;

    /**
     * Default class attribute to apply to the root table elements
     * @default 'table table-bordered'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function className($value): static
    {
        $this->_usedProperties['className'] = true;
        $this->className = $value;

        return $this;
    }

    /**
     * If and where to enable the DataTables Filter module
     * @default null
     * @param ParamConfigurator|'thead'|'tfoot'|'both'|NULL $value
     * @return $this
     */
    public function columnFilter($value): static
    {
        $this->_usedProperties['columnFilter'] = true;
        $this->columnFilter = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('className', $value)) {
            $this->_usedProperties['className'] = true;
            $this->className = $value['className'];
            unset($value['className']);
        }

        if (array_key_exists('columnFilter', $value)) {
            $this->_usedProperties['columnFilter'] = true;
            $this->columnFilter = $value['columnFilter'];
            unset($value['columnFilter']);
        }

        $this->_extraKeys = $value;

    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['className'])) {
            $output['className'] = $this->className;
        }
        if (isset($this->_usedProperties['columnFilter'])) {
            $output['columnFilter'] = $this->columnFilter;
        }

        return $output + $this->_extraKeys;
    }

    /**
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function set(string $key, mixed $value): static
    {
        $this->_extraKeys[$key] = $value;

        return $this;
    }

}
