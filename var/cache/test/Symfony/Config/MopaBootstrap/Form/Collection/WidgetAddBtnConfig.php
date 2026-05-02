<?php

namespace Symfony\Config\MopaBootstrap\Form\Collection;

require_once __DIR__.\DIRECTORY_SEPARATOR.'WidgetAddBtn'.\DIRECTORY_SEPARATOR.'AttrConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class WidgetAddBtnConfig 
{
    private $attr;
    private $label;
    private $translationDomain;
    private $icon;
    private $iconInverted;
    private $_usedProperties = [];

    /**
     * @default {"class":"btn btn-default"}
    */
    public function attr(array $value = []): \Symfony\Config\MopaBootstrap\Form\Collection\WidgetAddBtn\AttrConfig
    {
        if (null === $this->attr) {
            $this->_usedProperties['attr'] = true;
            $this->attr = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetAddBtn\AttrConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "attr()" has already been initialized. You cannot pass values the second time you call attr().');
        }

        return $this->attr;
    }

    /**
     * @default 'add_item'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function label($value): static
    {
        $this->_usedProperties['label'] = true;
        $this->label = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function translationDomain($value): static
    {
        $this->_usedProperties['translationDomain'] = true;
        $this->translationDomain = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function icon($value): static
    {
        $this->_usedProperties['icon'] = true;
        $this->icon = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function iconInverted($value): static
    {
        $this->_usedProperties['iconInverted'] = true;
        $this->iconInverted = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('attr', $value)) {
            $this->_usedProperties['attr'] = true;
            $this->attr = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetAddBtn\AttrConfig($value['attr']);
            unset($value['attr']);
        }

        if (array_key_exists('label', $value)) {
            $this->_usedProperties['label'] = true;
            $this->label = $value['label'];
            unset($value['label']);
        }

        if (array_key_exists('translation_domain', $value)) {
            $this->_usedProperties['translationDomain'] = true;
            $this->translationDomain = $value['translation_domain'];
            unset($value['translation_domain']);
        }

        if (array_key_exists('icon', $value)) {
            $this->_usedProperties['icon'] = true;
            $this->icon = $value['icon'];
            unset($value['icon']);
        }

        if (array_key_exists('icon_inverted', $value)) {
            $this->_usedProperties['iconInverted'] = true;
            $this->iconInverted = $value['icon_inverted'];
            unset($value['icon_inverted']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['attr'])) {
            $output['attr'] = $this->attr->toArray();
        }
        if (isset($this->_usedProperties['label'])) {
            $output['label'] = $this->label;
        }
        if (isset($this->_usedProperties['translationDomain'])) {
            $output['translation_domain'] = $this->translationDomain;
        }
        if (isset($this->_usedProperties['icon'])) {
            $output['icon'] = $this->icon;
        }
        if (isset($this->_usedProperties['iconInverted'])) {
            $output['icon_inverted'] = $this->iconInverted;
        }

        return $output;
    }

}
