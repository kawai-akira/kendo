<?php
/**
 * @version EC=CUBE4.3系
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年09月3日作成
 *
 * app\Customize\Service\OptionService.php
 *
 *
 * 
 * 商品オプションサービス
 *
 *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Service;

    use Doctrine\ORM\EntityManagerInterface;
    use Eccube\Entity\Product;
    use Eccube\Entity\Master\Sex;
    use Customize\Service\CommonService;
    use Customize\Entity\Master\DouiType;
    use Customize\Entity\Master\DouiHope;


 
    class OptionService{

        const UnSetOption = ['quantity','product_id','ProductClass','_token','classcategory_id1','classcategory_id2'];
        const Options     = ['sex','men','kote','dou','tare','hakama','doui','shinai','zekken'];

        const sex         = ['sex'];#
        const height      = ['height'];#
        const men         = ['men_size_a','men_size_b','men_size_c','men_etc']; #
        const kote        = ['kote_size_left_d','kote_size_left_e','kote_size_left_f','kote_size_right_d','kote_size_right_e','kote_size_right_f','kote_etc'];#
        const dou         = ['dou_size_bust','dou_size_waist','dou_size_g','dou_etc'];
        const tare        = ['tare_size_waist','tare_etc'];
        const doui        = ['doui_type','doui_hope','doui_etc']; #
        const hakama      = ['hakama_size_waist','hakama_size_length','hakama_etc'];#
        const shinai      = ['shinai_etc'];
        const zekken      = ['zekken_etc']; #'zekken_font','zekken_text'

        const FreeInput1  = ['value','name'];#
        const FreeInput2  = ['value','name'];#
        const FreeInput3  = ['value','name'];#
        const kakko       = ['【','】'];

       /**
        * @var EntityManagerInterface
       */
        private $em;

        /**
         * @var CommonService
         */
        private $CommonService;


        public function __construct(
            EntityManagerInterface $EntityManager
            ,CommonService $CommonService
        )
            {
            $this->em = $EntityManager;
            $this->CommonService = $CommonService;
        }

    /**
     * カートに入れる
     *
     * @param array $FormData
     * @return array|null
     */
    public function setOption($FormData){

        foreach(self::UnSetOption as $Column){
            unset($FormData[$Column]);
        }
        
        if(Count($FormData)<1){return null;}

        return $this->serializer($FormData);

    }

    /**
     * 配列にオブジェクトが混じる　SerializerInterface　は　余計なColumnが入る
     *
     * @param array $OPtions
     * @return array
     */
    public function serializer($OPtions){
      
        $Re = $OPtions;

        foreach ($OPtions as $key1 => $Datas){

            foreach ($Datas  as $key2 => $DAta){
                switch(true) {
                    case is_object($DAta):
                        $Re[$key1][$key2] = $DAta->getId();
                    break;    

                }
            }
        }
       
        return $Re;                
        }

    /**
     * 
     */
    public function setShowOption(array $Options){
        
        $OptionName = $this->CommonService->getYaml('ItemOpionName.yaml');                
        
        $Re['common'] = null ;     
        foreach ($Options as $key => $OPtion){
            $Name = '';
            if($Name = $OptionName[$key]['name'] ?? null){
              $Name = self::kakko[0] . $Name . self::kakko[1]; 
            }
                               
            switch ($key){
                    case 'sex':
                    $Sex =  $this->em->getRepository(sex::class)->find($OPtion['sex']); 
                        $Re['common'].= $Name . ':'.$Sex->getName() .' ';  
                        break;   
                    case 'height';
                        $Re['common'].= $Name . ':'. $OPtion['height'].'cm';
                        break;  
                    
                    case 'FreeInput1':
                    case 'FreeInput2':
                    case 'FreeInput3':
                        $Re[$key] = self::kakko[0] . $OPtion['name'].':'.$OPtion['value'] .self::kakko[1];    

                        break;
                    case 'doui':
                        foreach (self::doui  as $Doui){
                            $Mbt = null;
                            if ('doui_etc' == $Doui){continue;}

                            if ('doui_type' == $Doui){
                                $Mbt = $this->em->getRepository(DouiType::class)->find($OPtion[$Doui]);
                                
                                 }
                            if ('doui_hope' == $Doui){
                                $Mbt = $this->em->getRepository(DouiHope::class)->find($OPtion[$Doui]);
                            }

                            $OPtion[$Doui] = $Mbt->getName(); 
                        }
                       
                    default: 
                    $Re[$key] = $this->setOpitionName($Name,$OPtion,$OptionName[$key]['data']);


            }          


        }                
   

    return $Re;

    }
    private function setOpitionName(string $Name,array $Options, array $OptionNames ,$span='<span title="TITLE" >'){
      
        $span2 = $span ? '</span>' : '';
        $Re = $Name .':';

        foreach ($OptionNames as $Key => $OptionName){
               $Value  =  $Options[$Key] ?? '';   
        
               $span1 = $span ? str_replace('TITLE',$OptionName['title'],$span) : '';
               if (preg_match('/\_etc/',$Key) && !empty($Value)){
                    $span1 = '<p>';    
                    $span2 = '</p>';    
               } 
               $Re .= $span1 .$OptionName['name'].':'. $Value.$OptionName['unit'].$span2;       

            }     

    return  $Re   ;                
    }                     

}
