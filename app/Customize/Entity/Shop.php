<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月20日作成
   *
   * app\Customize\Entity\Shop.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/
  namespace Customize\Entity;

    use Doctrine\ORM\Mapping as ORM;
    #use Eccube\Annotation\EntityExtension;
    #use Doctrine\Common\Collections\Collection;
    use Doctrine\Common\Collections\ArrayCollection;
    use Eccube\Entity\AbstractEntity;
    use Eccube\Entity\Member;
    use Eccube\Entity\Master\Pref;
    use Customize\Entity\Master\ShopStatus;

    /**
     * @ORM\Table(name="dtb_shop")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\Entity(repositoryClass="Customize\Repository\ShopRepository")
     * @ORM\HasLifecycleCallbacks()
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     */

    class Shop extends AbstractEntity
    {
        /**
         * @var integer
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var Member
         * @ORM\ManyToOne(targetEntity="\Eccube\Entity\Member")
         * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable=true)
         */
        private $member;

        /**
         * @var ShopStatus
         * @ORM\ManyToOne(targetEntity="\Customize\Entity\Master\ShopStatus")
         * @ORM\JoinColumn(name="shop_status_id", referencedColumnName="id", nullable=true)
         */
        private $ShopStatus;

        /**
         * @var Pref
         * @ORM\ManyToOne(targetEntity="\Eccube\Entity\Master\Pref")
         * @ORM\JoinColumn(name="pref_id", referencedColumnName="id", nullable=true)
         */
        private $pref;

        /**
         * @var string
         * @ORM\Column(name="shop_name", type="string", length=255, nullable=true)
         */
        private $shopName;

        /**
         * @var string
         * @ORM\Column(name="postal_code", type="string", length=8, nullable=true)
         */
        private $postal_code;

        /**
         * @var string
         * @ORM\Column(name="addr01", type="string", length=255, nullable=true)
         */
        private $addr01;

        /**
         * @var string
         * @ORM\Column(name="addr02" ,type="string", length=255, nullable=true)
         */
        private $addr02;

        /**
         * @var string
         * @ORM\Column(name="phone_number", type="string", length=14, nullable=true)
         */
        private $phoneNumber;

        /**
         * @var string
         * @ORM\Column(name="memo", type="text", nullable=true)
         */
        private $memo;

        /**
         * @var string
         * @ORM\Column(name="appeal" , type="text", nullable=true)
         */
        private $appeal;

        /**
         * @var string
         * @ORM\Column(name="shop_image",  type="text", nullable=true)
         */
        private $shopImage;
        /**
         * @var string
         * @ORM\Column(name="delivery_free_amount", type="decimal", precision=10, scale=0, nullable=true)
         */
        private $deliveryFreeAmount;

        /**
         * @var string
         * @ORM\Column(name="shop_url", type="text", nullable=true)
         */
        private $shopUrl;

        /**
         * @var string
         * @ORM\Column(name="product_detail_memo", type="text", nullable=true)
         */
        private $productDetailMemo;
    
        /**
         * @var Member
         * @ORM\ManyToOne(targetEntity="\Eccube\Entity\Member")
         * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=true)
         */
        private $creator;

        /**
         * @var \DateTime
         * @ORM\Column(name="create_date", type="datetime", nullable=true)
         */
        private $createDate;

        /**
         * @var \DateTime
         * @ORM\Column(name="update_date", type="datetime", nullable=true)
         */
        private $updateDate;



        public function getId(): ?int
        {
            return $this->id;
        }

        public function setMember(?Member $member): self
        {
            $this->member = $member;
            return $this;
        }

        public function getMember(): ?Member
        {
            return $this->member;
        }

        public function setShopStatus(?ShopStatus $ShopStatus): self
        {
            $this->ShopStatus = $ShopStatus;
            return $this;
        }

        public function getShopStatus(): ?ShopStatus
        {
            return $this->ShopStatus;
        }

        public function setPref(?Pref $pref): self
        {
            $this->pref = $pref;
            return $this;
        }

        public function getPref(): ?Pref
        {
            return $this->pref;
        }

        public function setShopName(?string $shopName): self
        {
            $this->shopName = $shopName;
            return $this;
        }

        public function getShopName(): ?string
        {
            return $this->shopName;
        }

        public function setPostalCode(?string $postal_code): self
        {
            $this->postal_code = $postal_code;
            return $this;
        }

        public function getPostalCode(): ?string
        {
            return $this->postal_code;
        }

        public function setAddr01(?string $addr01): self
        {
            $this->addr01 = $addr01;
            return $this;
        }

        public function getAddr01(): ?string
        {
            return $this->addr01;
        }

        public function setAddr02(?string $addr02): self
        {
            $this->addr02 = $addr02;
            return $this;
        }

        public function getAddr02(): ?string
        {
            return $this->addr02;
        }

        public function setPhoneNumber(?string $phoneNumber): self
        {
            $this->phoneNumber = $phoneNumber;
            return $this;
        }

        public function getPhoneNumber(): ?string
        {
            return $this->phoneNumber;
        }

        public function setMemo(?string $memo): self
        {
            $this->memo = $memo;
            return $this;
        }

        public function getMemo(): ?string
        {
            return $this->memo;
        }

        public function setAppeal(?string $appeal): self
        {
            $this->appeal = $appeal;
            return $this;
        }

        public function getAppeal(): ?string
        {
            return $this->appeal;
        }

        public function setShopImage(?string $ShopImage): self
        {
            $this->shopImage = $ShopImage;
            return $this;
        }

        public function getShopImage()
        {
            return $this->shopImage;
        }

        public function setCreator(?Member $Creator): self
        {
            $this->creator = $Creator;
            return $this;
        }

        public function getCreator(): ?Member
        {
            return $this->creator;
        }

        public function setCreateDate(?\DateTimeInterface $createDate): self
        {
            $this->createDate = $createDate;
            return $this;
        }

        public function getCreateDate(): ?\DateTimeInterface
        {
            return $this->createDate;
        }

        public function setUpdateDate(?\DateTimeInterface $updateDate): self
        {
            $this->updateDate = $updateDate;
            return $this;
        }

        public function getUpdateDate(): ?\DateTimeInterface
        {
            return $this->updateDate;
        }

        public function setDeliveryFreeAmount(?string $deliveryFreeAmount): self
        {
            $this->deliveryFreeAmount = $deliveryFreeAmount;
            return $this;
        }

        public function getDeliveryFreeAmount(): ?string
        {
            return $this->deliveryFreeAmount;
        }

        public function setShopUrl(?string $shopUrl): self
        {
            $this->shopUrl = $shopUrl;
            return $this;
        }

        public function getShopUrl(): ?string
        {
            return $this->shopUrl;
        }

        public function setProductDetailMemo(?string $productDetailMemo): self
        {
            $this->productDetailMemo = $productDetailMemo;
            return $this;
        }

        public function getProductDetailMemo(): ?string
        {
            return $this->productDetailMemo;
        }
    }