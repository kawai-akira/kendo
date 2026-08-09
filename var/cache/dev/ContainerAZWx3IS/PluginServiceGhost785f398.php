<?php

namespace ContainerAZWx3IS;
include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Eccube'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'PluginService.php';

class PluginServiceGhost785f398 extends \Eccube\Service\PluginService implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".'*'."\0".'cacheUtil' => [parent::class, 'cacheUtil', null],
        "\0".'*'."\0".'composerService' => [parent::class, 'composerService', null],
        "\0".'*'."\0".'container' => [parent::class, 'container', null],
        "\0".'*'."\0".'eccubeConfig' => [parent::class, 'eccubeConfig', null],
        "\0".'*'."\0".'entityManager' => [parent::class, 'entityManager', null],
        "\0".'*'."\0".'entityProxyService' => [parent::class, 'entityProxyService', null],
        "\0".'*'."\0".'pluginRepository' => [parent::class, 'pluginRepository', null],
        "\0".'*'."\0".'schemaService' => [parent::class, 'schemaService', null],
        "\0".parent::class."\0".'environment' => [parent::class, 'environment', null],
        "\0".parent::class."\0".'pluginApiService' => [parent::class, 'pluginApiService', null],
        "\0".parent::class."\0".'pluginContext' => [parent::class, 'pluginContext', null],
        "\0".parent::class."\0".'projectRoot' => [parent::class, 'projectRoot', null],
        "\0".parent::class."\0".'systemService' => [parent::class, 'systemService', null],
        'cacheUtil' => [parent::class, 'cacheUtil', null],
        'composerService' => [parent::class, 'composerService', null],
        'container' => [parent::class, 'container', null],
        'eccubeConfig' => [parent::class, 'eccubeConfig', null],
        'entityManager' => [parent::class, 'entityManager', null],
        'entityProxyService' => [parent::class, 'entityProxyService', null],
        'environment' => [parent::class, 'environment', null],
        'pluginApiService' => [parent::class, 'pluginApiService', null],
        'pluginContext' => [parent::class, 'pluginContext', null],
        'pluginRepository' => [parent::class, 'pluginRepository', null],
        'projectRoot' => [parent::class, 'projectRoot', null],
        'schemaService' => [parent::class, 'schemaService', null],
        'systemService' => [parent::class, 'systemService', null],
    ];
}

// Help opcache.preload discover always-needed symbols
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('PluginServiceGhost785f398', false)) {
    \class_alias(__NAMESPACE__.'\\PluginServiceGhost785f398', 'PluginServiceGhost785f398', false);
}
