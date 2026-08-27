<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\TwigExtention\ProductTwigExtention.php
 *
 * TWIG 
 *
 *
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/

namespace Customize\TwigExtention;

use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
#use Twig\TwigFilter;
#use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment as Twig;
use Eccube\Entity\Product;
use Customize\Service\CommonService;






class ProductTwigExtention extends AbstractExtension
{

    private const ItemDetail    = 'Product/Parts/ItemDetailArea.twig';
   
   /**
     * Twig\Environment Twig;
     * @var Twig; 
     */
    private $Twig;

    /**
     * @var CommonService.
     */
    private $CommonService;

    private $Weeks;
    /**
     * ServiceExtension 
     *
     * @param Twig $Twig
     * @param CommonService $CommonService

     */
    public function __construct(
            Twig $Twig
            ,CommonService $CommonService

    ) {
        $this->Twig = $Twig;
        $this->CommonService = $CommonService;

    }


    public function getFunctions()
    {
        return [
            new TwigFunction('ItemDetail', [$this, 'setItemDetail']),

        #    new TwigFunction('BK_Timer_Detail_mins', [$this->TimerService, 'getTimerDetailMinis']),
        ];
    }

    /**
     * @param Product $Product
     */
    public function setItemDetail(Product $Product ){

  
        $title  = $this->CommonService->getYaml('productDetail.yaml');
    
        return $this->Twig->render(self::ItemDetail, [
           'title' =>  $title,                
           'Product' =>  $Product,
   
               
        ]);


    }

  
}         


