<?php

namespace Symfony\Config\MopaBootstrap;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Initializr'.\DIRECTORY_SEPARATOR.'MetaConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Initializr'.\DIRECTORY_SEPARATOR.'GoogleConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class InitializrConfig 
{
    private $meta;
    private $dnsPrefetch;
    private $google;
    private $diagnosticMode;
    private $_usedProperties = [];

    /**
     * @default {"title":"MopaBootstrapBundle","description":"MopaBootstrapBundle","keywords":"MopaBootstrapBundle, Twitter Bootstrap, HTML5 Boilerplate","author_name":"My name","author_url":"#","nofollow":false,"noindex":false}
    */
    public function meta(array $value = []): \Symfony\Config\MopaBootstrap\Initializr\MetaConfig
    {
        if (null === $this->meta) {
            $this->_usedProperties['meta'] = true;
            $this->meta = new \Symfony\Config\MopaBootstrap\Initializr\MetaConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "meta()" has already been initialized. You cannot pass values the second time you call meta().');
        }

        return $this->meta;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function dnsPrefetch(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['dnsPrefetch'] = true;
        $this->dnsPrefetch = $value;

        return $this;
    }

    /**
     * @default {"extendedanalytics":false}
    */
    public function google(array $value = []): \Symfony\Config\MopaBootstrap\Initializr\GoogleConfig
    {
        if (null === $this->google) {
            $this->_usedProperties['google'] = true;
            $this->google = new \Symfony\Config\MopaBootstrap\Initializr\GoogleConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "google()" has already been initialized. You cannot pass values the second time you call google().');
        }

        return $this->google;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function diagnosticMode($value): static
    {
        $this->_usedProperties['diagnosticMode'] = true;
        $this->diagnosticMode = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('meta', $value)) {
            $this->_usedProperties['meta'] = true;
            $this->meta = new \Symfony\Config\MopaBootstrap\Initializr\MetaConfig($value['meta']);
            unset($value['meta']);
        }

        if (array_key_exists('dns_prefetch', $value)) {
            $this->_usedProperties['dnsPrefetch'] = true;
            $this->dnsPrefetch = $value['dns_prefetch'];
            unset($value['dns_prefetch']);
        }

        if (array_key_exists('google', $value)) {
            $this->_usedProperties['google'] = true;
            $this->google = new \Symfony\Config\MopaBootstrap\Initializr\GoogleConfig($value['google']);
            unset($value['google']);
        }

        if (array_key_exists('diagnostic_mode', $value)) {
            $this->_usedProperties['diagnosticMode'] = true;
            $this->diagnosticMode = $value['diagnostic_mode'];
            unset($value['diagnostic_mode']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['meta'])) {
            $output['meta'] = $this->meta->toArray();
        }
        if (isset($this->_usedProperties['dnsPrefetch'])) {
            $output['dns_prefetch'] = $this->dnsPrefetch;
        }
        if (isset($this->_usedProperties['google'])) {
            $output['google'] = $this->google->toArray();
        }
        if (isset($this->_usedProperties['diagnosticMode'])) {
            $output['diagnostic_mode'] = $this->diagnosticMode;
        }

        return $output;
    }

}
