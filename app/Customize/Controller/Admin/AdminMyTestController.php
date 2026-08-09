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


    class AdminMyTestController extends AbstractController
    {

        /**
         * @var OrderRepository.
         */
        protected $OrderRepository;

        /**
         * @var CodeService 
         * 
         */
       
        private $CodeService;

        /**
         * @var CustomerRepository;
         */

        private $CustomerRepository;

        /**
         * @var CommonService
         */
     
        private $CommonService;
        /**
         * @var CarenderSearvice;
         */
        private $CarenderSearvice;

        /**
         * @var HolidayRepository
         */
        private $HolidayRepository;
        /**
         * @var SettingRepository
         */
        private $SettingRepository;

         /**
         * @var   OrderRepository  $OrderRepository
         * @var   MailService $MailService
         */
         public function __construct(



        )
        {

            $this->OrderRepository = $OrderRepository;
            $this->CustomerRepository = $CustomerRepository;
            $this->HolidayRepository = $HolidayRepository;
            $this->SettingRepository = $SettingRepository;
           // $this->CommonService = $CommonService;
           // $this->CarenderSearvice = $CarenderSearvice;
    

        }    

    /**
     * MyText
     * @param Request $request
     * @return array
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Route("/%eccube_admin_route%/MyTest", name="admin_my_test" , methods={"GET", "POST"})
     * @Template("@admin/MyTest/index.twig")
     */
    public function index(Request $request)
    {

    //$Days = new Carbon('2026-05-29');
    //$Setting = $this->SettingRepository->find(2);

    
    //$this->HolidayRepository->getHolidyBySettingDays($Setting,$Days);
   print_r($_SESSION['data'] ?? []) ; unset($_SESSION['data']);
    //$Path = 'D:\htdocs\SubContent\www\composer.json';
    //$Path = "D:\htdocs\Yakiimo\www\composer.json";
                    

       //  $Json = file_get_contents($Path);

   //$b = json_decode($Json,true);
   //print_r($b);
   
   
   
   
   
    // $this->CarenderSearvice->collCsv();
          
    

       return  [
            'message' => 'MYTEST　DA～Yo^^～Yo^^～' ,
           //'message' => $Sql 
        ];

     }
       
    public function callApi($Year){ 


    
            $client = new Client([
                'base_uri' => 'https://koyomi.techjunk.net/sakubou/' . $Year,
            ]);
            //
        /* $options = array_merge([
                'http_errors' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ], $options);*/
            
            //
            $response = $client->request('GET');
            # $response = $client->request('', $path, $options);
            $responseJson = json_decode($response->getBody(), true);

            $this->CommonService->writeYml($responseJson,'Catendar/Moon_'. $Year.'.yaml');
          
    

    }


}