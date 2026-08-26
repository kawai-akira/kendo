<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月06日作成
   *
   * app\Customize\Entity\Master\ProductType.php
   *
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/

namespace Customize\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

    /**
     * ProductType
     *
     * @ORM\Table(name="mtb_product_type")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Customize\Repository\Master\ProductTypeRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class ProductType extends \Eccube\Entity\Master\AbstractMasterEntity
    {

    }
