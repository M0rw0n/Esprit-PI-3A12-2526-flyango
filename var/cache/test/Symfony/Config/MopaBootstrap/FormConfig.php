<?php

namespace Symfony\Config\MopaBootstrap;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Form'.\DIRECTORY_SEPARATOR.'DateWrapperClassConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Form'.\DIRECTORY_SEPARATOR.'TabsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Form'.\DIRECTORY_SEPARATOR.'HelpWidgetConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Form'.\DIRECTORY_SEPARATOR.'HelpLabelConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Form'.\DIRECTORY_SEPARATOR.'HelpBlockConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Form'.\DIRECTORY_SEPARATOR.'CollectionConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class FormConfig 
{
    private $templating;
    private $layout;
    private $horizontalLabelClass;
    private $horizontalLabelDivClass;
    private $horizontalLabelOffsetClass;
    private $horizontalInputWrapperClass;
    private $dateWrapperClass;
    private $renderFieldset;
    private $renderCollectionItem;
    private $showLegend;
    private $showChildLegend;
    private $legendTag;
    private $checkboxLabel;
    private $renderOptionalText;
    private $renderRequiredAsterisk;
    private $errorType;
    private $tabs;
    private $helpWidget;
    private $helpLabel;
    private $helpBlock;
    private $collection;
    private $_usedProperties = [];

    /**
     * @default '@MopaBootstrap/Form/fields.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function templating($value): static
    {
        $this->_usedProperties['templating'] = true;
        $this->templating = $value;

        return $this;
    }

    /**
     * Default form layout
     * @default 'horizontal'
     * @param ParamConfigurator|false|'horizontal'|'inline' $value
     * @return $this
     */
    public function layout($value): static
    {
        $this->_usedProperties['layout'] = true;
        $this->layout = $value;

        return $this;
    }

