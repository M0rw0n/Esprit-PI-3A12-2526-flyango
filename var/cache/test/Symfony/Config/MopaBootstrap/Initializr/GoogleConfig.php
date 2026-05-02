<?php

namespace Symfony\Config\MopaBootstrap\Initializr;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class GoogleConfig 
{
    private $wt;
    private $analytics;
    private $extendedanalytics;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function wt($value): static
    {
        $this->_usedProperties['wt'] = true;
        $this->wt = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function analytics($value): static
    {
        $this->_usedProperties['analytics'] = true;
        $this->analytics = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function extendedanalytics($value): static
    {
        $this->_usedProperties['extendedanalytics'] = true;
        $this->extendedanalytics = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('wt', $value)) {
            $this->_usedProperties['wt'] = true;
            $this->wt = $value['wt'];
            unset($value['wt']);
        }

        if (array_key_exists('analytics', $value)) {
            $this->_usedProperties['analytics'] = true;
            $this->analytics = $value['analytics'];
            unset($value['analytics']);
        }

        if (array_key_exists('extendedanalytics', $value)) {
            $this->_usedProperties['extendedanalytics'] = true;
            $this->extendedanalytics = $value['extendedanalytics'];
            unset($value['extendedanalytics']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['wt'])) {
            $output['wt'] = $this->wt;
        }
        if (isset($this->_usedProperties['analytics'])) {
            $output['analytics'] = $this->analytics;
        }
        if (isset($this->_usedProperties['extendedanalytics'])) {
            $output['extendedanalytics'] = $this->extendedanalytics;
        }

        return $output;
    }

}
