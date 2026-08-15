<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月06日作成
   *
   * D:\htdocs82\kendo\www\app\Customize\Entity\Master\DouColor.php
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
     * DouColor
     *
     * @ORM\Table(name="mtb_dou_color")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Customize\Repository\Master\DouColorRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class DouColor extends \Eccube\Entity\Master\AbstractMasterEntity
    {

    }
