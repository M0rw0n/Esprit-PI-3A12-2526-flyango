<?php

namespace Symfony\Config\MopaBootstrap\Form\HelpWidget;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PopoverConfig 
{
    private $title;
    private $content;
    private $trigger;
    private $toggle;
    private $placement;
    private $selector;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function title($value): static
    {
        $this->_usedProperties['title'] = true;
        $this->title = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function content($value): static
    {
        $this->_usedProperties['content'] = true;
        $this->content = $value;

        return $this;
    }

    /**
     * @default 'hover'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function trigger($value): static
    {
        $this->_usedProperties['trigger'] = true;
        $this->trigger = $value;

        return $this;
    }

    /**
     * @default 'popover'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function toggle($value): static
    {
        $this->_usedProperties['toggle'] = true;
        $this->toggle = $value;

        return $this;
    }

    /**
     * @default 'right'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function placement($value): static
    {
        $this->_usedProperties['placement'] = true;
        $this->placement = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function selector($value): static
    {
        $this->_usedProperties['selector'] = true;
        $this->selector = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('title', $value)) {
            $this->_usedProperties['title'] = true;
            $this->title = $value['title'];
            unset($value['title']);
        }

        if (array_key_exists('content', $value)) {
            $this->_usedProperties['content'] = true;
            $this->content = $value['content'];
            unset($value['content']);
        }

        if (array_key_exists('trigger', $value)) {
            $this->_usedProperties['trigger'] = true;
            $this->trigger = $value['trigger'];
            unset($value['trigger']);
        }

        if (array_key_exists('toggle', $value)) {
            $this->_usedProperties['toggle'] = true;
            $this->toggle = $value['toggle'];
            unset($value['toggle']);
        }

        if (array_key_exists('placement', $value)) {
            $this->_usedProperties['placement'] = true;
            $this->placement = $value['placement'];
            unset($value['placement']);
        }

        if (array_key_exists('selector', $value)) {
            $this->_usedProperties['selector'] = true;
            $this->selector = $value['selector'];
            unset($value['selector']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['title'])) {
            $output['title'] = $this->title;
        }
        if (isset($this->_usedProperties['content'])) {
            $output['content'] = $this->content;
        }
        if (isset($this->_usedProperties['trigger'])) {
            $output['trigger'] = $this->trigger;
        }
        if (isset($this->_usedProperties['toggle'])) {
            $output['toggle'] = $this->toggle;
        }
        if (isset($this->_usedProperties['placement'])) {
            $output['placement'] = $this->placement;
        }
        if (isset($this->_usedProperties['selector'])) {
            $output['selector'] = $this->selector;
        }

        return $output;
    }

}
