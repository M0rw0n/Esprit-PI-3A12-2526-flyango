<?php

namespace Symfony\Config\StofDoctrineExtensions;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SoftdeleteableConfig 
{
    private $handlePostFlushEvent;
    private $_usedProperties = [];

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function handlePostFlushEvent($value): static
    {
        $this->_usedProperties['handlePostFlushEvent'] = true;
        $this->handlePostFlushEvent = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('handle_post_flush_event', $value)) {
            $this->_usedProperties['handlePostFlushEvent'] = true;
            $this->handlePostFlushEvent = $value['handle_post_flush_event'];
            unset($value['handle_post_flush_event']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['handlePostFlushEvent'])) {
            $output['handle_post_flush_event'] = $this->handlePostFlushEvent;
        }

        return $output;
    }

}
