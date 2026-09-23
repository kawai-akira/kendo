<?php
   /**
    * @version EC-CUBE4.3
    * @copyright 株式会社 翔 kakeru.co.jp
    *
    * 2026年09月21日作成
    *
    * app\Customize\Controller\Plugin\RecommendController.php
    *
    * Customize領域で行ってもよいが　
    *
    *
    *                                        ≡≡≡┏(＾o＾)┛
    *****************************************************/

namespace Customize\Controller\Admin\Plugin;

#use Eccube\Controller\AbstractController;
use Eccube\Form\Type\Admin\SearchProductType;
use Plugin\Recommend42\Entity\RecommendProduct;
use Customize\Form\Type\Plugin\RecommendProductType;
use Customize\Form\Type\Plugin\RecommendSearchType;
use Customize\Repository\Plugin\RecommendProductRepository;
use Plugin\Recommend42\Service\RecommendService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eccube\Repository\CategoryRepository;
use Customize\Entity\Master\RecommendType;


/**
 * Class RecommendController.
 */
class RecommendController extends \Plugin\Recommend42\Controller\RecommendController
//class RecommendController extends AbstractController
{

    /**
     * @var RecommendProductRepository
     */
    private $recommendProductRepository;

    /**
     * @var RecommendService
     */
    private $recommendService;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;
    /**
     * RecommendController constructor.
     *
     * @param RecommendProductRepository $recommendProductRepository
     * @param RecommendService $recommendService
     */
    public function __construct(RecommendProductRepository $recommendProductRepository, RecommendService $recommendService
                                ,  CategoryRepository $categoryRepository,
    
    )
    {
        parent::__construct($recommendProductRepository, $recommendService);
        $this->recommendProductRepository = $recommendProductRepository;
        $this->recommendService = $recommendService;
        $this->categoryRepository = $categoryRepository;
        
    }

    /**
     * おすすめ商品一覧.
     *
     * @param Request     $request
     *
     * @return array
     * @Route("/%eccube_admin_route%/plugin/recommend", name="plugin_recommend_new_list")
     * @Route("/%eccube_admin_route%/plugin/recommend/{type}/{id}/search", name="plugin_recommend_new_list_search")
     * @Template("@Recommend42/admin/index.twig")
     */
    public function index(Request $request,$type=null,$id=null)
    {
        $Data = null;
        $form = $this->createForm(RecommendSearchType::class,null,['type'=>$type,'id'=>$id]);
        if($type) {

            //$Data = $form->getData(); で値が取れない
            foreach ($form as $name => $child) {
                $Data[$name] = $child->getData();
            }
        }

        $pagination =[];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $Data = $form->getData();
        }
        $pagination = $this->recommendProductRepository->getRecommendList($Data);
        return [
            'pagination' => $pagination,
            'total_item_count' => count($pagination),
            'form' => $form->createView(),
            'Categories' => $this->categoryRepository->getList(null,true),
            'Type'      =>$this->entityManager->getRepository(RecommendType::class)->get(),
        ];
    }

    /**
     * Create & Edit.
     *
     * @param Request     $request
     * @param int         $id
     *
     * @throws \Exception
     *
     * @return array|RedirectResponse
     * @Route("/%eccube_admin_route%/plugin/recommend/new", name="plugin_recommend__new")
     * @Route("/%eccube_admin_route%/plugin/recommend/{id}/edit", name="plugin_recommend__edit", requirements={"id" = "\d+"})
     * @Template("@Recommend42/admin/regist.twig")
     */
    public function edit(Request $request, $id = null)
    {
        
        $Recommend = null;
        $Product = null;
        if (!is_null($id)) {
            // IDからおすすめ商品情報を取得する
            /** @var RecommendProduct */
            $Recommend = $this->recommendProductRepository->find($id);

            if (!$Recommend) {
                $this->addError('plugin_recommend.admin.not_found', 'admin');
                log_info('The recommend product is not found.', ['Recommend id' => $id]);

                return $this->redirectToRoute('plugin_recommend_list');
            }

            $Product = $Recommend->getProduct();
        }else{
            $Recommend = new RecommendProduct();
        }

        $searchForm = $this->createForm(RecommendSearchType::class);
        $searchForm->handleRequest($request);

        // formの作成
        /* @var Form $form */
        $form = $this->formFactory
            ->createBuilder(RecommendProductType::class, $Recommend)
            ->getForm();
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchData = $searchForm->getData();
        
            foreach ($searchForm as $name => $child) {
                $form->get($name)->setData($child->getData());
            }
            $Recommend->setCategory($searchData['Category'])
                      ->setRecommendKbn($searchData['RecommendKbn']);
        }


        $form->handleRequest($request);
        $data = $form->getData();

        $reId = RecommendType::CATEGORY == $form->get('type')->getData()->getid() ? $form->get('Category')->getData()->getId() : $form->get('Kbn')->getData()->getId();
        
        
        if ($form->isSubmitted() && $form->isValid()) {
                $Recommend->setVisible(1);

                $this->entityManager->persist($Recommend);
                $this->entityManager->flush();

                $this->addSuccess('plugin_recommend.admin.register.success', 'admin');
                log_info('Update the recommend product success.', ['Recommend id' => $Recommend->getId(), 'Product id' => $data['Product']->getId()]);
                return $this->redirectToRoute('plugin_recommend_new_list_search',['type'=>$form->get('type')->getData()->getid() ,'id'=>$reId ]);
        
/*
             //$service = $this->recommendService;
            if (is_null($data['id'])) {
                if ($status = $service->createRecommend($data)) {
                    $this->addSuccess('plugin_recommend.admin.register.success', 'admin');
                    log_info('Add the new recommend product success.', ['Product id' => $data['Product']->getId()]);
                }
            } else {
                if ($status = $service->updateRecommend($data)) {
                    $this->addSuccess('plugin_recommend.admin.update.success', 'admin');
                    log_info('Update the recommend product success.', ['Recommend id' => $Recommend->getId(), 'Product id' => $data['Product']->getId()]);
                }
            }

        
            if (!$status) {
                $this->addError('plugin_recommend.admin.not_found', 'admin');
                log_info('Failed the recommend product updating.', ['Product id' => $data['Product']->getId()]);
            }
*/

        }    

        if (!empty($data['Product'])) {
            $Product = $data['Product'];
        }

        $arrProductIdByRecommend = $this->recommendProductRepository->getRecommendProductIdAll();

        return $this->registerView(
            [
                'form' => $form->createView(),
                'recommend_products' => json_encode($arrProductIdByRecommend),
                'Product' => $Product,
                'reid' => $reId,
                'typeTitle' => 'plugin_recommend.admin.tipe_title'.$form->get('type')->getData()->getid(),
                'titleNmae' => RecommendType::CATEGORY == $form->get('type')->getData()->getid() ? $Recommend->getCategory()->getName() : $Recommend->getRecommendKbn()->getName(),
            ]
        );
    }

    /**
     * おすすめ商品の削除.
     *
     * @param Request     $request
     * @param RecommendProduct $RecommendProduct
     *
     * @throws \Exception
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/%eccube_admin_route%/plugin/recommend/{id}/remove", name="plugin_recommend_remove", requirements={"id" = "\d+"}, methods={"DELETE"})
     */
    public function delete(Request $request, RecommendProduct $RecommendProduct)
    {
        // Valid token
        $this->isTokenValid();

        //リダイレクトした時のDATA保存
        $type = RecommendType::CATEGORY;
        if ($Category = $RecommendProduct->getCategory()){
            $id = $Category->getId();
        }else{
            $type= RecommendType::KBN;
            $id = $RecommendProduct->getRecommendKbn()->getId();
        }
       
        // おすすめ商品情報を削除する
        if ($this->recommendProductRepository->deleteRecommend($RecommendProduct)) {
            log_info('The recommend product delete success!', ['Recommend id' => $RecommendProduct->getId()]);
            $this->addSuccess('plugin_recommend.admin.delete.success', 'admin');
        } else {
            $this->addError('plugin_recommend.admin.not_found', 'admin');
            log_info('The recommend product is not found.', ['Recommend id' => $RecommendProduct->getId()]);
        }

        return $this->redirectToRoute('plugin_recommend_new_list_search',['type'=>$type,'id'=>$id]);
    }


}
