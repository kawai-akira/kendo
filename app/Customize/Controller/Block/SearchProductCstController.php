<?php
/**
 * @version EC=CUBE4.3
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年10月14日作成
 *
 * app\Customize\Controller\Block\SearchProductCstController.php
 *
 *  
 *
 *SELECT * FROM `dtb_product`T1 LEFT JOIN dtb_product_category T2 ON T1.id = T2.product_id WHERE T1.`shop_id` =17 
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/

namespace Customize\Controller\Block;

use Eccube\Controller\AbstractController;
use Customize\Form\Type\SearchProductCstType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Customize\Entity\Shop;

class SearchProductCstController extends AbstractController
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/block/search_product_cast", name="block_search_product_cst", methods={"GET"})
     * @Template("Block/Parts/SearchProductCst.twig")
     */
    public function index(Request $request)
    {
        $request = $this->requestStack->getMainRequest();
     
        $builder = $this->formFactory
            ->createNamedBuilder('', SearchProductCstType::class,null, ['ShopId'=>$request->get('Shop')])
            ->setMethod('GET');
      
        $form = $builder->getForm();
        $form->handleRequest($request);

        return [
            'formCst' => $form->createView(),
        ];
    }
    /**
     * @Route("/block/search_category", name="block_search_category_load", methods={"GET"})
     * @Template("Block/Parts/SetCategory.twig")
     */
    public function setCategory(Request $request){

        $builder = $this->formFactory
            ->createNamedBuilder('', SearchProductCstType::class,null, ['ShopId'=>$request->get('Shop')]);

        $form = $builder->getForm();    
        return [
            'formCst' => $form->createView(),
        ];


    }

}
