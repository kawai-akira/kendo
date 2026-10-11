<?php
/**
 * @version EC=CUBE4.3系
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年09月23日作成
 *
 * app\Customize\Service\DeliveryFeeService.php
 *
 *
 * クーポンサービス
 *
 * 
 * 　     
 *                          C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Service;


use Eccube\Entity\Shipping;
use Eccube\Entity\DeliveryFee;
use Eccube\Repository\DeliveryFeeRepository;
use Customize\entity\Shop;
class DeliveryFeeService{

    /**
     * @var DeliveryFeeRepository
     */
    private $DeliveryFeeRepository;

    /**
     * @var Shop
     */
    private $Shop;


    public function __construct(
            DeliveryFeeRepository $DeliveryFeeRepository
    )
    { 
        $this->DeliveryFeeRepository = $DeliveryFeeRepository;
    }

public function setDeliveryFee(Shipping $Shipping){

    

    $total = 0;
    foreach ($Shipping->getProductOrderItems() as $Item){
        $total += $Item->getPriceIncTax() * $Item->getQuantity();
      
        $this->Shop = $Item->getShop();
    } 
    if(!$this->Shop){return 0;}

         /** @var DeliveryFee|null $DeliveryFee */
           $DeliveryFee = $this->DeliveryFeeRepository->findOneBy([
               'Delivery' => $Shipping->getDelivery(),
               'Pref' => $Shipping->getPref(),
           ]);

        $deliveryFreeAmount = $this->Shop->getdeliveryFreeAmount();

        $fee =  $deliveryFreeAmount <= $total ? 0 : $DeliveryFee->getFee();


        return $fee;

    }

 
    public function getShop(): ?Shop
    {
        return $this->Shop;
    }
}