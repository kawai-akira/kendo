<?php

namespace ContainerRbMJd3C;

include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Eccube'.\DIRECTORY_SEPARATOR.'Repository'.\DIRECTORY_SEPARATOR.'PluginRepository.php';
class PluginRepositoryGhostD6a6052 extends \Eccube\Repository\PluginRepository implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;
    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".'*'."\0".'_class' => [parent::class, '_class', null],
        "\0".'*'."\0".'_em' => [parent::class, '_em', null],
        "\0".'*'."\0".'_entityName' => [parent::class, '_entityName', null],
        "\0".'*'."\0".'eccubeConfig' => [parent::class, 'eccubeConfig', null],
        "\0".'Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository'."\0".'entityClass' => ['Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository', 'entityClass', null],
        "\0".'Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository'."\0".'registry' => ['Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository', 'registry', null],
        '_class' => [parent::class, '_class', null],
        '_em' => [parent::class, '_em', null],
        '_entityName' => [parent::class, '_entityName', null],
        'eccubeConfig' => [parent::class, 'eccubeConfig', null],
        'entityClass' => ['Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository', 'entityClass', null],
        'registry' => ['Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository', 'registry', null],
    ];
}
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('PluginRepositoryGhostD6a6052', false)) {
    \class_alias(__NAMESPACE__.'\\PluginRepositoryGhostD6a6052', 'PluginRepositoryGhostD6a6052', false);
}
