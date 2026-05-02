<?php

namespace Symfony\Config\SonataAdmin;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ExtensionConfig 
{
    private $global;
    private $admins;
    private $excludes;
    private $implements;
    private $extends;
    private $instanceof;
    private $uses;
    private $adminImplements;
    private $adminExtends;
    private $adminInstanceof;
    private $adminUses;
    private $priority;
    private $_usedProperties = [];

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function global($value): static
    {
        $this->_usedProperties['global'] = true;
        $this->global = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function admins(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['admins'] = true;
        $this->admins = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function excludes(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['excludes'] = true;
        $this->excludes = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function implements(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['implements'] = true;
        $this->implements = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function extends(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['extends'] = true;
        $this->extends = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function instanceof(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['instanceof'] = true;
        $this->instanceof = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function uses(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['uses'] = true;
        $this->uses = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function adminImplements(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['adminImplements'] = true;
        $this->adminImplements = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function adminExtends(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['adminExtends'] = true;
        $this->adminExtends = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function adminInstanceof(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['adminInstanceof'] = true;
        $this->adminInstanceof = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function adminUses(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['adminUses'] = true;
        $this->adminUses = $value;

        return $this;
    }

    /**
     * Positive or negative integer. The higher the priority, the earlier it’s executed.
     * @default 0
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function priority($value): static
    {
        $this->_usedProperties['priority'] = true;
        $this->priority = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('global', $value)) {
            $this->_usedProperties['global'] = true;
            $this->global = $value['global'];
            unset($value['global']);
        }

        if (array_key_exists('admins', $value)) {
            $this->_usedProperties['admins'] = true;
            $this->admins = $value['admins'];
            unset($value['admins']);
        }

        if (array_key_exists('excludes', $value)) {
            $this->_usedProperties['excludes'] = true;
            $this->excludes = $value['excludes'];
            unset($value['excludes']);
        }

        if (array_key_exists('implements', $value)) {
            $this->_usedProperties['implements'] = true;
            $this->implements = $value['implements'];
            unset($value['implements']);
        }

        if (array_key_exists('extends', $value)) {
            $this->_usedProperties['extends'] = true;
            $this->extends = $value['extends'];
            unset($value['extends']);
        }

        if (array_key_exists('instanceof', $value)) {
            $this->_usedProperties['instanceof'] = true;
            $this->instanceof = $value['instanceof'];
            unset($value['instanceof']);
        }

        if (array_key_exists('uses', $value)) {
            $this->_usedProperties['uses'] = true;
            $this->uses = $value['uses'];
            unset($value['uses']);
        }

        if (array_key_exists('admin_implements', $value)) {
            $this->_usedProperties['adminImplements'] = true;
            $this->adminImplements = $value['admin_implements'];
            unset($value['admin_implements']);
        }

        if (array_key_exists('admin_extends', $value)) {
            $this->_usedProperties['adminExtends'] = true;
            $this->adminExtends = $value['admin_extends'];
            unset($value['admin_extends']);
        }

        if (array_key_exists('admin_instanceof', $value)) {
            $this->_usedProperties['adminInstanceof'] = true;
            $this->adminInstanceof = $value['admin_instanceof'];
            unset($value['admin_instanceof']);
        }

        if (array_key_exists('admin_uses', $value)) {
            $this->_usedProperties['adminUses'] = true;
            $this->adminUses = $value['admin_uses'];
            unset($value['admin_uses']);
        }

        if (array_key_exists('priority', $value)) {
            $this->_usedProperties['priority'] = true;
            $this->priority = $value['priority'];
            unset($value['priority']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['global'])) {
            $output['global'] = $this->global;
        }
        if (isset($this->_usedProperties['admins'])) {
            $output['admins'] = $this->admins;
        }
        if (isset($this->_usedProperties['excludes'])) {
            $output['excludes'] = $this->excludes;
        }
        if (isset($this->_usedProperties['implements'])) {
            $output['implements'] = $this->implements;
        }
        if (isset($this->_usedProperties['extends'])) {
            $output['extends'] = $this->extends;
        }
        if (isset($this->_usedProperties['instanceof'])) {
            $output['instanceof'] = $this->instanceof;
        }
        if (isset($this->_usedProperties['uses'])) {
            $output['uses'] = $this->uses;
        }
        if (isset($this->_usedProperties['adminImplements'])) {
            $output['admin_implements'] = $this->adminImplements;
        }
        if (isset($this->_usedProperties['adminExtends'])) {
            $output['admin_extends'] = $this->adminExtends;
        }
        if (isset($this->_usedProperties['adminInstanceof'])) {
            $output['admin_instanceof'] = $this->adminInstanceof;
        }
        if (isset($this->_usedProperties['adminUses'])) {
            $output['admin_uses'] = $this->adminUses;
        }
        if (isset($this->_usedProperties['priority'])) {
            $output['priority'] = $this->priority;
        }

        return $output;
    }

}
