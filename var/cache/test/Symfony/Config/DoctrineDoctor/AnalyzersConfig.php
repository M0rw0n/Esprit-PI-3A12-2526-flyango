<?php

namespace Symfony\Config\DoctrineDoctor;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'NPlusOneConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'SlowQueryConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'MissingIndexConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'HydrationConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'EagerLoadingConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'FindAllConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'EntityManagerClearConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'GetReferenceConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'FlushInLoopConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'LazyLoadingConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'DqlInjectionConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'BulkOperationConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'StrictModeConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'CharsetConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'InnoDbEngineConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'ConnectionPoolingConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'CollectionInitializationConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'CascadeConfigurationConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'SensitiveDataExposureConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'InsecureRandomConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'SqlInjectionRawQueriesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'ForeignKeyMappingConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'PartialObjectConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'DtoHydrationConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'CascadeAllConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'CascadePersistIndependentConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'MissingOrphanRemovalConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'CascadeRemoveIndependentConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'BidirectionalConsistencyConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'OrphanRemovalNoCascadeConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'OndeleteMismatchConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'JoinOptimizationConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'DoctrineCacheConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'NamingConventionConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'MissingEmbeddableOpportunityConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Analyzers'.\DIRECTORY_SEPARATOR.'BlameableTraitConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AnalyzersConfig 
{
    private $nPlusOne;
    private $slowQuery;
    private $missingIndex;
    private $hydration;
    private $eagerLoading;
    private $findAll;
    private $entityManagerClear;
    private $getReference;
    private $flushInLoop;
    private $lazyLoading;
    private $dqlInjection;
    private $bulkOperation;
    private $strictMode;
    private $charset;
    private $innoDbEngine;
    private $connectionPooling;
    private $collectionInitialization;
    private $cascadeConfiguration;
    private $sensitiveDataExposure;
    private $insecureRandom;
    private $sqlInjectionRawQueries;
    private $foreignKeyMapping;
    private $partialObject;
    private $dtoHydration;
    private $cascadeAll;
    private $cascadePersistIndependent;
    private $missingOrphanRemoval;
    private $cascadeRemoveIndependent;
    private $bidirectionalConsistency;
    private $orphanRemovalNoCascade;
    private $ondeleteMismatch;
    private $joinOptimization;
    private $doctrineCache;
    private $namingConvention;
    private $missingEmbeddableOpportunity;
    private $blameableTrait;
    private $_usedProperties = [];

