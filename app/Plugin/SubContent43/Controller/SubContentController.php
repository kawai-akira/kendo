<?php
	/**
	 * @version EC-CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年05月10日作成
	 *
	 *　app/Plugin/SubContent43/Controller/SubContentController.php
     *
	 *
	 * サブコンテンツ フロント用
	 *
	 * @Route("/SubContent/{cd}", requirements={"cd" = "\w+"}, name="Plugin_SubContent")
	 *
	 * 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	 ******************************************************/
	namespace Plugin\SubContent43\Controller;

	use Eccube\Controller\AbstractController;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\HttpFoundation\Request;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Eccube\Repository\PageRepository;
	use Eccube\Repository\Master\PageMaxRepository;
	use Plugin\SubContent43\Repository\ConfigRepository;
	use Plugin\SubContent43\Repository\SubContentRepository;
	use Plugin\SubContent43\Entity\Config;
	use Plugin\SubContent43\Service\ScService;
	use Twig\Environment;
	use Knp\Component\Pager\PaginatorInterface;
	class SubContentController extends AbstractController
	{
	   
		
		const Twig ='index.twig';
		const ListTwig ='List.twig';
		const TwigPath = '@SubContent43/default/';
		const SubContentUrl ='Plugin_SubContent_Index';
		
		const SessionPageCount = 'plugin.SubContent.search.Page_count';
		const SessionPageNo    = 'plugin.SubContent.search.page_no';

    	/**
    	 * @var PageMaxRepository
    	 */
    	protected $PageMaxRepository;	
		
		/**
		 * @var ConfigRepository
		 */
		protected $ConfigRepository;
		
		/**
		 * @var SubContentRepository
		 */
		protected $SubContentRepository;

		protected $Globals;
    	protected $Twig;
    	protected $Page;
    	protected $Common;
		protected $ScService;


	 public function __construct(
								 PageMaxRepository $PageMaxRepository
								,ConfigRepository $ConfigRepository
								,SubContentRepository $SubContentRepository
	 							,Environment $twig
	 							,PageRepository $PageRepository
								,ScService $ScService
	 							)

	{
		$this->PageMaxRepository = $PageMaxRepository;
		$this->ConfigRepository = $ConfigRepository; # Plugin Config
		$this->SubContentRepository=$SubContentRepository;
		$this->Twig     			= $twig;
		$this->Globals 				= $twig->getGlobals();
		$this->Page 				= $PageRepository;
		$this->ScService 				= $ScService;
	}

   /**
  	* @Route("/SubContent/{cd}" , requirements={"cd" = "\w+"}, name="Plugin_SubContent_Index")
	* @Template("@SubContent43/default/index.twig")
    */
	public function index(Request $request,PaginatorInterface $paginator,$cd = null){
	

		$Twig =self::Twig;
		$UrlName = $request->get('_route');
		$pagination = null;
		$ListId = '';
	
		/** @var Config */
		if($Config = $this->ConfigRepository->findOneBy(['name'=> $UrlName])){

			#LOGINしなければ NG
			if ($Config->getLoginFlg() && !$this->getUser()){
				$Url = $this->ScService->GetConfig('Plugin_SubContent_target_path');
				return $this->redirectToRoute($Url);
			}

			#$Twig の設定
			if ($Config->getTwig()){
				$Twig = $Config->getTwig() .'.twig';
			}elseif (Config::LIST == $Config->getType()){
				$Twig = self::ListTwig;
			}

		}	

		$SubContent  = null;
		switch (true) {
				case is_null($Config):
				case $UrlName == self::SubContentUrl;

					 $SubContent = $this->SubContentRepository->find($cd);
	     		break;
				case Config::LIST == $Config->Gettype():

				#リストタイプ(ブログ)	
				$pageCount = $this->eccubeConfig['Plugin_SubContent_list_page_count'];
				$page_no = $request->get('page_no',1);
				if(!is_numeric($page_no)){
					throw new NotFoundHttpException();
				}

				$this->session->set(self::SessionPageNo, $page_no);
				$ListId = $Config->getId();
				
				$qb = $this->SubContentRepository->getSubcontentList($Config);

				$pagination = $paginator->paginate(
					$qb,
					$page_no,
					$pageCount
				);
				
					$SubContent = $this->SubContentRepository->getSubcontentListFarst($qb);

				break;	
				case is_null($cd):

					if (Config::SINGLE == $Config->Gettype()){
						$SubContent  = $this->SubContentRepository->findOneBy(['Config'=>$Config->getId()]);
					}

				break;
			case $cd:

					$SubContent = $this->SubContentRepository->findOneBy(['Config'=>$Config->getId(),'parameter' => $cd]);
				break;

		}


		if (!$SubContent){
			throw new NotFoundHttpException();
		}


		if ($UrlName != Self::SubContentUrl && $Config){
			$this->SetPageLayout($SubContent,$Config);
		}

		$this->ScService->SetSubcontent($SubContent);

		#動的な表示の特殊処理 2019/11/27
		return  $this->render(self::TwigPath . $Twig , [
				'SubContent' => $this->ScService->GetSubcontent(),
				'Parameter'=>$cd,
				'pagination' => $pagination,
				'ListId' => $ListId,
				'Config' => $Config,
			]);
	}


	protected function SetPageLayout($SubContent,$Config){

	
		$Page = $this->Globals['Page'];
		if (!$Page->getId()){
			 $Page = $this->Page->getPageByRoute(self::SubContentUrl);
		}

			//Author
			if ($Author=$SubContent->getAuthor()) {
				$Page->setAuthor($Author);
			}
			//Description
			if ($Description = $SubContent->getDescription()) {
				$Page->setDescription($Description);
			}
	
			//Robots
			if ($Robots = $SubContent->getMetaRobots()) {
				$Page->setMetaRobots($Robots);
			}
			#MetaTags
			if ($MetaTags =  $SubContent->getMetaTags()) {
				$Page->setMetaTags($MetaTags);
			}


			if($Layout = $Config->getLayout()){
				$this->Twig->addGlobal('Layout', $Layout);
			}else{
				$Layouts = $Page->getLayouts();
				$this->Twig->addGlobal('Layout', $Layouts[0]);
			}
			

			$this->Twig->addGlobal('Page', $Page);

	}



 }