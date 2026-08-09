<?php

namespace ContainerAZWx3IS;
include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Eccube'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'Composer'.\DIRECTORY_SEPARATOR.'ComposerServiceInterface.php';
include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Eccube'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'Composer'.\DIRECTORY_SEPARATOR.'ComposerApiService.php';

class ComposerApiServiceGhost8881cc2 extends \Eccube\Service\Composer\ComposerApiService implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".'*'."\0".'eccubeConfig' => [parent::class, 'eccubeConfig', null],
        "\0".parent::class."\0".'baseInfoRepository' => [parent::class, 'baseInfoRepository', null],
        "\0".parent::class."\0".'consoleApplication' => [parent::class, 'consoleApplication', null],
        "\0".parent::class."\0".'pluginContext' => [parent::class, 'pluginContext', null],
        "\0".parent::class."\0".'schemaService' => [parent::class, 'schemaService', null],
        "\0".parent::class."\0".'workingDir' => [parent::class, 'workingDir', null],
        'baseInfoRepository' => [parent::class, 'baseInfoRepository', null],
        'consoleApplication' => [parent::class, 'consoleApplication', null],
        'eccubeConfig' => [parent::class, 'eccubeConfig', null],
        'pluginContext' => [parent::class, 'pluginContext', null],
        'schemaService' => [parent::class, 'schemaService', null],
        'workingDir' => [parent::class, 'workingDir', null],
    ];
}

// Help opcache.preload discover always-needed symbols
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('ComposerApiServiceGhost8881cc2', false)) {
    \class_alias(__NAMESPACE__.'\\ComposerApiServiceGhost8881cc2', 'ComposerApiServiceGhost8881cc2', false);
}
