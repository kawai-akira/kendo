<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年08月06日作成
	 *
	 * app\Controller\Admin\AdminConverterController.php
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
    
    use Customize\Service\SqlService;
    
    use Customize\Form\Type\Admin\ConverterType;
    use Eccube\Entity\Master\CsvType;
    use Eccube\Entity\BaseInfo;
    use Eccube\Entity\Member;
    use Eccube\Entity\MailTemplate;
    use Eccube\Entity\Page;
    use Eccube\Entity\PageLayout;
    use Eccube\Entity\Layout;
    use Eccube\Entity\Block;
    use Eccube\Entity\BlockPosition;
    use Eccube\Repository\Master\PrefRepository;
    use Eccube\Entity\Master\DeviceType;
    use Carbon\Carbon;
    use Customize\Service\Converter\CustomerComberter;
use PhpCsFixer\Fixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer;

    class AdminConverterController extends AbstractController
    {

        const Message1 = '顧客・売上・商品';
        const Message2 = 'ベーズ・カテゴリー';
        const IINSERT1  = "INSERT INTO TableName (id, name, sort_no, discriminator_type) VALUES ('rid','rname,','rsort_no','rtype');";
        const VALUES   = ['TableName','rid','rname,','rsort_no','rtype'];

        const IINSERT2 = "INSERT INTO TableName (id,display_order_count, name, sort_no, discriminator_type) VALUES ";
        const TRUNCATE = "TRUNCATE TABLE ";
        Const FOREIGN  = 'SET FOREIGN_KEY_CHECKS = ';
        /**
         * @var SqlService. $SqlService
         */
        private $SqlService;
        /**
         * @var PrefRepository. $PrefRepository
         */
        private $PrefRepository;

         /**
         * @param   SqlService  $SqlService
         */

         /**
          * @var CustomerComberter
          */
         private $CustomerComberter;

         public function __construct(
            SqlService $SqlService
            ,PrefRepository $PrefRepository
            ,CustomerComberter $CustomerComberter
         )
        {

        $this->SqlService = $SqlService;
        $this->PrefRepository = $PrefRepository;
        $this->CustomerComberter = $CustomerComberter;

        }    

    /**
     * MyText
     * @param Request $request
     * @return array
     *
     * @Route("/%eccube_admin_route%/Converter", name="admin_Converter" , methods={"GET", "POST"})
     * @Template("@admin/Converter/index.twig")
     */
    public function index(Request $request)
    {
    // $this->CarenderSearvice->collCsv();
       $this->ShowColumn();

       return  [
        'message1' => self::Message1,
        'message2' => self::Message2,

        ];

     

    }
    /**
     * MyText
     * @param Request $request
     * @return array
     *
     * @Route("/%eccube_admin_route%/Converter/Converter", name="admin_Converter_Converter" , methods={"GET", "POST"})
     * @Template("@admin/Converter/Converter.twig")
     */
    public function Converter(Request $request)
    {

        $form   = $this->createForm(ConverterType::class);
            

   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->CustomerComberter->Menu();

        }

       return  [
           'form' => $form->createView(),
           'message1' => self::Message1,

          

        ];

     }
   
    /**
     * MyText
     * @param Request $request
     * @return array
     *
     * @Route("/%eccube_admin_route%/Converter/inAdvance", name="admin_Converter_inAdvance" , methods={"GET", "POST"})
     * @Template("@admin/Converter/inAdvance.twig")
     */
    public function inAdvance(Request $request){

        $form   = $this->createForm(ConverterType::class);
        $this->Member();     

   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->MakeMtbSql();
            $this->setBaseInfo();
            $this->MailTemplate();
            $this->Page();
            $this->Layout();

        }

   
       return  [
           'form' => $form->createView(),
           'message2' => self::Message2,

        ];

    }

    private function Member(){



        $Members =$this->SqlService->Converter1('dtb_member');
       // print_r($Customers);           
                         
                         ;
        
        $Re = [];
        foreach ($Members as $Member){
            $data = [];

            $data['id']             = $Member['member_id'];
            $data['work_id']        = $Member['work'];
            $data['authority_id']   = $Member['authority'];
            $data['creator_id']     = null;
            $data['name']           = $Member['name'];
            $data['department']     = $Member['department'];
            $data['login_id']         = $Member['login_id'];
            $data['password']       = '';
            $data['salt']           = null;
            $data['sort_no']        = $Member['rank'];
            $data['two_factor_auth_key'] = null;
            $data['two_factor_auth_enabled'] = 0;
            $data['create_date']    = $Member['create_date'];
            $data['update_date']    = $Member['update_date'];
            $data['login_date']     = $Member['login_date'];
            $data['discriminator_type'] = 'member';
            $data['reset_key']      = null;
            $data['reset_expire']   = null;


            $Re[] = $data;
        }

        $this->SqlService->Converter2('dtb_member',$Re);



    }

    private function Layout(){

        $Datas[] = ['name' => 'ヘッターロゴ フッターのみ',];

  
                    
        $DeviceType = $this->entityManager->getRepository(DeviceType::class)->find(10);

        foreach ($Datas as $Data){

            $layout = new Layout();
            $layout->setName($Data['name'])
                   ->setDeviceType($DeviceType);

            $this->entityManager->persist($layout);		
            $this->entityManager->flush();

            $this->PageLayout1($layout);
            $this->BlockPosition1($layout);
        }
    }


    private function PageLayout1(Layout $layout){

        $Pages = $this->entityManager->getRepository(Page::class)->findBy(['id'=>[49,50]]) ;
        $sortNo =[45,46] ;
        
        foreach ($Pages as $i => $Page){

            $PageLayout = new PageLayout();
            
            $PageLayout          #->setPageId($page)
                    ->setLayoutId($layout->getId())
                    ->setPageId($Page->getId())
                    ->setSortNo($sortNo[$i])
                    ->setPage($Page)
                    ->setLayout($layout);

            $this->entityManager->persist($PageLayout);		
            $this->entityManager->flush();
            
            

        }

    }  
    private function BlockPosition1(Layout $layout){

        $Datas[]= [
            'setion' => 3,    
            'block'  => $this->entityManager->getRepository(Block::class)->find(10),
            'row'    => 1,
        ];

        $Datas[]= [
            'setion' => 10,    
            'block'  => $this->entityManager->getRepository(Block::class)->find(6),
            'row'    => 1,
        ];

        
        foreach($Datas as $Data){
            $Position = new BlockPosition();
            $Position->setSection($Data['setion'])
                     ->setBlockId($Data['block']->getId())
                     ->setBlock($Data['block'])
                     ->setLayoutId($layout->getId())
                     ->setLayout($layout)
                     ->setBlockRow($Data['row']);
            $this->entityManager->persist($Position);		
            $this->entityManager->flush();

        }


    } 



    private function Page(){

    $date =  Carbon::now()->format('Y-m-d h-i-s');

  
    $Datas[] = [
        
        'master_page_id' => null,
        'page_name' => '管理者用パうワード変更',
        'url' => 'admin_renew_pass_index',
        'file_name' => 'AdminRenewPass/index', 
        'meta_robots' => 'noindex'
    ];
    $Datas[] = [
        'master_page_id' => null,
        'page_name' => '管理者用パうワード変更終了',
        'url' => 'admin_renew_pass_complete',
        'file_name' => 'AdminRenewPass/complete', 
        'meta_robots' => 'noindex'
    ];

    foreach( $Datas as $Data){
            if( 'admin_renew_pass_complete' ==$Data['url']){
                $Data['master_page_id'] = $Page;
            }

            $Page = new Page();
            $Page->setMasterPage($Data['master_page_id'])
                 ->setName($Data['page_name'])
              ->setUrl($Data['url'])
              ->setFileName($Data['file_name'])
              ->setEditType(2)
              ->setCreateDate($date)
              ->setUpdateDate($date)
              ->setMetaRobots($Data['meta_robots']);
            $this->entityManager->persist($Page);		
            $this->entityManager->flush();	
    }


    }


   private function MailTemplate(){


    $Temp = new MailTemplate(); 
        $Temp->setName('管理画面用パスワード再セットメール')
             ->setFileName('Mail/admin_renew_mail.twig')
             ->setMailSubject('パスワード再セット');
            $this->entityManager->persist($Temp);		
            $this->entityManager->flush();		

   } 


    /**
     * 
     */
    private function setBaseInfo(){


        $Info = $this->SqlService->Table('dtb_base_info')
                                 ->Find();


        $baseInfo = $this->entityManager->getRepository(BaseInfo::class)->find(1);



           $pref =  $this->PrefRepository->find($Info['pref']);

           $baseInfo->setPref($pref)
                    ->setCompanyName($Info['company_name'])
                    ->setCompanyKana($Info['company_kana'])
                    ->setPostalCode($Info['zip01'].$Info['zip02'])
                    ->setAddr01($Info['addr01'])
                    ->setAddr02($Info['addr02'])
                    ->setPhoneNumber($Info['tel01'].$Info['tel02'].$Info['tel03'])
                    ->setBusinessHour(null)
                    ->setEmail01($Info['email01'])
                    ->setEmail02($Info['email02'])
                    ->setEmail03($Info['email03'])
                    ->setEmail04($Info['email04'])
                    ->setShopName($Info['shop_name'])
                    ->setShopKana($Info['shop_kana']) 
                    ->setShopNameEng($Info['shop_name_eng'])
                //  ->setUpdateDate($updateDate)
                    ->setGoodTraded($Info['good_traded'])
                    ->setMessage($Info['message'])
                    ->setDeliveryFreeAmount($Info['delivery_free_amount'])
                    ->setDeliveryFreeQuantity($Info['delivery_free_quantity'])
                    ->setOptionMypageOrderStatusDisplay($Info['option_mypage_order_status_display'])
                    ->setOptionNostockHidden($Info['nostock_hidden'])
                    ->setOptionFavoriteProduct($Info['option_favorite_product'])
                    ->setOptionProductDeliveryFee($Info['option_product_delivery_fee'])
                    ->setInvoiceRegistrationNumber(null)
                //   ->setOptionProductTaxRule(false)
                    ->setOptionCustomerActivate($Info['option_customer_activate'])
                    ->setOptionRememberMe($Info['option_remember_me'])
                //  ->setOptionMailNotifier(false)
                //  ->setAuthenticationKey(null)
                //  ->setCountry(null)
                    ->setOptionPoint(1)
                //    ->setPointConversionRate(1)
                //    ->setBasicPointRate(1)
                ;



            $this->entityManager->persist($baseInfo);		
            $this->entityManager->flush();		

    }







