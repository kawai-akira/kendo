<?php
/**
 * @version EC=CUBE4.3系
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年03月09日作成
 *
 * app\Customize\Service\CommonService.php
 *
 *
 * 共通サービス
 *
 * YAML・EC-CUBE CONFIG の読み込み・サービス
 *
 *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Service;

use Customize\Repository\ProductRepository;
use Customize\Repository\OrderItemRepository;

class ProductServics
{

/**
 * @var ProductRepository
 */
 private $ProductRepository;

/**
 * @var OrderItemRepository
 */
 private $OrderItemRepository;

 public function __construct(
     ProductRepository $ProductRepository
    ,OrderItemRepository $OrderItemRepository  
){
    $this->ProductRepository = $ProductRepository;
    $this->OrderItemRepository = $OrderItemRepository;

}

    public Function getProductTotal(){

        log_info('商品売り上げランキングSET開始');
        $this->ProductRepository->intializeTotal();
        $this->OrderItemRepository->updateProductTotal();
        log_info('商品売り上げランキングSET終了');

    }
}