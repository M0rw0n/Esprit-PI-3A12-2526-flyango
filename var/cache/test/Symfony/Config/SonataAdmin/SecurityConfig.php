<?php

namespace Symfony\Config\SonataAdmin;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SecurityConfig 
{
    private $handler;
    private $information;
    private $adminPermissions;
    private $roleAdmin;
    private $roleSuperAdmin;
    private $objectPermissions;
    private $aclUserManager;
    private $_usedProperties = [];

    /**
     * @default 'sonata.admin.security.handler.noop'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function handler($value): static
    {
        $this->_usedProperties['handler'] = true;
        $this->handler = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function information(string $id, ParamConfigurator|string|array $value): static
    {
        $this->_usedProperties['information'] = true;
        $this->information[$id] = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function adminPermissions(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['adminPermissions'] = true;
        $this->adminPermissions = $value;

        return $this;
    }

    /**
     * Role which will see the top nav bar and dropdown groups regardless of its configuration
     * @default 'ROLE_SONATA_ADMIN'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function roleAdmin($value): static
    {
        $this->_usedProperties['roleAdmin'] = true;
        $this->roleAdmin = $value;

        return $this;
    }

    /**
     * Role which will perform all admin actions, see dashboard, menu and search groups regardless of its configuration
     * @default 'ROLE_SUPER_ADMIN'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function roleSuperAdmin($value): static
    {
        $this->_usedProperties['roleSuperAdmin'] = true;
        $this->roleSuperAdmin = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function objectPermissions(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['objectPermissions'] = true;
        $this->objectPermissions = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function aclUserManager($value): static
    {
        $this->_usedProperties['aclUserManager'] = true;
        $this->aclUserManager = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('handler', $value)) {
            $this->_usedProperties['handler'] = true;
            $this->handler = $value['handler'];
            unset($value['handler']);
        }

        if (array_key_exists('information', $value)) {
            $this->_usedProperties['information'] = true;
            $this->information = $value['information'];
            unset($value['information']);
        }

        if (array_key_exists('admin_permissions', $value)) {
            $this->_usedProperties['adminPermissions'] = true;
            $this->adminPermissions = $value['admin_permissions'];
            unset($value['admin_permissions']);
        }

        if (array_key_exists('role_admin', $value)) {
            $this->_usedProperties['roleAdmin'] = true;
            $this->roleAdmin = $value['role_admin'];
            unset($value['role_admin']);
        }

        if (array_key_exists('role_super_admin', $value)) {
            $this->_usedProperties['roleSuperAdmin'] = true;
            $this->roleSuperAdmin = $value['role_super_admin'];
            unset($value['role_super_admin']);
        }

        if (array_key_exists('object_permissions', $value)) {
            $this->_usedProperties['objectPermissions'] = true;
            $this->objectPermissions = $value['object_permissions'];
            unset($value['object_permissions']);
        }

        if (array_key_exists('acl_user_manager', $value)) {
            $this->_usedProperties['aclUserManager'] = true;
            $this->aclUserManager = $value['acl_user_manager'];
            unset($value['acl_user_manager']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['handler'])) {
            $output['handler'] = $this->handler;
        }
        if (isset($this->_usedProperties['information'])) {
            $output['information'] = $this->information;
        }
        if (isset($this->_usedProperties['adminPermissions'])) {
            $output['admin_permissions'] = $this->adminPermissions;
        }
        if (isset($this->_usedProperties['roleAdmin'])) {
            $output['role_admin'] = $this->roleAdmin;
        }
        if (isset($this->_usedProperties['roleSuperAdmin'])) {
            $output['role_super_admin'] = $this->roleSuperAdmin;
        }
        if (isset($this->_usedProperties['objectPermissions'])) {
            $output['object_permissions'] = $this->objectPermissions;
        }
        if (isset($this->_usedProperties['aclUserManager'])) {
            $output['acl_user_manager'] = $this->aclUserManager;
        }

        return $output;
    }

}
