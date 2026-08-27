<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月26日作成
   *
   * app\Customize\Entity\CategoryTrait.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/
    namespace Customize\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Eccube\Annotation\EntityExtension;
    use Eccube\Entit\Category;


    /**
     * @EntityExtension("Eccube\Entity\Category")
     */

    Trait CategoryTrait 
    {
        const SET = 9;
        const MEN = 10;
        const KOTE = 11;
        const DOU = 12;
        const TARE = 13;
        const DOUI = 4;
        const HAKAMA = 3;
        const SHINAI = 6;
        const ZEKKEN = 16;
        const SHINAIBUKURO = 14;
        const BOUGUBUKURO = 15;
        const SONOTA = 17;
    }