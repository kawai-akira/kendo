<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月06日作成
   *
   * app\Customize\Entity\Master\AuthorityTrait.php
   *
   * const AUTHORITY_SYSTEM_ADMIN = 0; 
   * const AUTHORITY_SHOP = 1;  → 1
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/
    namespace Customize\Entity\Master;

    use Doctrine\ORM\Mapping as ORM;
    use Eccube\Annotation\EntityExtension;


    /**
     * @EntityExtension("Eccube\Entity\Master\Authority")
     */

    Trait AuthorityTrait 
    {

        public const SHOP = 1 ;


    }
