<?php

namespace Symfony\Config\FosElastica;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SerializerConfig 
{
    private $callbackClass;
    private $serializer;
    private $_usedProperties = [];

    /**
     * @default 'FOS\\ElasticaBundle\\Serializer\\Callback'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function callbackClass($value): static
    {
        $this->_usedProperties['callbackClass'] = true;
        $this->callbackClass = $value;

        return $this;
    }

    /**
     * @default 'serializer'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function serializer($value): static
    {
        $this->_usedProperties['serializer'] = true;
        $this->serializer = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('callback_class', $value)) {
            $this->_usedProperties['callbackClass'] = true;
            $this->callbackClass = $value['callback_class'];
            unset($value['callback_class']);
        }

        if (array_key_exists('serializer', $value)) {
            $this->_usedProperties['serializer'] = true;
            $this->serializer = $value['serializer'];
            unset($value['serializer']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['callbackClass'])) {
            $output['callback_class'] = $this->callbackClass;
        }
        if (isset($this->_usedProperties['serializer'])) {
            $output['serializer'] = $this->serializer;
        }

        return $output;
    }

}
