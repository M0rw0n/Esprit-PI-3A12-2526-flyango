<?php

namespace Symfony\Config;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SonataFormConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $formType;
    private $_usedProperties = [];

    /**
     * Must be one of standard, horizontal
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

    public function getExtensionAlias(): string
    {
        return 'sonata_form';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('form_type', $value)) {
            $this->_usedProperties['formType'] = true;
            $this->formType = $value['form_type'];
            unset($value['form_type']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['formType'])) {
            $output['form_type'] = $this->formType;
        }

        return $output;
    }

}
