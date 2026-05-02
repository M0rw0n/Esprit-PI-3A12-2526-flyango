<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'DoctrineDoctor'.\DIRECTORY_SEPARATOR.'AnalysisConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'DoctrineDoctor'.\DIRECTORY_SEPARATOR.'AnalyzersConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'DoctrineDoctor'.\DIRECTORY_SEPARATOR.'ProfilerConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'DoctrineDoctor'.\DIRECTORY_SEPARATOR.'DebugConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DoctrineDoctorConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $enabled;
    private $analysis;
    private $analyzers;
    private $profiler;
    private $debug;
    private $_usedProperties = [];

    /**
     * Enable or disable Doctrine Doctor
     * @default true
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
     * @default {"exclude_third_party_entities":true,"exclude_paths":["vendor\/"]}
    */
    public function analysis(array $value = []): \Symfony\Config\DoctrineDoctor\AnalysisConfig
    {
        if (null === $this->analysis) {
            $this->_usedProperties['analysis'] = true;
            $this->analysis = new \Symfony\Config\DoctrineDoctor\AnalysisConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "analysis()" has already been initialized. You cannot pass values the second time you call analysis().');
        }

        return $this->analysis;
    }

    /**
     * @default {"n_plus_one":{"enabled":true,"threshold":5},"slow_query":{"enabled":true,"threshold":100},"missing_index":{"enabled":true,"slow_query_threshold":50,"explain_queries":true,"min_rows_scanned":1000},"hydration":{"enabled":true,"row_threshold":99,"critical_threshold":999},"eager_loading":{"enabled":true,"join_threshold":4,"critical_join_threshold":7},"find_all":{"enabled":true,"threshold":99},"entity_manager_clear":{"enabled":true,"batch_size_threshold":20},"get_reference":{"enabled":true,"threshold":2},"flush_in_loop":{"enabled":true,"flush_count_threshold":5,"time_window_ms":1000},"lazy_loading":{"enabled":true,"threshold":10},"dql_injection":{"enabled":true},"bulk_operation":{"enabled":true,"threshold":20},"strict_mode":{"enabled":true},"charset":{"enabled":true},"inno_db_engine":{"enabled":true},"connection_pooling":{"enabled":true},"collection_initialization":{"enabled":true},"cascade_configuration":{"enabled":true},"sensitive_data_exposure":{"enabled":true},"insecure_random":{"enabled":true},"sql_injection_raw_queries":{"enabled":true},"foreign_key_mapping":{"enabled":true},"partial_object":{"enabled":true,"threshold":5},"dto_hydration":{"enabled":true},"cascade_all":{"enabled":true},"cascade_persist_independent":{"enabled":true},"missing_orphan_removal":{"enabled":true},"cascade_remove_independent":{"enabled":true},"bidirectional_consistency":{"enabled":true},"orphan_removal_no_cascade":{"enabled":true},"ondelete_mismatch":{"enabled":true},"join_optimization":{"enabled":true,"max_joins_recommended":5,"max_joins_critical":8},"doctrine_cache":{"enabled":true},"naming_convention":{"enabled":true},"missing_embeddable_opportunity":{"enabled":true},"blameable_trait":{"enabled":true}}
    */
    public function analyzers(array $value = []): \Symfony\Config\DoctrineDoctor\AnalyzersConfig
    {
        if (null === $this->analyzers) {
            $this->_usedProperties['analyzers'] = true;
            $this->analyzers = new \Symfony\Config\DoctrineDoctor\AnalyzersConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "analyzers()" has already been initialized. You cannot pass values the second time you call analyzers().');
        }

        return $this->analyzers;
    }

    /**
     * @default {"show_in_toolbar":true,"show_debug_info":false}
    */
    public function profiler(array $value = []): \Symfony\Config\DoctrineDoctor\ProfilerConfig
    {
        if (null === $this->profiler) {
            $this->_usedProperties['profiler'] = true;
            $this->profiler = new \Symfony\Config\DoctrineDoctor\ProfilerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "profiler()" has already been initialized. You cannot pass values the second time you call profiler().');
        }

        return $this->profiler;
    }

    /**
     * Debug settings for contributors and advanced users
     * @default {"enabled":false,"internal_logging":false}
    */
    public function debug(array $value = []): \Symfony\Config\DoctrineDoctor\DebugConfig
    {
        if (null === $this->debug) {
            $this->_usedProperties['debug'] = true;
            $this->debug = new \Symfony\Config\DoctrineDoctor\DebugConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "debug()" has already been initialized. You cannot pass values the second time you call debug().');
        }

        return $this->debug;
    }

    public function getExtensionAlias(): string
    {
        return 'doctrine_doctor';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('analysis', $value)) {
            $this->_usedProperties['analysis'] = true;
            $this->analysis = new \Symfony\Config\DoctrineDoctor\AnalysisConfig($value['analysis']);
            unset($value['analysis']);
        }

        if (array_key_exists('analyzers', $value)) {
            $this->_usedProperties['analyzers'] = true;
            $this->analyzers = new \Symfony\Config\DoctrineDoctor\AnalyzersConfig($value['analyzers']);
            unset($value['analyzers']);
        }

        if (array_key_exists('profiler', $value)) {
            $this->_usedProperties['profiler'] = true;
            $this->profiler = new \Symfony\Config\DoctrineDoctor\ProfilerConfig($value['profiler']);
            unset($value['profiler']);
        }

        if (array_key_exists('debug', $value)) {
            $this->_usedProperties['debug'] = true;
            $this->debug = new \Symfony\Config\DoctrineDoctor\DebugConfig($value['debug']);
            unset($value['debug']);
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
        if (isset($this->_usedProperties['analysis'])) {
            $output['analysis'] = $this->analysis->toArray();
        }
        if (isset($this->_usedProperties['analyzers'])) {
            $output['analyzers'] = $this->analyzers->toArray();
        }
        if (isset($this->_usedProperties['profiler'])) {
            $output['profiler'] = $this->profiler->toArray();
        }
        if (isset($this->_usedProperties['debug'])) {
            $output['debug'] = $this->debug->toArray();
        }

        return $output;
    }

}
