<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年09月08日作成
   *
   * app\Customize\Entity\OrderItemTrait.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/
    namespace Customize\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Eccube\Annotation\EntityExtension;
    use Eccube\Entit\Category;


    /**
     * @EntityExtension("Eccube\Entity\OrderItem")
     */
    Trait OrderItemTrait 
    {
       /**
         * @var string|null
         * @ORM\Column(name="options", type="text", nullable=true)
         */
        private $Options;
    

        public function setOption(?string $Options = null): self
        {
            $this->Options = $Options;
            return $this;
        }

        public function getOption(): ?string
        {
            return $this->Options;
        }

        public function setOptions(?array $Oprions): self
        {
            if (is_null($Oprions)){
                $this->Options = null;
            }else{
                $this->Options = json_encode($Oprions, JSON_UNESCAPED_UNICODE);
            }

        return $this;
        }
        public function getOptions(): ?array
        {
            if(is_null($this->Options)){
                return null;
            }
           return json_decode($this->Options ,true); 
        }
    }