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
        const SET  = 9;
        const SET2 = 81;
        const MEN  = [10,9,81];
        const KOTE = [11,9,81];
        const DOU  = [12,9];
        const TARE = [13,9,81];
        const HEIGHT =[10,9,12,13,81,4,3];
        const SEX    =[10,11,12,13,9,81,4,3];
        const DOUI   =[4];
        const HAKAMA = [3];
        const DOUISEt =[3,4];
        const SHINAI = [6];
        const ZEKKEN = [16];
        const SHINAIBUKURO = 14; # 未使用
        const BOUGUBUKURO = 15; #未使用
        const SONOTA = 17; #未使用
    }