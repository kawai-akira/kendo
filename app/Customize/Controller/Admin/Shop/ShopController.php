<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年09月09日作成
	 *
	 * app\Customize\Controller\Admin\Shop\ShopController.php
     *
     *
	 * 
	 *
	 * 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	 ******************************************************/
    namespace Customize\Controller\Admin\Shop;

    use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
    use Eccube\Controller\AbstractController;
    use Knp\Component\Pager\PaginatorInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Eccube\Repository\Master\PageMaxRepository;
    use Eccube\Util\FormUtil;
    use Customize\Entity\Shop;
    use Eccube\Entity\Member;
    use Customize\Form\Type\Admin\SearchShopType;
    use Customize\Form\Type\Admin\ShopType;
     
    use Customize\Repository\ShopRepository;
    

    class ShopController extends AbstractController{


        /**
         * @var PageMaxRepository
         */
        protected $pageMaxRepository;

        /**
         * @var ShopRepository
         */
        private $ShopRepository;


        public function __construct(
            PageMaxRepository $pageMaxRepository
            ,ShopRepository $ShopRepository

    ) {
        $this->pageMaxRepository = $pageMaxRepository;
        $this->ShopRepository = $ShopRepository;

    }




        /**
         * Shop
         * @param Request $request
         * @return array
         * @Route("/%eccube_admin_route%/Shop", name="admin_shop_index" , methods={"GET", "POST"})
         * @Route("/%eccube_admin_route%/Shop/page/{page_no}", requirements={"page_no" = "\d+"}, name="admin_shop_index_page", methods={"GET", "POST"})
         * @Template("@admin/Shop/index.twig")
         */
        public function index(Request $request, PaginatorInterface $paginator, $page_no = null){

            $searchForm = $this->createForm(SearchShopType::class);

            $session = $this->session;
                    
            $pageMaxis = $this->pageMaxRepository->findAll();
            $pageCount = $session->get('eccube.admin.shop.search.page_count', $this->eccubeConfig['eccube_default_page_count']);
            $pageCountParam = $request->get('page_count');
            if ($pageCountParam && is_numeric($pageCountParam)) {
                foreach ($pageMaxis as $pageMax) {
                    if ($pageCountParam == $pageMax->getName()) {
                        $pageCount = $pageMax->getName();
                        $session->set('eccube.admin.shop.search.page_count', $pageCount);
                        break;
                    }
                }
            }

            if ('POST' === $request->getMethod()) {
                $searchForm->handleRequest($request);
                if ($searchForm->isValid()) {
                    $searchData = $searchForm->getData();
                    $page_no = 1;

                    $session->set('eccube.admin.shop.search', FormUtil::getViewData($searchForm));
                    $session->set('eccube.admin.shop.search.page_no', $page_no);
                } else {
                    return [
                        'searchForm' => $searchForm->createView(),
                        'pagination' => [],
                        'pageMaxis' => $pageMaxis,
                        'page_no' => $page_no,
                        'page_count' => $pageCount,
                        'has_errors' => true,
                    ];
                }
            } else {
                if (null !== $page_no || $request->get('resume')) {
                    if ($page_no) {
                        $session->set('eccube.admin.shop.search.page_no', (int) $page_no);
                    } else {
                        $page_no = $session->get('eccube.admin.shop.search.page_no', 1);
                    }
                    $viewData = $session->get('eccube.admin.shop.search', []);
                } else {
                    $page_no = 1;
                    $viewData = FormUtil::getViewData($searchForm);
                    $session->set('eccube.admin.shop.search', $viewData);
                    $session->set('eccube.admin.shop.search.page_no', $page_no);
                }
                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
            }

            /** @var QueryBuilder $qb */
            $qb = $this->ShopRepository->getQueryBuilderBySearchData($searchData);

    

            $pagination = $paginator->paginate(
                $qb,
                $page_no,
                $pageCount
            );

            return [
                'searchForm' => $searchForm->createView(),
                'pagination' => $pagination,
                'pageMaxis' => $pageMaxis,
                'page_no' => $page_no,
                'page_count' => $pageCount,
                'has_errors' => false,
            ];

        }

         /** Shop
         * @param Request $request
         * @return array
         * @Route("/%eccube_admin_route%/shop/new", name="admin_shop_new", methods={"GET", "POST"})
         * @Route("/%eccube_admin_route%/Sho/{id}edit", name="admin_shop_edit" , methods={"GET", "POST"})
         * @Template("@admin/shop/edit.twig")
         */
        public function edit(Request $request,$id = null){

            /** @var Shop */
            $Shop = $this->ShopRepository->New($id);
            
            $form = $this->createForm(ShopType::class,$Shop);

            $form->handleRequest($request);

            $oldShopImage = $Shop->getShopImage();
            if ($form->isSubmitted() && $form->isValid()) {
       
            // ファイルアップロード
            $file = $form['shop_image']->getData();

            $fs = new Filesystem();
            if ($file && strpos($file, '..') === false && $fs->exists($this->getParameter('eccube_temp_image_dir').'/'.$file)) {
                $fs->rename(
                    $this->getParameter('eccube_temp_image_dir').'/'.$file,
                    $this->getParameter('eccube_save_image_dir').'/'.$file
                );
            }



            /** @var Member */
            $Member = $Shop->getMember();
            if (!$Member->getShop()){

                $Member->setShop($Shop);
                $Shop->setMember($Member);
            }
         
            $this->entityManager->persist($Shop);
            $this->entityManager->flush();
           
            $this->addSuccess('店舗を登録しました。', 'admin');
            
            return $this->redirectToRoute('admin_shop_edit',['id'=>$Shop->getId()]);
 
            
            }
            return [
                'form' => $form->createView(),
                'Shop' => $Shop,
                'oldShopImage' =>$oldShopImage,


            ];
        }
        
    /**
     * 画像アップロード時にリクエストされるメソッド.
     *
     * @see https://pqina.nl/filepond/docs/api/server/#process
     * @Route("/%eccube_admin_route%/shop/image/process", name="admin_shop_image_process", methods={"POST"})
     */
    public function imageProcess(Request $request)
    {
        if (!$request->isXmlHttpRequest() && $this->isTokenValid()) {
            throw new BadRequestHttpException();
        }

        $images = $request->files->get('admin_shop');

        $allowExtensions = ['gif', 'jpg', 'jpeg', 'png'];

        $filename = null;
        if (isset($images['shop_image_file'])) {
            $image = $images['shop_image_file'];

            // ファイルフォーマット検証
            $mimeType = $image->getMimeType();
            if (0 !== strpos($mimeType, 'image')) {
                throw new UnsupportedMediaTypeHttpException();
            }

            // 拡張子
            $extension = $image->getClientOriginalExtension();
            if (!in_array(strtolower($extension), $allowExtensions)) {
                throw new UnsupportedMediaTypeHttpException();
            }

            $filename = date('mdHis').uniqid('_').'.'.$extension;
            $image->move($this->getParameter('eccube_temp_image_dir'), $filename);
        }


        return new Response($filename);
    }

    /**
     * アップロード画像を取得する際にコールされるメソッド.
     *
     * @see https://pqina.nl/filepond/docs/api/server/#load
     * @Route("/%eccube_admin_route%/shop/image/load", name="admin_shop_image_load", methods={"GET"})
     */
    public function imageLoad(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $dirs = [
            $this->eccubeConfig['eccube_save_image_dir'],
            $this->eccubeConfig['eccube_temp_image_dir'],
        ];

        foreach ($dirs as $dir) {
            $image = \realpath($dir.'/'.$request->query->get('source'));

            $dir = \realpath($dir);

            if (\is_file($image) && \str_starts_with($image, $dir)) {
                $file = new \SplFileObject($image);

                return $this->file($file, $file->getBasename());
            }
        }

        throw new NotFoundHttpException();
    }

    /**
     * アップロード画像をすぐ削除する際にコールされるメソッド.
     *
     * @see https://pqina.nl/filepond/docs/api/server/#revert
     * @Route("/%eccube_admin_route%/shop/image/revert", name="admin_shop_image_revert", methods={"DELETE"})
     */
    public function imageRevert(Request $request)
    {

        if (!$request->isXmlHttpRequest() && $this->isTokenValid()) {
            throw new BadRequestHttpException();
        }

        $tempFile = $this->eccubeConfig['eccube_temp_image_dir'].'/'.$request->getContent();
        if (is_file($tempFile) && stripos(realpath($tempFile), $this->eccubeConfig['eccube_temp_image_dir']) === 0) {
            $fs = new Filesystem();
            $fs->remove($tempFile);

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        throw new NotFoundHttpException();
    }

    /**
     * @Route("/%eccube_admin_route%/shop/{id}/delete", requirements={"id" = "\d+"}, name="admin_shop_payment_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Shop $TargetShop
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Shop $TargetShop)
    {
        #未使用 　画像の削除もしていない　ではない　
        $this->isTokenValid();

        $sortNo = 1;
        $Shops = $this->ShopRepository->findBy([], ['sort_no' => 'ASC']);
        foreach ($Shops as $Shops) {
            $Shops->setSortNo($sortNo++);
        }

        try {
            $this->ShopRepository->delete($TargetShop);
            $this->entityManager->flush();



            $this->addSuccess('admin.common.delete_complete', 'admin');
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->entityManager->rollback();

            $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $TargetShop->getShopName()]);
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('admin_shop_edit');
    }
    /**
    *  @Route("/%eccube_admin_route%/Shop/ajax/membarSearch", name="admin_shop_ajax_member_search", methods={"POST","GET"})
    */
    public function getMembar(Request $request){

        $this->isTokenValid();
        $Data = $request->get('admin_shop');
        if(!$MemberId = $Data['Member'] ?? null){
            return $this->json(['status' => 'error',400]);
        }

        if(!is_numeric($MemberId)){
            return $this->json(['status' => 'error',400]);
        }

        if(!$Member = $this->entityManager->getRepository(Member::class)->find($MemberId)){
            return $this->json(['status' => 'reeor',400]);
        };

         return $this->json(['status' => 'succes','name'=>$Member->getName(),'email'=>$Member->getLoginId()] );

        
    }
  }
    