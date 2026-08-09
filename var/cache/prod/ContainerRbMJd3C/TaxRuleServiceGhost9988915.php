<?php

namespace ContainerRbMJd3C;

include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Eccube'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'TaxRuleService.php';
class TaxRuleServiceGhost9988915 extends \Eccube\Service\TaxRuleService implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;
    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".'*'."\0".'BaseInfo' => [parent::class, 'BaseInfo', null],
        "\0".'*'."\0".'taxRuleRepository' => [parent::class, 'taxRuleRepository', null],
        'BaseInfo' => [parent::class, 'BaseInfo', null],
        'taxRuleRepository' => [parent::class, 'taxRuleRepository', null],
    ];
}
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('TaxRuleServiceGhost9988915', false)) {
    \class_alias(__NAMESPACE__.'\\TaxRuleServiceGhost9988915', 'TaxRuleServiceGhost9988915', false);
}
