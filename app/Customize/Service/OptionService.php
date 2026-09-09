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
    const Options     = ['sex','men','kote','tare','hakama','doui','shinai','zekken'];

    const sex         = ['sex'];#
    const height      = ['height'];#
    const men         = ['men_size_a','men_size_b','men_size_c','men_etc']; #
    const kote        = ['kote_size_left_d','kote_size_left_e','kote_size_left_f','kote_size_right_d','kote_size_right_e','kote_size_right_f','kote_etc'];#
    const tare        = ['tare_size_waist','tare_etc'];
    const doui        = ['doui_type','doui_hope','doui_etc']; #
    const hakama      = ['hakama_size_waist','hakama_size_length','hakama_etc'];#
    const shinai      = ['shinai_etc'];
    const zekken      = ['zekken_etc']; #'zekken_font','zekken_text'

    const FreeInput1  = ['value','name'];#
    const FreeInput2  = ['value','name'];#
    const FreeInput3  = ['value','name'];#


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

            if(isset($FormData['freeInput' . $i])){

                $Free = 'getfreeInputName'.$i;
                $FormData['freeInput' . $i]['name'] = $Product->$Free();
            }
      
        }
   
        return $FormData;

    }

}
