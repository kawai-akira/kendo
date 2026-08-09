<?php

namespace ContainerAk3JV6P;

include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR.'psr'.\DIRECTORY_SEPARATOR.'log'.\DIRECTORY_SEPARATOR.'Psr'.\DIRECTORY_SEPARATOR.'Log'.\DIRECTORY_SEPARATOR.'AbstractLogger.php';
include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Eccube'.\DIRECTORY_SEPARATOR.'Log'.\DIRECTORY_SEPARATOR.'Logger.php';
class LoggerGhostDc24f23 extends \Eccube\Log\Logger implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;
    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".'*'."\0".'adminLogger' => [parent::class, 'adminLogger', null],
        "\0".'*'."\0".'context' => [parent::class, 'context', null],
        "\0".'*'."\0".'frontLogger' => [parent::class, 'frontLogger', null],
        "\0".'*'."\0".'logger' => [parent::class, 'logger', null],
        'adminLogger' => [parent::class, 'adminLogger', null],
        'context' => [parent::class, 'context', null],
        'frontLogger' => [parent::class, 'frontLogger', null],
        'logger' => [parent::class, 'logger', null],
    ];
}
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('LoggerGhostDc24f23', false)) {
    \class_alias(__NAMESPACE__.'\\LoggerGhostDc24f23', 'LoggerGhostDc24f23', false);
}
