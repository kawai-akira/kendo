<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月17日作成
   *
   * Customize\Entity\CustomerTrait.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/
    namespace Customize\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Eccube\Annotation\EntityExtension;
    use Eccube\Entit\Customer;


    /**
     * @EntityExtension("Eccube\Entity\Customer")
     */

    Trait CustomerTrait 
    {
        /**
         * Customer
         */


        /**
         * @var string|null
         *
         * @ORM\Column(name="fax_number", type="string", length=14, nullable=true)
         */
        private $fax_number;

        /**
         * Set fax_number.
         *
         * @param string|null $fax_number
         *
         * @return Customer
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