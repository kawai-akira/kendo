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
use Customize\Form\Type\ItemOptionType;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\Form\FormView;
use Twig\TwigFilter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment as Twig;
use Symfony\Component\Form\FormFactoryInterface;
use Eccube\Entity\Product;
use Customize\Service\CommonService;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Category;
use Eccube\Entity\Order;
use Customize\Repository\Plugin\RecommendProductRepository;
use Customize\Service\OptionService;
use Customize\Service\DeliveryFeeService;
use Customize\Entity\Shop;
use Plugin\SubContent43\Entity\SubContent;
use Eccube\Entity\Shipping;




class ProductTwigExtention extends AbstractExtension
{
    private const ItemDetail      = 'Product/Parts/ItemDetailArea.twig';
    private const SexTwig         = 'Product/Parts/SexForm.twig';
    private const HeightTwig      = 'Product/Parts/HeightForm.twig';
    private const MenTwig         = 'Product/Parts/MenForm.twig';
    private const KoteTwig        = 'Product/Parts/KoteForm.twig';
    private const DouTwig         = 'Product/Parts/DouForm.twig';
    private const TareTwig        = 'Product/Parts/TareForm.twig';
    private const DouiTwig        = 'Product/Parts/DouiForm.twig';
    private const HakamaTwig      = 'Product/Parts/HakamaForm.twig';
    private const ShinaiTwig      = 'Product/Parts/ShinaiForm.twig';
    private const ZekkenTwig      = 'Product/Parts/ZekkenForm.twig';
    private const FreeInput1      = 'Product/Parts//FreeInput1Form.twig';
    private const FreeInput2      = 'Product/Parts//FreeInput2Form.twig';
    private const FreeInput3      = 'Product/Parts//FreeInput3Form.twig';
    private const SexTwigAdmion   = '@admin/Order/Parts//SexForm.twig';
    private const HeightTwigAdmin = '@admin/Order/Parts//HeightForm.twig';
    private const AdminTtemOption = '@admin/Order/Parts/ItemOptionForm.twig';
    private const MenTwigAdmin    = '@admin/Order/Parts/MenForm.twig';
    private const KoteTwigAdmin   = '@admin/Order/Parts/KoteForm.twig';
    private const DouTwigAdmin    = '@admin/Order/Parts/DouForm.twig';
    private const TareTwigAdmin   = '@admin/Order/Parts/TareForm.twig';
    private const DouiTwigAdmin   = '@admin/Order/Parts/DouiForm.twig';
    private const HakamaTwigAdmin = '@admin/Order/Parts/HakamaForm.twig';
    private const ShinaiTwigAdmin = '@admin/Order/Parts/ShinaiForm.twig';
    private const ZekkenTwigAdmin = '@admin/Order/Parts/ZekkenForm.twig';
    private const FreeInput1Admin = '@admin/Order/Parts/FreeInput1Form.twig';
    private const FreeInput2Admin = '@admin/Order/Parts/FreeInput2Form.twig';
    private const FreeInput3Admin = '@admin/Order/Parts/FreeInput3Form.twig';
    private const ProductListImgTwig    = 'Product/Parts/ProductListImage.twig';
    private Const categoryImgDir        = 'assets/img/categoryImg/';
    private Const BestItemTwig          = 'Product/Parts/bestItem.twig';
    private const ProductListInfoTwig   = 'Product/Parts/ProductListInfo.twig';
    private const ItemOption            = 'Common/ItemOption.twig';
    private const DeliveryFeeAlert      = 'Cart\Parts\DeliveryFreeAlert.twig';
 

   /**
     * Twig\Environment Twig;
     * @var Twig; 
     */
    private $Twig;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var CommonService.
     */
    private $CommonService;

    /**
     * @var RecommendProductRepository
     */
    private $RecommendRepository;

    /**
     *  @var OptionService 
    */
    private $OptionService;

    
    private DeliveryFeeService $DeliveryFeeService; 
    
    /**
     * @var FormFactoryInterface
     */
    private $FormFactory;


