<?php
    /**
     * @version EC-CUBE4.3
     * @copyright 株式会社 翔 kakeru.co.jp
     * @author
     * 2026年05月10日作成
     *
     * app/Plugin/SubContent43/Entity/Config.php
     *
     *
     *
     * サブコンテンツエンティティ
     *
     * 							       C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
     ******************************************************/

namespace Plugin\SubContent43\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Config
 *
 * @ORM\Table(name="plg_sub_content_config", indexes={
 *     @ORM\Index(name="plg_sub_content_config_name_idx", columns={"name"}),
 *     @ORM\Index(name="plg_sub_content_config_Join_id_idx", columns={"join_id"}), 
 *  })
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Plugin\SubContent43\Repository\ConfigRepository")
 */
class Config
{
    const SINGLE = 1 ;#シングルタイプ
    const LIST   = 2 ;#リストタイプ

    public function getType(){
        if ($this->id == $this->join_id ){
            return self::SINGLE;
        }
        return self::LIST;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="holder", type="string", length=255,nullable=true)
     */
    private $holder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="requirements", type="string", length=255,nullable=true)
     */
    private $requirements;

    /**
     * @var \Eccube\Entity\Layout
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Layout")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="layout_id", referencedColumnName="id")
     * })
     */

    private $Layout;

    /**
     * @var string
     *
     * @ORM\Column(name="login_flg", type="integer", nullable=true , options={"default":0} )
     */
    private $login_flg = 0 ; 
    /**
     * @var int
     *
     * @ORM\Column(name="Join_id", type="integer", nullable=true , options={"default":0} )
     */
    private $join_id = 0 ; 
    /**
     * @var int
     *
     * @ORM\Column(name="sort_flg", type="integer", nullable=true , options={"default":0} )
     */
    private $sort_flg = 0; 
     /**
     * @var string
     *
     * @ORM\Column(name="methods", type="string", length=255,nullable=true)
     */
    private $methods;
    /**
     * @var string
     *
     * @ORM\Column(name="twig", type="string", length=255,nullable=true)
     */
    private $twig;

    /**
     * @var int
     * @ORM\Column(name="js_kbn", type="smallint", nullable=false, options={"default":0, "unsigned": true})
     */
    private $JsKbn = 0;    


    /**
     * @var \Eccube\Entity\Member
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * })
     */
    private $Creator;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
     private $create_date;

     /**
      * @var \Eccube\Entity\Member
      *
      * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
      * @ORM\JoinColumns({
      * @ORM\JoinColumn(name="update_id", referencedColumnName="id")
      * })
      */
    public $updater;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
     private $update_date;


    public function getId()
    {
        return $this->id;
    }

    public function getHolder():string
    {
        return $this->holder;
    }

    public function setHolder(string $Value):self
    {
        $this->holder = $Value;
        return $this;
    }
     /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this;
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    /**
     * @return string
     */
    public function getRequirements():?string
    {
        return $this->requirements;
    }
    public function setRequirements(?string $Value = null):self
    {
        $this->requirements =$Value;
        return $this;
    }
    /**
     * @return string
     */
    public function getMethods()
    {
        return $this->methods;
    }
    /**
     * @param string $Value
     *
     * @return $this;
     */
    public function setMethods($Value)
    {
        $this->methods = $Value;
        return $this;
    }
   /**
     * @return \Eccube\Entity\Layout|null 
     */
    public function getLayout()
    {
        return $this->Layout;
    }
    /**
     * @param \Eccube\Entity\Layout|null $Layout
     *
     * @return $this;
     */
    public function setLayout(\Eccube\Entity\Layout $Layout = null)
    {
        $this->Layout = $Layout;
        return $this;
    }
    /**
     * @return integer
     */
    public function getLoginFlg()
    {
        return $this->login_flg;
    }
    /**
     * @param integer 
     *
     * @return $this;
     */
    public function setLoginFlg($Value)
    {         
        $this->login_flg = $Value;
        return $this;
    }

   /**
    * @return integer
    */
    public function getJoinId()
    {
        return $this->join_id;
    }
    /**
     * @param integer 
     *
     * @return $this;
     */
    public function setJoinId($Value)
    {
        $this->join_id = $Value;
        return $this;
    }
   /**
     * @return integer
     */
    public function getSortFlg()
    {
        return $this->sort_flg;
    }
    /**
     * @param integer 
     *
     * @return $this;
     */
    public function setSortFlg($Value)
    {
        $this->sort_flg = $Value;
        return $this;
    }
    /**
     * @return string
     */
    public function getTwig()
    {
        return $this->twig;
    }
    /**
     * @param string $Value
     *
     * @return $this;
     */
    public function setTwig($Value)
    {
        $this->twig = $Value;
        return $this;
    }

   /**
     * @return int
     */
    public function getJsKbn()
    {
        return $this->JsKbn;
    }
    /**
     * @param int $Value
     *
     * @return $this;
     */
    public function setJsKbn($Value)
    {
        $this->JsKbn = $Value;
        return $this;
    }



    /**
     * Set creator.
     *
     * @param \Eccube\Entity\Member|null $creator
     *
     */
     public function setCreator(\Eccube\Entity\Member $creator = null)
     {
         $this->Creator = $creator;

         return $this;
     }
     /**
      * Get creator.
      *
      * @return \Eccube\Entity\Member|null
      */
     public function getCreator()
     {
         return $this->Creator;
     }

  /**
   * Get create_date
   *
   * @return \DateTime
   */
  public function getCreateDate(){
       return $this->create_date;
  }
   /**
    * Set create_date
    *
    * @param \DateTime
    * @return create_date
    */
    public function setCreateDate($Value){
           $this->create_date= $Value;
           return $this;
    }

  /**
   * Get updat_id
   *
   * @return integer
   */
  public function getUpdater(){
       return $this->updater;
  }
   /**
    * Set updat_id
    *
    * @param  \Eccube\Entity\Member|null
    * @return updat_id
    */
    public function setUpdater(\Eccube\Entity\Member $Updater = null){
           $this->updater = $Updater;
           return $this;
    }
  /**
   * Get update_date
   *
   * @return \DateTime
   */
  public function getUpdateDate(){
       return $this->update_date;
  }
   /**
    * Set update_date
    *
    * @param \DateTime
    * @return update_date
    */
    public function setUpdateDate($Value){
           $this->update_date= $Value;
           return $this;
    }
}
