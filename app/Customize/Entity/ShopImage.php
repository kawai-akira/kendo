<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月20日作成
   *
   * app\Customize\Entity\ShopImage.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/
    namespace Customize\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Eccube\Annotation\EntityExtension;
    use Eccube\Entity\AbstractEntity;
    use Eccube\Entity\Member;
    use Customize\Entity\Shop;
    use DateTimeInterface;

    /**
     * @ORM\Table(name="dtb_shop_image")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\Entity(repositoryClass="Eccube\Repository\ShopImageRepository")
     * @ORM\HasLifecycleCallbacks()
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     */
    class ShopImage extends AbstractEntity
    {
        /**
         * @var integer
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         * @ORM\Column(name="file_name", type="string", length=255)
         */
        private $fileName;

        /**
         * @var int
         * @ORM\Column(name="sort_no", type="integer")
         */
        private $sort_no = 0;

        /**
         * @var Shop
         * @ORM\ManyToOne(targetEntity="\Customize\Entity\Shop", inversedBy="shopImages")
         * @ORM\JoinColumn(name="shop_id", referencedColumnName="id", nullable=false)
         */
        private $shop;

        /**
         * @var Member
         * @ORM\ManyToOne(targetEntity="\Eccube\Entity\Member")
         * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=true)
         */
        private $Creator;

        /**
         * @var \DateTime
         * @ORM\Column(name="create_date", type="datetime")
         */
        private $create_date;

        /**
         * @var \DateTime
         * @ORM\Column(name="update_date", type="datetime")
         */
        private $update_date;

        // --- 以下、すべてのプロパティで「setが先、getが後」 ---

        public function getId(): ?int
        {
            return $this->id;
        }

        public function setFileName(string $fileName): self
        {
            $this->fileName = $fileName;
            return $this;
        }

        public function getFileName(): string
        {
            return $this->fileName;
        }

        public function setSortNo(int $sort_no): self
        {
            $this->sort_no = $sort_no;
            return $this;
        }

        public function getSortNo(): int
        {
            return $this->sort_no;
        }

        public function setShop(Shop $shop): self
        {
            $this->shop = $shop;
            return $this;
        }

        public function getShop(): Shop
        {
            return $this->shop;
        }

        public function setCreator(?Member $Creator): self
        {
            $this->Creator = $Creator;
            return $this;
        }

        public function getCreator(): ?Member
        {
            return $this->Creator;
        }

        public function setCreateDate(DateTimeInterface $create_date): self
        {
            $this->create_date = $create_date;
            return $this;
        }

        public function getCreateDate(): DateTimeInterface
        {
            return $this->create_date;
        }

        public function setUpdateDate(DateTimeInterface $update_date): self
        {
            $this->update_date = $update_date;
            return $this;
        }

        public function getUpdateDate(): DateTimeInterface
        {
            return $this->update_date;
        }
    }