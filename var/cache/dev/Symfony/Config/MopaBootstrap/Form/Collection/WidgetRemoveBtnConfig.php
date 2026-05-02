<?php

namespace Symfony\Config\MopaBootstrap\Form\Collection;

require_once __DIR__.\DIRECTORY_SEPARATOR.'WidgetRemoveBtn'.\DIRECTORY_SEPARATOR.'AttrConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'WidgetRemoveBtn'.\DIRECTORY_SEPARATOR.'WrapperDivConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'WidgetRemoveBtn'.\DIRECTORY_SEPARATOR.'HorizontalWrapperDivConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class WidgetRemoveBtnConfig 
{
    private $attr;
    private $wrapperDiv;
    private $horizontalWrapperDiv;
    private $label;
    private $translationDomain;
    private $icon;
    private $iconInverted;
    private $_usedProperties = [];

    /**
     * @default {"class":"btn btn-default"}
    */
    public function attr(array $value = []): \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtn\AttrConfig
    {
        if (null === $this->attr) {
            $this->_usedProperties['attr'] = true;
            $this->attr = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtn\AttrConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "attr()" has already been initialized. You cannot pass values the second time you call attr().');
        }

        return $this->attr;
    }

    /**
     * @default {"class":"form-group"}
    */
    public function wrapperDiv(array $value = []): \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtn\WrapperDivConfig
    {
        if (null === $this->wrapperDiv) {
            $this->_usedProperties['wrapperDiv'] = true;
            $this->wrapperDiv = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtn\WrapperDivConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "wrapperDiv()" has already been initialized. You cannot pass values the second time you call wrapperDiv().');
        }

        return $this->wrapperDiv;
    }

    /**
     * @default {"class":"col-sm-3 col-sm-offset-3"}
    */
    public function horizontalWrapperDiv(array $value = []): \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtn\HorizontalWrapperDivConfig
    {
        if (null === $this->horizontalWrapperDiv) {
            $this->_usedProperties['horizontalWrapperDiv'] = true;
            $this->horizontalWrapperDiv = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtn\HorizontalWrapperDivConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "horizontalWrapperDiv()" has already been initialized. You cannot pass values the second time you call horizontalWrapperDiv().');
        }

        return $this->horizontalWrapperDiv;
    }

    /**
     * @default 'remove_item'
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
            $this->attr = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtn\AttrConfig($value['attr']);
            unset($value['attr']);
        }

        if (array_key_exists('wrapper_div', $value)) {
            $this->_usedProperties['wrapperDiv'] = true;
            $this->wrapperDiv = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtn\WrapperDivConfig($value['wrapper_div']);
            unset($value['wrapper_div']);
        }

        if (array_key_exists('horizontal_wrapper_div', $value)) {
            $this->_usedProperties['horizontalWrapperDiv'] = true;
            $this->horizontalWrapperDiv = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtn\HorizontalWrapperDivConfig($value['horizontal_wrapper_div']);
            unset($value['horizontal_wrapper_div']);
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
        if (isset($this->_usedProperties['wrapperDiv'])) {
            $output['wrapper_div'] = $this->wrapperDiv->toArray();
        }
        if (isset($this->_usedProperties['horizontalWrapperDiv'])) {
            $output['horizontal_wrapper_div'] = $this->horizontalWrapperDiv->toArray();
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
