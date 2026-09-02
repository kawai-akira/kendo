<?php
/**
 * @version EC=CUBE4.3系
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年03月09日作成
 *
 * app\Customize\Service\CommonService.php
 *
 *
 * 共通サービス
 *
 * YAML・EC-CUBE CONFIG の読み込み・サービス
 *
 *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Service;

//use Symfony\Component\DependencyInjection\ContainerInterface;
#use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Eccube\Common\EccubeConfig;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Request\Context;
#use Customize\Repository\ConfigRepository;
#use Eccube\Common\Constant;
#use Symfony\Component\Security\Csrf\CsrfToken;



class CommonService{

    /**
     * @var EntityManagerInterface
     */
    
    Public $entityManager;
    /**
     * @param RequestStack
     */
    protected $Request;

    protected const PhonNumberUnit          = '-'; #電話番号区切り文字

   /*
    * @param ContainerInterface 
    */
    //protected $Container;

    /** @var TokenStorageInterface */
    protected $TokenStorage;

    /**
     * @var \Eccube\Common\EccubeConfig
     */
    protected $Config;

    /**
     * string
     * @param $YamlPath 
     */
    public $YamlPath;
    /**
     * @var Context
     */
    private $Context;

    /**
     * @var BaseInfoRepository
     */
    protected $BaseInfoRepository;

    protected $BaseInfo;
    protected $YamlValue;
       
    public function __construct(
             EntityManagerInterface $EntityManager ,
             TokenStorageInterface $tokenStorage,
             RequestStack $RequestStack
            ,EccubeConfig $EccubeConfig
            ,BaseInfoRepository $BaseInfoRepository
            ,Context $Context
 
        #    ,ConfigRepository $ConfigRepository
    ){
      //$this->Container  = $Container;
       $this->TokenStorage = $tokenStorage;
       $this->entityManager =  $EntityManager ;
       $this->Request   = $RequestStack->getCurrentRequest();
       $this->Config    = $EccubeConfig;
       $this->YamlPath  = $this->getConfig('Customize_Yaml_dir');
       $this->BaseInfoRepository = $BaseInfoRepository;
       $this->Context = $Context;
       // $this->ConfigRepository = $ConfigRepository;
    }



    /**
     * コンフィグ取得する
     * @param string $Str コンフィグ名 null 全ての配列を返す
     * @return  mixed
     */
    public function getConfig($Str)
    {
        if (!$Str) {
            return $this->Config;
        } else {
            $Config = $this->Config[$Str] ?? $Str;
            return $Config;
        }

    }

     /**
     * Ymlファイルを配列で取得する
     * @param string $Yaml YMLファイル名
     * @param %flg boolean true form 逆
     * @param $Path str パス
     * @return array
     */
    public function getYaml($Yaml, $Flg = false, $Path = null)
    {

        $Path = $Path ?? $this->YamlPath;

        $Path .= $Yaml;

        if (!is_file($Path)) {
            return [];
        }
        $Yml = Yaml::parse(file_get_contents($Path));


        return $Flg ? array_flip($Yml) : $Yml;
    }

    /**
     * Ymlファイルを書き込む
     * @param array $Adds 書き込む配列
     * @param string $Yaml ファイル名
     * @param string|null $Path パス
     * @return array
     */
    public function mergeYaml(array $Adds, $Yaml, $Path = null)
    {

        $Datas = $this->getYaml($Yaml, false, $Path);

        $Path = ($Path ?? $this->YamlPath) . $Yaml;
        if (!is_file($Path)) {
            return;
        }

        $Adds = array_merge($Datas, $Adds);
        $Yml = '#' . date('Ymd') . ' ' . $Yaml . ' AddYml ' . PHP_EOL;
        $Yml .= Yaml::dump($Adds);
        $fp = fopen($Path, 'w');
        fwrite($fp, $Yml);
        fclose($fp);
    }

    /**
     * Ymlファイルを書き込む
     * @param array $Data 書き込む配列
     * @param string $Yaml str YMLファイル名
     * @param $Path str パス
     */
    public function writeYml(array $Data, $Yaml, $Path = null)
    {
        $Path = $Path ?? $this->YamlPath;
        if (!file_exists($Path)) {
            return;
        }
        $Path .= $Yaml;
        $Yml = '#' . date('Ymd') . ' ' . $Yaml . ' WriteYml ' . PHP_EOL;
        $Yml .= Yaml::dump($Data);
        $fp = fopen($Path, 'w');
        fwrite($fp, $Yml);
        fclose($fp);
    }

    /**
     * @param string $Yaml 
     * @param mixed  $Id 
     * @return mixed 
     */
     public function getYamlValue($Yaml,$Id){



        if(!isset($this->YamlValue[$Yaml])){
            $this->YamlValue[$Yaml] = $this->getYaml($Yaml);
        }

        return $this->YamlValue[$Yaml][$Id] ?? null;

     }
        public function getUrlName(){
        return $this->Request->get('_route');
    }
    
    public function getRoot(){
        return $this->getConfig('ECCUBE_COOKIE_PATH') =='/'? '': $this->getConfig('ECCUBE_COOKIE_PATH');
    }

    /**
     * リクエストを返す
     * @param string $Name
     * @return mix
     */
    public function getRequest($Name){
        return $this->Request->get($Name);
    }

    /**
     * リクエストを返す
     * @ param string $Name
     * @ return mix
     */
   /* public function allRequest(){
        return $this->Request->query->all();
    }*/


    

    public function getRemmendTitle($flg = false ){

        $Re = [];

        $Recommends = $this->getYaml('Recommend.yaml');

        foreach  ($Recommends as $i=>$Recommend){
                 $Re[$i] = $Recommend['jp'];
        }            

            return $flg ?   array_flip($Re)   : $Re;

    }
    public function getImageList($Path=null){


		$ImagePath = $Path ?? $this->getConfig('eccube_html_front_dir') .'/assets/img/top/slider';

	

      	$this->MkDir($ImagePath);

        $ImageList=[];

		$Regular = implode('|',['gif','jpg','jpeg','png']);
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



    public function mkDir($Path,  $mode = 0777){
        $FileSys = new Filesystem();
        if(!file_exists($Path)){
         	$FileSys->mkdir($Path,$mode);
        }
    }



    /**
     * 乱数を取得する
     *
     * @param int $Num 桁数
     * @return string  乱数
     */
    public static function getRand($Num)
    {
        $ReRand = '';
        $Arr = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
        for ($i = 0; $i < $Num; $i++) {
            $ReRand .= $Arr[rand(0, count($Arr) - 1)];
        }
        return $ReRand;
    }


    public function getUser(){

  
        if (null === $Token = $this->TokenStorage->getToken()) {return null;}
    
        return  $Token->getUser(); 

    }


      /**
     * BaseINfo
     */
    public function getBaseInfo($Param = null)
    {

        if (!is_array($this->BaseInfo)) {
            #連想配列で格納
            $BaseInfos = $this->BaseInfoRepository->get();

            $SetName = function ($Keys) {
                $Re = '';
                foreach (explode('_', $Keys) as $Key) {
                    $Re .= ucfirst($Key);
                }
                return $Re;
            };


            foreach ($BaseInfos->ToNormalizedArray() as $Key => $BaseInfo){

                $Name = $SetName($Key);
       

                switch ($Key) {
                    case 'postal_code':
                    case 'PostalCode':    
                        $this->BaseInfo[$Name] = $this->SetPostalCd($BaseInfo);
                        break;
                    case 'Pref':
                        if(!$Pref = $BaseInfos->getPref()){return null;};
                        $this->BaseInfo[$Name] = $Pref->getName() ;
                        break;
                    case 'phone_number';
                        $this->BaseInfo[$Name] = $this->SetPhoneNumber($BaseInfo,false) ;
                        $this->BaseInfo['Tel'] = $BaseInfo;
                        break;
                    case 'fax_number';
                        $this->BaseInfo[$Name] = $this->SetPhoneNumber($BaseInfo,false) ;
                        $this->BaseInfo['Fax'] = $BaseInfo;
                        break;

                    case 'delivery_free_amount':
                        $this->BaseInfo['delivery_free_amounte'] = number_format((int)$BaseInfo);
                        $this->BaseInfo[$Name] = number_format((int)$BaseInfo);
                        break;
                    #建災防特殊    
                    case 'CorporationNumber1':
                        $this->BaseInfo['CompanyId'] = 'T'.$BaseInfo;
                        default:
                        $this->BaseInfo[$Name] = $BaseInfo;
                        break;
                }
            }

            $this->BaseInfo['Addr'] = $BaseInfos->getPref()->getName().$BaseInfos->getAddr01().$BaseInfos->getAddr02();



        }
        if (!$Param) {
            return $this->BaseInfo;
        }
        return $this->BaseInfo[$Param] ?? '' ;
    }

    public function setPostalCd($PostalCode){
        return trans('common.pdf.PostalUnit') . substr_replace($PostalCode, '-', 3, 0);
    }

    /**
     * 電話番号をハイフン付きで出力する
     * @param string $PhonNumber 
     * @param bool $Flg 
     * @return mixed $Flg true arrat false string 
     */
    public function setPhoneNumber($PhonNumber,$Flg = false)
    {

        if (!$PhonNumber) {
            return  $Flg ? [null,null,null] : null;
        }


        $RePhon[2] = substr($PhonNumber, -4);

        $AreaCodes = $this->getYaml('PhoneNumber.yaml');

        for ($i = 5; $i >= 2; $i--) {

            $Code = substr($PhonNumber, 0, $i);
            if (isset($AreaCodes[$Code])) {
                $RePhon[0] = $Code;
                break;
            }
        }
        $Len1 = strlen($PhonNumber);
 
        if (isset($RePhon[0])) {
            $len2 = strlen($RePhon[0]);
            $RePhon[1] = substr($PhonNumber, $len2, $Len1 - $len2 - 4);
        } else {
            $RePhon[0] = substr($PhonNumber, -0, $Len1 - 8);
            $RePhon[1] = substr($PhonNumber, -5, 4);
        }
        for ($i = 0; $i <= 2; $i++) {
            if (!isset($RePhon[$i])) {
                $RePhon[$i] = null;
            }
        }

        return $Flg ? $RePhon : $RePhon[0] .self::PhonNumberUnit .$RePhon[1] .self::PhonNumberUnit . $RePhon[2];
    }


    public Function getAddress($Data){
        $Addr  = $this->SetPostalCd($Data->getPostalCode()) .' ';
        $Addr .= $Data->getPref()->getName().$Data->getAddr01().$Data->getAddr02();
        $Phone  = $Data->getPhoneNumber();
        return [$Addr,$Phone];
    }

    public function getPhoneNumber($PhonNumber,$Flg = false)
    {

        if (!$PhonNumber) {
            return  $Flg ? [null,null,null] : null;
        }


        $RePhon[2] = substr($PhonNumber, -4);

        $AreaCodes = $this->getYaml('PhoneNumber.yaml');

        for ($i = 5; $i >= 2; $i--) {

            $Code = substr($PhonNumber, 0, $i);
            if (isset($AreaCodes[$Code])) {
                $RePhon[0] = $Code;
                break;
            }
        }
        $Len1 = strlen($PhonNumber);
 
        if (isset($RePhon[0])) {
            $len2 = strlen($RePhon[0]);
            $RePhon[1] = substr($PhonNumber, $len2, $Len1 - $len2 - 4);
        } else {
            $RePhon[0] = substr($PhonNumber, -0, $Len1 - 8);
            $RePhon[1] = substr($PhonNumber, -5, 4);
        }
        for ($i = 0; $i <= 2; $i++) {
            if (!isset($RePhon[$i])) {
                $RePhon[$i] = null;
            }
        }

        return $Flg ? $RePhon : $RePhon[0] .'-' .$RePhon[1] .'-' . $RePhon[2];
    }


     public function getCsv($Path){

        if(!file_exists($Path)){return null;};
        $file = fopen($Path, "r");
       

        $data = null;
        #/ ファイルが開けた場合のみ処理を実行
        if ($file !== false) {
   
            // 1行ずつCSVデータを読み込む
            while (($data[] = fgetcsv($file)) !== false) {

            }
        }
        // ファイルを閉じる
        fclose($file);



        return $data;
    }



    /**
     * @param object $Entity
     */
    public function emPersist($Entity){

        $this->entityManager->persist($Entity);				
        $this->entityManager->flush();
    }		


    /**
     * @return bool
     */
    public function isAdmin(){
        return $this->Context->IsAdmin();
    }

}