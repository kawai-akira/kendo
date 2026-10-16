<?php
/**
 * @version EC=CUBE4.3
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年10月12日作成
 *
 * app\Customize\Controller\ShopController.php
 *
 *  
 *
 *
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Controller;


use Eccube\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;						
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Customize\Entity\Shop;




class ShopController extends AbstractController
{

    /**
     * HelpController constructor.
     */
    public function __construct()
    {
    }
    /**
     * @Route("/shop/list", name="shop_list", methods={"GET"} )
     * @Template("Shop/list.twig")
     */
    public function list(Request $request)
    {
    $Shops = $this->entityManager->getRepository(Shop::class)->findBy(['ShopStatus'=>1,'visible'=>true],['sort_no'=>'ASC','id'=>'ASC']);

        return[
            'Shops'=>$Shops,
        ];
    }
    /**
     * @Route("/shop/detail/{id}", name="shop_detail", methods={"GET"} , requirements={"id" = "\d+"}))
     * 
     */
    public function detail(Request $request, $id = null)
    {
        if(!$id){
            throw new NotFoundHttpException();
        }
        if(!$Shop = $this->entityManager->getRepository(Shop::class)->find($id)){
            throw new NotFoundHttpException();
        }

        $Url = $this->generateUrl('product_list').'?Shop='.$id; 
        return $this->redirect($Url);
    }
}