<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Datatables'.\DIRECTORY_SEPARATOR.'TemplateParametersConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DatatablesConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $languageFromCdn;
    private $persistState;
    private $method;
    private $options;
    private $renderer;
    private $template;
    private $templateParameters;
    private $translationDomain;
    private $_usedProperties = [];

    /**
     * Load i18n data from DataTables CDN or locally
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function languageFromCdn($value): static
    {
        $this->_usedProperties['languageFromCdn'] = true;
        $this->languageFromCdn = $value;

        return $this;
    }

    /**
     * Where to persist the current table state automatically
     * @default 'fragment'
     * @param ParamConfigurator|'none'|'query'|'fragment'|'local'|'session' $value
     * @return $this
     */
    public function persistState($value): static
    {
        $this->_usedProperties['persistState'] = true;
        $this->persistState = $value;

        return $this;
    }

    /**
     * Default HTTP method to be used for callbacks
     * @default 'POST'
     * @param ParamConfigurator|'GET'|'POST' $value
     * @return $this
     */
    public function method($value): static
    {
        $this->_usedProperties['method'] = true;
        $this->method = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function options(string $option, mixed $value): static
    {
        $this->_usedProperties['options'] = true;
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Default service used to render templates, built-in TwigRenderer uses global Twig environment
     * @default 'Omines\\DataTablesBundle\\Twig\\TwigRenderer'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function renderer($value): static
    {
        $this->_usedProperties['renderer'] = true;
        $this->renderer = $value;

        return $this;
    }

    /**
     * Default template to be used for DataTables HTML
     * @default '@DataTables/datatable_html.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function template($value): static
    {
        $this->_usedProperties['template'] = true;
        $this->template = $value;

        return $this;
    }

    /**
     * Default parameters to be passed to the template
     * @default {"className":"table table-bordered","columnFilter":null}
    */
    public function templateParameters(array $value = []): \Symfony\Config\Datatables\TemplateParametersConfig
    {
        if (null === $this->templateParameters) {
            $this->_usedProperties['templateParameters'] = true;
            $this->templateParameters = new \Symfony\Config\Datatables\TemplateParametersConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "templateParameters()" has already been initialized. You cannot pass values the second time you call templateParameters().');
        }

        return $this->templateParameters;
    }

    /**
     * Default translation domain to be used
     * @default 'messages'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function translationDomain($value): static
    {
        $this->_usedProperties['translationDomain'] = true;
        $this->translationDomain = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'datatables';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('language_from_cdn', $value)) {
            $this->_usedProperties['languageFromCdn'] = true;
            $this->languageFromCdn = $value['language_from_cdn'];
            unset($value['language_from_cdn']);
        }

        if (array_key_exists('persist_state', $value)) {
            $this->_usedProperties['persistState'] = true;
            $this->persistState = $value['persist_state'];
            unset($value['persist_state']);
        }

        if (array_key_exists('method', $value)) {
            $this->_usedProperties['method'] = true;
            $this->method = $value['method'];
            unset($value['method']);
        }

        if (array_key_exists('options', $value)) {
            $this->_usedProperties['options'] = true;
            $this->options = $value['options'];
            unset($value['options']);
        }

        if (array_key_exists('renderer', $value)) {
            $this->_usedProperties['renderer'] = true;
            $this->renderer = $value['renderer'];
            unset($value['renderer']);
        }

        if (array_key_exists('template', $value)) {
            $this->_usedProperties['template'] = true;
            $this->template = $value['template'];
            unset($value['template']);
        }

        if (array_key_exists('template_parameters', $value)) {
            $this->_usedProperties['templateParameters'] = true;
            $this->templateParameters = new \Symfony\Config\Datatables\TemplateParametersConfig($value['template_parameters']);
            unset($value['template_parameters']);
        }

        if (array_key_exists('translation_domain', $value)) {
            $this->_usedProperties['translationDomain'] = true;
            $this->translationDomain = $value['translation_domain'];
            unset($value['translation_domain']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['languageFromCdn'])) {
            $output['language_from_cdn'] = $this->languageFromCdn;
        }
        if (isset($this->_usedProperties['persistState'])) {
            $output['persist_state'] = $this->persistState;
        }
        if (isset($this->_usedProperties['method'])) {
            $output['method'] = $this->method;
        }
        if (isset($this->_usedProperties['options'])) {
            $output['options'] = $this->options;
        }
        if (isset($this->_usedProperties['renderer'])) {
            $output['renderer'] = $this->renderer;
        }
        if (isset($this->_usedProperties['template'])) {
            $output['template'] = $this->template;
        }
        if (isset($this->_usedProperties['templateParameters'])) {
            $output['template_parameters'] = $this->templateParameters->toArray();
        }
        if (isset($this->_usedProperties['translationDomain'])) {
            $output['translation_domain'] = $this->translationDomain;
        }

        return $output;
    }

}
