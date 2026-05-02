<?php

namespace Symfony\Config\SonataAdmin;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DefaultAdminServicesConfig 
{
    private $modelManager;
    private $dataSource;
    private $fieldDescriptionFactory;
    private $formContractor;
    private $showBuilder;
    private $listBuilder;
    private $datagridBuilder;
    private $translator;
    private $configurationPool;
    private $routeGenerator;
    private $securityHandler;
    private $menuFactory;
    private $routeBuilder;
    private $labelTranslatorStrategy;
    private $pagerType;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function modelManager($value): static
    {
        $this->_usedProperties['modelManager'] = true;
        $this->modelManager = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function dataSource($value): static
    {
        $this->_usedProperties['dataSource'] = true;
        $this->dataSource = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function fieldDescriptionFactory($value): static
    {
        $this->_usedProperties['fieldDescriptionFactory'] = true;
        $this->fieldDescriptionFactory = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function formContractor($value): static
    {
        $this->_usedProperties['formContractor'] = true;
        $this->formContractor = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function showBuilder($value): static
    {
        $this->_usedProperties['showBuilder'] = true;
        $this->showBuilder = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function listBuilder($value): static
    {
        $this->_usedProperties['listBuilder'] = true;
        $this->listBuilder = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function datagridBuilder($value): static
    {
        $this->_usedProperties['datagridBuilder'] = true;
        $this->datagridBuilder = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function translator($value): static
    {
        $this->_usedProperties['translator'] = true;
        $this->translator = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function configurationPool($value): static
    {
        $this->_usedProperties['configurationPool'] = true;
        $this->configurationPool = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function routeGenerator($value): static
    {
        $this->_usedProperties['routeGenerator'] = true;
        $this->routeGenerator = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function securityHandler($value): static
    {
        $this->_usedProperties['securityHandler'] = true;
        $this->securityHandler = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function menuFactory($value): static
    {
        $this->_usedProperties['menuFactory'] = true;
        $this->menuFactory = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function routeBuilder($value): static
    {
        $this->_usedProperties['routeBuilder'] = true;
        $this->routeBuilder = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function labelTranslatorStrategy($value): static
    {
        $this->_usedProperties['labelTranslatorStrategy'] = true;
        $this->labelTranslatorStrategy = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function pagerType($value): static
    {
        $this->_usedProperties['pagerType'] = true;
        $this->pagerType = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('model_manager', $value)) {
            $this->_usedProperties['modelManager'] = true;
            $this->modelManager = $value['model_manager'];
            unset($value['model_manager']);
        }

        if (array_key_exists('data_source', $value)) {
            $this->_usedProperties['dataSource'] = true;
            $this->dataSource = $value['data_source'];
            unset($value['data_source']);
        }

        if (array_key_exists('field_description_factory', $value)) {
            $this->_usedProperties['fieldDescriptionFactory'] = true;
            $this->fieldDescriptionFactory = $value['field_description_factory'];
            unset($value['field_description_factory']);
        }

        if (array_key_exists('form_contractor', $value)) {
            $this->_usedProperties['formContractor'] = true;
            $this->formContractor = $value['form_contractor'];
            unset($value['form_contractor']);
        }

        if (array_key_exists('show_builder', $value)) {
            $this->_usedProperties['showBuilder'] = true;
            $this->showBuilder = $value['show_builder'];
            unset($value['show_builder']);
        }

        if (array_key_exists('list_builder', $value)) {
            $this->_usedProperties['listBuilder'] = true;
            $this->listBuilder = $value['list_builder'];
            unset($value['list_builder']);
        }

        if (array_key_exists('datagrid_builder', $value)) {
            $this->_usedProperties['datagridBuilder'] = true;
            $this->datagridBuilder = $value['datagrid_builder'];
            unset($value['datagrid_builder']);
        }

        if (array_key_exists('translator', $value)) {
            $this->_usedProperties['translator'] = true;
            $this->translator = $value['translator'];
            unset($value['translator']);
        }

        if (array_key_exists('configuration_pool', $value)) {
            $this->_usedProperties['configurationPool'] = true;
            $this->configurationPool = $value['configuration_pool'];
            unset($value['configuration_pool']);
        }

        if (array_key_exists('route_generator', $value)) {
            $this->_usedProperties['routeGenerator'] = true;
            $this->routeGenerator = $value['route_generator'];
            unset($value['route_generator']);
        }

        if (array_key_exists('security_handler', $value)) {
            $this->_usedProperties['securityHandler'] = true;
            $this->securityHandler = $value['security_handler'];
            unset($value['security_handler']);
        }

        if (array_key_exists('menu_factory', $value)) {
            $this->_usedProperties['menuFactory'] = true;
            $this->menuFactory = $value['menu_factory'];
            unset($value['menu_factory']);
        }

        if (array_key_exists('route_builder', $value)) {
            $this->_usedProperties['routeBuilder'] = true;
            $this->routeBuilder = $value['route_builder'];
            unset($value['route_builder']);
        }

        if (array_key_exists('label_translator_strategy', $value)) {
            $this->_usedProperties['labelTranslatorStrategy'] = true;
            $this->labelTranslatorStrategy = $value['label_translator_strategy'];
            unset($value['label_translator_strategy']);
        }

        if (array_key_exists('pager_type', $value)) {
            $this->_usedProperties['pagerType'] = true;
            $this->pagerType = $value['pager_type'];
            unset($value['pager_type']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['modelManager'])) {
            $output['model_manager'] = $this->modelManager;
        }
        if (isset($this->_usedProperties['dataSource'])) {
            $output['data_source'] = $this->dataSource;
        }
        if (isset($this->_usedProperties['fieldDescriptionFactory'])) {
            $output['field_description_factory'] = $this->fieldDescriptionFactory;
        }
        if (isset($this->_usedProperties['formContractor'])) {
            $output['form_contractor'] = $this->formContractor;
        }
        if (isset($this->_usedProperties['showBuilder'])) {
            $output['show_builder'] = $this->showBuilder;
        }
        if (isset($this->_usedProperties['listBuilder'])) {
            $output['list_builder'] = $this->listBuilder;
        }
        if (isset($this->_usedProperties['datagridBuilder'])) {
            $output['datagrid_builder'] = $this->datagridBuilder;
        }
        if (isset($this->_usedProperties['translator'])) {
            $output['translator'] = $this->translator;
        }
        if (isset($this->_usedProperties['configurationPool'])) {
            $output['configuration_pool'] = $this->configurationPool;
        }
        if (isset($this->_usedProperties['routeGenerator'])) {
            $output['route_generator'] = $this->routeGenerator;
        }
        if (isset($this->_usedProperties['securityHandler'])) {
            $output['security_handler'] = $this->securityHandler;
        }
        if (isset($this->_usedProperties['menuFactory'])) {
            $output['menu_factory'] = $this->menuFactory;
        }
        if (isset($this->_usedProperties['routeBuilder'])) {
            $output['route_builder'] = $this->routeBuilder;
        }
        if (isset($this->_usedProperties['labelTranslatorStrategy'])) {
            $output['label_translator_strategy'] = $this->labelTranslatorStrategy;
        }
        if (isset($this->_usedProperties['pagerType'])) {
            $output['pager_type'] = $this->pagerType;
        }

        return $output;
    }

}
