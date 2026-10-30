<?php
    /**
     * @version EC-CUBE4.3
     * @copyright 株式会社 翔 kakeru.co.jp
     * @author
     * 2026年05月10日作成
     *
     * SubContent\Twig\Extension\SubContentExtension.php
     *
     * TWIGからサービスの呼び出し
     *
     *
     *
     *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
     ******************************************************/
    namespace Plugin\SubContent43\Twig\Extension;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;
use Twig\Environment as Twig;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use plugin\SubContent43\Entity\SubContent;
use Plugin\SubContent43\Service\ScService;
use Plugin\SubContent43\Repository\SubContentRepository;
use Symfony\Component\Filesystem\Filesystem;
use Eccube\Entity\Member;


class SubContentExtension extends AbstractExtension
{
    
    /** @var Twig */
    protected $Twig;

    protected $ScService;
    /** @var SubContentRepository */
    protected $SubContentRepository;
    /** @var Member */
    protected $Member;
    /** @var TokenStorageInterface */
    protected $TokenStorage;


    public function __construct(
         Twig    $Twig
        ,ScService $ScService
        ,SubContentRepository $SubContentRepository
        ,TokenStorageInterface $tokenStorage,

    )
    {
        $this->ScService  = $ScService;
        $this->Twig = $Twig;
        $this->SubContentRepository = $SubContentRepository;
        $this->TokenStorage = $tokenStorage;
    }

    public function getFunctions()
    {
        return [
           new  TwigFunction('SC_Thumbnail', [$this, 'SetThumbnail']),
           new  TwigFunction('SC_ImageList', [$this, 'SetImageList']),
           new  TwigFunction('SC_ImageUrl', [$this->ScService, 'GetImageRoot']),
           new  TwigFunction('SC_ThumbUrl', [$this->ScService, 'getThumbUrl']),
           new  TwigFunction('SC_Parts', [$this, 'GetParts']),
           new  TwigFunction('SC_PartsId', [$this, 'GetPartId']),
           new  TwigFunction('SC_PartCompoPage', [$this , 'getPartCompoPage']),
           new  TwigFunction('SC_PageJs', [$this , 'GetPageJs']),
           new  TwigFunction('SC_Class', [$this , 'SetClass']),
           new  TwigFunction('SC_Converter', [$this , 'SetConverter']),

            ];
    }

    public function SetConverter(SubContent $SubContent){
            $this->ScService->SetSubcontent($SubContent);
        return  $this->ScService->GetSubcontent();  

    }
    
    public function getPartCompoPage(string $Holder , ?string $Parameter =null , bool $titleFlg = false, $customerFlg = false){

        if ($customerFlg){
          if (!$this->GetLogin()){return ;}
        }
        if(!$Holder){return ;}


        if(!$SubContent = $this->SubContentRepository->getSubContentCompoPage($Holder,$Parameter)){ return null;}

        return $this->Twig->render('@SubContent43/default/Parts.twig',
        [
           'SubContent' => $SubContent,
           'titleFlg'   => $titleFlg
        ]);

    }


    /**
     * 実際ブログとして使う場合は 
     * plg_sub_conten 予備のカラム　Text を使ってください
     * #TEXT01 例
     * $Images = $this->ScService->GetImageList($Id);
     * $Image  =  $Images[0] ?? null ; 
     * の2行をコメントアウト
     * $Image  =  $SUbContent->getText01();
     * のコメントアウトを外す
     * 
     */
    public function SetThumbnail($SUbContent){

        $Id = $SUbContent->getId();
        $Images = $this->ScService->GetImageList($Id);
        $Image  =  $Images[0] ?? null ; 
        //$Image  =  $SUbContent->getText01();
        if($Image){
            return $this->ScService->GetImageRoot($Id). $Image;
        }
    }

    public function SetImageList($Id = null){

        if(is_null($Id)){return ;}

        $Images =$this->ScService->GetImageList($Id);
 
        if (count($Images)<1){return ;}
    
        $Class = 'common';
        if ($Id){
            $Class = 'unique';  
        }
            return $this->Twig->render('@SubContent43/admin/Parts/ImageList.twig',
        [
            'Images' => $Images,
            'Root'   => $this->ScService->GetImageRoot($Id),
            'Id'     => $Id,
            'Class'  => $Class,

        ]);
       
    }


    public function GetParts(string $Holder , ?string $Parameter =null , bool $titleFlg = false, $Customer = false){

        if ($Customer){
          if (!$this->GetLogin()){return ;}
        }
        if(!$Holder){return ;}


        if(!$SubContent = $this->SubContentRepository->GetSubContentPart($Holder,$Parameter)){ return null;}

        return $this->Twig->render('@SubContent43/default/Parts.twig',
        [
           'SubContent' => $SubContent,
           'titleFlg'   => $titleFlg
        ]);
    }

    public function GetPartId(int $Id,bool $titleFlg = false ,bool $CustomerFlg = false,){

        if ($CustomerFlg){
            if (!$this->GetLogin()){return ;}
        }


        if(!$SubContent = $this->SubContentRepository->GetSubContentData($Id) ){return ;}

        return $this->Twig->render('@SubContent43/default/Parts.twig',
        [
           'SubContent' => $SubContent,
           'titleFlg' => $titleFlg
        ]);
    }

    public function GetPageJs(){

		$Js = $this->ScService->UrlName(). $this->ScService->GetConfig('Plugin_SubContent_js_extension');
		$Path = $this->ScService->GetConfig('Plugin_SubContent_Template_js_dir').$Js;
        $FileSys = new Filesystem(); 
		if (!$FileSys->exists($Path)){return ;}
    
        return $this->Twig->render('@SubContent43/js/'.$Js);

	}

    public function SetClass($SubContent){

        $Class= '';
        if ($Config = $SubContent->getConfig()){
            $holder = $Config->getHolder();
        }else{
            $holder = $SubContent->getHolder();
        }

        $Param = '';
        if($Param = $SubContent->getParameter()){
            $Param = '_' . $Param;
        }

        if ($holder){
            $Class = 'SubContent_' . $holder  . $Param;
        }

        return $Class; 
    }

    public function getFilters(){
        return [
            new TwigFilter('SC_MD',[$this,'SetMd']),
            new TwigFilter('SC_YMD',[$this,'SetYMd']),
            new TwigFilter('SC_Image',[$this,'SetImage']),
        ];
    
    }
   
    public function SetMD($MD){

        $Days = $this->ScService->GetConfig('Plugin_SubContent_Date_format');
        return substr($MD,0,2) .$Days[1].substr($MD,2,2).$Days[2];

    }
    public function SetYmd($Ymd){
        $Days = $this->ScService->GetConfig('Plugin_SubContent_Date_format');
        return  substr($Ymd,0,4).$Days[0].substr($Ymd,4,2).$Days[1].substr($Ymd,6,2) .$Days[2];
    }

    protected function GetLogin(){
        $Token = $this->TokenStorage->getToken();
        $Member = null;
        if (null !== $Token){
            $Member = $Token->getUser(); 
         }
        
        return $Member ? true : false; 

    }
    
}