    /**
     *
     * @param Twig $Twig
     * @param CommonService $CommonService

     */
    public function __construct(
            Twig $Twig
            ,EntityManagerInterface $EntityManager
            ,CommonService $CommonService
            ,RecommendProductRepository $RecommendProductRepository
            ,OptionService $OptionService
            ,DeliveryFeeService $DeliveryFeeService
            ,FormFactoryInterface $FormFactory


    ) {
        $this->Twig = $Twig;
        $this->em = $EntityManager;
        $this->CommonService = $CommonService;
        $this->RecommendRepository = $RecommendProductRepository;
        $this->OptionService = $OptionService;
        $this->DeliveryFeeService = $DeliveryFeeService;
        $this->FormFactory =$FormFactory;

    }

    public function getFunctions()
    {
        return [
            new TwigFunction('ItemDetail', [$this, 'setItemDetail']),
            new TwigFunction('SexForm', [$this, 'setSexForm']),
            new TwigFunction('HeightForm', [$this, 'setHeightForm']),
            new TwigFunction('MenForm', [$this, 'setMenForm']),
            new TwigFunction('KoteForm', [$this, 'setKoteForm']),
            new TwigFunction('DouForm', [$this, 'setDouForm']),
            new TwigFunction('TareForm', [$this, 'setTareForm']),
            new TwigFunction('TareForm', [$this, 'setTareForm']),
            new TwigFunction('DouiForm', [$this, 'setDouiForm']),
            new TwigFunction('HakamaForm', [$this, 'setHakamaForm']),
            new TwigFunction('ShinaiForm', [$this, 'setShinaiForm']),
            new TwigFunction('ZekkenForm', [$this, 'setZekkenForm']),
            new TwigFunction('FreeInput1Form', [$this, 'setfreeInput1']),
            new TwigFunction('FreeInput2Form', [$this, 'setfreeInput2']),
            new TwigFunction('FreeInput3Form', [$this, 'setfreeInput3']),
            new TwigFunction('AdminItemOption', [$this, 'setAdminItemOption']),
            new TwigFunction('categoryFormat1', [$this, 'setcategoryFormat1']),
            new TwigFunction('ProductListImg', [$this, 'setProductListImg']),
            new TwigFunction('CategoryBestItem', [$this, 'setCategoryBestItem']),
            new TwigFunction('ProductListInfo', [$this, 'setProductListInfo']),
            new TwigFunction('showOption', [$this, 'setShowOption']),
            new TwigFunction('DeliveryFreeAlert', [$this, 'setSDeliveryFreeAlert']),
            new TwigFunction('DeliveryFree', [$this->DeliveryFeeService, 'getDeliveryFree']),
            new TwigFunction('AdminOrderShop', [$this, 'getAdminOrderShop']),

        ];
    }



    
    public function setProductListImg(?array $Datas){

            extract($Datas);
            list($Flg,$Image) = $this->setProductLisrFlg($Datas);
            
     
            if(0 == $Flg){return ;}         
        
        return $this->Twig->render(self::ProductListImgTwig, [
           'image' => $Image,
           'Category'  => $category_id,
           'flg'   => $Flg,
           'Shop'  => $Shop, 
        ]);                    

    }

    public function setProductListInfo(?array $Datas){
        extract($Datas);

        list($Flg,$Image) = $this->setProductLisrFlg($Datas ,true);
        if(0 == $Flg){return ;}

        $Info = null;
    
        if('category' == $Flg){
            $Paths = $category_id->getPath();
            $Path  = array_shift($Paths);
            if(!$Info = $this->em->getRepository(SubContent::class)->GetSubContentPart('category',$Path->getId())){return ;};
        }
        $urlFlg = 0;
        if('shop' == $Flg){
            $urlFlg = preg_match('/^http:\/\/+.$/',$Shop->getShopUrl()) ? 1:2;
        }

        return $this->Twig->render(self::ProductListInfoTwig, [
            'Flg' => $Flg, 
            'Info' => $Info,
            'Shop' => $Shop,
            'urlFlg' => $urlFlg
        ]);

    }

