<?php

namespace Symfony\Config\SonataAdmin;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'StylesheetsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'ExtraStylesheetsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'RemoveStylesheetsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'JavascriptsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'ExtraJavascriptsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'RemoveJavascriptsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AssetsConfig 
{
    private $stylesheets;
    private $extraStylesheets;
    private $removeStylesheets;
    private $javascripts;
    private $extraJavascripts;
    private $removeJavascripts;
    private $_usedProperties = [];

    /**
     * @template TValue of mixed
     * @param TValue $value
     * @default [{"path":"bundles\/sonataadmin\/app.css","package_name":"sonata_admin"},{"path":"bundles\/sonataform\/app.css","package_name":"sonata_admin"}]
     * @return \Symfony\Config\SonataAdmin\Assets\StylesheetsConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\SonataAdmin\Assets\StylesheetsConfig : static)
     */
    public function stylesheets(mixed $value = []): \Symfony\Config\SonataAdmin\Assets\StylesheetsConfig|static
    {
        $this->_usedProperties['stylesheets'] = true;
        if (!\is_array($value)) {
            $this->stylesheets[] = $value;

            return $this;
        }

        return $this->stylesheets[] = new \Symfony\Config\SonataAdmin\Assets\StylesheetsConfig($value);
    }

    /**
     * @template TValue of mixed
     * @param TValue $value
     * stylesheets to add to the page
     * @return \Symfony\Config\SonataAdmin\Assets\ExtraStylesheetsConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\SonataAdmin\Assets\ExtraStylesheetsConfig : static)
     */
    public function extraStylesheets(mixed $value = []): \Symfony\Config\SonataAdmin\Assets\ExtraStylesheetsConfig|static
    {
        $this->_usedProperties['extraStylesheets'] = true;
        if (!\is_array($value)) {
            $this->extraStylesheets[] = $value;

            return $this;
        }

        return $this->extraStylesheets[] = new \Symfony\Config\SonataAdmin\Assets\ExtraStylesheetsConfig($value);
    }

    /**
     * @template TValue of mixed
     * @param TValue $value
     * stylesheets to remove from the page
     * @return \Symfony\Config\SonataAdmin\Assets\RemoveStylesheetsConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\SonataAdmin\Assets\RemoveStylesheetsConfig : static)
     */
    public function removeStylesheets(mixed $value = []): \Symfony\Config\SonataAdmin\Assets\RemoveStylesheetsConfig|static
    {
        $this->_usedProperties['removeStylesheets'] = true;
        if (!\is_array($value)) {
            $this->removeStylesheets[] = $value;

            return $this;
        }

        return $this->removeStylesheets[] = new \Symfony\Config\SonataAdmin\Assets\RemoveStylesheetsConfig($value);
    }

    /**
     * @template TValue of mixed
     * @param TValue $value
     * @default [{"path":"bundles\/sonataadmin\/app.js","package_name":"sonata_admin"},{"path":"bundles\/sonataform\/app.js","package_name":"sonata_admin"}]
     * @return \Symfony\Config\SonataAdmin\Assets\JavascriptsConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\SonataAdmin\Assets\JavascriptsConfig : static)
     */
    public function javascripts(mixed $value = []): \Symfony\Config\SonataAdmin\Assets\JavascriptsConfig|static
    {
        $this->_usedProperties['javascripts'] = true;
        if (!\is_array($value)) {
            $this->javascripts[] = $value;

            return $this;
        }

        return $this->javascripts[] = new \Symfony\Config\SonataAdmin\Assets\JavascriptsConfig($value);
    }

    /**
     * @template TValue of mixed
     * @param TValue $value
     * javascripts to add to the page
     * @return \Symfony\Config\SonataAdmin\Assets\ExtraJavascriptsConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\SonataAdmin\Assets\ExtraJavascriptsConfig : static)
     */
    public function extraJavascripts(mixed $value = []): \Symfony\Config\SonataAdmin\Assets\ExtraJavascriptsConfig|static
    {
        $this->_usedProperties['extraJavascripts'] = true;
        if (!\is_array($value)) {
            $this->extraJavascripts[] = $value;

            return $this;
        }

        return $this->extraJavascripts[] = new \Symfony\Config\SonataAdmin\Assets\ExtraJavascriptsConfig($value);
    }

