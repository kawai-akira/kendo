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

   
        return $FormData;

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



}
