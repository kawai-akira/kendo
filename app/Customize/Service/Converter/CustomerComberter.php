<?php

   /**
    * @version EC-CUBE4.3
    * @copyright 株式会社 翔 kakeru.co.jp
    *
    * 2026年08月16日作成
    *
    * app\Customize\Service\Converter\CustomerComberter.php
    * 
    *
    * SQL文を作成する サイトがデッキしだい削除する
    *
    *
    *                                        ≡≡≡┏(＾o＾)┛
    *****************************************************/
   namespace Customize\Service\Converter;

use AddressInfo;
use Customize\Service\SqlService; 
    use Eccube\Repository\Master\CustomerStatusRepository;


   class CustomerComberter{


        private const Customer = 'dtb_customer';
        private const Address  = 'dtb_customer_address';
        private const Favorite = 'dtb_customer_favorite_product';  
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

        public function Menu(){

            $this->setCustomer();
            $this->setAddress();
            $this->favorite();

        }

       

        private function setCustomer(){


        $this->SqlService->ForeignKey(0)
                         ->Table(self::Customer)
                         ->TRUNCATE($this->SqlService::DBNAMES[0]);

        $Customers =$this->SqlService->Table(self::Customer)->FindAll();
       // print_r($Customers);           
                         
                         ;
        $mg = $this->getMg();
        $Re = [];
        foreach ($Customers as $Customer){
            $data = [];

            $data['id']                 = $Customer['customer_id'];
            $data['customer_status_id'] = $Customer['status'] ;
            $data['sex_id']             = $Customer['sex'];
            $data['job_id']             = $Customer['job'];
            #$data['country_id']         = $Customer[''];
            $data['pref_id']            = $Customer['pref'];
            $data['name01']             = $Customer['name01'];
            $data['name02']             = $Customer['name02'];
            $data['kana01']             = $Customer['kana01'];
            $data['kana02']             = $Customer['kana02'];
            $data['company_name']       = $Customer['company_name'];
            $data['postal_code']        = $Customer['zip01'].$Customer['zip01'];
            $data['addr01']             = $Customer['addr01'];
            $data['addr02']             = $Customer['addr02'];
            $data['email']              = $Customer['email'];
            $data['phone_number']       = $Customer['tel01'] . $Customer['tel02'] .$Customer['tel03'] ;
            $data['fax_number']         = $Customer['fax01'] . $Customer['fax02'] .$Customer['fax03'] ;
            $data['birth']              = $Customer['birth'];
            $data['password']           = '';
            $data['salt']               = null;
            $data['secret_key']         = $Customer['secret_key'];
            $data['first_buy_date']     = $Customer['first_buy_date'];
            $data['last_buy_date']      = $Customer['last_buy_date'];
            $data['buy_times']          = $Customer['buy_times'];
            $data['buy_total']          = $Customer['buy_total'];
            $data['note']               = $Customer['note'];
            $data['reset_key']          = null;
            $data['reset_expire']       = null;
            $data['point']              = 0;
            $data['create_date']        = $Customer['create_date'];
            $data['update_date']        = $Customer['update_date'];
            $data['discriminator_type'] = 'customer';
            $data['v2_amazon_user_id']  = null;
            $data['gmo_epsilon_credit_card_expiration_date'] = null;
            $data['card_change_request_mail_send_date'] = null;

            $data['plg_mailmagazine_flg'] = $mg[$Customer['customer_id']] ?? 0;

            $Re[] = $data;
            

        }
//print_r($Re);exit;

        $this->SqlService->ForeignKey(0)
                         ->Table(self::Customer)
                         ->Sqls($Re)
                         ->Inserts($this->SqlService::DBNAMES[0]);



      return ;




        }

        private function getMg(){


            $Datas = $this->SqlService->Table('plg_mailmaga_customer')->FindAll();

            //print_r($Datas);
            $Re = [];
            foreach ($Datas as $Data){

            $Re[$Data['customer_id']] = $Data['mailmaga_flg'];


            }

        return $Re;


        }

        private function setAddress(){



        
        $this->SqlService->ForeignKey(0)
                         ->Table(self::Address)
                         ->TRUNCATE($this->SqlService::DBNAMES[0]);

        $Where = 'NOT (T.name01 = T1.name01 
                  AND  T.name02 = T1.name02
                  AND  T.zip01  = T1.zip01
                  AND  T.zip02  = T1.zip02
                  AND  T.addr01 = T1.addr01 
                  AND COALESCE(T.addr02, "") = COALESCE(T1.addr02, "")
                  AND T.tel01 = T1.tel01
                  AND T.tel02 = T1.tel02
                  AND T.tel03 = T1.tel03

        )';





            $Address = $this->SqlService->Table(self::Address)
                                        ->Join(self::Customer,'T1.customer_id = T.customer_id','INNER')
                                        ->Select('T.*')
                                        ->Where($Where)
                                        ->FindAll();
            $Re = [];
            $i = 1;
            foreach( $Address as $Addre ){

                $data = [];
        
                $data['id']               = $i;
                $data['customer_id']      = $Addre['customer_id'];
                $data['country_id']       = null;
                $data['pref_id']          = $Addre['pref'];
                $data['name01']           = $Addre['name01'];
                $data['name02']           = $Addre['name02'];
                $data['kana01']           = $Addre['kana01'];
                $data['kana02']           = $Addre['kana02'];
                $data['company_name']     = $Addre['company_name'];
                $data['postal_code']      = $Addre['zip01'] .$Addre['zip02'] ;

                $data['addr01']           = $Addre['addr01'];
                $data['addr02']           = $Addre['addr01'];
                $data['phone_number']     = $Addre['tel01']. $Addre['tel02'] .$Addre['tel02'];
                $data['create_date']      = $Addre['create_date'];
                $data['update_date']      = $Addre['update_date'];
                $data['discriminator_type'] = 'customeraddress';
     
                $Re[] = $data;
                $i ++ ;
        }
        //print_r($Re);exit;

        $this->SqlService->ForeignKey(0)
                         ->Table(self::Address)
                         ->Sqls($Re)
                         ->Inserts($this->SqlService::DBNAMES[0]);
       
        
        }
   
        private function favorite(){

                $this->SqlService->ForeignKey(0)
                         ->Table(self::Favorite)
                         ->TRUNCATE($this->SqlService::DBNAMES[0]);

            $favorites =$this->SqlService->Table(self::Favorite)->FindAll();
            $Re = [] ;
            foreach ($favorites as $favorite){
                $data['id']             = $favorite['favorite_id'];
                $data['customer_id']    = $favorite['customer_id'];
                $data['product_id']     = $favorite['product_id'];
                $data['create_date']    = $favorite['create_date'];
                $data['update_date']    = $favorite['update_date'];
                $data['discriminator_type'] = 'customerfavoriteproduct';

                $Re[] = $data;

            }
                    $this->SqlService->ForeignKey(0)
                         ->Table(self::Favorite)
                         ->Sqls($Re)
                         ->Inserts($this->SqlService::DBNAMES[0]);
            }    


   }