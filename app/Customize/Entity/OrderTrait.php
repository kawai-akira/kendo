<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月24日作成
   *
   * app\Customize\Entity\OrderTrait.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/
    namespace Customize\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Eccube\Annotation\EntityExtension;
    use Customize\Entity\Shop;
    


    /** 
    * @EntityExtension("Eccube\Entity\Order")
    */



    Trait OrderTrait 
    {
        /**
         * @var Shop
         * @ORM\ManyToOne(targetEntity="Customize\Entity\Shop")
         * @ORM\JoinColumn(name="shop_id", referencedColumnName="id", nullable=true)
         */
        private $Shop;

        /**
         * @var string|null
         *
         * @ORM\Column(name="fax_number", type="string", length=14, nullable=true)
         */
        private $fax_number;

 
        public function setShop(?Shop $Shop = null): self
        {
            $this->Shop = $Shop;

            return $this;
        }
        public function getShop(): Shop
        {
            return $this->Shop;
        }


                /**
         * Set fax_number.
         *
         * @param string|null $fax_number
         *
         * @return Order
         */
        public function setFaxNumber($fax_number = null)
        {
            $this->fax_number = $fax_number;

            return $this;
        }

        /**
         * Get fax_number.
         *
         * @return string|null
         */
        public function getFaxNumber()
        {
            return $this->fax_number;
        }

    }