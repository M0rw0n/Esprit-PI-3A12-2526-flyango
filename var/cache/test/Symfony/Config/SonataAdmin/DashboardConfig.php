<?php

namespace Symfony\Config\SonataAdmin;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Dashboard'.\DIRECTORY_SEPARATOR.'GroupConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Dashboard'.\DIRECTORY_SEPARATOR.'BlockConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DashboardConfig 
{
    private $groups;
    private $blocks;
    private $_usedProperties = [];

    public function group(string $id, array $value = []): \Symfony\Config\SonataAdmin\Dashboard\GroupConfig
    {
        if (!isset($this->groups[$id])) {
            $this->_usedProperties['groups'] = true;
            $this->groups[$id] = new \Symfony\Config\SonataAdmin\Dashboard\GroupConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "group()" has already been initialized. You cannot pass values the second time you call group().');
        }

        return $this->groups[$id];
    }

    /**
     * @default [{"position":"left","settings":[],"type":"sonata.admin.block.admin_list","roles":[]}]
    */
    public function block(array $value = []): \Symfony\Config\SonataAdmin\Dashboard\BlockConfig
    {
        $this->_usedProperties['blocks'] = true;

        return $this->blocks[] = new \Symfony\Config\SonataAdmin\Dashboard\BlockConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('groups', $value)) {
            $this->_usedProperties['groups'] = true;
            $this->groups = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\SonataAdmin\Dashboard\GroupConfig($v) : $v, $value['groups']);
            unset($value['groups']);
        }

        if (array_key_exists('blocks', $value)) {
            $this->_usedProperties['blocks'] = true;
            $this->blocks = array_map(fn ($v) => new \Symfony\Config\SonataAdmin\Dashboard\BlockConfig($v), $value['blocks']);
            unset($value['blocks']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['groups'])) {
            $output['groups'] = array_map(fn ($v) => $v instanceof \Symfony\Config\SonataAdmin\Dashboard\GroupConfig ? $v->toArray() : $v, $this->groups);
        }
        if (isset($this->_usedProperties['blocks'])) {
            $output['blocks'] = array_map(fn ($v) => $v->toArray(), $this->blocks);
        }

        return $output;
    }

}
