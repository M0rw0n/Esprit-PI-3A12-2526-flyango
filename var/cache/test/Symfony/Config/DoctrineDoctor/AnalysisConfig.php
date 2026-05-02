<?php

namespace Symfony\Config\DoctrineDoctor;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AnalysisConfig 
{
    private $excludeThirdPartyEntities;
    private $excludePaths;
    private $_usedProperties = [];

    /**
     * Exclude entities from vendor/ directory during analysis (recommended for cleaner reports)
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function excludeThirdPartyEntities($value): static
    {
        $this->_usedProperties['excludeThirdPartyEntities'] = true;
        $this->excludeThirdPartyEntities = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function excludePaths(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['excludePaths'] = true;
        $this->excludePaths = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('exclude_third_party_entities', $value)) {
            $this->_usedProperties['excludeThirdPartyEntities'] = true;
            $this->excludeThirdPartyEntities = $value['exclude_third_party_entities'];
            unset($value['exclude_third_party_entities']);
        }

        if (array_key_exists('exclude_paths', $value)) {
            $this->_usedProperties['excludePaths'] = true;
            $this->excludePaths = $value['exclude_paths'];
            unset($value['exclude_paths']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['excludeThirdPartyEntities'])) {
            $output['exclude_third_party_entities'] = $this->excludeThirdPartyEntities;
        }
        if (isset($this->_usedProperties['excludePaths'])) {
            $output['exclude_paths'] = $this->excludePaths;
        }

        return $output;
    }

}
