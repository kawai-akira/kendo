<?php

   /**
    * @version EC-CUBE4.3
    * @copyright 株式会社 翔 kakeru.co.jp
    *
    * 2026年08月24日作成
    *
    * app\Customize\Service\Converter\OrderConverter.php
    * 
    *
    * SQL文を作成する サイトがデッキしだい削除する
    *
    *
    *                                        ≡≡≡┏(＾o＾)┛
    *****************************************************/
   namespace Customize\Service\Converter;

use AddressInfo;
use Carbon\Carbon;
use Customize\Service\SqlService;
use Customize\Service\OptionService;



   class OrderConverter
   {

        private const Order = 'dtb_order';
        private const OrderItem = 'dtb_order_item';
        private const Shipping = 'dtb_shipping';

        
     
        /**
         * @var SqlService
         */
        private $SqlService;

        public function __construct(
            SqlService $SqlService
        

        )
        {
            $this->SqlService = $SqlService;
        }

        public function Menu1(){
            $this->Oreder();
            $this->OrederItem();
            $this->Shipping();
        
        }
   
        private function Oreder(){


           $Re= []; 
           foreach( $this->SqlService->Converter1(self::Order) as $o){

                $d['id']                    = $o['order_id'];
                $d['customer_id']           = $o['customer_id'];
                $d['country_id']            = $o['order_country_id'];
                $d['pref_id']               = $o['order_pref'];
                $d['sex_id']                = $o['order_sex'];
                $d['job_id']                = $o['order_job'];
                $d['payment_id']            = $o['payment_id'];
                $d['device_type_id']        = $o['device_type_id'];
                $d['pre_order_id']          = $o['pre_order_id'];
                $d['order_no']              = $o['order_id'];
                $d['message']               = $o['message'];
                $d['name01']                = $o['order_name01'];
                $d['name02']                = $o['order_name02'];
                $d['kana01']                = $o['order_kana01'];
                $d['kana02']                = $o['order_kana02'];
                $d['company_name']          = addslashes($o['order_company_name']);
                $d['email']                 = $o['order_email'];
                $d['phone_number']          = $o['order_tel01'].$o['order_tel02'] .$o['order_tel03'];
                $d['fax_number']            = is_null($o['order_fax01']) ? null:$o['order_fax01'].$o['order_fax02'] .$o['order_fax03'];
                $d['postal_code']           = $o['order_zipcode'];//$o['order_zip01'].$o['order_zip02'] ;
                $d['addr01']                = $o['order_addr01'];
                $d['addr02']                = $o['order_addr02'];
                $d['birth']                 = $o['order_birth'];
                $d['subtotal']              = $o['subtotal'];
                $d['discount']              = $o['discount'];
                $d['delivery_fee_total']    = $o['delivery_fee_total'];
                $d['charge']                = $o['charge'];
                $d['tax']                   = $o['tax'];
                $d['total']                 = $o['total'];
                $d['payment_total']         = $o['payment_total'];
                $d['payment_method']        = $o['payment_method'];
                $d['note'] = $o['note'];
                    if (1 == $o['del_flg']){
                    $d['note'] .=  Carbon::now()->format('Y-m-d'). 'status ='.$o['status'];
                    }
                $d['create_date']           = $o['create_date'];
                $d['update_date']           = $o['update_date'];
                $d['order_date']            = $o['order_date'];

                $d['payment_date']          = $o['payment_date'];
                $d['currency_code']         = null;
                $d['complete_message']      = null;
                $d['complete_mail_message'] = null;
                $d['add_point']             = 0;
                $d['use_point']             = 0;
                $d['order_status_id']       = $o['del_flg'] == 0 ? $o['status'] : 9;
                $d['discriminator_type']    = 'order';
                $d['shop_id']               = $o['shop_id'];

                $Re[] = $d;
        }

                $this->SqlService->Converter2(self::Order,$Re);

//

        }
   
    private function OrederItem(){


//$item = $this->SqlService->Table('dtb_order_detail')->Set('order_detail_id',7000,'>')->FindAll();

        $ShiItem = $this->getShippingItem();
        $Re =[];
        $TaxRate = [];
        foreach($this->SqlService->Converter1('dtb_order_detail') as $o){

            $d['id']                        = $o['order_detail_id'];
            $d['order_id']                  = $o['order_id'];
            $d['product_id']                = $o['product_id'];
            $d['product_class_id']          = $o['product_class_id'];
            $d['shipping_id']               = $ShiItem[$o['order_id']];
            $d['rounding_type_id']          = 2 ; # 切り捨て
            $d['tax_type_id']               = 1 ; # 課税
            $d['tax_display_type_id']       = 1 ; # 税抜き
            $d['order_item_type_id']        = 1 ; # 賞品;
            $d['product_name']              = $o['product_name'];
            $d['product_code']              = $o['product_code'];
            $d['class_name1']               = $o['class_name1'];
            $d['class_name2']               = $o['class_name2'];
            $d['class_category_name1']      = $o['class_category_name1'];
            $d['class_category_name2']      = $o['class_category_name2'];
            $d['price']                     = $o['price'];
            $d['quantity']                  = $o['quantity'];
            $d['tax']                       = (int)($o['price']*$o['quantity'] * $o['tax_rate']/100);
            $d['tax_rate']                  = $o['tax_rate'];
            $d['tax_adjust']                = 0 ;
            $d['tax_rule_id']               = null;

            $d['currency_code']             = 'JPY';
            $d['processor_name']            = null;
            $d['point_rate']                = 0 ;
            $d['discriminator_type']        = 'orderitem';
            $d['options']                   = $this->setOprions($o) ;
            $Re[] = $d;
            $TaxRate[$o['order_id']]        = $o['tax_rate'];
          }

         

        $i =   $o['order_detail_id'] +1 ;      
        foreach ($this->SqlService->Converter1(self::Order)  as $o){

            foreach(['delivery_fee_total','discount'] as $key => $Column) {


                if ( 0 == $o[$Column] ){continue ;}  


                    $Price =0 == $key  ? $o[$Column] :$o[$Column] * -1;
                    $d['id']                        = $i;
                    $d['order_id']                  = $o['order_id'];
                    $d['product_id']                = null;
                    $d['product_class_id']          = null;
                    $d['shipping_id']               = 0 == $key   ? $ShiItem[$o['order_id']] : null;
                    $d['rounding_type_id']          = 2 ; # 切り捨て
                    $d['tax_type_id']               = 1 ; # 課税
                    $d['tax_display_type_id']       = 2 ; # 税込み
                    $d['order_item_type_id']        = 0  == $key   ? 2:4;
                    $d['product_name']              = 0 == $key   ? '送料':'割引';
                    $d['product_code']              = null;
                    $d['class_name1']               = null;
                    $d['class_name2']               = null;
                    $d['class_category_name1']      = null;
                    $d['class_category_name2']      = null;
                    $d['price']                     = $Price ;
                    $d['quantity']                  = 1;
                    $d['tax']                       = $Price -floor($Price/(1+$TaxRate[$o['order_id']]/100));
                    $d['tax_rate']                  = $TaxRate[$o['order_id']];
                    $d['tax_adjust']                = 0 ;
                    $d['tax_rule_id']               = null;

                    $d['currency_code']             = 'JPY';
                    $d['processor_name']            = 0 == $key ? 'Eccube\Service\PurchaseFlow\Processor\DeliveryFeePreprocessor':null;
                    $d['point_rate']                = 0 ;
                    $d['discriminator_type']        = 'orderitem';
                    $d['options']                   = $this->setOprions($o) ;
                    $i ++;
                    $Re[] = $d;

            }
        }
            $this->SqlService->Converter2(self::OrderItem,$Re);

        

    }


        
    private function Shipping(){


        $Re =[];
        foreach($this->SqlService->Converter1(self::Shipping) as $o){

            $d['id']                    = $o['shipping_id'];
            $d['order_id']              = $o['order_id'];
            $d['country_id']            = $o['shipping_country_id'];
            $d['pref_id']               = $o['shipping_pref'];
            $d['delivery_id']           = $o['delivery_id'];
            $d['creator_id']            = null;
            $d['name01']                = $o['shipping_name01'];
            $d['name02']                = $o['shipping_name02'];
            $d['kana01']                = $o['shipping_kana01'];
            $d['kana02']                = $o['shipping_kana02'];
            $d['company_name']          = addslashes($o['shipping_company_name']);
            $d['phone_number']          = $o['shipping_tel01'] .$o['shipping_tel02'] .$o['shipping_tel03'];
            $d['postal_code']           = $o['shipping_zip01'] .$o['shipping_zip02'];
            $d['addr01']                = $o['shipping_addr01'];
            $d['addr02']                = $o['shipping_addr02'];
            $d['time_id']               = $o['time_id'];
            $d['delivery_time']         = $o['shipping_delivery_time'];
            $d['delivery_date']         = $o['shipping_delivery_date'];
            $d['shipping_date']         = null;
            $d['tracking_number']       = $o['tracking_number'];

            $d['note']                  = 'FAX:' . $o['shipping_fax01'].$o['shipping_fax02'].$o['shipping_fax03'];
            $d['sort_no']               = null;
            $d['create_date']           = $o['create_date'];
            $d['update_date']           = $o['update_date'];
            $d['mail_send_date']        = null;
            $d['discriminator_type']    = 'shipping';
            $Re[] = $d;
        }
        $this->SqlService->Converter2(self::Shipping,$Re);
    }

    private function setOprions($o){

        $Re =[];

        foreach(OptionService::Options as $key){

            $Options = constant(OptionService::class.'::'.$key);
            if(!empty($o[$Options[0]])){

                foreach ($Options as $Option){
                    $Re[$key][$Option] = $o[$Option];
                }

            }
        }

        foreach (['tare_size_height','doui_size_height','hakama_size_height','men_height','dou_height'] as $height){

            if(!empty($o[$height])){

                if(isset($Re['hright'])){
                    if($o[$height] > $Re['hright']){
                        $Re['hright']['hright'] = $o[$height];
                    }
                }else{
     
                    $Re['hright']['hright'] = $o[$height];
                }
            }
        }


        for($i = 1; $i<=3; $i ++){
            if(!empty($o['free_input_name'.$i])){
                $Re['FreeInput' . $i]['name']  = $o['free_input_name' .$i];
                $Re['FreeInput' . $i]['value'] = $o['free_input_value'.$i];
            }
        }
        
        if (count($Re)<1) {return null;}


       return json_encode($Re, JSON_UNESCAPED_UNICODE);


          
        

        


/*
    'sex'
    'men_size_a'
'men_size_b'
'men_size_c'
'men_etc'
//'men_color
'kote_size_left_d'
'kote_size_left_e'
'kote_size_right_d'
'kote_size_right_e'
'kote_size_left_f'
'kote_size_right_f'

'kote_etc'

//kote_color
'dou_size_bust'
'dou_size_waist'
'dou_size_g'
'dou_etc'

'tare_size_height'
'tare_size_waist'
'tare_etc'
//dou_color
zekken_font
zekken_text
zekken_etc

option_id
doui_type
doui_hope
shinai_length


doui_size_height
doui_etc


hakama_size_waist
hakama_size_length
hakama_etc


'free_input_name1'
'free_input_name2'
'free_input_name3'
'free_input_value1'
'free_input_value2'
'free_input_value3'





hakama_size_height
shinai_etc


men_height
dou_height*/


   
   
   
        }

        
        private function getShippingItem(){

        
        $Re = [] ;
        foreach( $this->SqlService->Table('dtb_shipment_item')->FindAll() as $Shipping){
    
            $Re[$Shipping['order_id']]=$Shipping['shipping_id'];
        }
        return $Re;
        
        } 


   }
