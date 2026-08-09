<?php

namespace ContainerRbMJd3C;

include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Eccube'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'SystemService.php';
class SystemServiceGhost5905d9d extends \Eccube\Service\SystemService implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;
    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".'*'."\0".'eccubeConfig' => [parent::class, 'eccubeConfig', null],
        "\0".'*'."\0".'entityManager' => [parent::class, 'entityManager', null],
        "\0".parent::class."\0".'disableMaintenanceAfterResponse' => [parent::class, 'disableMaintenanceAfterResponse', null],
        "\0".parent::class."\0".'maintenanceMode' => [parent::class, 'maintenanceMode', null],
        'disableMaintenanceAfterResponse' => [parent::class, 'disableMaintenanceAfterResponse', null],
        'eccubeConfig' => [parent::class, 'eccubeConfig', null],
        'entityManager' => [parent::class, 'entityManager', null],
        'maintenanceMode' => [parent::class, 'maintenanceMode', null],
    ];
}
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('SystemServiceGhost5905d9d', false)) {
    \class_alias(__NAMESPACE__.'\\SystemServiceGhost5905d9d', 'SystemServiceGhost5905d9d', false);
}
