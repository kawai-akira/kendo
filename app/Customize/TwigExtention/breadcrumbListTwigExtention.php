<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\TwigExtention\breadcrumbListTwigExtention.php
 *
 * TWIG 
 * 
 * パンくずを一元管理する
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/

namespace Customize\TwigExtention;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment as Twig;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\Category;
use Eccube\Entity\Product;
use Eccube\Entity\Page;
use Customize\Service\CommonService;

class breadcrumbListTwigExtention extends AbstractExtension
{

    private const breadcrumbListTwig ='Block/Parts/breadcrumbList.twig';

    /**
     * @var Twig; 
     */
    private $Twig;

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var CommonService
     */
    private $CommonService;



    public function __construct(
            Twig $Twig
            ,EntityManagerInterface $EntityManager
            ,CommonService $CommonService
    ){
        $this->Twig = $Twig;
        $this->em = $EntityManager;
        $this->CommonService = $CommonService;
 
     }

    public function getFunctions()
    {
        return [
            new TwigFunction('breadcrumbList', [$this, 'setbreadcrumbList']),
            ];
    }


    public function setbreadcrumbList(mixed $data = null){

        $Category = null;
        $Name     = null; 
            switch($UrlName = $this->CommonService->getUrlName()){

            case 'product_list':
                /** @var Category  */
                $Category = $data ;
                break;

            case 'product_detail':
               
                /** @var Product */
                $Product = $data;
                $Name = $Product->getName();
                $PCategorys = $Product->getProductCategories();

                $hierarchy = 0;
                foreach($PCategorys as $PcCategory){
                    $Cat = $PcCategory->getCategory();
                    if ($hierarchy < $Cat->getHierarchy() && $Cat->getHierarchy() < 4){
                        /** @var Category */
                        $Category = $Cat;
                        $hierarchy = $Cat->getHierarchy();
                    }
               }
               break;
            case 'contact':
            case 'help_faq':
            case 'help_about':    
            case 'entry':
            case 'help_tradelaw':
            case 'help_privacy':     

                
                $Page = $this->em->getRepository(Page::class)->getPageByRoute($UrlName);
                $Name =str_replace('(入力ページ)','', $Page->getName());
                break;    
            default:
               return ; 
               break; 
                
        };
    
        
    
    
    return $this->Twig->render(self::breadcrumbListTwig, [
        'Category' => $Category,
        'Name'     => $Name

      
      
       ]);




    }



}