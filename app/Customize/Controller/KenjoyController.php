<?php
/**
 * @version EC=CUBE4.3
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年10月20日作成
 *
 * D:\htdocs82\kendo\www\app\Customize\Controller\KenJoyController.php
 *                                                
 *  
 *
 *
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Customize\Repository\Plugin\SubContentRepository;
use Customize\Form\Type\KenJopypaginationType;
use Customize\Service\CommonService;

class KenjoyController extends \Eccube\Controller\HelpController
{

    /**
     * @var SubContentRepository
     */
    private $SubContentRepository;    

    /**
     * @var CommonService;
     */
    private $CommonService;

    /**
     * KenjoyController constructor.
     */
    public function __construct(
            SubContentRepository $SubContentRepository
            ,CommonService $CommonService
    )
    {
            $this->SubContentRepository = $SubContentRepository;
            $this->CommonService = $CommonService; 
            }
    /**
     * @Route("/kenjoy/list/{holder}", name="SubContent_Kenjoy_List", methods={"GET"} , requirements={"holder"="[a-zA-Z\-]+"})
     * @Template("Kenjoy/list.twig")
     */
    public function list(Request $request,?string $holder = null ,PaginatorInterface $paginator)
    {
    


        $subtitle = null;
        if($holder){
            if(!$subtitle = $this->CommonService->getYamlValue('KenjoyCategorys.yaml',$holder)){
                $holder = null;
            }
        }


        /** @var \Symfony\Component\Form\FormBuilderInterface $builder */
        $builder = $this->formFactory->createNamedBuilder('aa', KenJopypaginationType::class);

        /** @var \Symfony\Component\Form\FormInterface $searchForm */
        $form   = $builder->getForm();
        $form->handleRequest($request);
        $searPagination = $form->getData();


        $qb = $this->SubContentRepository->getKenJoylist($holder,$searPagination );



        $SubContents = $this->SubContentRepository->findBy(['holder'=>$holder,'Config'=>1],['id'=>'DESC']);

        $query = $qb->getQuery()
        ->useResultCache(true, $this->eccubeConfig['eccube_result_cache_lifetime_short']);

        
        /** @var SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $query,
            $searPagination['pageno'] ?? 1 ,
            $searPagination['disp_number'] ?? 10,
        );


        return[
            'subtitle' => $subtitle,
            'pagination' => $pagination,
            'form' => $form->createView(),
            'SubContents' => $SubContents, 
        ];
    }



}