    /**
     * @template TValue of mixed
     * @param TValue $value
     * javascripts to remove from the page
     * @return \Symfony\Config\SonataAdmin\Assets\RemoveJavascriptsConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\SonataAdmin\Assets\RemoveJavascriptsConfig : static)
     */
    public function removeJavascripts(mixed $value = []): \Symfony\Config\SonataAdmin\Assets\RemoveJavascriptsConfig|static
    {
        $this->_usedProperties['removeJavascripts'] = true;
        if (!\is_array($value)) {
            $this->removeJavascripts[] = $value;

            return $this;
        }

        return $this->removeJavascripts[] = new \Symfony\Config\SonataAdmin\Assets\RemoveJavascriptsConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('stylesheets', $value)) {
            $this->_usedProperties['stylesheets'] = true;
            $this->stylesheets = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\SonataAdmin\Assets\StylesheetsConfig($v) : $v, $value['stylesheets']);
            unset($value['stylesheets']);
        }

        if (array_key_exists('extra_stylesheets', $value)) {
            $this->_usedProperties['extraStylesheets'] = true;
            $this->extraStylesheets = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\SonataAdmin\Assets\ExtraStylesheetsConfig($v) : $v, $value['extra_stylesheets']);
            unset($value['extra_stylesheets']);
        }

        if (array_key_exists('remove_stylesheets', $value)) {
            $this->_usedProperties['removeStylesheets'] = true;
            $this->removeStylesheets = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\SonataAdmin\Assets\RemoveStylesheetsConfig($v) : $v, $value['remove_stylesheets']);
            unset($value['remove_stylesheets']);
        }

        if (array_key_exists('javascripts', $value)) {
            $this->_usedProperties['javascripts'] = true;
            $this->javascripts = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\SonataAdmin\Assets\JavascriptsConfig($v) : $v, $value['javascripts']);
            unset($value['javascripts']);
        }

        if (array_key_exists('extra_javascripts', $value)) {
            $this->_usedProperties['extraJavascripts'] = true;
            $this->extraJavascripts = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\SonataAdmin\Assets\ExtraJavascriptsConfig($v) : $v, $value['extra_javascripts']);
            unset($value['extra_javascripts']);
        }

        if (array_key_exists('remove_javascripts', $value)) {
            $this->_usedProperties['removeJavascripts'] = true;
            $this->removeJavascripts = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\SonataAdmin\Assets\RemoveJavascriptsConfig($v) : $v, $value['remove_javascripts']);
            unset($value['remove_javascripts']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['stylesheets'])) {
            $output['stylesheets'] = array_map(fn ($v) => $v instanceof \Symfony\Config\SonataAdmin\Assets\StylesheetsConfig ? $v->toArray() : $v, $this->stylesheets);
        }
        if (isset($this->_usedProperties['extraStylesheets'])) {
            $output['extra_stylesheets'] = array_map(fn ($v) => $v instanceof \Symfony\Config\SonataAdmin\Assets\ExtraStylesheetsConfig ? $v->toArray() : $v, $this->extraStylesheets);
        }
        if (isset($this->_usedProperties['removeStylesheets'])) {
            $output['remove_stylesheets'] = array_map(fn ($v) => $v instanceof \Symfony\Config\SonataAdmin\Assets\RemoveStylesheetsConfig ? $v->toArray() : $v, $this->removeStylesheets);
        }
        if (isset($this->_usedProperties['javascripts'])) {
            $output['javascripts'] = array_map(fn ($v) => $v instanceof \Symfony\Config\SonataAdmin\Assets\JavascriptsConfig ? $v->toArray() : $v, $this->javascripts);
        }
        if (isset($this->_usedProperties['extraJavascripts'])) {
            $output['extra_javascripts'] = array_map(fn ($v) => $v instanceof \Symfony\Config\SonataAdmin\Assets\ExtraJavascriptsConfig ? $v->toArray() : $v, $this->extraJavascripts);
        }
        if (isset($this->_usedProperties['removeJavascripts'])) {
            $output['remove_javascripts'] = array_map(fn ($v) => $v instanceof \Symfony\Config\SonataAdmin\Assets\RemoveJavascriptsConfig ? $v->toArray() : $v, $this->removeJavascripts);
        }

        return $output;
    }

}
