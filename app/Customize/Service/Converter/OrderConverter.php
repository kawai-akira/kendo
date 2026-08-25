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
use Customize\Entity\Master\ShopStatu;
    use Eccube\Repository\Master\CustomerStatusRepository;


   class OrderConverter
   {

        private const order = 'dtb_oeder';
        private const ProductTag = 'dtb_product_tag';
        private const Tag = 'dtb_tag';
        
        private const notConvert= 1;
        /**
         * @var SqlService
         */
        private $SqlService;

        public function __construct(
            SqlService $SqlService
            ,CustomerStatusRepository $CustomerStatusRepository

        )
        {
            $this->SqlService = $SqlService;
        }

        public function Menu(){
            $this->Oreder();

        
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
                $d['company_name']          = $o['order_company_name'];
                $d['email']                 = $o['order_email'];
                $d['phone_number']          = $o['order_tel01'].$o['order_tel02'].$o['order_tel03'];
                $d['fax_number']            = $o['order_fax01'].$o['order_fax02'] .$o['order_fax03'];
                $d['postal_code']           = $o['order_zip01'].$o['order_zip02'] ;
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
                $d['shop_id']              = $o['shop_id'];

                $Re[] = $d;
        }

                $this->SqlService->Converter2(self::Order,$Re);





        }
   
   
   
   
   
   
   
   
        }


