<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月06日作成
   *
   * app\Customize\Entity\Master\ShinaiLength.php
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
     * ShinaiLength
     *
     * @ORM\Table(name="mtb_shinai_length")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Customize\Repository\Master\ShinaiLengthRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class ShinaiLength extends \Eccube\Entity\Master\AbstractMasterEntity
    {

    }
