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
use Symfony\Component\Form\FormView;
#use Twig\TwigFilter;
#use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment as Twig;
use Eccube\Entity\Product;
use Customize\Service\CommonService;
#use Google\Service\GKEOnPrem\BareMetalNetworkConfig;
use Eccube\Entity\OrderItem;

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
       

}
