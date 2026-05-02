<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataAdmin'.\DIRECTORY_SEPARATOR.'SecurityConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataAdmin'.\DIRECTORY_SEPARATOR.'GlobalSearchConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataAdmin'.\DIRECTORY_SEPARATOR.'BreadcrumbsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataAdmin'.\DIRECTORY_SEPARATOR.'OptionsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataAdmin'.\DIRECTORY_SEPARATOR.'DashboardConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataAdmin'.\DIRECTORY_SEPARATOR.'DefaultAdminServicesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataAdmin'.\DIRECTORY_SEPARATOR.'TemplatesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataAdmin'.\DIRECTORY_SEPARATOR.'AssetsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataAdmin'.\DIRECTORY_SEPARATOR.'ExtensionConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class SonataAdminConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $security;
    private $title;
    private $titleLogo;
    private $search;
    private $globalSearch;
    private $defaultController;
    private $breadcrumbs;
    private $options;
    private $dashboard;
    private $defaultAdminServices;
    private $templates;
    private $assets;
    private $extensions;
    private $persistFilters;
    private $filterPersister;
    private $showMosaicButton;
    private $_usedProperties = [];

    /**
     * @default {"handler":"sonata.admin.security.handler.noop","information":[],"admin_permissions":["CREATE","LIST","DELETE","UNDELETE","EXPORT","OPERATOR","MASTER"],"role_admin":"ROLE_SONATA_ADMIN","role_super_admin":"ROLE_SUPER_ADMIN","object_permissions":["VIEW","EDIT","HISTORY","DELETE","UNDELETE","OPERATOR","MASTER","OWNER"],"acl_user_manager":null}
    */
    public function security(array $value = []): \Symfony\Config\SonataAdmin\SecurityConfig
    {
        if (null === $this->security) {
            $this->_usedProperties['security'] = true;
            $this->security = new \Symfony\Config\SonataAdmin\SecurityConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "security()" has already been initialized. You cannot pass values the second time you call security().');
        }

        return $this->security;
    }

    /**
     * @default 'Sonata Admin'
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
     * @default 'bundles/sonataadmin/images/logo_title.png'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function titleLogo($value): static
    {
        $this->_usedProperties['titleLogo'] = true;
        $this->titleLogo = $value;

        return $this;
    }

    /**
     * Enable/disable the search form in the sidebar
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function search($value): static
    {
        $this->_usedProperties['search'] = true;
        $this->search = $value;

        return $this;
    }

    /**
     * @default {"empty_boxes":"show","admin_route":"show"}
    */
    public function globalSearch(array $value = []): \Symfony\Config\SonataAdmin\GlobalSearchConfig
    {
        if (null === $this->globalSearch) {
            $this->_usedProperties['globalSearch'] = true;
            $this->globalSearch = new \Symfony\Config\SonataAdmin\GlobalSearchConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "globalSearch()" has already been initialized. You cannot pass values the second time you call globalSearch().');
        }

        return $this->globalSearch;
    }

    /**
     * Name of the controller class to be used as a default in admin definitions
     * @default 'sonata.admin.controller.crud'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultController($value): static
    {
        $this->_usedProperties['defaultController'] = true;
        $this->defaultController = $value;

        return $this;
    }

    /**
     * @default {"child_admin_route":"show"}
    */
    public function breadcrumbs(array $value = []): \Symfony\Config\SonataAdmin\BreadcrumbsConfig
    {
        if (null === $this->breadcrumbs) {
            $this->_usedProperties['breadcrumbs'] = true;
            $this->breadcrumbs = new \Symfony\Config\SonataAdmin\BreadcrumbsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "breadcrumbs()" has already been initialized. You cannot pass values the second time you call breadcrumbs().');
        }

        return $this->breadcrumbs;
    }

    /**
     * @default {"html5_validate":true,"sort_admins":false,"confirm_exit":true,"js_debug":false,"skin":"skin-black","use_select2":true,"use_icheck":true,"use_bootlint":false,"use_stickyforms":true,"pager_links":null,"form_type":"standard","default_admin_route":"show","default_group":"default","default_label_catalogue":"SonataAdminBundle","default_translation_domain":null,"default_icon":"fas fa-folder","dropdown_number_groups_per_colums":2,"logo_content":"all","list_action_button_content":"all","lock_protection":false,"mosaic_background":"bundles\/sonataadmin\/images\/default_mosaic_image.png"}
    */
    public function options(array $value = []): \Symfony\Config\SonataAdmin\OptionsConfig
    {
        if (null === $this->options) {
            $this->_usedProperties['options'] = true;
            $this->options = new \Symfony\Config\SonataAdmin\OptionsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "options()" has already been initialized. You cannot pass values the second time you call options().');
        }

        return $this->options;
    }

    /**
     * @default {"groups":[],"blocks":[{"position":"left","settings":[],"type":"sonata.admin.block.admin_list","roles":[]}]}
    */
    public function dashboard(array $value = []): \Symfony\Config\SonataAdmin\DashboardConfig
    {
        if (null === $this->dashboard) {
            $this->_usedProperties['dashboard'] = true;
            $this->dashboard = new \Symfony\Config\SonataAdmin\DashboardConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "dashboard()" has already been initialized. You cannot pass values the second time you call dashboard().');
        }

        return $this->dashboard;
    }

    /**
     * @default {"model_manager":null,"data_source":null,"field_description_factory":null,"form_contractor":null,"show_builder":null,"list_builder":null,"datagrid_builder":null,"translator":null,"configuration_pool":null,"route_generator":null,"security_handler":null,"menu_factory":null,"route_builder":null,"label_translator_strategy":null,"pager_type":null}
    */
    public function defaultAdminServices(array $value = []): \Symfony\Config\SonataAdmin\DefaultAdminServicesConfig
    {
        if (null === $this->defaultAdminServices) {
            $this->_usedProperties['defaultAdminServices'] = true;
            $this->defaultAdminServices = new \Symfony\Config\SonataAdmin\DefaultAdminServicesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "defaultAdminServices()" has already been initialized. You cannot pass values the second time you call defaultAdminServices().');
        }

        return $this->defaultAdminServices;
    }

    /**
     * @default {"user_block":"@SonataAdmin\/Core\/user_block.html.twig","add_block":"@SonataAdmin\/Core\/add_block.html.twig","layout":"@SonataAdmin\/standard_layout.html.twig","ajax":"@SonataAdmin\/ajax_layout.html.twig","dashboard":"@SonataAdmin\/Core\/dashboard.html.twig","search":"@SonataAdmin\/Core\/search.html.twig","list":"@SonataAdmin\/CRUD\/list.html.twig","filter":"@SonataAdmin\/Form\/filter_admin_fields.html.twig","show":"@SonataAdmin\/CRUD\/show.html.twig","show_compare":"@SonataAdmin\/CRUD\/show_compare.html.twig","edit":"@SonataAdmin\/CRUD\/edit.html.twig","preview":"@SonataAdmin\/CRUD\/preview.html.twig","history":"@SonataAdmin\/CRUD\/history.html.twig","acl":"@SonataAdmin\/CRUD\/acl.html.twig","history_revision_timestamp":"@SonataAdmin\/CRUD\/history_revision_timestamp.html.twig","action":"@SonataAdmin\/CRUD\/action.html.twig","select":"@SonataAdmin\/CRUD\/list__select.html.twig","list_block":"@SonataAdmin\/Block\/block_admin_list.html.twig","search_result_block":"@SonataAdmin\/Block\/block_search_result.html.twig","short_object_description":"@SonataAdmin\/Helper\/short-object-description.html.twig","delete":"@SonataAdmin\/CRUD\/delete.html.twig","batch":"@SonataAdmin\/CRUD\/list__batch.html.twig","batch_confirmation":"@SonataAdmin\/CRUD\/batch_confirmation.html.twig","inner_list_row":"@SonataAdmin\/CRUD\/list_inner_row.html.twig","outer_list_rows_mosaic":"@SonataAdmin\/CRUD\/list_outer_rows_mosaic.html.twig","outer_list_rows_list":"@SonataAdmin\/CRUD\/list_outer_rows_list.html.twig","outer_list_rows_tree":"@SonataAdmin\/CRUD\/list_outer_rows_tree.html.twig","base_list_field":"@SonataAdmin\/CRUD\/base_list_field.html.twig","pager_links":"@SonataAdmin\/Pager\/links.html.twig","pager_results":"@SonataAdmin\/Pager\/results.html.twig","tab_menu_template":"@SonataAdmin\/Core\/tab_menu_template.html.twig","knp_menu_template":"@SonataAdmin\/Menu\/sonata_menu.html.twig","action_create":"@SonataAdmin\/CRUD\/dashboard__action_create.html.twig","button_acl":"@SonataAdmin\/Button\/acl_button.html.twig","button_create":"@SonataAdmin\/Button\/create_button.html.twig","button_edit":"@SonataAdmin\/Button\/edit_button.html.twig","button_history":"@SonataAdmin\/Button\/history_button.html.twig","button_list":"@SonataAdmin\/Button\/list_button.html.twig","button_show":"@SonataAdmin\/Button\/show_button.html.twig","form_theme":[],"filter_theme":[]}
    */
    public function templates(array $value = []): \Symfony\Config\SonataAdmin\TemplatesConfig
    {
        if (null === $this->templates) {
            $this->_usedProperties['templates'] = true;
            $this->templates = new \Symfony\Config\SonataAdmin\TemplatesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "templates()" has already been initialized. You cannot pass values the second time you call templates().');
        }

        return $this->templates;
    }

    /**
     * @default {"stylesheets":[{"path":"bundles\/sonataadmin\/app.css","package_name":"sonata_admin"},{"path":"bundles\/sonataform\/app.css","package_name":"sonata_admin"}],"extra_stylesheets":[],"remove_stylesheets":[],"javascripts":[{"path":"bundles\/sonataadmin\/app.js","package_name":"sonata_admin"},{"path":"bundles\/sonataform\/app.js","package_name":"sonata_admin"}],"extra_javascripts":[],"remove_javascripts":[]}
    */
    public function assets(array $value = []): \Symfony\Config\SonataAdmin\AssetsConfig
    {
        if (null === $this->assets) {
            $this->_usedProperties['assets'] = true;
            $this->assets = new \Symfony\Config\SonataAdmin\AssetsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "assets()" has already been initialized. You cannot pass values the second time you call assets().');
        }

        return $this->assets;
    }

    public function extension(string $id, array $value = []): \Symfony\Config\SonataAdmin\ExtensionConfig
    {
        if (!isset($this->extensions[$id])) {
            $this->_usedProperties['extensions'] = true;
            $this->extensions[$id] = new \Symfony\Config\SonataAdmin\ExtensionConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "extension()" has already been initialized. You cannot pass values the second time you call extension().');
        }

        return $this->extensions[$id];
    }

    /**
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function persistFilters($value): static
    {
        $this->_usedProperties['persistFilters'] = true;
        $this->persistFilters = $value;

        return $this;
    }

    /**
     * @default 'sonata.admin.filter_persister.session'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function filterPersister($value): static
    {
        $this->_usedProperties['filterPersister'] = true;
        $this->filterPersister = $value;

        return $this;
    }

    /**
     * Show mosaic button on all admin screens
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function showMosaicButton($value): static
    {
        $this->_usedProperties['showMosaicButton'] = true;
        $this->showMosaicButton = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'sonata_admin';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('security', $value)) {
            $this->_usedProperties['security'] = true;
            $this->security = new \Symfony\Config\SonataAdmin\SecurityConfig($value['security']);
            unset($value['security']);
        }

        if (array_key_exists('title', $value)) {
            $this->_usedProperties['title'] = true;
            $this->title = $value['title'];
            unset($value['title']);
        }

        if (array_key_exists('title_logo', $value)) {
            $this->_usedProperties['titleLogo'] = true;
            $this->titleLogo = $value['title_logo'];
            unset($value['title_logo']);
        }

        if (array_key_exists('search', $value)) {
            $this->_usedProperties['search'] = true;
            $this->search = $value['search'];
            unset($value['search']);
        }

        if (array_key_exists('global_search', $value)) {
            $this->_usedProperties['globalSearch'] = true;
            $this->globalSearch = new \Symfony\Config\SonataAdmin\GlobalSearchConfig($value['global_search']);
            unset($value['global_search']);
        }

        if (array_key_exists('default_controller', $value)) {
            $this->_usedProperties['defaultController'] = true;
            $this->defaultController = $value['default_controller'];
            unset($value['default_controller']);
        }

        if (array_key_exists('breadcrumbs', $value)) {
            $this->_usedProperties['breadcrumbs'] = true;
            $this->breadcrumbs = new \Symfony\Config\SonataAdmin\BreadcrumbsConfig($value['breadcrumbs']);
            unset($value['breadcrumbs']);
        }

        if (array_key_exists('options', $value)) {
            $this->_usedProperties['options'] = true;
            $this->options = new \Symfony\Config\SonataAdmin\OptionsConfig($value['options']);
            unset($value['options']);
        }

        if (array_key_exists('dashboard', $value)) {
            $this->_usedProperties['dashboard'] = true;
            $this->dashboard = new \Symfony\Config\SonataAdmin\DashboardConfig($value['dashboard']);
            unset($value['dashboard']);
        }

        if (array_key_exists('default_admin_services', $value)) {
            $this->_usedProperties['defaultAdminServices'] = true;
            $this->defaultAdminServices = new \Symfony\Config\SonataAdmin\DefaultAdminServicesConfig($value['default_admin_services']);
            unset($value['default_admin_services']);
        }

        if (array_key_exists('templates', $value)) {
            $this->_usedProperties['templates'] = true;
            $this->templates = new \Symfony\Config\SonataAdmin\TemplatesConfig($value['templates']);
            unset($value['templates']);
        }

        if (array_key_exists('assets', $value)) {
            $this->_usedProperties['assets'] = true;
            $this->assets = new \Symfony\Config\SonataAdmin\AssetsConfig($value['assets']);
            unset($value['assets']);
        }

        if (array_key_exists('extensions', $value)) {
            $this->_usedProperties['extensions'] = true;
            $this->extensions = array_map(fn ($v) => new \Symfony\Config\SonataAdmin\ExtensionConfig($v), $value['extensions']);
            unset($value['extensions']);
        }

        if (array_key_exists('persist_filters', $value)) {
            $this->_usedProperties['persistFilters'] = true;
            $this->persistFilters = $value['persist_filters'];
            unset($value['persist_filters']);
        }

        if (array_key_exists('filter_persister', $value)) {
            $this->_usedProperties['filterPersister'] = true;
            $this->filterPersister = $value['filter_persister'];
            unset($value['filter_persister']);
        }

        if (array_key_exists('show_mosaic_button', $value)) {
            $this->_usedProperties['showMosaicButton'] = true;
            $this->showMosaicButton = $value['show_mosaic_button'];
            unset($value['show_mosaic_button']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['security'])) {
            $output['security'] = $this->security->toArray();
        }
        if (isset($this->_usedProperties['title'])) {
            $output['title'] = $this->title;
        }
        if (isset($this->_usedProperties['titleLogo'])) {
            $output['title_logo'] = $this->titleLogo;
        }
        if (isset($this->_usedProperties['search'])) {
            $output['search'] = $this->search;
        }
        if (isset($this->_usedProperties['globalSearch'])) {
            $output['global_search'] = $this->globalSearch->toArray();
        }
        if (isset($this->_usedProperties['defaultController'])) {
            $output['default_controller'] = $this->defaultController;
        }
        if (isset($this->_usedProperties['breadcrumbs'])) {
            $output['breadcrumbs'] = $this->breadcrumbs->toArray();
        }
        if (isset($this->_usedProperties['options'])) {
            $output['options'] = $this->options->toArray();
        }
        if (isset($this->_usedProperties['dashboard'])) {
            $output['dashboard'] = $this->dashboard->toArray();
        }
        if (isset($this->_usedProperties['defaultAdminServices'])) {
            $output['default_admin_services'] = $this->defaultAdminServices->toArray();
        }
        if (isset($this->_usedProperties['templates'])) {
            $output['templates'] = $this->templates->toArray();
        }
        if (isset($this->_usedProperties['assets'])) {
            $output['assets'] = $this->assets->toArray();
        }
        if (isset($this->_usedProperties['extensions'])) {
            $output['extensions'] = array_map(fn ($v) => $v->toArray(), $this->extensions);
        }
        if (isset($this->_usedProperties['persistFilters'])) {
            $output['persist_filters'] = $this->persistFilters;
        }
        if (isset($this->_usedProperties['filterPersister'])) {
            $output['filter_persister'] = $this->filterPersister;
        }
        if (isset($this->_usedProperties['showMosaicButton'])) {
            $output['show_mosaic_button'] = $this->showMosaicButton;
        }

        return $output;
    }

}
