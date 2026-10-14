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
 *
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
        $builder = $this->formFactory
            ->createNamedBuilder('', SearchProductCstType::class)
            ->setMethod('GET');


        $request = $this->requestStack->getMainRequest();

        $form = $builder->getForm();
        $form->handleRequest($request);

        return [
            'formCst' => $form->createView(),
        ];
    }
}
