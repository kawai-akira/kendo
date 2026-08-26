<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年08月06日作成
	 *
	 * app\Customize\Controller\Admin\AdminConverterController.php
     *
     *
	 * 
	 *
	 * 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	 ******************************************************/
    namespace Customize\Controller\Admin;
    use Eccube\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Customize\Service\MailService;
    use Eccube\Entity\Member;
    use Customize\Entity\Master\ProductType;
    use Customize\Repository\Master\ProductTypeRepository;


    class AdminMyTestController extends AbstractController
    {

        /**
         * @var OrderRepository.
         */
        protected $OrderRepository;

        /**
         * @var MailService 
         * 
         */
        private $MailService; 
       


         /**
         
         * @var   MailService $MailService
         */
         public function __construct(

            MailService $MailService

        )
        {
           
        $a = $this->entityManager->getRepository(ProductType::class)->findAll(); 

        print_r($a);
          //  $this->MailService = $MailService;
           // $this->CarenderSearvice = $CarenderSearvice;
    

        }    

    /**
     * MyText
     * @param Request $request
     * @return array

     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Route("/%eccube_admin_route%/MyTest", name="admin_my_test" , methods={"GET", "POST"})
     * @Template("@admin/MyTest/index.twig")
     */
    public function index(Request $request)
    {

    // $Mrnber = $this->entityManager->getRepository(Member::class)->find(2);
    //$this->MailService->sendAdminRenuwMail( $Mrnber);

    //$Days = new Carbon('2026-05-29');
    //$Setting = $this->SettingRepository->find(2);

    
    //$this->HolidayRepository->getHolidyBySettingDays($Setting,$Days);
   //print_r($_SESSION['data'] ?? []) ; unset($_SESSION['data']);

          
    

       return  [
            'message' => 'MYTEST　DA～Yo^^～Yo^^～' ,
           //'message' => $Sql 
        ];

     }
protected  function MakeSql(){



    foreach ($this->mtbLists()  as $mtbName){

        $Sql .= str_replace(self::TRUNCATE,'TableNmae',$mtbName);

        $Instert = str_replace(self::IINSERT, 'TableNmae',$mtbName);

        foreach ($this->$mtbName() as $Value){



        $Sql .= $Instert . $Value;


        };


    }

    return $Sql;







}

/**
 * @return array
 */
private function mtbLists(){

        $Mtbs = [

            //    'mtb_authority',
            //    'mtb_country',
  
                'mtb_csv_type',
                'mtb_customer_order_status',

            #    'mtb_customer_status',
            #    'mtb_device_type',
            #    'mtb_job',
            #    'mtb_login_history_status',
            #    'mtb_order_item_type',
                'mtb_order_status',
                'mtb_order_status_color',
               
            #    'mtb_page_max',
            #    'mtb_pref',
            #    'mtb_product_list_max',
                'mtb_product_list_order_by',
            #    'mtb_product_status',
            #    'mtb_rounding_type',
            #    'mtb_sale_type',
            #    'mtb_sex',
            #    'mtb_tax_display_type',
            #    'mtb_tax_type',
            #    'mtb_work'
        ];





return $Mtbs;

}



private function mtb_csv_type(){

    $Value[] = "(1, '商品CSV', 3, 'csvtype')";;
    $Value[] = "(2, '会員CSV', 4, 'csvtype')";;
    $Value[] = "(3, '受注CSV', 1, 'csvtype')";
    $Value[] = "(4, '配送CSV', 1, 'csvtype;')";
    $Value[] = "(5, 'カテゴリCSV', 5, 'csvtype');";
    $Value[] = "(6, '規格CSV', 6, 'csvtype');";
    $Value[] = "(7, '規格分類CSV', 7, 'csvtype');";
    $Value[] = "(8, '商品レビューCSV', 8, 'csvtype');";

    return $Value;


}

private function mtb_customer_order_status(){

    $Value[] = "(1, '注文受付', 0, 'customerorderstatus');";
    $Value[] = "(2, '入金待ち', 8, 'customerorderstatus');";
    $Value[] = "(3, '注文取消し', 3, 'customerorderstatus');";
    $Value[] = "(4, '注文受付', 2, 'customerorderstatus');";
    $Value[] = "(5, '発送済み', 4, 'customerorderstatus');";
    $Value[] = "(6, '注文受付, 1, 'customerorderstatus');";
    $Value[] = "(7, '注文未完了', 6, 'customerorderstatus');";
    $Value[] = "(8, '注文未完了', 5, 'customerorderstatus');";
    $Value[] = "(9, '入金確認中', 7, 'customerorderstatus');";

return $Value;

}

private function mtb_order_status(){

    $Value[] = "(1, '新規受付', 0, 'orderstatus');";
    $Value[] = "(2, '入金待ち', 8, 'orderstatus');";
    $Value[] = "(3, '注文取消し', 3, 'orderstatus');";
    $Value[] = "(4, '取り寄せ中', 2, 'orderstatus');";
    $Value[] = "(5, '発送済み', 4, 'orderstatus');";
    $Value[] = "(6, '入金済み', 1, 'orderstatus');";
    $Value[] = "(7, '決済処理中', 6, 'orderstatus');";
    $Value[] = "(8, '購入処理中', 5, 'orderstatus');";
    $Value[] = "(9, '受注未確定', 7, 'orderstatus');";

return $Value;
}

private function mtb_order_status_color(){

    $Value[] = "(1, '#437ec4', 0, 'orderstatuscolor');";
    $Value[] = "(2, '#FFDE9B', 8, 'orderstatuscolor');";
    $Value[] = "(3, '#C04949', 3, 'orderstatuscolor');";
    $Value[] = "(4, '#EEB128', 2, 'orderstatuscolor');";
    $Value[] = "(5, '#25B877', 4, 'orderstatuscolor');";
    $Value[] = "(6, '#25B877', 1, 'orderstatuscolor');";
    $Value[] = "(7, '#A3A3A3', 6, 'orderstatuscolor');";
    $Value[] = "(8, '#A3A3A3', 5, 'orderstatuscolor');";
    $Value[] = "(9, '#C04949', 7, 'orderstatuscolor');";

return $Value;
}

private function mtb_product_list_order_by(){

    $Value[] = "(1, '価格が低い順', 0, 'productlistorderby');";
    $Value[] = "(2, '新着順', 1, 'productlistorderby');";
    $Value[] = "(3, 'オススメ順', 2, 'productlistorderby');";
    $Value[] = "(4, 'レビュー順', 3, 'productlistorderby');";

}


}