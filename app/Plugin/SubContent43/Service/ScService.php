<?php
	/**
	* @version EC-CUBE4.3
	* @copyright 株式会社 翔 kakeru.co.jp
	* @author
	* 2026年05月10日作成
	*
	* app/Plugin/SubContent43/Service/ScService.php
	*
	*
	* 自動生成
	*
	* サブコンテンツ YAML・EC-CUBE CONFIG の読み込み・サービス
	*
	* 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	******************************************************/
	namespace Plugin\SubContent43\Service;

	use DOMDocument;
    use DOMXPath;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\HttpFoundation\Request;
	use Twig\Environment as Twig;
	use Eccube\Common\EccubeConfig;
	use Eccube\Repository\PageRepository;
	use Eccube\Repository\ProductRepository;
	use Eccube\Repository\BaseInfoRepository;
	use Symfony\Component\Yaml\Yaml;
	use Symfony\Component\Filesystem\Filesystem;
	use Symfony\Component\Finder\Finder;
	use Symfony\Component\Routing\RouterInterface;
	use Plugin\SubContent43\Entity\SubContent;
	use Doctrine\ORM\EntityManagerInterface;


	class ScService {


	const SubContentImageDir = 'Image/'; 
	const SubContentCommonDir ='Common/';
	const PluginDir 	= 'html/user_data/SubContent/';
	const TextEditor	= 'TextEditor.yaml';
	const Kigo 			= ' **';
	const MainJs        ='js\SubContnet.js';
    protected Twig $Twig;
	protected EccubeConfig $Config;
	protected PageRepository $PageRepository;
	protected ?Request $Request;
	protected $BaseInfoRepository;
	protected $BasePath;
	protected $BaseInfo;
	protected $ProductRepository;
	protected $SubContent;
	protected EccubeConfig $EccubeConfig;
	protected RouterInterface $Router;
	protected string $ImagePath ;
	protected string $ImageRoot;
	protected string $CommonPath;
	protected string $CommonRoot;
	protected ?array $TextEditor = null;
	protected EntityManagerInterface $em;


	public function __construct(
	                            RequestStack $RequestStack
							   ,PageRepository $PageRepository
							   ,BaseInfoRepository $BaseInfoRepository
							   ,Twig $Twig
							   ,ProductRepository $ProductRepository
							   ,EccubeConfig $EccubeConfig
							   ,RouterInterface $RouterInterface
							   ,EntityManagerInterface $EntityManager
	
	){

		$this->Request   = $RequestStack->getCurrentRequest();
		$this->Config    = $EccubeConfig;
		$this->PageRepository = $PageRepository;
		$this->BaseInfoRepository = $BaseInfoRepository;
		$this->Twig = $Twig;
		$this->ProductRepository = $ProductRepository;

		$this->ImagePath  = $this->GetConfig('Plugin_SubContent_Image_Dir').'/'.self::SubContentImageDir;
		$this->CommonPath = $this->GetConfig('Plugin_SubContent_Image_Dir').'/'.self::SubContentCommonDir;
		$this->Router     = $RouterInterface;
		$this->em = $EntityManager;


	}

	/**
	 * コンフィグ取得する コンフィグ名 null 全ての配列を返す
	 * @param str $Str  
	 * @return Array|string
	 */
	public function GetConfig($Str=null){
		if (!$Str){
			return $this->Config;
		}else{
			return $this->Config[$Str] ?? $Str;
		}
	}
	/**
	 * routes.yamlファイルを書き込む
	 * @param  array $Routes
	 * @return null
	 */
	public function MakeRoutes($Routes,$File=null){

		$File = $File ??  $this->GetConfig('Plugin_SubContent_RoutesYaml');

			$Yml = '#'.date('Y-m-d H:i:s') . ' ' . $File. PHP_EOL;
			$Yml.= Yaml::dump($Routes);

			$fp = fopen($File, 'w');
				  fwrite($fp, $Yml);
            fclose($fp);
			
		 return ;
	}

    /**
     * Ymlファイルを配列で取得する
     * @param string $File ファイル名
     * @param %flg boolean true form 逆

     * @return array
     */
    public function GetYaml($File, $Flg = false, $Path = null)
    {
		$Path = $Path ??  $this->GetConfig('Plugin_SubContent_YamlPath');

		$File= $Path .$File;
		if (!is_file($File)) {
            return [];
        }
        if (!$Yml = Yaml::parse(file_get_contents($File))){
			return [];
		}

        return $Flg ? array_flip($Yml) : $Yml;
    }

	public function GetImagePath(?int $Id = null): string
	{
		if ($Id){
		return $this->ImagePath .(string)$Id . '/';
		}
		return $this->CommonPath;
	}

	
	public function GetImageRoot($Id=null){


		if ($Id){
        	return  $this->getBasePath() .'/'.self::PluginDir .self::SubContentImageDir .$Id.'/';
		}
		return  $this->getBasePath() .'/'.self::PluginDir .self::SubContentCommonDir;
	}

	public function getThumbUrl(SubContent $SubContent): ?string
	{
	
		    if(!$Thumb = $SubContent->getThumbnail()){return '';}
			
			return $this->getBasePath().'/'.self::PluginDir .self::SubContentImageDir .$SubContent->getId().'/' . $Thumb;
	}
    /**
     * 2020/11/22
     * ホルダーの作成 画像用
     * @param int|null $Id
     */
     public function MakeImageDir($Id = null){

     
        $Path = $this->GetImagePath($Id);

        if(file_exists($Path)){return false;}
          $fs = new Filesystem();
          $fs->mkdir($Path, 0777 );
          return true;
     }
    /**
    * 2020/11/22
    * イメージの削除
    * @param int $Id
    * @param string $File
    */
    public function ReMoveImage($Id,$File){
     
		$Path = $this->GetImagePath($Id).$File;
        // 削除
            $fs = new Filesystem();
            $fs->remove($Path);
      }

   /**
    * 2022/04/12
    * ホルダーにある画像の取得
    * @param int|null $Id
    */
     public function GetImageList($Id=null){

		$ImagePath=$this->GetImagePath($Id);

		$FileSys = new Filesystem();
        if(!file_exists($ImagePath)){
         	$FileSys->mkdir($ImagePath,'0757');
        }
        $ImageList=[];

		$Regular = implode('|',$this->GetConfig('Plugin_SubContent_Image_Exe'));
        $fileFinder = Finder::create()
                    ->in($ImagePath)
                    ->files()
                    ->name('/\.('.$Regular.')$/')
        //          ->sortByName()
                    ;
        $Files = iterator_to_array($fileFinder);
        foreach ($Files as $File) {
            $ImageList[] = $File->getFilename();
        }
        return $ImageList;

    }

	/**
	 * Urlをホルダーから作成
	 * @param string $Name
	 * 
	 * @return string $Name
	 */
	public function SetUrlName($Name)
	{
		$Re = '';
		foreach (explode('/',$Name) as $Value ){
			$Re.= ucwords($Value).'_';
		}
		
		return $this->GetConfig('Plugin_SubContent_Url_name').trim($Re,'_');

	}

	public function GetEccubeRouting($Flg = null){
			
		$Pages = $this->PageRepository->getPageList();

		foreach ($Pages as $Page){
			if(!$Page->getId()){continue;}
			if(!$Rout = $this->Router->getRouteCollection()->get($Page->getUrl())){continue;}
			$Url = preg_replace('/\{.+\}/','',$Rout->getPath());
			$Kigo = $Rout->getPath() != $Url ? self::Kigo :'';

			if ($Flg){	
				$Url = rtrim($Url,'/');
			}else{
				$Url =  trim($Url,'/');
			}	

			$Re[$Url] = $Page->getName() .$Kigo;
		}
	
		return $Re;

	}


    public function getTextEditor($Param = null,$Name = null){
        
		if (!$this->TextEditor){
             $this->TextEditor = $this->GetYaml(self::TextEditor);
        }
        if ($Param){
			if ($Name){
            	return $this->TextEditor[$Param][$Name] ?? [];
			}else{
				return $this->TextEditor[$Param] ?? [];
			}
        }

        return $this->TextEditor;
    }


   /**
    * Html イメージリンクの設定
    */
    protected function SetImage(int $Id ,string $Html) : string
	{
		if (!$Html){return '';}
	
		//イメージSRCの返還
		//preg_match('/src=(\"|\')(.+?)(\"|\')/',$Html,$Param);
		$ImagUrl  = $this->GetImageRoot($Id);
		#while(preg_match('/src=\"(.*?)\"/',$Html,$Param)){
			while(preg_match('/src=(\"|\')(.+?)(\"|\')/',$Html,$Param)){	
			switch (true) {
				 //外部リンク
				case preg_match('/^http(.+)/', $Param[2]):
						$Html=str_replace($Param[0],'src@="'.$Param[2].'"',$Html);
					break;
				case preg_match('/^src=\'(.+)\'$/', $Param[0]):
						$Html=str_replace($Param[0],"src@='".$this->GetImageRoot().$Param[2]."'",$Html);
					break;
				default:
						$Html=str_replace($Param[0],'src@="'.$ImagUrl.$Param[2].'"',$Html);
					break;
			}
	
		}

		
		return str_replace('src@' ,'src',$Html);
	}
	
		protected function SetAnchor($Html){ 

	   // $Html=str_replace('{title}',$SubContent->getTitle() , $Html);
	
		while(preg_match('/href=\"(.*?)\"/',$Html,$Param)){
			switch (true) {
				#TEl 外部リンク　何もしない
				case preg_match('/^tel:/', $Param[1]):
				case preg_match('/http/', $Param[1]):
					 //外部リンク
						$Html=str_replace($Param[0],'href@="'.$Param[1].'"',$Html);
					break;
				#ページ内リンク
				case preg_match('/^#(.)?/',$Param[1]):
				#JS
				case 'javascript:void(0)'==$Param[1]:
						$Html=str_replace($Param[0],'href@="'.$Param[1].'"',$Html);
					break;
				default:
					#内部リンク
						$Html=str_replace($Param[0],'href@="'.$this->getBasePath() . $Param[1].'"',$Html);
					break;
			}
		}
		return str_replace('href@=','href=',$Html);
	
	}

	public function setYoutube(string $Html){

 		$Html = preg_replace_callback(
  
			 '/\{\{youtube\s+url="([^"]+)"\s+\}\}/',
             function ($matches) {
  

               if( preg_match('/^https:\/\/www\.youtube\.com/',$matches[1])){
                  $Youtube = '<iframe src="' . $matches[1] . '" frameborder="0" allowfullscreen></iframe>';
				  return str_replace('watch?v=','embed/',$Youtube );

               }
                  return '';
            },
           
            $Html
            );      

	return $Html;



	}

	protected function SetInfo(string $Html){
	//その他の変換

		while(preg_match('/\{\{(.+)\}\}/',$Html,$Param)){

			$Value= '';
			if(preg_match('/product\((.+)\)/',$Param[1],$ProductIds)){
				$Ids = explode(',',$ProductIds[1]);
				if( $Products = $this->ProductRepository->findBy(['id'=>$Ids,'Status'=>1],['update_date'=>'DESC'])){
				$Value = $this->Twig->render('@SubContent43\default\Products.twig',
				['Products' =>$Products ] );
			   }
			}
			elseif(preg_match('/parts\((.+)\)/',$Param[1],$PartsId)){
				if( $Parts = $this->em->getRepository(SubContent::class)->find($PartsId[1])){
					$Value = $Parts->getHtml();
					$Value = $this->SetImage(0,$Value);
					$Value = $this->SetAnchor($Value);
				}   

			}else{
				$Value = $this->setYoutube($Value);
				$Value = $this->getBaseInfo(trim($Param[1],' '));
			}
					
			$Html = str_replace($Param[0],$Value,$Html);

	    }

	  return $Html;
	}
   /**
	* 2020/09/03

    * @return string $Css
    */
	public function SetCss(SubContent $SubContent)
	{
		if (!$SubContent){return ;}
		if(!$Id = $SubContent->getId()){return ;};
		if(!$Css = $SubContent->getCss()){return ;};
		$ImagUrl  = $this->GetImageRoot($Id);
		$Css =str_replace("url('","url('".$ImagUrl,$Css);

	return $Css;
	}

	public function UrlName(){
		return  $this->Request->get('_route');
	}

	public function getBaseInfo($Param = null)
    {

        if (!is_array($this->BaseInfo)) {
            #連想配列で格納
           	$BaseInfo = $this->BaseInfoRepository->get();
		   	foreach ($BaseInfo->ToNormalizedArray()  as $Key =>$Value){
		   		
				if ($Key == 'good_traded' || $Key== 'message'){
					$this->BaseInfo[$Key] = nl2br($Value);
				}else{
					$this->BaseInfo[$Key] = $Value;
				}

		   	}
			   if( $Pref = $BaseInfo->getPref()){ 
			   		$this->BaseInfo['pref'] = $Pref->getName();
			   }
		}
	
        return $this->BaseInfo[$Param] ?? null ;
    }

	public function GetSubcontent(){
		return $this->SubContent;
	}
	public function SetSubcontent(Subcontent $SubContent){
		if(!is_object ($SubContent)){$this->SubContent  = $SubContent;return;}
		$this->SubContent = clone $SubContent;
	
		$Html = $this->SetImage($SubContent->getId(),$SubContent->getHtml());
        $Html = $this->SetAnchor($Html);
        $Html = $this->setYoutube($Html);
		$Html = $this->SetInfo($Html);
	    $Css = $this->SetCss($SubContent);
	
		$this->SubContent->setHtml($Html)->setCss($Css);

	}
	protected function getBasePath(){
		return  $this->Request->getBasePath() ?? '';
	}

	/**
	 * $name　はクラス名| | ID名
	 * Attribute class or id
	 */					

    public function getTag(Subcontent $SubContent,string $name ,string $Attribute ='class' ){

	    $html = $SubContent->getHtml(); 

		$dom = new DOMDocument();

		libxml_use_internal_errors(true);
	
		$dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED);

		$errors = libxml_get_errors();

		$maxLevel = !empty($errors) ? max(array_map(fn($e) => $e->level, $errors)) : 0;

		if($maxLevel >2){			
			log_info('サブコンテンツタグ抽出エラー',['SubContentId'=>$SubContent->getId(),'errors'=>$errors]);
			return null;
		}
		libxml_clear_errors();

		$xpath = new DOMXPath($dom);
	
		$nodes = $xpath->query(
    	"//*[contains(concat(' ', normalize-space(@'.$Attribute.'), ' '), '{$name}')]"
		);
		
		$Re =[];	
		foreach ($nodes as $node) {
    		$Re = $dom->saveHTML($node) . PHP_EOL;
		}
		return $Re;

		}					
				
		public function setHtml(int $id , string $Html){

			$Html = $this->SetImage($id ,$Html);
			$Html = $this->SetAnchor($Html);
			$Html = $this->setYoutube($Html);
			$Html = $this->SetInfo($Html);
			
		return $Html;			
		}

}