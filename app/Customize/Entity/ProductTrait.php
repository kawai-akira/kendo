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
    use Customize\Entity\Master\ProductType;
    


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
         * @var string
         * @ORM\Column(name="item_features", type="text", nullable=true)
         */
        private $ItemFeatures;

        /**
         * @var ProductType
         * @ORM\ManyToOne(targetEntity="Customize\Entity\Master\ProductType")
         * @ORM\JoinColumn(name="product_type_id", referencedColumnName="id", nullable=true)
         */
        private $ProductType;

        /**
         * @var string
         * @ORM\Column(name="material", type="text", nullable=true)
         */
        private $Material;

        /**
         * @var string
         * @ORM\Column(name="weight", length=255, nullable=true)
         */
        private $Weight;



        /**
         * @var string
         * @ORM\Column(name="stitch_type", length=255, nullable=true)
         */
        private $StitchType;
                /**
         * @var string
         * @ORM\Column(name="stitch_width", length=255, nullable=true)
         */
        private $StitchWidth;
         
        /**
         * @var string
         * @ORM\Column(name="men_base_size", length=255, nullable=true)
         */
        private $MenBaseSize;
        
        /**
         * @var string
         * @ORM\Column(name="kote_base_size", length=255, nullable=true)
         */
        private $KoteBaseSize;
        /**
         * @var string
         * @ORM\Column(name="tare_base_size", length=255, nullable=true)
         */
        private $TareBaseSize;
        
        /**
         * @var string
         * @ORM\Column(name="utikomi", length=255, nullable=true)
         */
        private $Utikomi;
        /**
         * @var string
         * @ORM\Column(name="dou_base_size", length=255, nullable=true)
         */
        private $DouBaseSize;


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


        public function setItemFeatures(?string $ItemFeatures): self
        {
            $this->ItemFeatures = $ItemFeatures;
            return $this;
        }

        public function getItemFeatures(): ?string
        {
            return $this->ItemFeatures;
        }

        public function setProductType(ProductType $ProductType = null): self
        {
            $this->ProductType = $ProductType;
            return $this;
        }

        public function getProductType() :ProductType
        {
            return $this->ProductType;
        }

        public function setMaterial(?string $Material): self
        {
            $this->Material = $Material;
            return $this;
        }

        public function getMaterial(): ?string
        {
            return $this->Material;
        }

        public function setWeight(?string $Weight): self
        {
            $this->Weight = $Weight;
            return $this;
        }

        public function getWeight(): ?string
        {
            return $this->Weight;
        }
    
        public function setStitchType(?string $StitchType): self
        {
            $this->StitchType = $StitchType;
            return $this;
        }

        public function getStitchType(): ?string
        {
            return $this->StitchType;
        }
    
        public function setStitchWidth(?string $StitchWidth): self
        {
            $this->StitchWidth = $StitchWidth;
            return $this;
        }

        public function getStitchWidth(): ?string
        {
            return $this->StitchWidth;
        }
    
        public function setMenBaseSize(?string $MenBaseSize): self
        {
            $this->MenBaseSize = $MenBaseSize;
            return $this;
        }

        public function getMenBaseSize(): ?string
        {
            return $this->MenBaseSize;
        }
    
        public function setKoteBaseSize(?string $KoteBaseSize): self
        {
            $this->KoteBaseSize = $KoteBaseSize;
            return $this;
        }

        public function getKoteBaseSize(): ?string
        {
            return $this->KoteBaseSize;
        }
    
        public function setTareBaseSize(?string $TareBaseSize): self
        {
            $this->TareBaseSize = $TareBaseSize;
            return $this;
        }

        public function getTareBaseSize(): ?string
        {
            return $this->TareBaseSize;
        }
    
        public function setUtikomi(?string $Utikomi): self
        {
            $this->Utikomi = $Utikomi;
            return $this;
        }

        public function getUtikomi(): ?string
        {
            return $this->Utikomi;
        }
    
        public function setDouBaseSize(?string $DouBaseSize): self
        {
            $this->DouBaseSize = $DouBaseSize;
            return $this;
        }

        public function getDouBaseSize(): ?string
        {
            return $this->DouBaseSize;
        }

    }