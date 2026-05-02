<?php

namespace Symfony\Config\SonataAdmin;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class OptionsConfig 
{
    private $html5Validate;
    private $sortAdmins;
    private $confirmExit;
    private $jsDebug;
    private $skin;
    private $useSelect2;
    private $useIcheck;
    private $useBootlint;
    private $useStickyforms;
    private $pagerLinks;
    private $formType;
    private $defaultAdminRoute;
    private $defaultGroup;
    private $defaultLabelCatalogue;
    private $defaultTranslationDomain;
    private $defaultIcon;
    private $dropdownNumberGroupsPerColums;
    private $logoContent;
    private $listActionButtonContent;
    private $lockProtection;
    private $mosaicBackground;
    private $_usedProperties = [];

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function html5Validate($value): static
    {
        $this->_usedProperties['html5Validate'] = true;
        $this->html5Validate = $value;

        return $this;
    }

    /**
     * Auto order groups and admins by label or id
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function sortAdmins($value): static
    {
        $this->_usedProperties['sortAdmins'] = true;
        $this->sortAdmins = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function confirmExit($value): static
    {
        $this->_usedProperties['confirmExit'] = true;
        $this->confirmExit = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function jsDebug($value): static
    {
        $this->_usedProperties['jsDebug'] = true;
        $this->jsDebug = $value;

        return $this;
    }

    /**
     * @default 'skin-black'
     * @param ParamConfigurator|'skin-black'|'skin-black-light'|'skin-blue'|'skin-blue-light'|'skin-green'|'skin-green-light'|'skin-purple'|'skin-purple-light'|'skin-red'|'skin-red-light'|'skin-yellow'|'skin-yellow-light' $value
     * @return $this
     */
    public function skin($value): static
    {
        $this->_usedProperties['skin'] = true;
        $this->skin = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function useSelect2($value): static
    {
        $this->_usedProperties['useSelect2'] = true;
        $this->useSelect2 = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function useIcheck($value): static
    {
        $this->_usedProperties['useIcheck'] = true;
        $this->useIcheck = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function useBootlint($value): static
    {
        $this->_usedProperties['useBootlint'] = true;
        $this->useBootlint = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function useStickyforms($value): static
    {
        $this->_usedProperties['useStickyforms'] = true;
        $this->useStickyforms = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function pagerLinks($value): static
    {
        $this->_usedProperties['pagerLinks'] = true;
        $this->pagerLinks = $value;

        return $this;
    }

    /**
     * @default 'standard'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function formType($value): static
    {
        $this->_usedProperties['formType'] = true;
        $this->formType = $value;

        return $this;
    }

    /**
     * Name of the admin route to be used as a default to generate the link to the object
     * @default 'show'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultAdminRoute($value): static
    {
        $this->_usedProperties['defaultAdminRoute'] = true;
        $this->defaultAdminRoute = $value;

        return $this;
    }

    /**
     * Group used for admin services if one isn't provided.
     * @default 'default'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultGroup($value): static
    {
        $this->_usedProperties['defaultGroup'] = true;
        $this->defaultGroup = $value;

        return $this;
    }

    /**
     * Label Catalogue used for admin services if one isn't provided.
     * @default 'SonataAdminBundle'
     * @param ParamConfigurator|mixed $value
     * @deprecated The "default_label_catalogue" node is deprecated, use "default_translation_domain" instead.
     * @return $this
     */
    public function defaultLabelCatalogue($value): static
    {
        $this->_usedProperties['defaultLabelCatalogue'] = true;
        $this->defaultLabelCatalogue = $value;

        return $this;
    }

    /**
     * Translation domain used for admin services if one isn't provided.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultTranslationDomain($value): static
    {
        $this->_usedProperties['defaultTranslationDomain'] = true;
        $this->defaultTranslationDomain = $value;

        return $this;
    }

    /**
     * Icon used for admin services if one isn't provided.
     * @default 'fas fa-folder'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultIcon($value): static
    {
        $this->_usedProperties['defaultIcon'] = true;
        $this->defaultIcon = $value;

        return $this;
    }

    /**
     * @default 2
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function dropdownNumberGroupsPerColums($value): static
    {
        $this->_usedProperties['dropdownNumberGroupsPerColums'] = true;
        $this->dropdownNumberGroupsPerColums = $value;

        return $this;
    }

    /**
     * @default 'all'
     * @param ParamConfigurator|'text'|'icon'|'all' $value
     * @return $this
     */
    public function logoContent($value): static
    {
        $this->_usedProperties['logoContent'] = true;
        $this->logoContent = $value;

        return $this;
    }

    /**
     * @default 'all'
     * @param ParamConfigurator|'text'|'icon'|'all' $value
     * @return $this
     */
    public function listActionButtonContent($value): static
    {
        $this->_usedProperties['listActionButtonContent'] = true;
        $this->listActionButtonContent = $value;

        return $this;
    }

    /**
     * Enable locking when editing an object, if the corresponding object manager supports it.
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function lockProtection($value): static
    {
        $this->_usedProperties['lockProtection'] = true;
        $this->lockProtection = $value;

        return $this;
    }

    /**
     * Background used in mosaic view
     * @default 'bundles/sonataadmin/images/default_mosaic_image.png'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function mosaicBackground($value): static
    {
        $this->_usedProperties['mosaicBackground'] = true;
        $this->mosaicBackground = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('html5_validate', $value)) {
            $this->_usedProperties['html5Validate'] = true;
            $this->html5Validate = $value['html5_validate'];
            unset($value['html5_validate']);
        }

        if (array_key_exists('sort_admins', $value)) {
            $this->_usedProperties['sortAdmins'] = true;
            $this->sortAdmins = $value['sort_admins'];
            unset($value['sort_admins']);
        }

        if (array_key_exists('confirm_exit', $value)) {
            $this->_usedProperties['confirmExit'] = true;
            $this->confirmExit = $value['confirm_exit'];
            unset($value['confirm_exit']);
        }

        if (array_key_exists('js_debug', $value)) {
            $this->_usedProperties['jsDebug'] = true;
            $this->jsDebug = $value['js_debug'];
            unset($value['js_debug']);
        }

        if (array_key_exists('skin', $value)) {
            $this->_usedProperties['skin'] = true;
            $this->skin = $value['skin'];
            unset($value['skin']);
        }

        if (array_key_exists('use_select2', $value)) {
            $this->_usedProperties['useSelect2'] = true;
            $this->useSelect2 = $value['use_select2'];
            unset($value['use_select2']);
        }

        if (array_key_exists('use_icheck', $value)) {
            $this->_usedProperties['useIcheck'] = true;
            $this->useIcheck = $value['use_icheck'];
            unset($value['use_icheck']);
        }

        if (array_key_exists('use_bootlint', $value)) {
            $this->_usedProperties['useBootlint'] = true;
            $this->useBootlint = $value['use_bootlint'];
            unset($value['use_bootlint']);
        }

        if (array_key_exists('use_stickyforms', $value)) {
            $this->_usedProperties['useStickyforms'] = true;
            $this->useStickyforms = $value['use_stickyforms'];
            unset($value['use_stickyforms']);
        }

        if (array_key_exists('pager_links', $value)) {
            $this->_usedProperties['pagerLinks'] = true;
            $this->pagerLinks = $value['pager_links'];
            unset($value['pager_links']);
        }

        if (array_key_exists('form_type', $value)) {
            $this->_usedProperties['formType'] = true;
            $this->formType = $value['form_type'];
            unset($value['form_type']);
        }

        if (array_key_exists('default_admin_route', $value)) {
            $this->_usedProperties['defaultAdminRoute'] = true;
            $this->defaultAdminRoute = $value['default_admin_route'];
            unset($value['default_admin_route']);
        }

        if (array_key_exists('default_group', $value)) {
            $this->_usedProperties['defaultGroup'] = true;
            $this->defaultGroup = $value['default_group'];
            unset($value['default_group']);
        }

        if (array_key_exists('default_label_catalogue', $value)) {
            $this->_usedProperties['defaultLabelCatalogue'] = true;
            $this->defaultLabelCatalogue = $value['default_label_catalogue'];
            unset($value['default_label_catalogue']);
        }

        if (array_key_exists('default_translation_domain', $value)) {
            $this->_usedProperties['defaultTranslationDomain'] = true;
            $this->defaultTranslationDomain = $value['default_translation_domain'];
            unset($value['default_translation_domain']);
        }

        if (array_key_exists('default_icon', $value)) {
            $this->_usedProperties['defaultIcon'] = true;
            $this->defaultIcon = $value['default_icon'];
            unset($value['default_icon']);
        }

        if (array_key_exists('dropdown_number_groups_per_colums', $value)) {
            $this->_usedProperties['dropdownNumberGroupsPerColums'] = true;
            $this->dropdownNumberGroupsPerColums = $value['dropdown_number_groups_per_colums'];
            unset($value['dropdown_number_groups_per_colums']);
        }

        if (array_key_exists('logo_content', $value)) {
            $this->_usedProperties['logoContent'] = true;
            $this->logoContent = $value['logo_content'];
            unset($value['logo_content']);
        }

        if (array_key_exists('list_action_button_content', $value)) {
            $this->_usedProperties['listActionButtonContent'] = true;
            $this->listActionButtonContent = $value['list_action_button_content'];
            unset($value['list_action_button_content']);
        }

        if (array_key_exists('lock_protection', $value)) {
            $this->_usedProperties['lockProtection'] = true;
            $this->lockProtection = $value['lock_protection'];
            unset($value['lock_protection']);
        }

        if (array_key_exists('mosaic_background', $value)) {
            $this->_usedProperties['mosaicBackground'] = true;
            $this->mosaicBackground = $value['mosaic_background'];
            unset($value['mosaic_background']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['html5Validate'])) {
            $output['html5_validate'] = $this->html5Validate;
        }
        if (isset($this->_usedProperties['sortAdmins'])) {
            $output['sort_admins'] = $this->sortAdmins;
        }
        if (isset($this->_usedProperties['confirmExit'])) {
            $output['confirm_exit'] = $this->confirmExit;
        }
        if (isset($this->_usedProperties['jsDebug'])) {
            $output['js_debug'] = $this->jsDebug;
        }
        if (isset($this->_usedProperties['skin'])) {
            $output['skin'] = $this->skin;
        }
        if (isset($this->_usedProperties['useSelect2'])) {
            $output['use_select2'] = $this->useSelect2;
        }
        if (isset($this->_usedProperties['useIcheck'])) {
            $output['use_icheck'] = $this->useIcheck;
        }
        if (isset($this->_usedProperties['useBootlint'])) {
            $output['use_bootlint'] = $this->useBootlint;
        }
        if (isset($this->_usedProperties['useStickyforms'])) {
            $output['use_stickyforms'] = $this->useStickyforms;
        }
        if (isset($this->_usedProperties['pagerLinks'])) {
            $output['pager_links'] = $this->pagerLinks;
        }
        if (isset($this->_usedProperties['formType'])) {
            $output['form_type'] = $this->formType;
        }
        if (isset($this->_usedProperties['defaultAdminRoute'])) {
            $output['default_admin_route'] = $this->defaultAdminRoute;
        }
        if (isset($this->_usedProperties['defaultGroup'])) {
            $output['default_group'] = $this->defaultGroup;
        }
        if (isset($this->_usedProperties['defaultLabelCatalogue'])) {
            $output['default_label_catalogue'] = $this->defaultLabelCatalogue;
        }
        if (isset($this->_usedProperties['defaultTranslationDomain'])) {
            $output['default_translation_domain'] = $this->defaultTranslationDomain;
        }
        if (isset($this->_usedProperties['defaultIcon'])) {
            $output['default_icon'] = $this->defaultIcon;
        }
        if (isset($this->_usedProperties['dropdownNumberGroupsPerColums'])) {
            $output['dropdown_number_groups_per_colums'] = $this->dropdownNumberGroupsPerColums;
        }
        if (isset($this->_usedProperties['logoContent'])) {
            $output['logo_content'] = $this->logoContent;
        }
        if (isset($this->_usedProperties['listActionButtonContent'])) {
            $output['list_action_button_content'] = $this->listActionButtonContent;
        }
        if (isset($this->_usedProperties['lockProtection'])) {
            $output['lock_protection'] = $this->lockProtection;
        }
        if (isset($this->_usedProperties['mosaicBackground'])) {
            $output['mosaic_background'] = $this->mosaicBackground;
        }

        return $output;
    }

}