    private function setProductLisrFlg(array $Datas,bool $InfoFlg = false ){

            extract($Datas);
            $Flg = 0;
            $Image = null;
                switch(true){
                    case $name:
                         $Flg = 0;
                        break;
                    case $Shop && is_null($category_id):
                        $Flg =  $Shop->isVisible() == true ? 'shop' : null;
                        break;
                    case $category_id:
                            $Flg = 'category';
                            if(!$InfoFlg){
                                $Image = self::categoryImgDir.$category_id->getId().'.jpg';
                                $Path = $this->CommonService->getConfig('html_user_data_dir').'/'. $Image;
                                $Flg  = file_exists($Path) ? 'category' :  null;
                            }
                        break;
                    case $price01:
                    case $price02:
                    case $Tag:    
                    default:    
                        $Flg = 0;
                        break;    
                }
   
            return [$Flg,$Image];

    } 


    /**
     * 
     */
    public function getAdminOrderShop(Shipping $Shipping ,Order $Order){

        /** @var Shop */    
        if($Shop = $Order->getShop()){
            return $Shop->getShopName();
        }

        /** @var Shop */
        $Shop =$Shipping->getShop();

        return $Shop->getShopName();
    }

    /**
     * カートで配送料のアラートを出力
     */
    public function setSDeliveryFreeAlert(?Shop $Shop ,array $Amount){

        if(is_null($Shop)){return ;} 
        
        return $this->Twig->render(self::DeliveryFeeAlert, [
                
          'Shop' => $Shop,
          'Amount' =>$Amount[$Shop->getId()],

        ]);

    }

    public function setShowOption(array|null $Options ,$flg = true){

        if(is_null($Options)){return;}
 
        return $this->Twig->render(self::ItemOption, [
          'ItemOptions' => $this->OptionService->setShowOption($Options,$flg),
          'flg' => $flg,
               
        ]);


    }    





    public function setCategoryBestItem(?Category $Category){
        
        if(!$Category){return ;}
        $Recommands = $this->RecommendRepository->getRecommendByCategory($Category);

        if (count($Recommands)<1){return ;}

        return $this->Twig->render(self::BestItemTwig, [
            'Recommands' => $Recommands,
            'Category'   => $Category
        ]);

    }




    public function setItemDetail(Product $Product ){

  
        $title  = $this->CommonService->getYaml('productDetail.yaml');
    
        return $this->Twig->render(self::ItemDetail, [
           'title' =>  $title,                
           'Product' =>  $Product,
   
               
        ]);


    }
    public function setSexForm(Product $Product, FormView $form,$adminFlg = false){

        if(!$Product->hasCategorySex()){return;}
        $twig = $adminFlg ? self::SexTwigAdmion : self::SexTwig;
        return $this->Twig->render($twig, [
            'form' =>   $form,             
            'Product' =>  $Product,
            'adminFlg' => $adminFlg,    
    ]);

    }

    public function setHeightForm(Product $Product, FormView $form,$adminFlg = false){

        if(!$Product->hasCategoryHeight()){return;}
        $twig = $adminFlg ? self::HeightTwigAdmin : self::HeightTwig;
        return $this->Twig->render($twig, [
            'form' =>   $form,             
            'Product' =>  $Product,
            'adminFlg' => $adminFlg,
                    
        ]);

    }

    public function setMenForm(Product $Product, FormView $form,$adminFlg = false){

        if(!$Product->hasCategoryMen()){return;}

        $twig = $adminFlg ? self::MenTwigAdmin : self::MenTwig;
        return $this->Twig->render($twig, [
           'form' =>   $form,             
           'Product' =>  $Product,
           'adminFlg' => $adminFlg,
                 
        ]);

    }

    public function setKoteForm(Product $Product, FormView $form,$adminFlg = false){

    if(!$Product->hasCategoryKote()){return;}

    $twig = $adminFlg ? self::KoteTwigAdmin : self::KoteTwig;
    return $this->Twig->render($twig, [
        'form' =>   $form,             
        'Product' =>  $Product,
        'adminFlg' => $adminFlg,
                
    ]);

}
    public function setDouForm(Product $Product, FormView $form,$adminFlg = false){

    if(!$Product->hasCategoryDou()){return;}

    $twig = $adminFlg ? self::DouTwigAdmin: self::DouTwig;
    return $this->Twig->render($twig, [
        'form' =>   $form,             
        'Product' =>  $Product,
        'adminFlg' => $adminFlg,
                
    ]);

    }
    public function setTareForm(Product $Product, FormView $form,$adminFlg = false){

    if(!$Product->hasCategoryTare()){return;}

    $twig = $adminFlg ? self::TareTwigAdmin : self::TareTwig;
    return $this->Twig->render($twig, [
        'form' =>   $form,             
        'Product' =>  $Product,
        'adminFlg' => $adminFlg,
                
    ]);
    }


