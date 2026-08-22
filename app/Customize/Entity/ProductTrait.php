<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月17日作成
   *
   * Customize\Entity\ProductTrait.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/
    namespace Customize\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Eccube\Annotation\EntityExtension;
    use Eccube\Entity\Product;
    


    /** 
    * @EntityExtension("Eccube\Entity\Product")
    */

    //#[EntityExtension(Product::class)] 
    //#[EntityExtension('Eccube\Entity\Product')]
    //#[ORM\Entity(product::class)]

    Trait ProductTrait 
    {
        /**
         * @var Shop
         * @ORM\ManyToOne(targetEntity="Customize\Entity\Shop")
         * @ORM\JoinColumn(name="shop_id", referencedColumnName="id", nullable=true)
         */
        private $Shop;

        /**
         * @var string
         * @ORM\Column(name="free_input_name1", type="text", nullable=true)
         */
        private $freeInputName1;
	
        /**
         * @var string
         * @ORM\Column(name="free_input_name2", type="text", nullable=true)
         */
        private $freeInputName2;

        /**
         * @var string
         * @ORM\Column(name="free_input_name3", type="text", nullable=true)
         */
        private $freeInputName3;


       /**
         * @param \Customize\Entity\Shop|null $Shop
         * @return Product
         */
        public function setShop(Shop $Shop = null)
        {
            $this->Shop = $Shop;

            return $this;
        }

        /**
         * @return \Customize\Entity\Shop|null
         */
        public function getShop()
        {
            return $this->Shop;
        }

        public function setFreeInputName1(?string $freeInputName1): self
        {
            $this->freeInputName1 = $freeInputName1;
            return $this;
        }

        public function getFreeInputName1(): ?string
        {
            return $this->freeInputName1;
        }

        public function setFreeInputName2(?string $freeInputName2): self
        {
            $this->freeInputName2 = $freeInputName2;
            return $this;
        }

        public function getFreeInputName2(): ?string
        {
            return $this->freeInputName2;
        }

        public function setFreeInputName3(?string $freeInputName3): self
        {
            $this->freeInputName3 = $freeInputName3;
            return $this;
        }

        public function getFreeInputName3(): ?string
        {
            return $this->freeInputName3;
        }

    }