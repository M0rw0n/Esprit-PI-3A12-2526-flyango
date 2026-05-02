<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataDoctrineOrmAdmin'.\DIRECTORY_SEPARATOR.'AuditConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'SonataDoctrineOrmAdmin'.\DIRECTORY_SEPARATOR.'TemplatesConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SonataDoctrineOrmAdminConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $entityManager;
    private $audit;
    private $templates;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function entityManager($value): static
    {
        $this->_usedProperties['entityManager'] = true;
        $this->entityManager = $value;

        return $this;
    }

    /**
     * @default {"force":true}
    */
    public function audit(array $value = []): \Symfony\Config\SonataDoctrineOrmAdmin\AuditConfig
    {
        if (null === $this->audit) {
            $this->_usedProperties['audit'] = true;
            $this->audit = new \Symfony\Config\SonataDoctrineOrmAdmin\AuditConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "audit()" has already been initialized. You cannot pass values the second time you call audit().');
        }

        return $this->audit;
    }

    public function templates(array $value = []): \Symfony\Config\SonataDoctrineOrmAdmin\TemplatesConfig
    {
        if (null === $this->templates) {
            $this->_usedProperties['templates'] = true;
            $this->templates = new \Symfony\Config\SonataDoctrineOrmAdmin\TemplatesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "templates()" has already been initialized. You cannot pass values the second time you call templates().');
        }

        return $this->templates;
    }

    public function getExtensionAlias(): string
    {
        return 'sonata_doctrine_orm_admin';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('entity_manager', $value)) {
            $this->_usedProperties['entityManager'] = true;
            $this->entityManager = $value['entity_manager'];
            unset($value['entity_manager']);
        }

        if (array_key_exists('audit', $value)) {
            $this->_usedProperties['audit'] = true;
            $this->audit = new \Symfony\Config\SonataDoctrineOrmAdmin\AuditConfig($value['audit']);
            unset($value['audit']);
        }

        if (array_key_exists('templates', $value)) {
            $this->_usedProperties['templates'] = true;
            $this->templates = new \Symfony\Config\SonataDoctrineOrmAdmin\TemplatesConfig($value['templates']);
            unset($value['templates']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['entityManager'])) {
            $output['entity_manager'] = $this->entityManager;
        }
        if (isset($this->_usedProperties['audit'])) {
            $output['audit'] = $this->audit->toArray();
        }
        if (isset($this->_usedProperties['templates'])) {
            $output['templates'] = $this->templates->toArray();
        }

        return $output;
    }

}
