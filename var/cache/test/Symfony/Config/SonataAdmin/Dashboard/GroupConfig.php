<?php

namespace Symfony\Config\SonataAdmin\Dashboard;

require_once __DIR__.\DIRECTORY_SEPARATOR.'GroupConfig'.\DIRECTORY_SEPARATOR.'ItemConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class GroupConfig 
{
    private $label;
    private $translationDomain;
    private $labelCatalogue;
    private $icon;
    private $onTop;
    private $keepOpen;
    private $provider;
    private $items;
    private $itemAdds;
    private $roles;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function label($value): static
    {
        $this->_usedProperties['label'] = true;
        $this->label = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function translationDomain($value): static
    {
        $this->_usedProperties['translationDomain'] = true;
        $this->translationDomain = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @deprecated The "label_catalogue" node is deprecated, use "translation_domain" instead.
     * @return $this
     */
    public function labelCatalogue($value): static
    {
        $this->_usedProperties['labelCatalogue'] = true;
        $this->labelCatalogue = $value;

        return $this;
    }

    /**
     * @default null
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
     * Show menu item in side dashboard menu without treeview
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function onTop($value): static
    {
        $this->_usedProperties['onTop'] = true;
        $this->onTop = $value;

        return $this;
    }

    /**
     * Keep menu group always open
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function keepOpen($value): static
    {
        $this->_usedProperties['keepOpen'] = true;
        $this->keepOpen = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function provider($value): static
    {
        $this->_usedProperties['provider'] = true;
        $this->provider = $value;

        return $this;
    }

    public function item(array $value = []): \Symfony\Config\SonataAdmin\Dashboard\GroupConfig\ItemConfig
    {
        $this->_usedProperties['items'] = true;

        return $this->items[] = new \Symfony\Config\SonataAdmin\Dashboard\GroupConfig\ItemConfig($value);
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function itemAdds(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['itemAdds'] = true;
        $this->itemAdds = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function roles(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['roles'] = true;
        $this->roles = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('label', $value)) {
            $this->_usedProperties['label'] = true;
            $this->label = $value['label'];
            unset($value['label']);
        }

        if (array_key_exists('translation_domain', $value)) {
            $this->_usedProperties['translationDomain'] = true;
            $this->translationDomain = $value['translation_domain'];
            unset($value['translation_domain']);
        }

        if (array_key_exists('label_catalogue', $value)) {
            $this->_usedProperties['labelCatalogue'] = true;
            $this->labelCatalogue = $value['label_catalogue'];
            unset($value['label_catalogue']);
        }

        if (array_key_exists('icon', $value)) {
            $this->_usedProperties['icon'] = true;
            $this->icon = $value['icon'];
            unset($value['icon']);
        }

        if (array_key_exists('on_top', $value)) {
            $this->_usedProperties['onTop'] = true;
            $this->onTop = $value['on_top'];
            unset($value['on_top']);
        }

        if (array_key_exists('keep_open', $value)) {
            $this->_usedProperties['keepOpen'] = true;
            $this->keepOpen = $value['keep_open'];
            unset($value['keep_open']);
        }

        if (array_key_exists('provider', $value)) {
            $this->_usedProperties['provider'] = true;
            $this->provider = $value['provider'];
            unset($value['provider']);
        }

        if (array_key_exists('items', $value)) {
            $this->_usedProperties['items'] = true;
            $this->items = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\SonataAdmin\Dashboard\GroupConfig\ItemConfig($v) : $v, $value['items']);
            unset($value['items']);
        }

        if (array_key_exists('item_adds', $value)) {
            $this->_usedProperties['itemAdds'] = true;
            $this->itemAdds = $value['item_adds'];
            unset($value['item_adds']);
        }

        if (array_key_exists('roles', $value)) {
            $this->_usedProperties['roles'] = true;
            $this->roles = $value['roles'];
            unset($value['roles']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['label'])) {
            $output['label'] = $this->label;
        }
        if (isset($this->_usedProperties['translationDomain'])) {
            $output['translation_domain'] = $this->translationDomain;
        }
        if (isset($this->_usedProperties['labelCatalogue'])) {
            $output['label_catalogue'] = $this->labelCatalogue;
        }
        if (isset($this->_usedProperties['icon'])) {
            $output['icon'] = $this->icon;
        }
        if (isset($this->_usedProperties['onTop'])) {
            $output['on_top'] = $this->onTop;
        }
        if (isset($this->_usedProperties['keepOpen'])) {
            $output['keep_open'] = $this->keepOpen;
        }
        if (isset($this->_usedProperties['provider'])) {
            $output['provider'] = $this->provider;
        }
        if (isset($this->_usedProperties['items'])) {
            $output['items'] = array_map(fn ($v) => $v instanceof \Symfony\Config\SonataAdmin\Dashboard\GroupConfig\ItemConfig ? $v->toArray() : $v, $this->items);
        }
        if (isset($this->_usedProperties['itemAdds'])) {
            $output['item_adds'] = $this->itemAdds;
        }
        if (isset($this->_usedProperties['roles'])) {
            $output['roles'] = $this->roles;
        }

        return $output;
    }

}
