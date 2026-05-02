<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'MopaBootstrap'.\DIRECTORY_SEPARATOR.'FormConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'MopaBootstrap'.\DIRECTORY_SEPARATOR.'IconsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'MopaBootstrap'.\DIRECTORY_SEPARATOR.'MenuConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'MopaBootstrap'.\DIRECTORY_SEPARATOR.'NavbarConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'MopaBootstrap'.\DIRECTORY_SEPARATOR.'InitializrConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'MopaBootstrap'.\DIRECTORY_SEPARATOR.'FlashConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class MopaBootstrapConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $form;
    private $icons;
    private $menu;
    private $navbar;
    private $initializr;
    private $flash;
    private $_usedProperties = [];

    /**
     * @template TValue of mixed
     * @param TValue $value
     * @return \Symfony\Config\MopaBootstrap\FormConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\MopaBootstrap\FormConfig : static)
     */
    public function form(mixed $value = []): \Symfony\Config\MopaBootstrap\FormConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['form'] = true;
            $this->form = $value;

            return $this;
        }

        if (!$this->form instanceof \Symfony\Config\MopaBootstrap\FormConfig) {
            $this->_usedProperties['form'] = true;
            $this->form = new \Symfony\Config\MopaBootstrap\FormConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "form()" has already been initialized. You cannot pass values the second time you call form().');
        }

        return $this->form;
    }

    /**
     * @default {"icon_set":"glyphicons","shortcut":"icon"}
    */
    public function icons(array $value = []): \Symfony\Config\MopaBootstrap\IconsConfig
    {
        if (null === $this->icons) {
            $this->_usedProperties['icons'] = true;
            $this->icons = new \Symfony\Config\MopaBootstrap\IconsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "icons()" has already been initialized. You cannot pass values the second time you call icons().');
        }

        return $this->icons;
    }

    /**
     * @default {"enabled":false,"template":"@MopaBootstrap\/Menu\/menu.html.twig"}
    */
    public function menu(array $value = []): \Symfony\Config\MopaBootstrap\MenuConfig
    {
        if (null === $this->menu) {
            $this->_usedProperties['menu'] = true;
            $this->menu = new \Symfony\Config\MopaBootstrap\MenuConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "menu()" has already been initialized. You cannot pass values the second time you call menu().');
        }

        return $this->menu;
    }

    /**
     * @default {"enabled":false,"template":"@MopaBootstrap\/Menu\/menu.html.twig"}
    */
    public function navbar(array $value = []): \Symfony\Config\MopaBootstrap\NavbarConfig
    {
        if (null === $this->navbar) {
            $this->_usedProperties['navbar'] = true;
            $this->navbar = new \Symfony\Config\MopaBootstrap\NavbarConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "navbar()" has already been initialized. You cannot pass values the second time you call navbar().');
        }

        return $this->navbar;
    }

    public function initializr(array $value = []): \Symfony\Config\MopaBootstrap\InitializrConfig
    {
        if (null === $this->initializr) {
            $this->_usedProperties['initializr'] = true;
            $this->initializr = new \Symfony\Config\MopaBootstrap\InitializrConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "initializr()" has already been initialized. You cannot pass values the second time you call initializr().');
        }

        return $this->initializr;
    }

    /**
     * @default {"mapping":{"success":["success"],"danger":["error","danger"],"warning":["warning","warn"],"info":["info","notice"]}}
    */
    public function flash(array $value = []): \Symfony\Config\MopaBootstrap\FlashConfig
    {
        if (null === $this->flash) {
            $this->_usedProperties['flash'] = true;
            $this->flash = new \Symfony\Config\MopaBootstrap\FlashConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "flash()" has already been initialized. You cannot pass values the second time you call flash().');
        }

        return $this->flash;
    }

    public function getExtensionAlias(): string
    {
        return 'mopa_bootstrap';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('form', $value)) {
            $this->_usedProperties['form'] = true;
            $this->form = \is_array($value['form']) ? new \Symfony\Config\MopaBootstrap\FormConfig($value['form']) : $value['form'];
            unset($value['form']);
        }

        if (array_key_exists('icons', $value)) {
            $this->_usedProperties['icons'] = true;
            $this->icons = new \Symfony\Config\MopaBootstrap\IconsConfig($value['icons']);
            unset($value['icons']);
        }

        if (array_key_exists('menu', $value)) {
            $this->_usedProperties['menu'] = true;
            $this->menu = \is_array($value['menu']) ? new \Symfony\Config\MopaBootstrap\MenuConfig($value['menu']) : $value['menu'];
            unset($value['menu']);
        }

        if (array_key_exists('navbar', $value)) {
            $this->_usedProperties['navbar'] = true;
            $this->navbar = \is_array($value['navbar']) ? new \Symfony\Config\MopaBootstrap\NavbarConfig($value['navbar']) : $value['navbar'];
            unset($value['navbar']);
        }

        if (array_key_exists('initializr', $value)) {
            $this->_usedProperties['initializr'] = true;
            $this->initializr = new \Symfony\Config\MopaBootstrap\InitializrConfig($value['initializr']);
            unset($value['initializr']);
        }

        if (array_key_exists('flash', $value)) {
            $this->_usedProperties['flash'] = true;
            $this->flash = new \Symfony\Config\MopaBootstrap\FlashConfig($value['flash']);
            unset($value['flash']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['form'])) {
            $output['form'] = $this->form instanceof \Symfony\Config\MopaBootstrap\FormConfig ? $this->form->toArray() : $this->form;
        }
        if (isset($this->_usedProperties['icons'])) {
            $output['icons'] = $this->icons->toArray();
        }
        if (isset($this->_usedProperties['menu'])) {
            $output['menu'] = $this->menu instanceof \Symfony\Config\MopaBootstrap\MenuConfig ? $this->menu->toArray() : $this->menu;
        }
        if (isset($this->_usedProperties['navbar'])) {
            $output['navbar'] = $this->navbar instanceof \Symfony\Config\MopaBootstrap\NavbarConfig ? $this->navbar->toArray() : $this->navbar;
        }
        if (isset($this->_usedProperties['initializr'])) {
            $output['initializr'] = $this->initializr->toArray();
        }
        if (isset($this->_usedProperties['flash'])) {
            $output['flash'] = $this->flash->toArray();
        }

        return $output;
    }

}
