<?php

namespace Symfony\Config\MopaBootstrap;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class IconsConfig 
{
    private $iconSet;
    private $shortcut;
    private $_usedProperties = [];

    /**
     * Icon set to use: ["glyphicons","fontawesome","fontawesome4","zmdi"]
     * @default 'glyphicons'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function iconSet($value): static
    {
        $this->_usedProperties['iconSet'] = true;
        $this->iconSet = $value;

        return $this;
    }

    /**
     * Alias for mopa_bootstrap_icon()
     * @default 'icon'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function shortcut($value): static
    {
        $this->_usedProperties['shortcut'] = true;
        $this->shortcut = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('icon_set', $value)) {
            $this->_usedProperties['iconSet'] = true;
            $this->iconSet = $value['icon_set'];
            unset($value['icon_set']);
        }

        if (array_key_exists('shortcut', $value)) {
            $this->_usedProperties['shortcut'] = true;
            $this->shortcut = $value['shortcut'];
            unset($value['shortcut']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['iconSet'])) {
            $output['icon_set'] = $this->iconSet;
        }
        if (isset($this->_usedProperties['shortcut'])) {
            $output['shortcut'] = $this->shortcut;
        }

        return $output;
    }

}
