<?php
/**
 * @version EC=CUBE4.3系
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年10月10日作成
 *
 * app\Customize\Service\PurchaseFlow\Processor\DeliveryFeeFreeByShippingPreprocessor.php
 *
 *
 *
 * 無視する
 *
 *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/

namespace Customize\Service\PurchaseFlow\Processor;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 送料無料条件を適用する.
 * お届け先ごとに条件判定を行う.
 */
class DeliveryFeeFreeByShippingPreprocessor implements ItemHolderPreprocessor
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * DeliveryFeeProcessor constructor.
     *
     * @param BaseInfoRepository $baseInfoRepository
     */
    public function __construct(BaseInfoRepository $baseInfoRepository)
    {
        $this->BaseInfo = $baseInfoRepository->get();
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        return ;
        if (!($this->BaseInfo->getDeliveryFreeAmount() || $this->BaseInfo->getDeliveryFreeQuantity())) {
            return;
        }

        // Orderの場合はお届け先ごとに判定する.
        if ($itemHolder instanceof Order) {
            /** @var Order $Order */
            $Order = $itemHolder;
            foreach ($Order->getShippings() as $Shipping) {
                $isFree = false;
                $total = 0;
                $quantity = 0;
                foreach ($Shipping->getProductOrderItems() as $Item) {
                    $total += $Item->getPriceIncTax() * $Item->getQuantity();
                    $quantity += $Item->getQuantity();
                }
                // 送料無料（金額）を超えている
                if ($this->BaseInfo->getDeliveryFreeAmount()) {
                    if ($total >= $this->BaseInfo->getDeliveryFreeAmount()) {
                        $isFree = true;
                    }
                }
                // 送料無料（個数）を超えている
                if ($this->BaseInfo->getDeliveryFreeQuantity()) {
                    if ($quantity >= $this->BaseInfo->getDeliveryFreeQuantity()) {
                        $isFree = true;
                    }
                }
                if ($isFree) {
                    foreach ($Shipping->getOrderItems() as $Item) {
                        if ($Item->getProcessorName() == DeliveryFeePreprocessor::class) {
                            $Item->setQuantity(0);
                        }
                    }
                }
            }
        }
    }
}
