<?php

namespace ContainerAk3JV6P;

include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Eccube'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'TaxRuleService.php';
class TaxRuleServiceGhostE0f5b2e extends \Eccube\Service\TaxRuleService implements \Symfony\Component\VarExporter\LazyObjectInterface
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

if (!\class_exists('TaxRuleServiceGhostE0f5b2e', false)) {
    \class_alias(__NAMESPACE__.'\\TaxRuleServiceGhostE0f5b2e', 'TaxRuleServiceGhostE0f5b2e', false);
}
