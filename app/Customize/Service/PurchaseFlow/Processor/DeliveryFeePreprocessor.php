<?php
/**
 * @version EC=CUBE4.3系
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年10月10日作成
 *
 * app\Customize\Service\CommonService.php
 *
 *
 *
 * メーカー毎に配送費を追加する 
 *
 *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/

namespace Customize\Service\PurchaseFlow\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Customize\Service\DeliveryFeeService;
use Eccube\Annotation\ShoppingFlow; 


/**
 * @ shoppingFlow
 */
class DeliveryFeePreprocessor implements ItemHolderPreprocessor
{


    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /** 
     * @var DeliveryFeeService
     */
    private $DeliveryFeeService;
    /**
     * DeliveryFeePreprocessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TaxRuleRepository $taxRuleRepository
     * @param DeliveryFeeService $DeliveryFeeService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TaxRuleRepository $taxRuleRepository
        ,DeliveryFeeService $DeliveryFeeService
    ) {
        $this->entityManager = $entityManager;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->DeliveryFeeService = $DeliveryFeeService;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws \Doctrine\ORM\NoResultException
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        log_info('aaaaaaaaaaa');
        $this->removeDeliveryFeeItem($itemHolder);
        $this->saveDeliveryFeeItem($itemHolder);
    }

    private function removeDeliveryFeeItem(ItemHolderInterface $itemHolder)
    {
        foreach ($itemHolder->getShippings() as $Shipping) {
            foreach ($Shipping->getOrderItems() as $item) {
                if ($item->getProcessorName() == DeliveryFeePreprocessor::class) {
                    $Shipping->removeOrderItem($item);
                    $itemHolder->removeOrderItem($item);
                    $this->entityManager->remove($item);
                }
            }
        }
    }

    /**
     * @param ItemHolderInterface $itemHolder
     *
     * @throws \Doctrine\ORM\NoResultException
     */
    private function saveDeliveryFeeItem(ItemHolderInterface $itemHolder)
    {
        $DeliveryFeeType = $this->entityManager
            ->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $TaxInclude = $this->entityManager
            ->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $Taxation = $this->entityManager
            ->find(TaxType::class, TaxType::TAXATION);

        /** @var Order $Order */
        $Order = $itemHolder;
        /* @var Shipping $Shipping */
        foreach ($Order->getShippings() as $Shipping) {

        $fee = $this->DeliveryFeeService->setDeliveryFee($Shipping);

    
            $OrderItem = new OrderItem();
            $OrderItem->setProductName($DeliveryFeeType->getName())
                ->setPrice($fee)
                ->setQuantity(1)
                ->setOrderItemType($DeliveryFeeType)
                ->setShipping($Shipping)
                ->setOrder($itemHolder)
                ->setTaxDisplayType($TaxInclude)
                ->setTaxType($Taxation)
                ->setProcessorName(DeliveryFeePreprocessor::class)
                ->setShop($this->DeliveryFeeService->getShop());

            $itemHolder->addItem($OrderItem);
            $Shipping->addOrderItem($OrderItem);
        }
    }
}
