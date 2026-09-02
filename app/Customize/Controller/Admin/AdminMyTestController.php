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
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Customize\Service\MailService;
    use Eccube\Entity\Member;
    use Customize\Entity\Master\ProductType;
    use Customize\Repository\Master\ProductTypeRepository;
    use Customize\Form\Type\Front\AddCartType;
    use Customize\Form\Type\Mentype;
    use Eccube\Entity\Product;


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





    /**
     * MyText
     * @param Request $request
     * @return array
     * @Route("/%eccube_admin_route%/MyAddCart", name="admin_my_addCart" , methods={"GET", "POST"})
     * @Template("@admin/MyTest/addCaet.twig")
     * @ ParamConverter("Product", options={"repository_method" = "findWithSortedClassCategories"})
     */
    public function addCart(Request $request){

        $Product = $this->entityManager->getRepository(Product::class)->find(2496);
       // $form = $this->createForm(AddCartType::class,null,['product' => $Product,'id_add_product_id' => false]);
        $form = $this->createForm(AddCartType::class);
       // $form = $this->createForm(Mentype::class);
        $form->handleRequest($request);

/*$Error = [];
        foreach ($form->getErrors(true) as $key => $error) {    
$Name ='';    
preg_match_all('/\[.*?\]/',(string)$error->getCause(),$Datas); 
print_r($Datas)  ;
$Data = $Datas[0][1] ?? $Datas[0][0];    
$Name = preg_replace('/\[|\]/','',$Data);    
$Error[$Name] = $error->getMessage();//->getName();          
}
print_r($Error);*/
        if ($form->isSubmitted() && $form->isValid()) {
            echo 'aaaaaaaaaaa';
        }

          return [
            'form' => $form->createView(),
            'Product' =>$Product,
          ];
    
    }







}