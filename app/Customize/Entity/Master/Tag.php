<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月06日作成
   *
   * app\Customize\Entity\Master\Tag.php
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
     * Tag
     *
     * @ORM\Table(name="mtb_tag")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Customize\Repository\Master\TagRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class Tag extends \Eccube\Entity\Master\AbstractMasterEntity
    {

    }
