<?php

namespace ContainerRbMJd3C;

include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Eccube'.\DIRECTORY_SEPARATOR.'Repository'.\DIRECTORY_SEPARATOR.'TaxRuleRepository.php';
class TaxRuleRepositoryGhost4765c6c extends \Eccube\Repository\TaxRuleRepository implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;
    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".'*'."\0".'_class' => [parent::class, '_class', null],
        "\0".'*'."\0".'_em' => [parent::class, '_em', null],
        "\0".'*'."\0".'_entityName' => [parent::class, '_entityName', null],
        "\0".'*'."\0".'authorizationChecker' => [parent::class, 'authorizationChecker', null],
        "\0".'*'."\0".'baseInfo' => [parent::class, 'baseInfo', null],
        "\0".'*'."\0".'eccubeConfig' => [parent::class, 'eccubeConfig', null],
        "\0".'*'."\0".'tokenStorage' => [parent::class, 'tokenStorage', null],
        "\0".'Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository'."\0".'entityClass' => ['Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository', 'entityClass', null],
        "\0".'Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository'."\0".'registry' => ['Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository', 'registry', null],
        "\0".parent::class."\0".'rules' => [parent::class, 'rules', null],
        '_class' => [parent::class, '_class', null],
        '_em' => [parent::class, '_em', null],
        '_entityName' => [parent::class, '_entityName', null],
        'authorizationChecker' => [parent::class, 'authorizationChecker', null],
        'baseInfo' => [parent::class, 'baseInfo', null],
        'eccubeConfig' => [parent::class, 'eccubeConfig', null],
        'entityClass' => ['Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository', 'entityClass', null],
        'registry' => ['Doctrine\\Bundle\\DoctrineBundle\\Repository\\LazyServiceEntityRepository', 'registry', null],
        'rules' => [parent::class, 'rules', null],
        'tokenStorage' => [parent::class, 'tokenStorage', null],
    ];
}
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('TaxRuleRepositoryGhost4765c6c', false)) {
    \class_alias(__NAMESPACE__.'\\TaxRuleRepositoryGhost4765c6c', 'TaxRuleRepositoryGhost4765c6c', false);
}
