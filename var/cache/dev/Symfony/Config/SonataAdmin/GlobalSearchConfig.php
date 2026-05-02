<?php

namespace Symfony\Config\SonataAdmin;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class GlobalSearchConfig 
{
    private $emptyBoxes;
    private $adminRoute;
    private $_usedProperties = [];

    /**
     * Perhaps one of the three options: show, fade, hide.
     * @default 'show'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function emptyBoxes($value): static
    {
        $this->_usedProperties['emptyBoxes'] = true;
        $this->emptyBoxes = $value;

        return $this;
    }

    /**
     * Change the default route used to generate the link to the object
     * @default 'show'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function adminRoute($value): static
    {
        $this->_usedProperties['adminRoute'] = true;
        $this->adminRoute = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('empty_boxes', $value)) {
            $this->_usedProperties['emptyBoxes'] = true;
            $this->emptyBoxes = $value['empty_boxes'];
            unset($value['empty_boxes']);
        }

        if (array_key_exists('admin_route', $value)) {
            $this->_usedProperties['adminRoute'] = true;
            $this->adminRoute = $value['admin_route'];
            unset($value['admin_route']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['emptyBoxes'])) {
            $output['empty_boxes'] = $this->emptyBoxes;
        }
        if (isset($this->_usedProperties['adminRoute'])) {
            $output['admin_route'] = $this->adminRoute;
        }

        return $output;
    }

}
