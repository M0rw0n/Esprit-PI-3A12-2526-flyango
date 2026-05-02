<?php

namespace Symfony\Config\MopaBootstrap\Form\HelpBlock;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PopoverConfig 
{
    private $title;
    private $content;
    private $text;
    private $icon;
    private $placement;
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
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function text($value): static
    {
        $this->_usedProperties['text'] = true;
        $this->text = $value;

        return $this;
    }

    /**
     * @default 'info-sign'
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
     * @default 'top'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function placement($value): static
    {
        $this->_usedProperties['placement'] = true;
        $this->placement = $value;

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

        if (array_key_exists('text', $value)) {
            $this->_usedProperties['text'] = true;
            $this->text = $value['text'];
            unset($value['text']);
        }

        if (array_key_exists('icon', $value)) {
            $this->_usedProperties['icon'] = true;
            $this->icon = $value['icon'];
            unset($value['icon']);
        }

        if (array_key_exists('placement', $value)) {
            $this->_usedProperties['placement'] = true;
            $this->placement = $value['placement'];
            unset($value['placement']);
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
        if (isset($this->_usedProperties['text'])) {
            $output['text'] = $this->text;
        }
        if (isset($this->_usedProperties['icon'])) {
            $output['icon'] = $this->icon;
        }
        if (isset($this->_usedProperties['placement'])) {
            $output['placement'] = $this->placement;
        }

        return $output;
    }

}
