<?php

   /**
    * @version EC-CUBE4.3
    * @copyright 株式会社 翔 kakeru.co.jp
    *
    * 2026年08月16日作成
    *
    * app\Customize\Service\Converter\ProductConverter.php
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


   class ProductConverter
   {

        private const Shop = 'dtb_shop';
        private const ShopImage = 'dtb_shop_image';
        private const Product  = 'dtb_product';
        private const ProductClass= 'dtb_product_class';
        private const ClassName   = 'dtb_class_name'; 
        private const ClassCategory  = 'dtb_class_category'; 
        private const Category  = 'dtb_category'; 
        private const ProductImage = 'dtb_product_image';
        private const ProductStock = 'dtb_product_stock';
        private const ProductTag = 'dtb_product_tag';
        private const Tag = 'dtb_tag';
        
        private const notConvert= 1;
        /**
         * @var SqlService
         */
        private $SqlService;

        /**
         * @var CustomerStatusRepository
         */
        private $CustomerStatusRepository;


        public function __construct(
            SqlService $SqlService
            ,CustomerStatusRepository $CustomerStatusRepository

        )
        {
            $this->SqlService = $SqlService;
            $this->CustomerStatusRepository = $CustomerStatusRepository;

        }

        public function Menu1(){

            
     
            $this->Shop();
            $this->ShopImage();
            //$this->setAddress();
            //$this->favorite();

        }
        public function Menu2(){
            $this->Product();
            $this->productClass();
            $this->className();
            $this->ClassCutegory();
            $this->Category();
            $this->ProductImage();
            $this->ProductStock();
            $this->Tag();
            $this->ProductTag();
        }

        private  function Product(){


        $Re= []; 
        foreach( $this->SqlService->Converter1(self::Product) as $o){
            if ($o['product_id']<= self::notConvert){continue;}
                $d['id']                = $o['product_id'];
                $d['creator_id']        = $o['creator_id'];
                $d['product_status_id'] = $o['del_flg'] == 0 ? $o['status'] : 3;
                $d['name']              = $o['name'];
                $d['note']              = $o['note'];
                $d['description_list']  = $o['description_list'];
                $d['description_detail']= $o['description_detail'];
                $d['search_word']       = $o['search_word'];
                $d['free_area']         = $o['free_area'];
                $d['create_date']       = $o['create_date'];
                $d['update_date']       = $o['update_date'];
                $d['shop_id']           = $o['shop_id'];
                $d['discriminator_type']= 'product';
                $d['free_input_name1']  = $o['free_input_name1'];
                $d['free_input_name2']  = $o['free_input_name2'];
                $d['free_input_name3']  = $o['free_input_name3'];

                $Re[] = $d;
        }
                $this->SqlService->Converter2(self::Product,$Re);

        }

        private function productClass(){


	    $Re= []; 

        foreach( $this->SqlService->Converter1(self::ProductClass) as $o){
	
            if ($o['product_id']<= self::notConvert){continue;}
            $d['id']                    = $o['product_class_id'];
            $d['product_id']            = $o['product_id'];
            $d['sale_type_id']          = 1;
            $d['class_category_id1']    = $o['class_category_id1'];
            $d['class_category_id2']    = $o['class_category_id2'];
            $d['delivery_duration_id']  = $o['delivery_date_id'];
            $d['creator_id']            = null;
            $d['product_code']          = $o['product_code'];
            $d['stock']                 = $o['stock'];
            $d['stock_unlimited']       = $o['stock_unlimited'];
            $d['sale_limit']            = $o['sale_limit'];
            $d['price01']               = $o['price01'];
            $d['price02']               = $o['price02'];
            $d['delivery_fee']          = $o['delivery_fee'];
            $d['visible']               = 1 ;
            $d['create_date']           = $o['create_date'];
            $d['update_date']           = $o['update_date'];
            $d['currency_code']         = 'JPY';
            $d['point_rate']            = null;
            $d['discriminator_type']    = 'productclass';
            $d['regular_discount_id']   = null;
            $Re[] = $d;
        }
            $this->SqlService->Converter2(self::ProductClass,$Re);
   

        }


        private function className(){

            $Re = [];
            foreach( $this->SqlService->Converter1(self::ClassName) as $o){
	
                if ( 1 == $o['del_flg']){continue;}

                    $d['id']                    = $o['class_name_id'];
                    $d['creator_id']            = $o['creator_id'];
                    $d['backend_name']          = $o['name'];
                    $d['name']                  = $o['name'];
                    $d['sort_no']               = $o['rank'];
                    $d['create_date']           = $o['create_date'];
                    $d['update_date']           = $o['update_date'];
                    $d['discriminator_type']    = 'classname';
                
                $Re[] = $d;
            }
            $this->SqlService->Converter2(self::ClassName,$Re);
   

        }

       public function ClassCutegory(){


            $Re= []; 
            foreach( $this->SqlService->Converter1(self::ClassCategory) as $o){
                $d['id']                    = $o['class_category_id'];
                $d['class_name_id']         = $o['class_name_id'];
                $d['creator_id']            = $o['creator_id'];
                $d['backend_name']          = $o['name'];
                $d['name']                  = $o['name'];
                $d['sort_no']               = $o['rank'];
                $d['visible']               = $o['del_flg'] == 0 ? 1 : 0;
                $d['create_date']           = $o['create_date'];
                $d['update_date']           = $o['update_date'];
                $d['discriminator_type']    = 'classcategory';

                $Re[] = $d;
            }
            $this->SqlService->Converter2(self::ClassCategory,$Re);
   
       }

        public function Category(){


            $Re= []; 
            foreach( $this->SqlService->Converter1(self::Category) as $o){

                if(1 == $o['del_flg']){continue;}	

                $d['id']                    = $o['category_id'];
                $d['creator_id']            = $o['creator_id'];
                $d['category_name']         = $o['category_name'];
                $d['hierarchy']             = $o['level'];
                $d['sort_no']               = $o['rank'];
                $d['create_date']           = $o['create_date'];
                $d['update_date']           = $o['update_date'];
                $d['discriminator_type']    = 'category';


            $Re[] = $d;
            }
            $this->SqlService->Converter2(self::Category,$Re);

        }

        public function ProductImage(){
                	
            $Re= [];
            $i =1;                                       
            foreach( $this->SqlService->Converter1(self::ProductImage) as $o){

                $d['id']                    = $i;
                $d['product_id']            = $o['product_id'];
                $d['creator_id']            = $o['creator_id'];
                $d['file_name']             = $o['file_name'];
                $d['sort_no']               = $o['rank'];
                $d['create_date']           = $o['create_date'];
                $d['discriminator_type']    = 'productimage';
                $Re[] = $d;
                $i++;
            }
            $this->SqlService->Converter2(self::ProductImage,$Re);

         }

        private function ProductStock(){

            $Re= [];
            $i =1;                                       
            foreach( $this->SqlService->Converter1(self::ProductStock) as $o){

                $d['id']                    = $i;
                $d['product_class_id']      = $o['product_class_id'];
                $d['creator_id']            = $o['creator_id'];
                $d['stock']                 = $o['stock'];
                $d['create_date']           = $o['create_date'];
                $d['update_date']           = $o['update_date'];
                $d['discriminator_type']    ='productstock';

                $Re[] = $d;
                $i++;
            }
            $this->SqlService->Converter2(self::ProductStock,$Re);
        }


        private function Tag(){

           $this->SqlService->ForeignKey(0)
                         ->Table(self::Tag)
                         ->TRUNCATE($this->SqlService::DBNAMES[0]);
           
            $Re = [];
            foreach( $this->SqlService->Table('mtb_tag')->FindAll() as $o){

                $d['id']                 = $o['id'];
                $d['name']               = $o['name'];
                $d['sort_no']            = $o['rank'];
                $d['discriminator_type'] = 'tag';

                $Re[] = $d;
         
            }
            $this->SqlService->Converter2(self::Tag,$Re);

        }



        private function ProductTag(){

            $Re= [];
            $i =1;                                       
            foreach( $this->SqlService->Converter1(self::ProductTag) as $o){


                $d['id']                 = $i;
                $d['product_id']         = $o['product_id'];
                $d['tag_id']             = $o['tag'];
                $d['creator_id']         = $o['creator_id'];
                $d['create_date']        = $o['create_date'];
                $d['discriminator_type'] =  'producttag';

                $Re[] = $d;
                $i++;
            }
            $this->SqlService->Converter2(self::ProductTag,$Re);
        }

        



        private function Shop(){
            
           $Re= []; 
           foreach( $this->SqlService->Converter1(self::Shop) as $o){
                

                $d['id']                = $o['shop_id'];
                $d['member_id']         = $o['member_id'];
                $d['shop_status_id']    = $o['del_flg'] == 0 ? $o['status']: 9;
                $d['pref_id']           = $o['pref'];
                $d['shop_name']         = $o['shop_name'];
                $d['postal_code']       = $o['zip01'] . $o['zip02'];
                $d['addr01']            = $o['addr01'];
                $d['addr02']            = $o['addr02'];
                $d['phone_number']      = str_replace('-','',$o['tel']);
                $d['memo']              = $o['memo'];
                $d['appeal']            = $o['appeal'];

                $d['creator_id']        = null;
                $d['create_date']           = $o['create_date'];
                $d['update_date']           = $o['update_date'];
                $d['delivery_free_amount']  = $o['delivery_free_amount'];
                $d['shop_url']              = $o['shop_url'];
                $d['product_detail_memo']   = $o['product_detail_memo'];
                $d['discriminator_type']    = 'shop';

                $Re[] = $d;
        }

                $this->SqlService->Converter2(self::Shop,$Re);
        }
   
            private function ShopImage(){

                $Re= []; 
                foreach( $this->SqlService->Converter1(self::ShopImage) as $o){

                    $d['id']                = $o['shop_image_id'];
                    $d['shop_id']           = $o['shop_id'];
                    $d['creator_id']        = null;
                    $d['file_name']         = $o['file_name'];
                    $d['sort_no']           = $o['rank'];
                    $d['create_date']       = $o['create_date'];
                    $d['update_date']       = Carbon::now()->format('Y-m-d h-i-s');
                    $d['discriminator_type']= 'shopname';

                    $Re[] = $d;
        }

                $this->SqlService->Converter2(self::ShopImage,$Re);
        }
   
   }