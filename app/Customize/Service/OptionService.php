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
 use Eccube\Entity\Product;


 
class OptionService{

    const UnSetOption = ['quantity','product_id','ProductClass','_token'];


    /**
     * Undocumented function
     *
     * @param Product $Product
     * @param array $FormData
     * @return array|null
     */
    public function setOption(Product $Product,array $FormData){

        foreach(self::UnSetOption as $Column){
            unset($FormData[$Column]);
        }
        
        if(Count($FormData)<1){return null;}


        for($i =1 ;$i<3 ; $i++){

            if(isset($FormData['FreeInput' . $i])){

                $Free = 'getFreeInputName'.$i;
                $FormData['FreeInput' . $i]['name'] = $Product->$Free();
            }
      
        }
   
        return $FormData;

    }

}
