<?php

namespace Symfony\Config\SonataAdmin;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class BreadcrumbsConfig 
{
    private $childAdminRoute;
    private $_usedProperties = [];

    /**
     * Change the default route used to generate the link to the parent object, when in a child admin
     * @default 'show'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function childAdminRoute($value): static
    {
        $this->_usedProperties['childAdminRoute'] = true;
        $this->childAdminRoute = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('child_admin_route', $value)) {
            $this->_usedProperties['childAdminRoute'] = true;
            $this->childAdminRoute = $value['child_admin_route'];
            unset($value['child_admin_route']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['childAdminRoute'])) {
            $output['child_admin_route'] = $this->childAdminRoute;
        }

        return $output;
    }

}