    public function setDouiForm(Product $Product, FormView $form,$adminFlg = false){

        if(!$Product->hasCategoryDoui()){return;}

        $twig = $adminFlg ? self::DouiTwigAdmin : self::DouiTwig;
        return $this->Twig->render($twig, [
            'form' =>   $form,             
            'Product' =>  $Product,
            'adminFlg' => $adminFlg,
                    
        ]);

    }

    public function setHakamaForm(Product $Product, FormView $form,$adminFlg = false){

        if(!$Product->hasCategoryHakama()){return;}

        $twig = $adminFlg ? self::HakamaTwigAdmin : self::HakamaTwig;
        return $this->Twig->render($twig, [
            'form' =>   $form,             
            'Product' =>  $Product,
            'adminFlg' => $adminFlg,
                    
        ]);

    }
    public function setShinaiForm(Product $Product, FormView $form,$adminFlg = false){

        if(!$Product->hasCategoryShinai()){return;}

        $twig = $adminFlg ? self::ShinaiTwigAdmin : self::ShinaiTwig;
        return $this->Twig->render($twig, [
            'form' =>   $form,             
            'Product' =>  $Product,
            'adminFlg' => $adminFlg,
                    
        ]);

    }
    public function setZekkenForm(Product $Product, FormView $form ,$adminFlg = false){


        if(!$Product->hasCategoryZekken()){return;}

        $twig = $adminFlg ? self::ZekkenTwigAdmin : self::ZekkenTwig;
        return $this->Twig->render($twig, [
            'form' =>   $form,             
            'Product' =>  $Product,
            'adminFlg' => $adminFlg,
                    
        ]);

    }
    public function setFreeInput1(Product $Product, FormView $form,$adminFlg = false){

        if(!$Product->getFreeInputName1()){return;}

        $twig = $adminFlg ? self::FreeInput1Admin : self::FreeInput1;
        return $this->Twig->render($twig, [
            'form' =>   $form,             
            'Product' =>  $Product,
            'adminFlg' => $adminFlg,
    
                    
        ]);

    }
    public function setFreeInput2(Product $Product, FormView $form,$adminFlg = false){

        if(!$Product->getFreeInputName2()){return;}

            $twig = $adminFlg ? self::FreeInput2Admin : self::FreeInput2;
        return $this->Twig->render($twig, [
            'form' =>   $form,             
            'Product' =>  $Product,
            'adminFlg' => $adminFlg,
                    
        ]);
        
    }

    public function setFreeInput3(Product $Product, FormView $form,$adminFlg = false){

        if(!$Product->getFreeInputName3()){return;}

        $twig = $adminFlg ? self::FreeInput3Admin : self::FreeInput3;
        return $this->Twig->render($twig, [
            'form' =>   $form,             
            'Product' =>  $Product,
            'adminFlg' => $adminFlg,
                    
        ]);
    
    }

    public function setAdminItemOption(OrderItem $Item,array $OptionForms){

        if (1 != $Item->getOrderItemType()->getId()){return;}

        /** @var Producr */
        $Product = $Item->getProduct();

        /** @ var FormView */
        $id = $Item->getId();
    
        if(!$form = $OptionForms[$id] ?? null){return ;};


        return $this->Twig->render(self::AdminTtemOption, [
            'Product' => $Item->getProduct(),
            'Item' =>  $Item,
            'form' => $form,
            'otion' =>$Item->getOption(),

        ]);     
    }
       
    public function setcategoryFormat1(Category $Category){

   
        return str_repeat("_ ", $Category->getHierarchy() -1 ) .$Category->getName()."({$Category->getId()})";
    }

    public function getFilters()
    {
        return [
            new TwigFilter('SALE', [$this, 'setSale']),

        ];
    }
    public function setSale(Product $Product){

        if(!$Product->priceDownRate()){ return ;}

        return '<span class="plgShiro8PriceDownRate">SALE</span>';
    }
}
