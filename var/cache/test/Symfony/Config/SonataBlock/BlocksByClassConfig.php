<?php

namespace Symfony\Config\SonataBlock;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class BlocksByClassConfig 
{
    private $settings;
    private $_usedProperties = [];

    /**
     * @return $this
     */
    public function setting(string $id, mixed $value): static
    {
        $this->_usedProperties['settings'] = true;
        $this->settings[$id] = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('settings', $value)) {
            $this->_usedProperties['settings'] = true;
            $this->settings = $value['settings'];
            unset($value['settings']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['settings'])) {
            $output['settings'] = $this->settings;
        }

        return $output;
    }

}
