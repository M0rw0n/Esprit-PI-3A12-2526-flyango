<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataBlock'.\DIRECTORY_SEPARATOR.'ProfilerConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataBlock'.\DIRECTORY_SEPARATOR.'TemplatesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataBlock'.\DIRECTORY_SEPARATOR.'ContainerConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataBlock'.\DIRECTORY_SEPARATOR.'BlockConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataBlock'.\DIRECTORY_SEPARATOR.'BlocksByClassConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataBlock'.\DIRECTORY_SEPARATOR.'ExceptionConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class SonataBlockConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $profiler;
    private $defaultContexts;
    private $contextManager;
    private $httpCache;
    private $templates;
    private $container;
    private $blocks;
    private $blocksByClass;
    private $exception;
    private $_usedProperties = [];

    /**
     * @default {"enabled":"%kernel.debug%","template":"@SonataBlock\/Profiler\/block.html.twig"}
    */
    public function profiler(array $value = []): \Symfony\Config\SonataBlock\ProfilerConfig
    {
        if (null === $this->profiler) {
            $this->_usedProperties['profiler'] = true;
            $this->profiler = new \Symfony\Config\SonataBlock\ProfilerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "profiler()" has already been initialized. You cannot pass values the second time you call profiler().');
        }

        return $this->profiler;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function defaultContexts(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['defaultContexts'] = true;
        $this->defaultContexts = $value;

        return $this;
    }

    /**
     * @default 'sonata.block.context_manager.default'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function contextManager($value): static
    {
        $this->_usedProperties['contextManager'] = true;
        $this->contextManager = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @deprecated The "http_cache" option is deprecated and not doing anything anymore since sonata-project/block-bundle 5.0. It will be removed in 6.0.
     * @return $this
     */
    public function httpCache($value): static
    {
        $this->_usedProperties['httpCache'] = true;
        $this->httpCache = $value;

        return $this;
    }

    /**
     * @default {"block_base":null,"block_container":null}
    */
    public function templates(array $value = []): \Symfony\Config\SonataBlock\TemplatesConfig
    {
        if (null === $this->templates) {
            $this->_usedProperties['templates'] = true;
            $this->templates = new \Symfony\Config\SonataBlock\TemplatesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "templates()" has already been initialized. You cannot pass values the second time you call templates().');
        }

        return $this->templates;
    }

    /**
     * block container configuration
     * @default {"types":["sonata.block.service.container","sonata.page.block.container","sonata.dashboard.block.container","cmf.block.container","cmf.block.slideshow"],"templates":{"SonataBlockBundle default template":"@SonataBlock\/Block\/block_container.html.twig"}}
    */
    public function container(array $value = []): \Symfony\Config\SonataBlock\ContainerConfig
    {
        if (null === $this->container) {
            $this->_usedProperties['container'] = true;
            $this->container = new \Symfony\Config\SonataBlock\ContainerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "container()" has already been initialized. You cannot pass values the second time you call container().');
        }

        return $this->container;
    }

    /**
     * configuration per block service
    */
    public function block(string $id, array $value = []): \Symfony\Config\SonataBlock\BlockConfig
    {
        if (!isset($this->blocks[$id])) {
            $this->_usedProperties['blocks'] = true;
            $this->blocks[$id] = new \Symfony\Config\SonataBlock\BlockConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "block()" has already been initialized. You cannot pass values the second time you call block().');
        }

        return $this->blocks[$id];
    }

    /**
     * configuration per block class
    */
    public function blocksByClass(string $class, array $value = []): \Symfony\Config\SonataBlock\BlocksByClassConfig
    {
        if (!isset($this->blocksByClass[$class])) {
            $this->_usedProperties['blocksByClass'] = true;
            $this->blocksByClass[$class] = new \Symfony\Config\SonataBlock\BlocksByClassConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "blocksByClass()" has already been initialized. You cannot pass values the second time you call blocksByClass().');
        }

        return $this->blocksByClass[$class];
    }

    /**
     * @default {"default":{"filter":"debug_only","renderer":"throw"},"filters":{"debug_only":"sonata.block.exception.filter.debug_only","ignore_block_exception":"sonata.block.exception.filter.ignore_block_exception","keep_all":"sonata.block.exception.filter.keep_all","keep_none":"sonata.block.exception.filter.keep_none"},"renderers":{"inline":"sonata.block.exception.renderer.inline","inline_debug":"sonata.block.exception.renderer.inline_debug","throw":"sonata.block.exception.renderer.throw"}}
    */
    public function exception(array $value = []): \Symfony\Config\SonataBlock\ExceptionConfig
    {
        if (null === $this->exception) {
            $this->_usedProperties['exception'] = true;
            $this->exception = new \Symfony\Config\SonataBlock\ExceptionConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "exception()" has already been initialized. You cannot pass values the second time you call exception().');
        }

        return $this->exception;
    }

    public function getExtensionAlias(): string
    {
        return 'sonata_block';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('profiler', $value)) {
            $this->_usedProperties['profiler'] = true;
            $this->profiler = new \Symfony\Config\SonataBlock\ProfilerConfig($value['profiler']);
            unset($value['profiler']);
        }

        if (array_key_exists('default_contexts', $value)) {
            $this->_usedProperties['defaultContexts'] = true;
            $this->defaultContexts = $value['default_contexts'];
            unset($value['default_contexts']);
        }

        if (array_key_exists('context_manager', $value)) {
            $this->_usedProperties['contextManager'] = true;
            $this->contextManager = $value['context_manager'];
            unset($value['context_manager']);
        }

        if (array_key_exists('http_cache', $value)) {
            $this->_usedProperties['httpCache'] = true;
            $this->httpCache = $value['http_cache'];
            unset($value['http_cache']);
        }

        if (array_key_exists('templates', $value)) {
            $this->_usedProperties['templates'] = true;
            $this->templates = new \Symfony\Config\SonataBlock\TemplatesConfig($value['templates']);
            unset($value['templates']);
        }

        if (array_key_exists('container', $value)) {
            $this->_usedProperties['container'] = true;
            $this->container = new \Symfony\Config\SonataBlock\ContainerConfig($value['container']);
            unset($value['container']);
        }

        if (array_key_exists('blocks', $value)) {
            $this->_usedProperties['blocks'] = true;
            $this->blocks = array_map(fn ($v) => new \Symfony\Config\SonataBlock\BlockConfig($v), $value['blocks']);
            unset($value['blocks']);
        }

        if (array_key_exists('blocks_by_class', $value)) {
            $this->_usedProperties['blocksByClass'] = true;
            $this->blocksByClass = array_map(fn ($v) => new \Symfony\Config\SonataBlock\BlocksByClassConfig($v), $value['blocks_by_class']);
            unset($value['blocks_by_class']);
        }

        if (array_key_exists('exception', $value)) {
            $this->_usedProperties['exception'] = true;
            $this->exception = new \Symfony\Config\SonataBlock\ExceptionConfig($value['exception']);
            unset($value['exception']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['profiler'])) {
            $output['profiler'] = $this->profiler->toArray();
        }
        if (isset($this->_usedProperties['defaultContexts'])) {
            $output['default_contexts'] = $this->defaultContexts;
        }
        if (isset($this->_usedProperties['contextManager'])) {
            $output['context_manager'] = $this->contextManager;
        }
        if (isset($this->_usedProperties['httpCache'])) {
            $output['http_cache'] = $this->httpCache;
        }
        if (isset($this->_usedProperties['templates'])) {
            $output['templates'] = $this->templates->toArray();
        }
        if (isset($this->_usedProperties['container'])) {
            $output['container'] = $this->container->toArray();
        }
        if (isset($this->_usedProperties['blocks'])) {
            $output['blocks'] = array_map(fn ($v) => $v->toArray(), $this->blocks);
        }
        if (isset($this->_usedProperties['blocksByClass'])) {
            $output['blocks_by_class'] = array_map(fn ($v) => $v->toArray(), $this->blocksByClass);
        }
        if (isset($this->_usedProperties['exception'])) {
            $output['exception'] = $this->exception->toArray();
        }

        return $output;
    }

}
