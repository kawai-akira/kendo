<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年09月19日作成
   *
   * app\Customize\Entity\Master\Recommendkbn.php
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
     * @ORM\Table(name="mtb_recommend_kbn")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Customize\Repository\Master\RecommendKbnRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class RecommendKbn extends \Eccube\Entity\Master\AbstractMasterEntity
    {

    }
