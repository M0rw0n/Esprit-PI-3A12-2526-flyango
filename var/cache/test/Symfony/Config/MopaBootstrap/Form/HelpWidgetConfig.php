<?php

namespace Symfony\Config\MopaBootstrap\Form;

require_once __DIR__.\DIRECTORY_SEPARATOR.'HelpWidget'.\DIRECTORY_SEPARATOR.'PopoverConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class HelpWidgetConfig 
{
    private $popover;
    private $_usedProperties = [];

    /**
     * @default {"title":null,"content":null,"trigger":"hover","toggle":"popover","placement":"right","selector":null}
    */
    public function popover(array $value = []): \Symfony\Config\MopaBootstrap\Form\HelpWidget\PopoverConfig
    {
        if (null === $this->popover) {
            $this->_usedProperties['popover'] = true;
            $this->popover = new \Symfony\Config\MopaBootstrap\Form\HelpWidget\PopoverConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "popover()" has already been initialized. You cannot pass values the second time you call popover().');
        }

        return $this->popover;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('popover', $value)) {
            $this->_usedProperties['popover'] = true;
            $this->popover = new \Symfony\Config\MopaBootstrap\Form\HelpWidget\PopoverConfig($value['popover']);
            unset($value['popover']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['popover'])) {
            $output['popover'] = $this->popover->toArray();
        }

        return $output;
    }

}