/**
 * MTBはマイグレーションで一元管理をする
 * 
 * Version2026080823281
 */

/**
 * 
 */

private function MakeMtbSql(){

    $Sql = self::FOREIGN. '0;';

    foreach ($this->mtbLists() as $mtbName){

    $Sql .= self::TRUNCATE . $mtbName .';';

        $Mtbs = $this->SqlService->Table($mtbName)
                                 ->FindAll();

        foreach ($Mtbs as $mtb){

            $Sql .= $this->setMtbSql($mtbName,$mtb);


        }                         

    }
    $Sql .= self::FOREIGN. '1;';
    $this->SqlService->setSql($Sql)
                     ->Exec($this->SqlService::DBNAMES[0]);




}



    /**
     * @return array
     */
    private function mtbLists(){

            $Mtbs = [

                'mtb_dou_color',
                'mtb_doui_hope',
                'mtb_doui_type',
                'mtb_kote_color',
                'mtb_men_color',
                'mtb_shinai_length',
                'mtb_shop_status',
                'mtb_zekken_font',
                'mtb_tag'
            ];





    return $Mtbs;

    }
    /**
     * @param string $mtbName
     * @param array  $mtb
     * @return string $Sql
     */
    private function setMtbSql($mtbName,$mtb){

        $Sql = self::IINSERT1;


        $setDiscriminatorType = function($mtbName){

           $name =  str_replace(['mtb','_'],['',''],$mtbName);
           return [$name];
        };

        $Arr = array_merge([$mtbName],$mtb,$setDiscriminatorType($mtbName));

       
        return  str_replace(self::VALUES,$Arr, $Sql);


    }

    public function truncateTable( $TableNmae)
    {
        $connection = $this->entityManager->getConnection();
    
    // 外部キー制約を一時的に無効化
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0;');
    
    // 対象テーブルをTRUNCATE（例: plg_your_entity_table）
        $connection->executeStatement('TRUNCATE TABLE '. $TableNmae . ' ;');
    
    // 外部キー制約を元に戻す
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    private function ShowColumn(){  
        $Columns = $this->SqlService->Table('dtb_member')
                                    ->ShowColumn($this->SqlService::DBNAMES[1]);

       // print_r($Columns);                            
        foreach ($Columns as $Column){
                //echo '<tr><td>'.$Column['Field'].'</td><td>' . $Column['Type'].'</td></tr>';
                echo $Column['Field'].PHP_EOL;           
        }    
    }

}

