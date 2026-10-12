<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年10月11日作成
   *
   * app\Customize\Entity\ShippingTrait.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/
    namespace Customize\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Eccube\Annotation\EntityExtension;
    use Eccube\Entit\Shgipping;


    /**
     * @EntityExtension("Eccube\Entity\Shipping")
     */

    Trait ShippingTrait 
    {

        public function getShop(): shop
        {

        $Items = $this->getProductOrderItems();
        return $Items[0]->getShop();

        }
    }