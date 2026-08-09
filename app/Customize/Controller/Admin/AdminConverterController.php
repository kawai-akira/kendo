<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年08月06日作成
	 *
	 * appe\Controller\Admin\AdminConverterController.php
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
    




    class AdminConverterController extends AbstractController
    {

        const Message1 = '顧客・売上・商品';
        const Message2 = 'ベーズ・カテゴリー';
        /**
         * @var SqlService.
         */
        private $SqlService;


         /**
         * @param   SqlServise  $SqlServise
         */
         public function __construct(
            SqlService $SqlServise
         )
        {

        $this->SqlService = $SqlServise;
    

        }    

    /**
     * MyText
     * @param Request $request
     * @return array
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Route("/%eccube_admin_route%/Converter", name="admin_Converter" , methods={"GET", "POST"})
     * @Template("@admin/Converter/index.twig")
     */
    public function index(Request $request)
    {
    // $this->CarenderSearvice->collCsv();
          
    

       return  [
        'message1' => self::Message1,
        'message2' => self::Message2,

        ];

     

    }
    /**
     * MyText
     * @param Request $request
     * @return array
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Route("/%eccube_admin_route%/Converter/Converter", name="admin_Converter_Converter" , methods={"GET", "POST"})
     * @Template("@admin/Converter/Converter.twig")
     */
    public function Converter(Request $request)
    {

        $form   = $this->createForm(ConverterType::class);


   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


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
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Route("/%eccube_admin_route%/Converter/inAdvance", name="admin_Converter_inAdvance" , methods={"GET", "POST"})
     * @Template("@admin/Converter/inAdvance.twig")
     */
    public function inAdvance(Request $request){

        $form   = $this->createForm(ConverterType::class);


   
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->setMtb();


        }

   
       return  [
           'form' => $form->createView(),
           'message2' => self::Message2,

        ];


    }



/**
 * MTBはマイグレーションで一元管理をする
 * 
 * Version2026080823281
 */



    private function setMtb($Year){ 

        $Mtbs = [
           
            ];
    


  
          
    

    }

   private function set(){

#INSERT INTO mtb_csv_type (id, name, sort_no, discriminator_type) VALUES (1, '商品CSV', 3, 'csvtype');
#INSERT INTO mtb_csv_type (id, name, sort_no, discriminator_type) VALUES (2, '会員CSV', 4, 'csvtype');
#INSERT INTO mtb_csv_type (id, name, sort_no, discriminator_type) VALUES (3, '受注CSV', 1, 'csvtype');
#INSERT INTO mtb_csv_type (id, name, sort_no, discriminator_type) VALUES (4, '配送CSV', 1, 'csvtype');
#INSERT INTO mtb_csv_type (id, name, sort_no, discriminator_type) VALUES (5, 'カテゴリCSV', 5, 'csvtype');
#INSERT INTO mtb_csv_type (id, name, sort_no, discriminator_type) VALUES (6, '規格CSV', 6, 'csvtype');
#INSERT INTO mtb_csv_type (id, name, sort_no, discriminator_type) VALUES (7, '規格分類CSV', 7, 'csvtype');
#INSERT INTO mtb_csv_type (id, name, sort_no, discriminator_type) VALUES (8, '商品レビューCSV', 8, 'csvtype');

    }



}