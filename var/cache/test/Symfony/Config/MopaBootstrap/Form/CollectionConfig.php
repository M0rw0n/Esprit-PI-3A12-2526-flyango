<?php

namespace Symfony\Config\MopaBootstrap\Form;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Collection'.\DIRECTORY_SEPARATOR.'WidgetRemoveBtnConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Collection'.\DIRECTORY_SEPARATOR.'WidgetAddBtnConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class CollectionConfig 
{
    private $widgetRemoveBtn;
    private $widgetAddBtn;
    private $_usedProperties = [];

    /**
     * @default {"attr":{"class":"btn btn-default"},"wrapper_div":{"class":"form-group"},"horizontal_wrapper_div":{"class":"col-sm-3 col-sm-offset-3"},"label":"remove_item","translation_domain":null,"icon":null,"icon_inverted":false}
    */
    public function widgetRemoveBtn(array $value = []): \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtnConfig
    {
        if (null === $this->widgetRemoveBtn) {
            $this->_usedProperties['widgetRemoveBtn'] = true;
            $this->widgetRemoveBtn = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtnConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "widgetRemoveBtn()" has already been initialized. You cannot pass values the second time you call widgetRemoveBtn().');
        }

        return $this->widgetRemoveBtn;
    }

    /**
     * @default {"attr":{"class":"btn btn-default"},"label":"add_item","translation_domain":null,"icon":null,"icon_inverted":false}
    */
    public function widgetAddBtn(array $value = []): \Symfony\Config\MopaBootstrap\Form\Collection\WidgetAddBtnConfig
    {
        if (null === $this->widgetAddBtn) {
            $this->_usedProperties['widgetAddBtn'] = true;
            $this->widgetAddBtn = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetAddBtnConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "widgetAddBtn()" has already been initialized. You cannot pass values the second time you call widgetAddBtn().');
        }

        return $this->widgetAddBtn;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('widget_remove_btn', $value)) {
            $this->_usedProperties['widgetRemoveBtn'] = true;
            $this->widgetRemoveBtn = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetRemoveBtnConfig($value['widget_remove_btn']);
            unset($value['widget_remove_btn']);
        }

        if (array_key_exists('widget_add_btn', $value)) {
            $this->_usedProperties['widgetAddBtn'] = true;
            $this->widgetAddBtn = new \Symfony\Config\MopaBootstrap\Form\Collection\WidgetAddBtnConfig($value['widget_add_btn']);
            unset($value['widget_add_btn']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['widgetRemoveBtn'])) {
            $output['widget_remove_btn'] = $this->widgetRemoveBtn->toArray();
        }
        if (isset($this->_usedProperties['widgetAddBtn'])) {
            $output['widget_add_btn'] = $this->widgetAddBtn->toArray();
        }

        return $output;
    }

}