    /**
     * @default 'col-sm-3'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function horizontalLabelClass($value): static
    {
        $this->_usedProperties['horizontalLabelClass'] = true;
        $this->horizontalLabelClass = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function horizontalLabelDivClass($value): static
    {
        $this->_usedProperties['horizontalLabelDivClass'] = true;
        $this->horizontalLabelDivClass = $value;

        return $this;
    }

    /**
     * @default 'col-sm-offset-3'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function horizontalLabelOffsetClass($value): static
    {
        $this->_usedProperties['horizontalLabelOffsetClass'] = true;
        $this->horizontalLabelOffsetClass = $value;

        return $this;
    }

    /**
     * @default 'col-sm-9'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function horizontalInputWrapperClass($value): static
    {
        $this->_usedProperties['horizontalInputWrapperClass'] = true;
        $this->horizontalInputWrapperClass = $value;

        return $this;
    }

    /**
     * @default {"year":"col-xs-4","month":"col-xs-4","day":"col-xs-4"}
    */
    public function dateWrapperClass(array $value = []): \Symfony\Config\MopaBootstrap\Form\DateWrapperClassConfig
    {
        if (null === $this->dateWrapperClass) {
            $this->_usedProperties['dateWrapperClass'] = true;
            $this->dateWrapperClass = new \Symfony\Config\MopaBootstrap\Form\DateWrapperClassConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "dateWrapperClass()" has already been initialized. You cannot pass values the second time you call dateWrapperClass().');
        }

        return $this->dateWrapperClass;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function renderFieldset($value): static
    {
        $this->_usedProperties['renderFieldset'] = true;
        $this->renderFieldset = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function renderCollectionItem($value): static
    {
        $this->_usedProperties['renderCollectionItem'] = true;
        $this->renderCollectionItem = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function showLegend($value): static
    {
        $this->_usedProperties['showLegend'] = true;
        $this->showLegend = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function showChildLegend($value): static
    {
        $this->_usedProperties['showChildLegend'] = true;
        $this->showChildLegend = $value;

        return $this;
    }

    /**
     * @default 'legend'
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function legendTag($value): static
    {
        $this->_usedProperties['legendTag'] = true;
        $this->legendTag = $value;

        return $this;
    }

    /**
     * @default 'both'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function checkboxLabel($value): static
    {
        $this->_usedProperties['checkboxLabel'] = true;
        $this->checkboxLabel = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function renderOptionalText($value): static
    {
        $this->_usedProperties['renderOptionalText'] = true;
        $this->renderOptionalText = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function renderRequiredAsterisk($value): static
    {
        $this->_usedProperties['renderRequiredAsterisk'] = true;
        $this->renderRequiredAsterisk = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function errorType($value): static
    {
        $this->_usedProperties['errorType'] = true;
        $this->errorType = $value;

        return $this;
    }

    /**
     * @default {"class":"nav nav-tabs"}
    */
    public function tabs(array $value = []): \Symfony\Config\MopaBootstrap\Form\TabsConfig
    {
        if (null === $this->tabs) {
            $this->_usedProperties['tabs'] = true;
            $this->tabs = new \Symfony\Config\MopaBootstrap\Form\TabsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "tabs()" has already been initialized. You cannot pass values the second time you call tabs().');
        }

        return $this->tabs;
    }

    /**
     * @default {"popover":{"title":null,"content":null,"trigger":"hover","toggle":"popover","placement":"right","selector":null}}
    */
    public function helpWidget(array $value = []): \Symfony\Config\MopaBootstrap\Form\HelpWidgetConfig
    {
        if (null === $this->helpWidget) {
            $this->_usedProperties['helpWidget'] = true;
            $this->helpWidget = new \Symfony\Config\MopaBootstrap\Form\HelpWidgetConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "helpWidget()" has already been initialized. You cannot pass values the second time you call helpWidget().');
        }

        return $this->helpWidget;
    }

    /**
     * @default {"tooltip":{"title":null,"text":null,"icon":"info-sign","placement":"top"},"popover":{"title":null,"content":null,"text":null,"icon":"info-sign","placement":"top"}}
    */
    public function helpLabel(array $value = []): \Symfony\Config\MopaBootstrap\Form\HelpLabelConfig
    {
        if (null === $this->helpLabel) {
            $this->_usedProperties['helpLabel'] = true;
            $this->helpLabel = new \Symfony\Config\MopaBootstrap\Form\HelpLabelConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "helpLabel()" has already been initialized. You cannot pass values the second time you call helpLabel().');
        }

        return $this->helpLabel;
    }

    /**
     * @default {"tooltip":{"title":null,"text":null,"icon":"info-sign","placement":"top"},"popover":{"title":null,"content":null,"text":null,"icon":"info-sign","placement":"top"}}
    */
    public function helpBlock(array $value = []): \Symfony\Config\MopaBootstrap\Form\HelpBlockConfig
    {
        if (null === $this->helpBlock) {
            $this->_usedProperties['helpBlock'] = true;
            $this->helpBlock = new \Symfony\Config\MopaBootstrap\Form\HelpBlockConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "helpBlock()" has already been initialized. You cannot pass values the second time you call helpBlock().');
        }

        return $this->helpBlock;
    }

    /**
     * @default {"widget_remove_btn":{"attr":{"class":"btn btn-default"},"wrapper_div":{"class":"form-group"},"horizontal_wrapper_div":{"class":"col-sm-3 col-sm-offset-3"},"label":"remove_item","translation_domain":null,"icon":null,"icon_inverted":false},"widget_add_btn":{"attr":{"class":"btn btn-default"},"label":"add_item","translation_domain":null,"icon":null,"icon_inverted":false}}
    */
    public function collection(array $value = []): \Symfony\Config\MopaBootstrap\Form\CollectionConfig
    {
        if (null === $this->collection) {
            $this->_usedProperties['collection'] = true;
            $this->collection = new \Symfony\Config\MopaBootstrap\Form\CollectionConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "collection()" has already been initialized. You cannot pass values the second time you call collection().');
        }

        return $this->collection;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('templating', $value)) {
            $this->_usedProperties['templating'] = true;
            $this->templating = $value['templating'];
            unset($value['templating']);
        }

        if (array_key_exists('layout', $value)) {
            $this->_usedProperties['layout'] = true;
            $this->layout = $value['layout'];
            unset($value['layout']);
        }

        if (array_key_exists('horizontal_label_class', $value)) {
            $this->_usedProperties['horizontalLabelClass'] = true;
            $this->horizontalLabelClass = $value['horizontal_label_class'];
            unset($value['horizontal_label_class']);
        }

        if (array_key_exists('horizontal_label_div_class', $value)) {
            $this->_usedProperties['horizontalLabelDivClass'] = true;
            $this->horizontalLabelDivClass = $value['horizontal_label_div_class'];
            unset($value['horizontal_label_div_class']);
        }

        if (array_key_exists('horizontal_label_offset_class', $value)) {
            $this->_usedProperties['horizontalLabelOffsetClass'] = true;
            $this->horizontalLabelOffsetClass = $value['horizontal_label_offset_class'];
            unset($value['horizontal_label_offset_class']);
        }

        if (array_key_exists('horizontal_input_wrapper_class', $value)) {
            $this->_usedProperties['horizontalInputWrapperClass'] = true;
            $this->horizontalInputWrapperClass = $value['horizontal_input_wrapper_class'];
            unset($value['horizontal_input_wrapper_class']);
        }

        if (array_key_exists('date_wrapper_class', $value)) {
            $this->_usedProperties['dateWrapperClass'] = true;
            $this->dateWrapperClass = new \Symfony\Config\MopaBootstrap\Form\DateWrapperClassConfig($value['date_wrapper_class']);
            unset($value['date_wrapper_class']);
        }

        if (array_key_exists('render_fieldset', $value)) {
            $this->_usedProperties['renderFieldset'] = true;
            $this->renderFieldset = $value['render_fieldset'];
            unset($value['render_fieldset']);
        }

        if (array_key_exists('render_collection_item', $value)) {
            $this->_usedProperties['renderCollectionItem'] = true;
            $this->renderCollectionItem = $value['render_collection_item'];
            unset($value['render_collection_item']);
        }

        if (array_key_exists('show_legend', $value)) {
            $this->_usedProperties['showLegend'] = true;
            $this->showLegend = $value['show_legend'];
            unset($value['show_legend']);
        }

        if (array_key_exists('show_child_legend', $value)) {
            $this->_usedProperties['showChildLegend'] = true;
            $this->showChildLegend = $value['show_child_legend'];
            unset($value['show_child_legend']);
        }

        if (array_key_exists('legend_tag', $value)) {
            $this->_usedProperties['legendTag'] = true;
            $this->legendTag = $value['legend_tag'];
            unset($value['legend_tag']);
        }

        if (array_key_exists('checkbox_label', $value)) {
            $this->_usedProperties['checkboxLabel'] = true;
            $this->checkboxLabel = $value['checkbox_label'];
            unset($value['checkbox_label']);
        }

        if (array_key_exists('render_optional_text', $value)) {
            $this->_usedProperties['renderOptionalText'] = true;
            $this->renderOptionalText = $value['render_optional_text'];
            unset($value['render_optional_text']);
        }

        if (array_key_exists('render_required_asterisk', $value)) {
            $this->_usedProperties['renderRequiredAsterisk'] = true;
            $this->renderRequiredAsterisk = $value['render_required_asterisk'];
            unset($value['render_required_asterisk']);
        }

        if (array_key_exists('error_type', $value)) {
            $this->_usedProperties['errorType'] = true;
            $this->errorType = $value['error_type'];
            unset($value['error_type']);
        }

        if (array_key_exists('tabs', $value)) {
            $this->_usedProperties['tabs'] = true;
            $this->tabs = new \Symfony\Config\MopaBootstrap\Form\TabsConfig($value['tabs']);
            unset($value['tabs']);
        }

        if (array_key_exists('help_widget', $value)) {
            $this->_usedProperties['helpWidget'] = true;
            $this->helpWidget = new \Symfony\Config\MopaBootstrap\Form\HelpWidgetConfig($value['help_widget']);
            unset($value['help_widget']);
        }

        if (array_key_exists('help_label', $value)) {
            $this->_usedProperties['helpLabel'] = true;
            $this->helpLabel = new \Symfony\Config\MopaBootstrap\Form\HelpLabelConfig($value['help_label']);
            unset($value['help_label']);
        }

        if (array_key_exists('help_block', $value)) {
            $this->_usedProperties['helpBlock'] = true;
            $this->helpBlock = new \Symfony\Config\MopaBootstrap\Form\HelpBlockConfig($value['help_block']);
            unset($value['help_block']);
        }

        if (array_key_exists('collection', $value)) {
            $this->_usedProperties['collection'] = true;
            $this->collection = new \Symfony\Config\MopaBootstrap\Form\CollectionConfig($value['collection']);
            unset($value['collection']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['templating'])) {
            $output['templating'] = $this->templating;
        }
        if (isset($this->_usedProperties['layout'])) {
            $output['layout'] = $this->layout;
        }
        if (isset($this->_usedProperties['horizontalLabelClass'])) {
            $output['horizontal_label_class'] = $this->horizontalLabelClass;
        }
        if (isset($this->_usedProperties['horizontalLabelDivClass'])) {
            $output['horizontal_label_div_class'] = $this->horizontalLabelDivClass;
        }
        if (isset($this->_usedProperties['horizontalLabelOffsetClass'])) {
            $output['horizontal_label_offset_class'] = $this->horizontalLabelOffsetClass;
        }
        if (isset($this->_usedProperties['horizontalInputWrapperClass'])) {
            $output['horizontal_input_wrapper_class'] = $this->horizontalInputWrapperClass;
        }
        if (isset($this->_usedProperties['dateWrapperClass'])) {
            $output['date_wrapper_class'] = $this->dateWrapperClass->toArray();
        }
        if (isset($this->_usedProperties['renderFieldset'])) {
            $output['render_fieldset'] = $this->renderFieldset;
        }
        if (isset($this->_usedProperties['renderCollectionItem'])) {
            $output['render_collection_item'] = $this->renderCollectionItem;
        }
        if (isset($this->_usedProperties['showLegend'])) {
            $output['show_legend'] = $this->showLegend;
        }
        if (isset($this->_usedProperties['showChildLegend'])) {
            $output['show_child_legend'] = $this->showChildLegend;
        }
        if (isset($this->_usedProperties['legendTag'])) {
            $output['legend_tag'] = $this->legendTag;
        }
        if (isset($this->_usedProperties['checkboxLabel'])) {
            $output['checkbox_label'] = $this->checkboxLabel;
        }
        if (isset($this->_usedProperties['renderOptionalText'])) {
            $output['render_optional_text'] = $this->renderOptionalText;
        }
        if (isset($this->_usedProperties['renderRequiredAsterisk'])) {
            $output['render_required_asterisk'] = $this->renderRequiredAsterisk;
        }
        if (isset($this->_usedProperties['errorType'])) {
            $output['error_type'] = $this->errorType;
        }
        if (isset($this->_usedProperties['tabs'])) {
            $output['tabs'] = $this->tabs->toArray();
        }
        if (isset($this->_usedProperties['helpWidget'])) {
            $output['help_widget'] = $this->helpWidget->toArray();
        }
        if (isset($this->_usedProperties['helpLabel'])) {
            $output['help_label'] = $this->helpLabel->toArray();
        }
        if (isset($this->_usedProperties['helpBlock'])) {
            $output['help_block'] = $this->helpBlock->toArray();
        }
        if (isset($this->_usedProperties['collection'])) {
            $output['collection'] = $this->collection->toArray();
        }

        return $output;
    }

}
