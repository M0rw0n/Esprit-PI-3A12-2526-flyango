<?php

namespace Symfony\Config\ExerciseHtmlPurifier;

require_once __DIR__.\DIRECTORY_SEPARATOR.'HtmlProfilesConfig'.\DIRECTORY_SEPARATOR.'ElementsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class HtmlProfilesConfig 
{
    private $config;
    private $attributes;
    private $elements;
    private $blankElements;
    private $parents;
    private $_usedProperties = [];

    /**
     * @return $this
     */
    public function config(string $parameter, mixed $value): static
    {
        $this->_usedProperties['config'] = true;
        $this->config[$parameter] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function attributes(string $tag_name, ParamConfigurator|array $value): static
    {
        $this->_usedProperties['attributes'] = true;
        $this->attributes[$tag_name] = $value;

        return $this;
    }

    /**
     * An array represents a definition, with three required elements: a type ("Inline", "Block", ...), a content type ("Empty", "Optional: #PCDATA", ...), an attributes set ("Core", "Common", ...), a fourth optional may define attributes rules as array, and fifth for forbidden attributes.
    */
    public function elements(string $tag_name, array $value = []): \Symfony\Config\ExerciseHtmlPurifier\HtmlProfilesConfig\ElementsConfig
    {
        if (!isset($this->elements[$tag_name])) {
            $this->_usedProperties['elements'] = true;
            $this->elements[$tag_name] = new \Symfony\Config\ExerciseHtmlPurifier\HtmlProfilesConfig\ElementsConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "elements()" has already been initialized. You cannot pass values the second time you call elements().');
        }

        return $this->elements[$tag_name];
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function blankElements(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['blankElements'] = true;
        $this->blankElements = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function parents(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['parents'] = true;
        $this->parents = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('config', $value)) {
            $this->_usedProperties['config'] = true;
            $this->config = $value['config'];
            unset($value['config']);
        }

        if (array_key_exists('attributes', $value)) {
            $this->_usedProperties['attributes'] = true;
            $this->attributes = $value['attributes'];
            unset($value['attributes']);
        }

        if (array_key_exists('elements', $value)) {
            $this->_usedProperties['elements'] = true;
            $this->elements = array_map(fn ($v) => new \Symfony\Config\ExerciseHtmlPurifier\HtmlProfilesConfig\ElementsConfig($v), $value['elements']);
            unset($value['elements']);
        }

        if (array_key_exists('blank_elements', $value)) {
            $this->_usedProperties['blankElements'] = true;
            $this->blankElements = $value['blank_elements'];
            unset($value['blank_elements']);
        }

        if (array_key_exists('parents', $value)) {
            $this->_usedProperties['parents'] = true;
            $this->parents = $value['parents'];
            unset($value['parents']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['config'])) {
            $output['config'] = $this->config;
        }
        if (isset($this->_usedProperties['attributes'])) {
            $output['attributes'] = $this->attributes;
        }
        if (isset($this->_usedProperties['elements'])) {
            $output['elements'] = array_map(fn ($v) => $v->toArray(), $this->elements);
        }
        if (isset($this->_usedProperties['blankElements'])) {
            $output['blank_elements'] = $this->blankElements;
        }
        if (isset($this->_usedProperties['parents'])) {
            $output['parents'] = $this->parents;
        }

        return $output;
    }

}
