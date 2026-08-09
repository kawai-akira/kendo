<?php

namespace ContainerAZWx3IS;
include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Eccube'.\DIRECTORY_SEPARATOR.'Service'.\DIRECTORY_SEPARATOR.'CartService.php';

class CartServiceGhostE8ccf99 extends \Eccube\Service\CartService implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".'*'."\0".'authorizationChecker' => [parent::class, 'authorizationChecker', null],
        "\0".'*'."\0".'cart' => [parent::class, 'cart', null],
        "\0".'*'."\0".'cartItemAllocator' => [parent::class, 'cartItemAllocator', null],
        "\0".'*'."\0".'cartItemComparator' => [parent::class, 'cartItemComparator', null],
        "\0".'*'."\0".'cartRepository' => [parent::class, 'cartRepository', null],
        "\0".'*'."\0".'carts' => [parent::class, 'carts', null],
        "\0".'*'."\0".'entityManager' => [parent::class, 'entityManager', null],
        "\0".'*'."\0".'orderRepository' => [parent::class, 'orderRepository', null],
        "\0".'*'."\0".'productClassRepository' => [parent::class, 'productClassRepository', null],
        "\0".'*'."\0".'session' => [parent::class, 'session', null],
        "\0".'*'."\0".'tokenStorage' => [parent::class, 'tokenStorage', null],
        'authorizationChecker' => [parent::class, 'authorizationChecker', null],
        'cart' => [parent::class, 'cart', null],
        'cartItemAllocator' => [parent::class, 'cartItemAllocator', null],
        'cartItemComparator' => [parent::class, 'cartItemComparator', null],
        'cartRepository' => [parent::class, 'cartRepository', null],
        'carts' => [parent::class, 'carts', null],
        'entityManager' => [parent::class, 'entityManager', null],
        'orderRepository' => [parent::class, 'orderRepository', null],
        'productClassRepository' => [parent::class, 'productClassRepository', null],
        'session' => [parent::class, 'session', null],
        'tokenStorage' => [parent::class, 'tokenStorage', null],
    ];
}

// Help opcache.preload discover always-needed symbols
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('CartServiceGhostE8ccf99', false)) {
    \class_alias(__NAMESPACE__.'\\CartServiceGhostE8ccf99', 'CartServiceGhostE8ccf99', false);
}
