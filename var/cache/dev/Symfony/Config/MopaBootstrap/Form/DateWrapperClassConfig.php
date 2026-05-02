<?php

namespace Symfony\Config\MopaBootstrap\Form;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DateWrapperClassConfig 
{
    private $year;
    private $month;
    private $day;
    private $_usedProperties = [];

    /**
     * @default 'col-xs-4'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function year($value): static
    {
        $this->_usedProperties['year'] = true;
        $this->year = $value;

        return $this;
    }

    /**
     * @default 'col-xs-4'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function month($value): static
    {
        $this->_usedProperties['month'] = true;
        $this->month = $value;

        return $this;
    }

    /**
     * @default 'col-xs-4'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function day($value): static
    {
        $this->_usedProperties['day'] = true;
        $this->day = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('year', $value)) {
            $this->_usedProperties['year'] = true;
            $this->year = $value['year'];
            unset($value['year']);
        }

        if (array_key_exists('month', $value)) {
            $this->_usedProperties['month'] = true;
            $this->month = $value['month'];
            unset($value['month']);
        }

        if (array_key_exists('day', $value)) {
            $this->_usedProperties['day'] = true;
            $this->day = $value['day'];
            unset($value['day']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['year'])) {
            $output['year'] = $this->year;
        }
        if (isset($this->_usedProperties['month'])) {
            $output['month'] = $this->month;
        }
        if (isset($this->_usedProperties['day'])) {
            $output['day'] = $this->day;
        }

        return $output;
    }

}
