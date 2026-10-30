<?php
    /**
    * @version EC-CUBE4.3
    * @copyright 株式会社 翔 kakeru.co.jp
    * @author
    * 2026年05月10日作成
    *
    * app/Plugin/SubContent43/Entity/SubContent.php
    *
    *
    *
    * サブコンテンツエンティティ
    *
    * 							       C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
    ******************************************************/
       namespace Plugin\SubContent43\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Eccube\Entity\AbstractEntity;


    /**
     * SubContent
     *
     * @ORM\Table(name="plg_sub_content" , indexes={@ORM\Index(name="plg_subcontent_holder_idx",columns={"holder"}) ,@ORM\Index(name="plg_Subcontent_parameter_idx",columns={"parameter"}) })
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Plugin\SubContent43\Repository\SubContentRepository")
     */

    class SubContent extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
     private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="kbn", type="integer")
     */
     private $kbn = 0 ;


     /**
      * @var \Plugin\SubContent43\Entity\Config
      *
      * @ORM\ManyToOne(targetEntity="Plugin\SubContent43\Entity\Config")
      * @ORM\JoinColumns({
      *   @ORM\JoinColumn(name="config_id", referencedColumnName="id")
      * })
      */
     private $Config;
    
    /**
     * @var string!null
     *
     * @ORM\Column(name="holder", type="string" , length=255, nullable=true)
     */
     private $holder;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string" , nullable=true)
     */
     private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="parameter", type="string" , length=255, nullable=true)
     */
     private $parameter;
    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string" , length=255, nullable=true)
     */
     private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string" , length=255, nullable=true)
     */
     private $description;



     /**
      * @var string|null
      *
      * @ORM\Column(name="meta_robots", type="string", length=255, nullable=true)
      */
     private $meta_robots;

        /**
         * @var string|null
         *
         * @ORM\Column(name="meta_tags", type="string", length=4000, nullable=true)
         */
     private $meta_tags;

    /**
     * @var string
     *
     * @ORM\Column(name="html", type="text" , nullable=true)
     */
     private $html;

    /**
     * @var string
     *
     * @ORM\Column(name="css", type="text" , nullable=true)
     */
     private $css;
    /**
     * @var string
     *
     * @ORM\Column(name=" thumbnail", type="text" , nullable=true)
     */
     private $thumbnail;
    /**
     * @var string
     *
     * @ORM\Column(name="text01", type="text" , nullable=true)
     */
   
     private $text01;
    /**
     * @var string
     *
     * @ORM\Column(name="text02", type="text" ,  nullable=true)
     */
     private $text02;
    /**
     * @var string
     *
     * @ORM\Column(name="text03", type="text" ,  nullable=true)
     */
     private $text03;
    /**
     * @ORM\Column(name="visible", type="boolean", options={"default"=true})
     */
      private bool $visible = true ;

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
     private $updater;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
     private $update_date;

     /**
      * Get id
      *
      * @return integer
      */
     public function getId(){
          return $this->id;
     }

     public function setId($Value)
     {
         return $this->id = $Value ;
     }
 
     /**
      * Get kbn
      *
      * @return integer
      */
     public function getKbn(){
          return $this->kbn;
     }
     /**
      * Set kbn
      *
      * @param integer
      * @return SubContent
      */
     public function setKbn($Value)
     {
              $this->kbn= $Value;
              return $this;
     }


     
     /**
      * Get title
      *
      * @return string
      */
     public function getTitle(){
          return $this->title;
     }
      /**
       * Set title
       *
       * @param string
       * @return SubContent
       */
       public function setTitle($Value){
              $this->title= $Value;
              return $this;
       }
     /**
      * Get Holder 
      *
      * @return integer
      */
      public function getHolder(){
          return $this->holder;
     }
     /**
      * Set holder
      *
      * @param integer
      * @return SubContent
      */
     public function setHolder($Value)
     {
              $this->holder = $Value;
              return $this;
     }


     /**
      * Get parameter
      *
      * @return string
      */
     public function getParameter(){
          return $this->parameter;
     }
      /**
       * Set parameter
       *
       * @param string
       * @return SubContent
       */
       public function setParameter($Value){
              $this->parameter= $Value;
              return $this;
      }
     /**
      * Get author
      *
      * @return string
      */
     public function getAuthor(){
          return $this->author;
     }
      /**
       * Set author
       *
       * @param string
       * @return SubContent
       */
       public function setAuthor($Value){
              $this->author= $Value;
              return $this;
       }

     /**
      * Get description
      *
      * @return string
      */
     public function getDescription(){
          return $this->description;
     }
      /**
       * Set description
       *
       * @param string
       * @return SubContent
       */
       public function setDescription($Value){
              $this->description= $Value;
              return $this;
       }


     /**
      * Set metaRobots.
      *
      * @param string|null $metaRobots
      *
      * @return SubContent
      */
     public function setMetaRobots($metaRobots = null)
     {
          $this->meta_robots = $metaRobots;
          return $this;
     }

     /**
      * Get metaRobots.
      *
      * @return string|null
      */
     public function getMetaRobots()
     {
          return $this->meta_robots;
     }

     /**
      * Set meta_tags
      *
      * @param string $metaTags
      *
      * @return SubContent
     */
     public function setMetaTags($metaTags)
     {
          $this->meta_tags = $metaTags;
          return $this;
     }
          /**
      * Get metaRobots.
      *
      * @return string|null
      */
      public function getMetaTags()
      {
           return $this->meta_tags;
      }

     /**
      * Get html
      *
      * @return string
      */
     public function getHtml(){
          return $this->html;
     }
     /**
      * Set html
      *
      * @param string
      * @return SubContent
      */
     public function setHtml($Value)
     {
              $this->html= $Value;
              return $this;
     }

     /**
      * Get css
      *
      * @return string
      */
     public function getCss(){
          return $this->css;
     }
      /**
       * Set css
       *
       * @param string
       * @return SubContent
       */
       public function setCss($Value){
              $this->css = $Value;
              return $this;
       }

     public function getThumbnail(): ?string
     {
          return $this->thumbnail;
     }
 
       public function setThumbnail(?string $Value = null):self
       {
              $this->thumbnail= $Value;
              return $this;
       }

     public function getText01(): ?string
     {
          return $this->text01;
     }
 
       public function setText01(?string $Value = null):self
       {
              $this->text01= $Value;
              return $this;
       }
    /**
      * Get text02
      *
      * @return string
      */
     public function getText02(){
          return $this->text02;
     }
      /**
       * Set text02
       *
       * @param string
       * @return SubContent
       */
       public function setText02($Value){
              $this->text02= $Value;
              return $this;
       }

    /**
      * Get text03
      *
      * @return string
      */
     public function getText03(){
          return $this->text03;
     }
  
       public function setText03(?string $Value):self
       {
              $this->text03 = $Value;
              return $this;
       }

     public function getVisible():bool
     {
          return $this->visible;
     }

      public function setVisible(bool $Value):self
      {
              $this->visible = $Value;
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
      * Get updater
      *
      * @return \Eccube\Entity\Member
      */
     public function getUpdater(){
          return $this->updater;
     }
      /**
       * Set updater
       *
       * @param \Eccube\Entity\Member
       * @return updater
       */
       public function setUpdateId(?\Eccube\Entity\Member $updater = null){
              $this->updater= $updater;
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

            /**
      * Get Config
      *
      * @return Plugin\SubContent43\Entity\Config|null
      */
     public function getConfig(){
          return $this->Config;
     }
      /**
       * Set Config
       *
       * @param Plugin\SubContent43\Entity\Config|null
       * @return SubContent
       */
       public function setConfig(Config $Config = null){
              $this->Config = $Config;
              return $this;
       }


}
