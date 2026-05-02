<?php

namespace ContainerG2VwSaC;
include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR.'league'.\DIRECTORY_SEPARATOR.'flysystem'.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'FilesystemAdapter.php';
include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR.'league'.\DIRECTORY_SEPARATOR.'flysystem'.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'ChecksumProvider.php';
include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR.'league'.\DIRECTORY_SEPARATOR.'flysystem-local'.\DIRECTORY_SEPARATOR.'LocalFilesystemAdapter.php';

class LocalFilesystemAdapterGhostA489400 extends \League\Flysystem\Local\LocalFilesystemAdapter implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".parent::class."\0".'linkHandling' => [parent::class, 'linkHandling', null, 16],
        "\0".parent::class."\0".'mimeTypeDetector' => [parent::class, 'mimeTypeDetector', null, 16],
        "\0".parent::class."\0".'prefixer' => [parent::class, 'prefixer', null, 16],
        "\0".parent::class."\0".'rootLocation' => [parent::class, 'rootLocation', null, 16],
        "\0".parent::class."\0".'rootLocationIsSetup' => [parent::class, 'rootLocationIsSetup', null, 16],
        "\0".parent::class."\0".'visibility' => [parent::class, 'visibility', null, 16],
        "\0".parent::class."\0".'writeFlags' => [parent::class, 'writeFlags', null, 16],
        'linkHandling' => [parent::class, 'linkHandling', null, 16],
        'mimeTypeDetector' => [parent::class, 'mimeTypeDetector', null, 16],
        'prefixer' => [parent::class, 'prefixer', null, 16],
        'rootLocation' => [parent::class, 'rootLocation', null, 16],
        'rootLocationIsSetup' => [parent::class, 'rootLocationIsSetup', null, 16],
        'visibility' => [parent::class, 'visibility', null, 16],
        'writeFlags' => [parent::class, 'writeFlags', null, 16],
    ];
}

// Help opcache.preload discover always-needed symbols
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('LocalFilesystemAdapterGhostA489400', false)) {
    \class_alias(__NAMESPACE__.'\\LocalFilesystemAdapterGhostA489400', 'LocalFilesystemAdapterGhostA489400', false);
}
