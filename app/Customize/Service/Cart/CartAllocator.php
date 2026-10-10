<?php
/**
 * @version EC=CUBE4.3系
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年10月08日作成
 *
 *
 * app\Customize\Service\Cart\CartAllocator.php
 *
 * 
 * 商品オプションサービス
 *
 *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Service\Cart;

use Eccube\Entity\CartItem;
use Eccube\Service\Cart\CartItemAllocator;

/**
 * すべての商品種別を同じカートに投入するためのアロケーター
 */
class CartAllocator implements CartItemAllocator
{
    /**
     * 商品の振り分け先となるカートの識別子を決定します。
     * ここで常に固定の文字列（例: '1'）を返すことで、カートの分割を防ぎます。
     *
     * @param CartItem $Item カート商品
     * @return string
     */
    public function allocate(CartItem $Item)
    {
   
        // 本来は $Item->getProductClass()->getSaleType()->getId() などで分けるが、
        // 制御を外すためにすべて同じカートID（'1'）を返します。
        return '1';
    }
}