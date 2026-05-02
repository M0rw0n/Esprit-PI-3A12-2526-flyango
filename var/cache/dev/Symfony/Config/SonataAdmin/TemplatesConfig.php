<?php

namespace Symfony\Config\SonataAdmin;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class TemplatesConfig 
{
    private $userBlock;
    private $addBlock;
    private $layout;
    private $ajax;
    private $dashboard;
    private $search;
    private $list;
    private $filter;
    private $show;
    private $showCompare;
    private $edit;
    private $preview;
    private $history;
    private $acl;
    private $historyRevisionTimestamp;
    private $action;
    private $select;
    private $listBlock;
    private $searchResultBlock;
    private $shortObjectDescription;
    private $delete;
    private $batch;
    private $batchConfirmation;
    private $innerListRow;
    private $outerListRowsMosaic;
    private $outerListRowsList;
    private $outerListRowsTree;
    private $baseListField;
    private $pagerLinks;
    private $pagerResults;
    private $tabMenuTemplate;
    private $knpMenuTemplate;
    private $actionCreate;
    private $buttonAcl;
    private $buttonCreate;
    private $buttonEdit;
    private $buttonHistory;
    private $buttonList;
    private $buttonShow;
    private $formTheme;
    private $filterTheme;
    private $_usedProperties = [];