    /**
     * @default {"enabled":true,"threshold":5}
    */
    public function nPlusOne(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\NPlusOneConfig
    {
        if (null === $this->nPlusOne) {
            $this->_usedProperties['nPlusOne'] = true;
            $this->nPlusOne = new \Symfony\Config\DoctrineDoctor\Analyzers\NPlusOneConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "nPlusOne()" has already been initialized. You cannot pass values the second time you call nPlusOne().');
        }

        return $this->nPlusOne;
    }

    /**
     * @default {"enabled":true,"threshold":100}
    */
    public function slowQuery(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\SlowQueryConfig
    {
        if (null === $this->slowQuery) {
            $this->_usedProperties['slowQuery'] = true;
            $this->slowQuery = new \Symfony\Config\DoctrineDoctor\Analyzers\SlowQueryConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "slowQuery()" has already been initialized. You cannot pass values the second time you call slowQuery().');
        }

        return $this->slowQuery;
    }

    /**
     * @default {"enabled":true,"slow_query_threshold":50,"explain_queries":true,"min_rows_scanned":1000}
    */
    public function missingIndex(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\MissingIndexConfig
    {
        if (null === $this->missingIndex) {
            $this->_usedProperties['missingIndex'] = true;
            $this->missingIndex = new \Symfony\Config\DoctrineDoctor\Analyzers\MissingIndexConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "missingIndex()" has already been initialized. You cannot pass values the second time you call missingIndex().');
        }

        return $this->missingIndex;
    }

    /**
     * @default {"enabled":true,"row_threshold":99,"critical_threshold":999}
    */
    public function hydration(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\HydrationConfig
    {
        if (null === $this->hydration) {
            $this->_usedProperties['hydration'] = true;
            $this->hydration = new \Symfony\Config\DoctrineDoctor\Analyzers\HydrationConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "hydration()" has already been initialized. You cannot pass values the second time you call hydration().');
        }

        return $this->hydration;
    }

    /**
     * @default {"enabled":true,"join_threshold":4,"critical_join_threshold":7}
    */
    public function eagerLoading(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\EagerLoadingConfig
    {
        if (null === $this->eagerLoading) {
            $this->_usedProperties['eagerLoading'] = true;
            $this->eagerLoading = new \Symfony\Config\DoctrineDoctor\Analyzers\EagerLoadingConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "eagerLoading()" has already been initialized. You cannot pass values the second time you call eagerLoading().');
        }

        return $this->eagerLoading;
    }

    /**
     * @default {"enabled":true,"threshold":99}
    */
    public function findAll(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\FindAllConfig
    {
        if (null === $this->findAll) {
            $this->_usedProperties['findAll'] = true;
            $this->findAll = new \Symfony\Config\DoctrineDoctor\Analyzers\FindAllConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "findAll()" has already been initialized. You cannot pass values the second time you call findAll().');
        }

        return $this->findAll;
    }

    /**
     * @default {"enabled":true,"batch_size_threshold":20}
    */
    public function entityManagerClear(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\EntityManagerClearConfig
    {
        if (null === $this->entityManagerClear) {
            $this->_usedProperties['entityManagerClear'] = true;
            $this->entityManagerClear = new \Symfony\Config\DoctrineDoctor\Analyzers\EntityManagerClearConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "entityManagerClear()" has already been initialized. You cannot pass values the second time you call entityManagerClear().');
        }

        return $this->entityManagerClear;
    }

    /**
     * @default {"enabled":true,"threshold":2}
    */
    public function getReference(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\GetReferenceConfig
    {
        if (null === $this->getReference) {
            $this->_usedProperties['getReference'] = true;
            $this->getReference = new \Symfony\Config\DoctrineDoctor\Analyzers\GetReferenceConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "getReference()" has already been initialized. You cannot pass values the second time you call getReference().');
        }

        return $this->getReference;
    }

    /**
     * @default {"enabled":true,"flush_count_threshold":5,"time_window_ms":1000}
    */
    public function flushInLoop(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\FlushInLoopConfig
    {
        if (null === $this->flushInLoop) {
            $this->_usedProperties['flushInLoop'] = true;
            $this->flushInLoop = new \Symfony\Config\DoctrineDoctor\Analyzers\FlushInLoopConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "flushInLoop()" has already been initialized. You cannot pass values the second time you call flushInLoop().');
        }

        return $this->flushInLoop;
    }

    /**
     * @default {"enabled":true,"threshold":10}
    */
    public function lazyLoading(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\LazyLoadingConfig
    {
        if (null === $this->lazyLoading) {
            $this->_usedProperties['lazyLoading'] = true;
            $this->lazyLoading = new \Symfony\Config\DoctrineDoctor\Analyzers\LazyLoadingConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "lazyLoading()" has already been initialized. You cannot pass values the second time you call lazyLoading().');
        }

        return $this->lazyLoading;
    }

    /**
     * @default {"enabled":true}
    */
    public function dqlInjection(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\DqlInjectionConfig
    {
        if (null === $this->dqlInjection) {
            $this->_usedProperties['dqlInjection'] = true;
            $this->dqlInjection = new \Symfony\Config\DoctrineDoctor\Analyzers\DqlInjectionConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "dqlInjection()" has already been initialized. You cannot pass values the second time you call dqlInjection().');
        }

        return $this->dqlInjection;
    }

    /**
     * @default {"enabled":true,"threshold":20}
    */
    public function bulkOperation(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\BulkOperationConfig
    {
        if (null === $this->bulkOperation) {
            $this->_usedProperties['bulkOperation'] = true;
            $this->bulkOperation = new \Symfony\Config\DoctrineDoctor\Analyzers\BulkOperationConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "bulkOperation()" has already been initialized. You cannot pass values the second time you call bulkOperation().');
        }

        return $this->bulkOperation;
    }

    /**
     * @default {"enabled":true}
    */
    public function strictMode(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\StrictModeConfig
    {
        if (null === $this->strictMode) {
            $this->_usedProperties['strictMode'] = true;
            $this->strictMode = new \Symfony\Config\DoctrineDoctor\Analyzers\StrictModeConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "strictMode()" has already been initialized. You cannot pass values the second time you call strictMode().');
        }

        return $this->strictMode;
    }

    /**
     * @default {"enabled":true}
    */
    public function charset(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\CharsetConfig
    {
        if (null === $this->charset) {
            $this->_usedProperties['charset'] = true;
            $this->charset = new \Symfony\Config\DoctrineDoctor\Analyzers\CharsetConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "charset()" has already been initialized. You cannot pass values the second time you call charset().');
        }

        return $this->charset;
    }

    /**
     * @default {"enabled":true}
    */
    public function innoDbEngine(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\InnoDbEngineConfig
    {
        if (null === $this->innoDbEngine) {
            $this->_usedProperties['innoDbEngine'] = true;
            $this->innoDbEngine = new \Symfony\Config\DoctrineDoctor\Analyzers\InnoDbEngineConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "innoDbEngine()" has already been initialized. You cannot pass values the second time you call innoDbEngine().');
        }

        return $this->innoDbEngine;
    }

    /**
     * @default {"enabled":true}
    */
    public function connectionPooling(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\ConnectionPoolingConfig
    {
        if (null === $this->connectionPooling) {
            $this->_usedProperties['connectionPooling'] = true;
            $this->connectionPooling = new \Symfony\Config\DoctrineDoctor\Analyzers\ConnectionPoolingConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "connectionPooling()" has already been initialized. You cannot pass values the second time you call connectionPooling().');
        }

        return $this->connectionPooling;
    }

    /**
     * @default {"enabled":true}
    */
    public function collectionInitialization(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\CollectionInitializationConfig
    {
        if (null === $this->collectionInitialization) {
            $this->_usedProperties['collectionInitialization'] = true;
            $this->collectionInitialization = new \Symfony\Config\DoctrineDoctor\Analyzers\CollectionInitializationConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "collectionInitialization()" has already been initialized. You cannot pass values the second time you call collectionInitialization().');
        }

        return $this->collectionInitialization;
    }

    /**
     * @default {"enabled":true}
    */
    public function cascadeConfiguration(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\CascadeConfigurationConfig
    {
        if (null === $this->cascadeConfiguration) {
            $this->_usedProperties['cascadeConfiguration'] = true;
            $this->cascadeConfiguration = new \Symfony\Config\DoctrineDoctor\Analyzers\CascadeConfigurationConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "cascadeConfiguration()" has already been initialized. You cannot pass values the second time you call cascadeConfiguration().');
        }

        return $this->cascadeConfiguration;
    }

    /**
     * @default {"enabled":true}
    */
    public function sensitiveDataExposure(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\SensitiveDataExposureConfig
    {
        if (null === $this->sensitiveDataExposure) {
            $this->_usedProperties['sensitiveDataExposure'] = true;
            $this->sensitiveDataExposure = new \Symfony\Config\DoctrineDoctor\Analyzers\SensitiveDataExposureConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "sensitiveDataExposure()" has already been initialized. You cannot pass values the second time you call sensitiveDataExposure().');
        }

        return $this->sensitiveDataExposure;
    }

    /**
     * @default {"enabled":true}
    */
    public function insecureRandom(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\InsecureRandomConfig
    {
        if (null === $this->insecureRandom) {
            $this->_usedProperties['insecureRandom'] = true;
            $this->insecureRandom = new \Symfony\Config\DoctrineDoctor\Analyzers\InsecureRandomConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "insecureRandom()" has already been initialized. You cannot pass values the second time you call insecureRandom().');
        }

        return $this->insecureRandom;
    }

    /**
     * @default {"enabled":true}
    */
    public function sqlInjectionRawQueries(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\SqlInjectionRawQueriesConfig
    {
        if (null === $this->sqlInjectionRawQueries) {
            $this->_usedProperties['sqlInjectionRawQueries'] = true;
            $this->sqlInjectionRawQueries = new \Symfony\Config\DoctrineDoctor\Analyzers\SqlInjectionRawQueriesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "sqlInjectionRawQueries()" has already been initialized. You cannot pass values the second time you call sqlInjectionRawQueries().');
        }

        return $this->sqlInjectionRawQueries;
    }

    /**
     * @default {"enabled":true}
    */
    public function foreignKeyMapping(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\ForeignKeyMappingConfig
    {
        if (null === $this->foreignKeyMapping) {
            $this->_usedProperties['foreignKeyMapping'] = true;
            $this->foreignKeyMapping = new \Symfony\Config\DoctrineDoctor\Analyzers\ForeignKeyMappingConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "foreignKeyMapping()" has already been initialized. You cannot pass values the second time you call foreignKeyMapping().');
        }

        return $this->foreignKeyMapping;
    }

    /**
     * @default {"enabled":true,"threshold":5}
    */
    public function partialObject(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\PartialObjectConfig
    {
        if (null === $this->partialObject) {
            $this->_usedProperties['partialObject'] = true;
            $this->partialObject = new \Symfony\Config\DoctrineDoctor\Analyzers\PartialObjectConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "partialObject()" has already been initialized. You cannot pass values the second time you call partialObject().');
        }

        return $this->partialObject;
    }

    /**
     * @default {"enabled":true}
    */
    public function dtoHydration(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\DtoHydrationConfig
    {
        if (null === $this->dtoHydration) {
            $this->_usedProperties['dtoHydration'] = true;
            $this->dtoHydration = new \Symfony\Config\DoctrineDoctor\Analyzers\DtoHydrationConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "dtoHydration()" has already been initialized. You cannot pass values the second time you call dtoHydration().');
        }

        return $this->dtoHydration;
    }

    /**
     * @default {"enabled":true}
    */
    public function cascadeAll(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\CascadeAllConfig
    {
        if (null === $this->cascadeAll) {
            $this->_usedProperties['cascadeAll'] = true;
            $this->cascadeAll = new \Symfony\Config\DoctrineDoctor\Analyzers\CascadeAllConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "cascadeAll()" has already been initialized. You cannot pass values the second time you call cascadeAll().');
        }

        return $this->cascadeAll;
    }

    /**
     * @default {"enabled":true}
    */
    public function cascadePersistIndependent(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\CascadePersistIndependentConfig
    {
        if (null === $this->cascadePersistIndependent) {
            $this->_usedProperties['cascadePersistIndependent'] = true;
            $this->cascadePersistIndependent = new \Symfony\Config\DoctrineDoctor\Analyzers\CascadePersistIndependentConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "cascadePersistIndependent()" has already been initialized. You cannot pass values the second time you call cascadePersistIndependent().');
        }

        return $this->cascadePersistIndependent;
    }

    /**
     * @default {"enabled":true}
    */
    public function missingOrphanRemoval(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\MissingOrphanRemovalConfig
    {
        if (null === $this->missingOrphanRemoval) {
            $this->_usedProperties['missingOrphanRemoval'] = true;
            $this->missingOrphanRemoval = new \Symfony\Config\DoctrineDoctor\Analyzers\MissingOrphanRemovalConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "missingOrphanRemoval()" has already been initialized. You cannot pass values the second time you call missingOrphanRemoval().');
        }

        return $this->missingOrphanRemoval;
    }

    /**
     * @default {"enabled":true}
    */
    public function cascadeRemoveIndependent(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\CascadeRemoveIndependentConfig
    {
        if (null === $this->cascadeRemoveIndependent) {
            $this->_usedProperties['cascadeRemoveIndependent'] = true;
            $this->cascadeRemoveIndependent = new \Symfony\Config\DoctrineDoctor\Analyzers\CascadeRemoveIndependentConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "cascadeRemoveIndependent()" has already been initialized. You cannot pass values the second time you call cascadeRemoveIndependent().');
        }

        return $this->cascadeRemoveIndependent;
    }

    /**
     * @default {"enabled":true}
    */
    public function bidirectionalConsistency(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\BidirectionalConsistencyConfig
    {
        if (null === $this->bidirectionalConsistency) {
            $this->_usedProperties['bidirectionalConsistency'] = true;
            $this->bidirectionalConsistency = new \Symfony\Config\DoctrineDoctor\Analyzers\BidirectionalConsistencyConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "bidirectionalConsistency()" has already been initialized. You cannot pass values the second time you call bidirectionalConsistency().');
        }

        return $this->bidirectionalConsistency;
    }

    /**
     * @default {"enabled":true}
    */
    public function orphanRemovalNoCascade(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\OrphanRemovalNoCascadeConfig
    {
        if (null === $this->orphanRemovalNoCascade) {
            $this->_usedProperties['orphanRemovalNoCascade'] = true;
            $this->orphanRemovalNoCascade = new \Symfony\Config\DoctrineDoctor\Analyzers\OrphanRemovalNoCascadeConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "orphanRemovalNoCascade()" has already been initialized. You cannot pass values the second time you call orphanRemovalNoCascade().');
        }

        return $this->orphanRemovalNoCascade;
    }

    /**
     * @default {"enabled":true}
    */
    public function ondeleteMismatch(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\OndeleteMismatchConfig
    {
        if (null === $this->ondeleteMismatch) {
            $this->_usedProperties['ondeleteMismatch'] = true;
            $this->ondeleteMismatch = new \Symfony\Config\DoctrineDoctor\Analyzers\OndeleteMismatchConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "ondeleteMismatch()" has already been initialized. You cannot pass values the second time you call ondeleteMismatch().');
        }

        return $this->ondeleteMismatch;
    }

    /**
     * @default {"enabled":true,"max_joins_recommended":5,"max_joins_critical":8}
    */
    public function joinOptimization(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\JoinOptimizationConfig
    {
        if (null === $this->joinOptimization) {
            $this->_usedProperties['joinOptimization'] = true;
            $this->joinOptimization = new \Symfony\Config\DoctrineDoctor\Analyzers\JoinOptimizationConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "joinOptimization()" has already been initialized. You cannot pass values the second time you call joinOptimization().');
        }

        return $this->joinOptimization;
    }

    /**
     * @default {"enabled":true}
    */
    public function doctrineCache(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\DoctrineCacheConfig
    {
        if (null === $this->doctrineCache) {
            $this->_usedProperties['doctrineCache'] = true;
            $this->doctrineCache = new \Symfony\Config\DoctrineDoctor\Analyzers\DoctrineCacheConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "doctrineCache()" has already been initialized. You cannot pass values the second time you call doctrineCache().');
        }

        return $this->doctrineCache;
    }

    /**
     * @default {"enabled":true}
    */
    public function namingConvention(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\NamingConventionConfig
    {
        if (null === $this->namingConvention) {
            $this->_usedProperties['namingConvention'] = true;
            $this->namingConvention = new \Symfony\Config\DoctrineDoctor\Analyzers\NamingConventionConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "namingConvention()" has already been initialized. You cannot pass values the second time you call namingConvention().');
        }

        return $this->namingConvention;
    }

    /**
     * @default {"enabled":true}
    */
    public function missingEmbeddableOpportunity(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\MissingEmbeddableOpportunityConfig
    {
        if (null === $this->missingEmbeddableOpportunity) {
            $this->_usedProperties['missingEmbeddableOpportunity'] = true;
            $this->missingEmbeddableOpportunity = new \Symfony\Config\DoctrineDoctor\Analyzers\MissingEmbeddableOpportunityConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "missingEmbeddableOpportunity()" has already been initialized. You cannot pass values the second time you call missingEmbeddableOpportunity().');
        }

        return $this->missingEmbeddableOpportunity;
    }

    /**
     * @default {"enabled":true}
    */
    public function blameableTrait(array $value = []): \Symfony\Config\DoctrineDoctor\Analyzers\BlameableTraitConfig
    {
        if (null === $this->blameableTrait) {
            $this->_usedProperties['blameableTrait'] = true;
            $this->blameableTrait = new \Symfony\Config\DoctrineDoctor\Analyzers\BlameableTraitConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "blameableTrait()" has already been initialized. You cannot pass values the second time you call blameableTrait().');
        }

        return $this->blameableTrait;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('n_plus_one', $value)) {
            $this->_usedProperties['nPlusOne'] = true;
            $this->nPlusOne = new \Symfony\Config\DoctrineDoctor\Analyzers\NPlusOneConfig($value['n_plus_one']);
            unset($value['n_plus_one']);
        }

        if (array_key_exists('slow_query', $value)) {
            $this->_usedProperties['slowQuery'] = true;
            $this->slowQuery = new \Symfony\Config\DoctrineDoctor\Analyzers\SlowQueryConfig($value['slow_query']);
            unset($value['slow_query']);
        }

        if (array_key_exists('missing_index', $value)) {
            $this->_usedProperties['missingIndex'] = true;
            $this->missingIndex = new \Symfony\Config\DoctrineDoctor\Analyzers\MissingIndexConfig($value['missing_index']);
            unset($value['missing_index']);
        }

        if (array_key_exists('hydration', $value)) {
            $this->_usedProperties['hydration'] = true;
            $this->hydration = new \Symfony\Config\DoctrineDoctor\Analyzers\HydrationConfig($value['hydration']);
            unset($value['hydration']);
        }

        if (array_key_exists('eager_loading', $value)) {
            $this->_usedProperties['eagerLoading'] = true;
            $this->eagerLoading = new \Symfony\Config\DoctrineDoctor\Analyzers\EagerLoadingConfig($value['eager_loading']);
            unset($value['eager_loading']);
        }

        if (array_key_exists('find_all', $value)) {
            $this->_usedProperties['findAll'] = true;
            $this->findAll = new \Symfony\Config\DoctrineDoctor\Analyzers\FindAllConfig($value['find_all']);
            unset($value['find_all']);
        }

        if (array_key_exists('entity_manager_clear', $value)) {
            $this->_usedProperties['entityManagerClear'] = true;
            $this->entityManagerClear = new \Symfony\Config\DoctrineDoctor\Analyzers\EntityManagerClearConfig($value['entity_manager_clear']);
            unset($value['entity_manager_clear']);
        }

        if (array_key_exists('get_reference', $value)) {
            $this->_usedProperties['getReference'] = true;
            $this->getReference = new \Symfony\Config\DoctrineDoctor\Analyzers\GetReferenceConfig($value['get_reference']);
            unset($value['get_reference']);
        }

        if (array_key_exists('flush_in_loop', $value)) {
            $this->_usedProperties['flushInLoop'] = true;
            $this->flushInLoop = new \Symfony\Config\DoctrineDoctor\Analyzers\FlushInLoopConfig($value['flush_in_loop']);
            unset($value['flush_in_loop']);
        }

        if (array_key_exists('lazy_loading', $value)) {
            $this->_usedProperties['lazyLoading'] = true;
            $this->lazyLoading = new \Symfony\Config\DoctrineDoctor\Analyzers\LazyLoadingConfig($value['lazy_loading']);
            unset($value['lazy_loading']);
        }

        if (array_key_exists('dql_injection', $value)) {
            $this->_usedProperties['dqlInjection'] = true;
            $this->dqlInjection = new \Symfony\Config\DoctrineDoctor\Analyzers\DqlInjectionConfig($value['dql_injection']);
            unset($value['dql_injection']);
        }

        if (array_key_exists('bulk_operation', $value)) {
            $this->_usedProperties['bulkOperation'] = true;
            $this->bulkOperation = new \Symfony\Config\DoctrineDoctor\Analyzers\BulkOperationConfig($value['bulk_operation']);
            unset($value['bulk_operation']);
        }

        if (array_key_exists('strict_mode', $value)) {
            $this->_usedProperties['strictMode'] = true;
            $this->strictMode = new \Symfony\Config\DoctrineDoctor\Analyzers\StrictModeConfig($value['strict_mode']);
            unset($value['strict_mode']);
        }

        if (array_key_exists('charset', $value)) {
            $this->_usedProperties['charset'] = true;
            $this->charset = new \Symfony\Config\DoctrineDoctor\Analyzers\CharsetConfig($value['charset']);
            unset($value['charset']);
        }

        if (array_key_exists('inno_db_engine', $value)) {
            $this->_usedProperties['innoDbEngine'] = true;
            $this->innoDbEngine = new \Symfony\Config\DoctrineDoctor\Analyzers\InnoDbEngineConfig($value['inno_db_engine']);
            unset($value['inno_db_engine']);
        }

        if (array_key_exists('connection_pooling', $value)) {
            $this->_usedProperties['connectionPooling'] = true;
            $this->connectionPooling = new \Symfony\Config\DoctrineDoctor\Analyzers\ConnectionPoolingConfig($value['connection_pooling']);
            unset($value['connection_pooling']);
        }

        if (array_key_exists('collection_initialization', $value)) {
            $this->_usedProperties['collectionInitialization'] = true;
            $this->collectionInitialization = new \Symfony\Config\DoctrineDoctor\Analyzers\CollectionInitializationConfig($value['collection_initialization']);
            unset($value['collection_initialization']);
        }

        if (array_key_exists('cascade_configuration', $value)) {
            $this->_usedProperties['cascadeConfiguration'] = true;
            $this->cascadeConfiguration = new \Symfony\Config\DoctrineDoctor\Analyzers\CascadeConfigurationConfig($value['cascade_configuration']);
            unset($value['cascade_configuration']);
        }

        if (array_key_exists('sensitive_data_exposure', $value)) {
            $this->_usedProperties['sensitiveDataExposure'] = true;
            $this->sensitiveDataExposure = new \Symfony\Config\DoctrineDoctor\Analyzers\SensitiveDataExposureConfig($value['sensitive_data_exposure']);
            unset($value['sensitive_data_exposure']);
        }

        if (array_key_exists('insecure_random', $value)) {
            $this->_usedProperties['insecureRandom'] = true;
            $this->insecureRandom = new \Symfony\Config\DoctrineDoctor\Analyzers\InsecureRandomConfig($value['insecure_random']);
            unset($value['insecure_random']);
        }

        if (array_key_exists('sql_injection_raw_queries', $value)) {
            $this->_usedProperties['sqlInjectionRawQueries'] = true;
            $this->sqlInjectionRawQueries = new \Symfony\Config\DoctrineDoctor\Analyzers\SqlInjectionRawQueriesConfig($value['sql_injection_raw_queries']);
            unset($value['sql_injection_raw_queries']);
        }

        if (array_key_exists('foreign_key_mapping', $value)) {
            $this->_usedProperties['foreignKeyMapping'] = true;
            $this->foreignKeyMapping = new \Symfony\Config\DoctrineDoctor\Analyzers\ForeignKeyMappingConfig($value['foreign_key_mapping']);
            unset($value['foreign_key_mapping']);
        }

        if (array_key_exists('partial_object', $value)) {
            $this->_usedProperties['partialObject'] = true;
            $this->partialObject = new \Symfony\Config\DoctrineDoctor\Analyzers\PartialObjectConfig($value['partial_object']);
            unset($value['partial_object']);
        }

        if (array_key_exists('dto_hydration', $value)) {
            $this->_usedProperties['dtoHydration'] = true;
            $this->dtoHydration = new \Symfony\Config\DoctrineDoctor\Analyzers\DtoHydrationConfig($value['dto_hydration']);
            unset($value['dto_hydration']);
        }

        if (array_key_exists('cascade_all', $value)) {
            $this->_usedProperties['cascadeAll'] = true;
            $this->cascadeAll = new \Symfony\Config\DoctrineDoctor\Analyzers\CascadeAllConfig($value['cascade_all']);
            unset($value['cascade_all']);
        }

        if (array_key_exists('cascade_persist_independent', $value)) {
            $this->_usedProperties['cascadePersistIndependent'] = true;
            $this->cascadePersistIndependent = new \Symfony\Config\DoctrineDoctor\Analyzers\CascadePersistIndependentConfig($value['cascade_persist_independent']);
            unset($value['cascade_persist_independent']);
        }

        if (array_key_exists('missing_orphan_removal', $value)) {
            $this->_usedProperties['missingOrphanRemoval'] = true;
            $this->missingOrphanRemoval = new \Symfony\Config\DoctrineDoctor\Analyzers\MissingOrphanRemovalConfig($value['missing_orphan_removal']);
            unset($value['missing_orphan_removal']);
        }

        if (array_key_exists('cascade_remove_independent', $value)) {
            $this->_usedProperties['cascadeRemoveIndependent'] = true;
            $this->cascadeRemoveIndependent = new \Symfony\Config\DoctrineDoctor\Analyzers\CascadeRemoveIndependentConfig($value['cascade_remove_independent']);
            unset($value['cascade_remove_independent']);
        }

        if (array_key_exists('bidirectional_consistency', $value)) {
            $this->_usedProperties['bidirectionalConsistency'] = true;
            $this->bidirectionalConsistency = new \Symfony\Config\DoctrineDoctor\Analyzers\BidirectionalConsistencyConfig($value['bidirectional_consistency']);
            unset($value['bidirectional_consistency']);
        }

        if (array_key_exists('orphan_removal_no_cascade', $value)) {
            $this->_usedProperties['orphanRemovalNoCascade'] = true;
            $this->orphanRemovalNoCascade = new \Symfony\Config\DoctrineDoctor\Analyzers\OrphanRemovalNoCascadeConfig($value['orphan_removal_no_cascade']);
            unset($value['orphan_removal_no_cascade']);
        }

        if (array_key_exists('ondelete_mismatch', $value)) {
            $this->_usedProperties['ondeleteMismatch'] = true;
            $this->ondeleteMismatch = new \Symfony\Config\DoctrineDoctor\Analyzers\OndeleteMismatchConfig($value['ondelete_mismatch']);
            unset($value['ondelete_mismatch']);
        }

        if (array_key_exists('join_optimization', $value)) {
            $this->_usedProperties['joinOptimization'] = true;
            $this->joinOptimization = new \Symfony\Config\DoctrineDoctor\Analyzers\JoinOptimizationConfig($value['join_optimization']);
            unset($value['join_optimization']);
        }

        if (array_key_exists('doctrine_cache', $value)) {
            $this->_usedProperties['doctrineCache'] = true;
            $this->doctrineCache = new \Symfony\Config\DoctrineDoctor\Analyzers\DoctrineCacheConfig($value['doctrine_cache']);
            unset($value['doctrine_cache']);
        }

        if (array_key_exists('naming_convention', $value)) {
            $this->_usedProperties['namingConvention'] = true;
            $this->namingConvention = new \Symfony\Config\DoctrineDoctor\Analyzers\NamingConventionConfig($value['naming_convention']);
            unset($value['naming_convention']);
        }

        if (array_key_exists('missing_embeddable_opportunity', $value)) {
            $this->_usedProperties['missingEmbeddableOpportunity'] = true;
            $this->missingEmbeddableOpportunity = new \Symfony\Config\DoctrineDoctor\Analyzers\MissingEmbeddableOpportunityConfig($value['missing_embeddable_opportunity']);
            unset($value['missing_embeddable_opportunity']);
        }

        if (array_key_exists('blameable_trait', $value)) {
            $this->_usedProperties['blameableTrait'] = true;
            $this->blameableTrait = new \Symfony\Config\DoctrineDoctor\Analyzers\BlameableTraitConfig($value['blameable_trait']);
            unset($value['blameable_trait']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['nPlusOne'])) {
            $output['n_plus_one'] = $this->nPlusOne->toArray();
        }
        if (isset($this->_usedProperties['slowQuery'])) {
            $output['slow_query'] = $this->slowQuery->toArray();
        }
        if (isset($this->_usedProperties['missingIndex'])) {
            $output['missing_index'] = $this->missingIndex->toArray();
        }
        if (isset($this->_usedProperties['hydration'])) {
            $output['hydration'] = $this->hydration->toArray();
        }
        if (isset($this->_usedProperties['eagerLoading'])) {
            $output['eager_loading'] = $this->eagerLoading->toArray();
        }
        if (isset($this->_usedProperties['findAll'])) {
            $output['find_all'] = $this->findAll->toArray();
        }
        if (isset($this->_usedProperties['entityManagerClear'])) {
            $output['entity_manager_clear'] = $this->entityManagerClear->toArray();
        }
        if (isset($this->_usedProperties['getReference'])) {
            $output['get_reference'] = $this->getReference->toArray();
        }
        if (isset($this->_usedProperties['flushInLoop'])) {
            $output['flush_in_loop'] = $this->flushInLoop->toArray();
        }
        if (isset($this->_usedProperties['lazyLoading'])) {
            $output['lazy_loading'] = $this->lazyLoading->toArray();
        }
        if (isset($this->_usedProperties['dqlInjection'])) {
            $output['dql_injection'] = $this->dqlInjection->toArray();
        }
        if (isset($this->_usedProperties['bulkOperation'])) {
            $output['bulk_operation'] = $this->bulkOperation->toArray();
        }
        if (isset($this->_usedProperties['strictMode'])) {
            $output['strict_mode'] = $this->strictMode->toArray();
        }
        if (isset($this->_usedProperties['charset'])) {
            $output['charset'] = $this->charset->toArray();
        }
        if (isset($this->_usedProperties['innoDbEngine'])) {
            $output['inno_db_engine'] = $this->innoDbEngine->toArray();
        }
        if (isset($this->_usedProperties['connectionPooling'])) {
            $output['connection_pooling'] = $this->connectionPooling->toArray();
        }
        if (isset($this->_usedProperties['collectionInitialization'])) {
            $output['collection_initialization'] = $this->collectionInitialization->toArray();
        }
        if (isset($this->_usedProperties['cascadeConfiguration'])) {
            $output['cascade_configuration'] = $this->cascadeConfiguration->toArray();
        }
        if (isset($this->_usedProperties['sensitiveDataExposure'])) {
            $output['sensitive_data_exposure'] = $this->sensitiveDataExposure->toArray();
        }
        if (isset($this->_usedProperties['insecureRandom'])) {
            $output['insecure_random'] = $this->insecureRandom->toArray();
        }
        if (isset($this->_usedProperties['sqlInjectionRawQueries'])) {
            $output['sql_injection_raw_queries'] = $this->sqlInjectionRawQueries->toArray();
        }
        if (isset($this->_usedProperties['foreignKeyMapping'])) {
            $output['foreign_key_mapping'] = $this->foreignKeyMapping->toArray();
        }
        if (isset($this->_usedProperties['partialObject'])) {
            $output['partial_object'] = $this->partialObject->toArray();
        }
        if (isset($this->_usedProperties['dtoHydration'])) {
            $output['dto_hydration'] = $this->dtoHydration->toArray();
        }
        if (isset($this->_usedProperties['cascadeAll'])) {
            $output['cascade_all'] = $this->cascadeAll->toArray();
        }
        if (isset($this->_usedProperties['cascadePersistIndependent'])) {
            $output['cascade_persist_independent'] = $this->cascadePersistIndependent->toArray();
        }
        if (isset($this->_usedProperties['missingOrphanRemoval'])) {
            $output['missing_orphan_removal'] = $this->missingOrphanRemoval->toArray();
        }
        if (isset($this->_usedProperties['cascadeRemoveIndependent'])) {
            $output['cascade_remove_independent'] = $this->cascadeRemoveIndependent->toArray();
        }
        if (isset($this->_usedProperties['bidirectionalConsistency'])) {
            $output['bidirectional_consistency'] = $this->bidirectionalConsistency->toArray();
        }
        if (isset($this->_usedProperties['orphanRemovalNoCascade'])) {
            $output['orphan_removal_no_cascade'] = $this->orphanRemovalNoCascade->toArray();
        }
        if (isset($this->_usedProperties['ondeleteMismatch'])) {
            $output['ondelete_mismatch'] = $this->ondeleteMismatch->toArray();
        }
        if (isset($this->_usedProperties['joinOptimization'])) {
            $output['join_optimization'] = $this->joinOptimization->toArray();
        }
        if (isset($this->_usedProperties['doctrineCache'])) {
            $output['doctrine_cache'] = $this->doctrineCache->toArray();
        }
        if (isset($this->_usedProperties['namingConvention'])) {
            $output['naming_convention'] = $this->namingConvention->toArray();
        }
        if (isset($this->_usedProperties['missingEmbeddableOpportunity'])) {
            $output['missing_embeddable_opportunity'] = $this->missingEmbeddableOpportunity->toArray();
        }
        if (isset($this->_usedProperties['blameableTrait'])) {
            $output['blameable_trait'] = $this->blameableTrait->toArray();
        }

        return $output;
    }

}
