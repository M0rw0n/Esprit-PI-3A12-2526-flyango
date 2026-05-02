<?php

namespace Symfony\Config\DoctrineDoctor;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DebugConfig 
{
    private $enabled;
    private $internalLogging;
    private $_usedProperties = [];

    /**
     * Enable debug mode (verbose logging, detailed error messages). Keep disabled for production.
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enabled($value): static
    {
        $this->_usedProperties['enabled'] = true;
        $this->enabled = $value;

        return $this;
    }

    /**
     * Enable internal logging for Doctrine Doctor analyzers. Can add ~133ms overhead. Enable only for debugging.
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function internalLogging($value): static
    {
        $this->_usedProperties['internalLogging'] = true;
        $this->internalLogging = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('internal_logging', $value)) {
            $this->_usedProperties['internalLogging'] = true;
            $this->internalLogging = $value['internal_logging'];
            unset($value['internal_logging']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['enabled'])) {
            $output['enabled'] = $this->enabled;
        }
        if (isset($this->_usedProperties['internalLogging'])) {
            $output['internal_logging'] = $this->internalLogging;
        }

        return $output;
    }

}