    /**
     * @default '@SonataAdmin/Core/user_block.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function userBlock($value): static
    {
        $this->_usedProperties['userBlock'] = true;
        $this->userBlock = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Core/add_block.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function addBlock($value): static
    {
        $this->_usedProperties['addBlock'] = true;
        $this->addBlock = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/standard_layout.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function layout($value): static
    {
        $this->_usedProperties['layout'] = true;
        $this->layout = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/ajax_layout.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function ajax($value): static
    {
        $this->_usedProperties['ajax'] = true;
        $this->ajax = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Core/dashboard.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function dashboard($value): static
    {
        $this->_usedProperties['dashboard'] = true;
        $this->dashboard = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Core/search.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function search($value): static
    {
        $this->_usedProperties['search'] = true;
        $this->search = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/list.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function list($value): static
    {
        $this->_usedProperties['list'] = true;
        $this->list = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Form/filter_admin_fields.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function filter($value): static
    {
        $this->_usedProperties['filter'] = true;
        $this->filter = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/show.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function show($value): static
    {
        $this->_usedProperties['show'] = true;
        $this->show = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/show_compare.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function showCompare($value): static
    {
        $this->_usedProperties['showCompare'] = true;
        $this->showCompare = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/edit.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function edit($value): static
    {
        $this->_usedProperties['edit'] = true;
        $this->edit = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/preview.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function preview($value): static
    {
        $this->_usedProperties['preview'] = true;
        $this->preview = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/history.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function history($value): static
    {
        $this->_usedProperties['history'] = true;
        $this->history = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/acl.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function acl($value): static
    {
        $this->_usedProperties['acl'] = true;
        $this->acl = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/history_revision_timestamp.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function historyRevisionTimestamp($value): static
    {
        $this->_usedProperties['historyRevisionTimestamp'] = true;
        $this->historyRevisionTimestamp = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/action.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function action($value): static
    {
        $this->_usedProperties['action'] = true;
        $this->action = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/list__select.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function select($value): static
    {
        $this->_usedProperties['select'] = true;
        $this->select = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Block/block_admin_list.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function listBlock($value): static
    {
        $this->_usedProperties['listBlock'] = true;
        $this->listBlock = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Block/block_search_result.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function searchResultBlock($value): static
    {
        $this->_usedProperties['searchResultBlock'] = true;
        $this->searchResultBlock = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Helper/short-object-description.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function shortObjectDescription($value): static
    {
        $this->_usedProperties['shortObjectDescription'] = true;
        $this->shortObjectDescription = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/delete.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function delete($value): static
    {
        $this->_usedProperties['delete'] = true;
        $this->delete = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/list__batch.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function batch($value): static
    {
        $this->_usedProperties['batch'] = true;
        $this->batch = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/batch_confirmation.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function batchConfirmation($value): static
    {
        $this->_usedProperties['batchConfirmation'] = true;
        $this->batchConfirmation = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/list_inner_row.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function innerListRow($value): static
    {
        $this->_usedProperties['innerListRow'] = true;
        $this->innerListRow = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/list_outer_rows_mosaic.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function outerListRowsMosaic($value): static
    {
        $this->_usedProperties['outerListRowsMosaic'] = true;
        $this->outerListRowsMosaic = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/list_outer_rows_list.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function outerListRowsList($value): static
    {
        $this->_usedProperties['outerListRowsList'] = true;
        $this->outerListRowsList = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/list_outer_rows_tree.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function outerListRowsTree($value): static
    {
        $this->_usedProperties['outerListRowsTree'] = true;
        $this->outerListRowsTree = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/base_list_field.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function baseListField($value): static
    {
        $this->_usedProperties['baseListField'] = true;
        $this->baseListField = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Pager/links.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function pagerLinks($value): static
    {
        $this->_usedProperties['pagerLinks'] = true;
        $this->pagerLinks = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Pager/results.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function pagerResults($value): static
    {
        $this->_usedProperties['pagerResults'] = true;
        $this->pagerResults = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Core/tab_menu_template.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function tabMenuTemplate($value): static
    {
        $this->_usedProperties['tabMenuTemplate'] = true;
        $this->tabMenuTemplate = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Menu/sonata_menu.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function knpMenuTemplate($value): static
    {
        $this->_usedProperties['knpMenuTemplate'] = true;
        $this->knpMenuTemplate = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/CRUD/dashboard__action_create.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function actionCreate($value): static
    {
        $this->_usedProperties['actionCreate'] = true;
        $this->actionCreate = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Button/acl_button.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function buttonAcl($value): static
    {
        $this->_usedProperties['buttonAcl'] = true;
        $this->buttonAcl = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Button/create_button.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function buttonCreate($value): static
    {
        $this->_usedProperties['buttonCreate'] = true;
        $this->buttonCreate = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Button/edit_button.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function buttonEdit($value): static
    {
        $this->_usedProperties['buttonEdit'] = true;
        $this->buttonEdit = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Button/history_button.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function buttonHistory($value): static
    {
        $this->_usedProperties['buttonHistory'] = true;
        $this->buttonHistory = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Button/list_button.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function buttonList($value): static
    {
        $this->_usedProperties['buttonList'] = true;
        $this->buttonList = $value;

        return $this;
    }

    /**
     * @default '@SonataAdmin/Button/show_button.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function buttonShow($value): static
    {
        $this->_usedProperties['buttonShow'] = true;
        $this->buttonShow = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function formTheme(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['formTheme'] = true;
        $this->formTheme = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function filterTheme(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['filterTheme'] = true;
        $this->filterTheme = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('user_block', $value)) {
            $this->_usedProperties['userBlock'] = true;
            $this->userBlock = $value['user_block'];
            unset($value['user_block']);
        }

        if (array_key_exists('add_block', $value)) {
            $this->_usedProperties['addBlock'] = true;
            $this->addBlock = $value['add_block'];
            unset($value['add_block']);
        }

        if (array_key_exists('layout', $value)) {
            $this->_usedProperties['layout'] = true;
            $this->layout = $value['layout'];
            unset($value['layout']);
        }

        if (array_key_exists('ajax', $value)) {
            $this->_usedProperties['ajax'] = true;
            $this->ajax = $value['ajax'];
            unset($value['ajax']);
        }

        if (array_key_exists('dashboard', $value)) {
            $this->_usedProperties['dashboard'] = true;
            $this->dashboard = $value['dashboard'];
            unset($value['dashboard']);
        }

        if (array_key_exists('search', $value)) {
            $this->_usedProperties['search'] = true;
            $this->search = $value['search'];
            unset($value['search']);
        }

        if (array_key_exists('list', $value)) {
            $this->_usedProperties['list'] = true;
            $this->list = $value['list'];
            unset($value['list']);
        }

        if (array_key_exists('filter', $value)) {
            $this->_usedProperties['filter'] = true;
            $this->filter = $value['filter'];
            unset($value['filter']);
        }

        if (array_key_exists('show', $value)) {
            $this->_usedProperties['show'] = true;
            $this->show = $value['show'];
            unset($value['show']);
        }

        if (array_key_exists('show_compare', $value)) {
            $this->_usedProperties['showCompare'] = true;
            $this->showCompare = $value['show_compare'];
            unset($value['show_compare']);
        }

        if (array_key_exists('edit', $value)) {
            $this->_usedProperties['edit'] = true;
            $this->edit = $value['edit'];
            unset($value['edit']);
        }

        if (array_key_exists('preview', $value)) {
            $this->_usedProperties['preview'] = true;
            $this->preview = $value['preview'];
            unset($value['preview']);
        }

        if (array_key_exists('history', $value)) {
            $this->_usedProperties['history'] = true;
            $this->history = $value['history'];
            unset($value['history']);
        }

        if (array_key_exists('acl', $value)) {
            $this->_usedProperties['acl'] = true;
            $this->acl = $value['acl'];
            unset($value['acl']);
        }

        if (array_key_exists('history_revision_timestamp', $value)) {
            $this->_usedProperties['historyRevisionTimestamp'] = true;
            $this->historyRevisionTimestamp = $value['history_revision_timestamp'];
            unset($value['history_revision_timestamp']);
        }

        if (array_key_exists('action', $value)) {
            $this->_usedProperties['action'] = true;
            $this->action = $value['action'];
            unset($value['action']);
        }

        if (array_key_exists('select', $value)) {
            $this->_usedProperties['select'] = true;
            $this->select = $value['select'];
            unset($value['select']);
        }

        if (array_key_exists('list_block', $value)) {
            $this->_usedProperties['listBlock'] = true;
            $this->listBlock = $value['list_block'];
            unset($value['list_block']);
        }

        if (array_key_exists('search_result_block', $value)) {
            $this->_usedProperties['searchResultBlock'] = true;
            $this->searchResultBlock = $value['search_result_block'];
            unset($value['search_result_block']);
        }

        if (array_key_exists('short_object_description', $value)) {
            $this->_usedProperties['shortObjectDescription'] = true;
            $this->shortObjectDescription = $value['short_object_description'];
            unset($value['short_object_description']);
        }

        if (array_key_exists('delete', $value)) {
            $this->_usedProperties['delete'] = true;
            $this->delete = $value['delete'];
            unset($value['delete']);
        }

        if (array_key_exists('batch', $value)) {
            $this->_usedProperties['batch'] = true;
            $this->batch = $value['batch'];
            unset($value['batch']);
        }

        if (array_key_exists('batch_confirmation', $value)) {
            $this->_usedProperties['batchConfirmation'] = true;
            $this->batchConfirmation = $value['batch_confirmation'];
            unset($value['batch_confirmation']);
        }

        if (array_key_exists('inner_list_row', $value)) {
            $this->_usedProperties['innerListRow'] = true;
            $this->innerListRow = $value['inner_list_row'];
            unset($value['inner_list_row']);
        }

        if (array_key_exists('outer_list_rows_mosaic', $value)) {
            $this->_usedProperties['outerListRowsMosaic'] = true;
            $this->outerListRowsMosaic = $value['outer_list_rows_mosaic'];
            unset($value['outer_list_rows_mosaic']);
        }

        if (array_key_exists('outer_list_rows_list', $value)) {
            $this->_usedProperties['outerListRowsList'] = true;
            $this->outerListRowsList = $value['outer_list_rows_list'];
            unset($value['outer_list_rows_list']);
        }

        if (array_key_exists('outer_list_rows_tree', $value)) {
            $this->_usedProperties['outerListRowsTree'] = true;
            $this->outerListRowsTree = $value['outer_list_rows_tree'];
            unset($value['outer_list_rows_tree']);
        }

        if (array_key_exists('base_list_field', $value)) {
            $this->_usedProperties['baseListField'] = true;
            $this->baseListField = $value['base_list_field'];
            unset($value['base_list_field']);
        }

        if (array_key_exists('pager_links', $value)) {
            $this->_usedProperties['pagerLinks'] = true;
            $this->pagerLinks = $value['pager_links'];
            unset($value['pager_links']);
        }

        if (array_key_exists('pager_results', $value)) {
            $this->_usedProperties['pagerResults'] = true;
            $this->pagerResults = $value['pager_results'];
            unset($value['pager_results']);
        }

        if (array_key_exists('tab_menu_template', $value)) {
            $this->_usedProperties['tabMenuTemplate'] = true;
            $this->tabMenuTemplate = $value['tab_menu_template'];
            unset($value['tab_menu_template']);
        }

        if (array_key_exists('knp_menu_template', $value)) {
            $this->_usedProperties['knpMenuTemplate'] = true;
            $this->knpMenuTemplate = $value['knp_menu_template'];
            unset($value['knp_menu_template']);
        }

        if (array_key_exists('action_create', $value)) {
            $this->_usedProperties['actionCreate'] = true;
            $this->actionCreate = $value['action_create'];
            unset($value['action_create']);
        }

        if (array_key_exists('button_acl', $value)) {
            $this->_usedProperties['buttonAcl'] = true;
            $this->buttonAcl = $value['button_acl'];
            unset($value['button_acl']);
        }

        if (array_key_exists('button_create', $value)) {
            $this->_usedProperties['buttonCreate'] = true;
            $this->buttonCreate = $value['button_create'];
            unset($value['button_create']);
        }

        if (array_key_exists('button_edit', $value)) {
            $this->_usedProperties['buttonEdit'] = true;
            $this->buttonEdit = $value['button_edit'];
            unset($value['button_edit']);
        }

        if (array_key_exists('button_history', $value)) {
            $this->_usedProperties['buttonHistory'] = true;
            $this->buttonHistory = $value['button_history'];
            unset($value['button_history']);
        }

        if (array_key_exists('button_list', $value)) {
            $this->_usedProperties['buttonList'] = true;
            $this->buttonList = $value['button_list'];
            unset($value['button_list']);
        }

        if (array_key_exists('button_show', $value)) {
            $this->_usedProperties['buttonShow'] = true;
            $this->buttonShow = $value['button_show'];
            unset($value['button_show']);
        }

        if (array_key_exists('form_theme', $value)) {
            $this->_usedProperties['formTheme'] = true;
            $this->formTheme = $value['form_theme'];
            unset($value['form_theme']);
        }

        if (array_key_exists('filter_theme', $value)) {
            $this->_usedProperties['filterTheme'] = true;
            $this->filterTheme = $value['filter_theme'];
            unset($value['filter_theme']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['userBlock'])) {
            $output['user_block'] = $this->userBlock;
        }
        if (isset($this->_usedProperties['addBlock'])) {
            $output['add_block'] = $this->addBlock;
        }
        if (isset($this->_usedProperties['layout'])) {
            $output['layout'] = $this->layout;
        }
        if (isset($this->_usedProperties['ajax'])) {
            $output['ajax'] = $this->ajax;
        }
        if (isset($this->_usedProperties['dashboard'])) {
            $output['dashboard'] = $this->dashboard;
        }
        if (isset($this->_usedProperties['search'])) {
            $output['search'] = $this->search;
        }
        if (isset($this->_usedProperties['list'])) {
            $output['list'] = $this->list;
        }
        if (isset($this->_usedProperties['filter'])) {
            $output['filter'] = $this->filter;
        }
        if (isset($this->_usedProperties['show'])) {
            $output['show'] = $this->show;
        }
        if (isset($this->_usedProperties['showCompare'])) {
            $output['show_compare'] = $this->showCompare;
        }
        if (isset($this->_usedProperties['edit'])) {
            $output['edit'] = $this->edit;
        }
        if (isset($this->_usedProperties['preview'])) {
            $output['preview'] = $this->preview;
        }
        if (isset($this->_usedProperties['history'])) {
            $output['history'] = $this->history;
        }
        if (isset($this->_usedProperties['acl'])) {
            $output['acl'] = $this->acl;
        }
        if (isset($this->_usedProperties['historyRevisionTimestamp'])) {
            $output['history_revision_timestamp'] = $this->historyRevisionTimestamp;
        }
        if (isset($this->_usedProperties['action'])) {
            $output['action'] = $this->action;
        }
        if (isset($this->_usedProperties['select'])) {
            $output['select'] = $this->select;
        }
        if (isset($this->_usedProperties['listBlock'])) {
            $output['list_block'] = $this->listBlock;
        }
        if (isset($this->_usedProperties['searchResultBlock'])) {
            $output['search_result_block'] = $this->searchResultBlock;
        }
        if (isset($this->_usedProperties['shortObjectDescription'])) {
            $output['short_object_description'] = $this->shortObjectDescription;
        }
        if (isset($this->_usedProperties['delete'])) {
            $output['delete'] = $this->delete;
        }
        if (isset($this->_usedProperties['batch'])) {
            $output['batch'] = $this->batch;
        }
        if (isset($this->_usedProperties['batchConfirmation'])) {
            $output['batch_confirmation'] = $this->batchConfirmation;
        }
        if (isset($this->_usedProperties['innerListRow'])) {
            $output['inner_list_row'] = $this->innerListRow;
        }
        if (isset($this->_usedProperties['outerListRowsMosaic'])) {
            $output['outer_list_rows_mosaic'] = $this->outerListRowsMosaic;
        }
        if (isset($this->_usedProperties['outerListRowsList'])) {
            $output['outer_list_rows_list'] = $this->outerListRowsList;
        }
        if (isset($this->_usedProperties['outerListRowsTree'])) {
            $output['outer_list_rows_tree'] = $this->outerListRowsTree;
        }
        if (isset($this->_usedProperties['baseListField'])) {
            $output['base_list_field'] = $this->baseListField;
        }
        if (isset($this->_usedProperties['pagerLinks'])) {
            $output['pager_links'] = $this->pagerLinks;
        }
        if (isset($this->_usedProperties['pagerResults'])) {
            $output['pager_results'] = $this->pagerResults;
        }
        if (isset($this->_usedProperties['tabMenuTemplate'])) {
            $output['tab_menu_template'] = $this->tabMenuTemplate;
        }
        if (isset($this->_usedProperties['knpMenuTemplate'])) {
            $output['knp_menu_template'] = $this->knpMenuTemplate;
        }
        if (isset($this->_usedProperties['actionCreate'])) {
            $output['action_create'] = $this->actionCreate;
        }
        if (isset($this->_usedProperties['buttonAcl'])) {
            $output['button_acl'] = $this->buttonAcl;
        }
        if (isset($this->_usedProperties['buttonCreate'])) {
            $output['button_create'] = $this->buttonCreate;
        }
        if (isset($this->_usedProperties['buttonEdit'])) {
            $output['button_edit'] = $this->buttonEdit;
        }
        if (isset($this->_usedProperties['buttonHistory'])) {
            $output['button_history'] = $this->buttonHistory;
        }
        if (isset($this->_usedProperties['buttonList'])) {
            $output['button_list'] = $this->buttonList;
        }
        if (isset($this->_usedProperties['buttonShow'])) {
            $output['button_show'] = $this->buttonShow;
        }
        if (isset($this->_usedProperties['formTheme'])) {
            $output['form_theme'] = $this->formTheme;
        }
        if (isset($this->_usedProperties['filterTheme'])) {
            $output['filter_theme'] = $this->filterTheme;
        }

        return $output;
    }

}
