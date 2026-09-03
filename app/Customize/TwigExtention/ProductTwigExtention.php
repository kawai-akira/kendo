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
use Google\Service\GKEOnPrem\BareMetalNetworkConfig;

class ProductTwigExtention extends AbstractExtension
{

    private const ItemDetail    = 'Product/Parts/ItemDetailArea.twig';
    private const SexTwig       = 'Product/Parts/SexForm.twig';
    private const HeightTwig    = 'Product/Parts/HeightForm.twig';
    private const MenTwig       = 'Product/Parts/MenForm.twig';
    private const KoteTwig      = 'Product/Parts/KoteForm.twig';
    private const DouTwig       = 'Product/Parts/DouForm.twig';
    private const TareTwig      = 'Product/Parts/TareForm.twig';
    private const DouiTwig      = 'Product/Parts/DouiForm.twig';
    private const HakamaTwig    = 'Product/Parts/HakamaForm.twig';
    private const ShinaiTwig    = 'Product/Parts/ShinaiForm.twig';
    private const ZekkenTwig    = 'Product/Parts/ZekkenForm.twig';
    private const FreeInput1    = 'Product/Parts/FreeInput1Form.twig';
    private const FreeInput2    = 'Product/Parts/FreeInput2Form.twig';
    private const FreeInput3    = 'Product/Parts/FreeInput3Form.twig';
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
    public function setSexForm(Product $Product, FormView $form){

        if(!$Product->hasCategorySex()){return;}

        return $this->Twig->render(self::SexTwig, [
            'form' =>   $form,             
            'Product' =>  $Product,
                
    ]);

    }

    public function setHeightForm(Product $Product, FormView $form){

        if(!$Product->hasCategoryHeight()){return;}

        return $this->Twig->render(self::HeightTwig, [
            'form' =>   $form,             
            'Product' =>  $Product,
                    
        ]);

    }

    public function setMenForm(Product $Product, FormView $form){

        if(!$Product->hasCategoryMen()){return;}

        return $this->Twig->render(self::MenTwig, [
           'form' =>   $form,             
           'Product' =>  $Product,
                 
        ]);

    }

    public function setKoteForm(Product $Product, FormView $form){

    if(!$Product->hasCategoryKote()){return;}


    return $this->Twig->render(self::KoteTwig, [
        'form' =>   $form,             
        'Product' =>  $Product,
                
    ]);

}
    public function setDouForm(Product $Product, FormView $form){

    if(!$Product->hasCategoryDou()){return;}


    return $this->Twig->render(self::DouTwig, [
        'form' =>   $form,             
        'Product' =>  $Product,
                
    ]);

    }
    public function setTareForm(Product $Product, FormView $form){

    if(!$Product->hasCategoryTare()){return;}


    return $this->Twig->render(self::TareTwig, [
        'form' =>   $form,             
        'Product' =>  $Product,
                
    ]);
    }

    public function setFreeInput1(Product $Product, FormView $form){

    if(!$Product->getFreeInputName1()){return;}


    return $this->Twig->render(self::FreeInput1, [
        'form' =>   $form,             
        'Product' =>  $Product,
                
    ]);

    }
    public function setFreeInput2(Product $Product, FormView $form){

    if(!$Product->getFreeInputName2()){return;}


    return $this->Twig->render(self::FreeInput2, [
        'form' =>   $form,             
        'Product' =>  $Product,
                
    ]);
        
    }

    public function setFreeInput3(Product $Product, FormView $form){

        if(!$Product->getFreeInputName3()){return;}


        return $this->Twig->render(self::FreeInput3, [
            'form' =>   $form,             
            'Product' =>  $Product,
                    
        ]);
    
    }
    public function setDouiForm(Product $Product, FormView $form){

        if(!$Product->hasCategoryDoui()){return;}


        return $this->Twig->render(self::DouiTwig, [
            'form' =>   $form,             
            'Product' =>  $Product,
                    
        ]);

    }

    public function setHakamaForm(Product $Product, FormView $form){

        if(!$Product->hasCategoryHakama()){return;}


        return $this->Twig->render(self::HakamaTwig, [
            'form' =>   $form,             
            'Product' =>  $Product,
                    
        ]);

    }
    public function setShinaiForm(Product $Product, FormView $form){

        if(!$Product->hasCategoryShinai()){return;}


        return $this->Twig->render(self::ShinaiTwig, [
            'form' =>   $form,             
            'Product' =>  $Product,
                    
        ]);

    }
        public function setZekkenForm(Product $Product, FormView $form){

        if(!$Product->hasCategoryZekken()){return;}


        return $this->Twig->render(self::ZekkenTwig, [
            'form' =>   $form,             
            'Product' =>  $Product,
                    
        ]);

    }
}         


