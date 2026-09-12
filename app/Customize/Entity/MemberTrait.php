<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月13日作成
   *
   * app\Customize\Entity\MemberTrait.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/
    namespace Customize\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Eccube\Annotation\EntityExtension;


    /**
     * @EntityExtension("Eccube\Entity\Member")
     */

    Trait MemberTrait 
    {

        /**
         * @var Shop
         * @ORM\ManyToOne(targetEntity="Customize\Entity\Shop")
         * @ORM\JoinColumn(name="shop_id", referencedColumnName="id", nullable=true)
         */
        private $Shop = null;

        /**
         * @var string|null
         *
         * @ORM\Column(name="reset_key", type="string", length=255, nullable=true)
         */
        private $reset_key;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="reset_expire", type="datetimetz", nullable=true)
         */
        private $reset_expire;

        /**
         * @param Shop|null $Shop
         * @return self
         */
        public function setShop(Shop $Shop = null): self
        {
            $this->Shop = $Shop;
            return $this;
        }

        public function getShop() :Shop|null
        {
            return $this->Shop;
        }

          /**
         * Set resetKey.
         *
         * @param string|null $resetKey
         *
         * @return Customer
         */
        public function setResetKey($resetKey = null)
        {
            $this->reset_key = $resetKey;

            return $this;
        }

        /**
         * Get resetKey.
         *
         * @return string|null
         */
        public function getResetKey()
        {
            return $this->reset_key;
        }

        /**
         * Set resetExpire.
         *
         * @param \DateTime|null $resetExpire
         *
         * @return Customer
         */
        public function setResetExpire($resetExpire = null)
        {
            $this->reset_expire = $resetExpire;

            return $this;
        }

        /**
         * Get resetExpire.
         *
         * @return \DateTime|null
         */
        public function getResetExpire()
        {
            return $this->reset_expire;
        }



    }