<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年09月15日作成
 *
 * app\Customize\Form\Type\ItemOptionType.php
 *
 * 
 *
 *
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Customize\Form\Type\MenType;
use Customize\Form\Type\KoteType;
use Customize\Form\Type\DouType;
use Customize\Form\Type\TareType;
use Customize\Form\Type\DouiType;
use Customize\Form\Type\HakamaType;
use Customize\Form\Type\SexType;
use Customize\Form\Type\HeightType;
use Customize\Form\Type\ShinaiType;
use Customize\Form\Type\ZekkenType;
use Customize\Form\Type\FreeInput1Type;
use Customize\Form\Type\FreeInput2Type;
use Customize\Form\Type\FreeInput3Type;


class ItemOptionType extends AbstractType
{



    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        //parent::buildForm($builder,$options);
        /** @var \Eccube\Entity\Product $Product */
        $Product = $options['product'];
        $itemOptions = $options['itemOptions'];


        if($Product->hasCategorySex()){
            $builder
                ->add('sex', SexType::class, [
                    'itemOptions' => $itemOptions,
            ]) ;
        }

        if($Product->hasCategoryHeight()){
            $builder
                ->add('height', HeightType::class, [
                    'itemOptions' => $itemOptions,
            ]) ;
        }
        if($Product->hasCategoryMen()){
           
            $builder
                ->add('men', Mentype::class, [
                    'itemOptions' => $itemOptions,
            ]) ;
        }
        if($Product->hasCategoryKote()){
            $builder
                ->add('kote', KoteType::class, [
                    'itemOptions' => $itemOptions,
            ]) ;

        }
        if($Product->hasCategoryDou()){
            $builder
                ->add('dou', DouType::class, [
                    'itemOptions' => $itemOptions,
                ]) ;

        }
        if($Product->hasCategoryTare()){
            $builder
                ->add('tare', TareType::class, [
                    'itemOptions' => $itemOptions,
                ]) ;
        }
        if($Product->hasCategoryDoui()){
            $builder
                ->add('doui', DouiType::class, [
                      'itemOptions' => $itemOptions,
                ]) ;
        }
        if($Product->hasCategoryHakama()){
            $builder
                ->add('hakama', HakamaType::class, [
                    'itemOptions' => $itemOptions,
                ]) ;
        }
        if($Product->hasCategoryShinai()){
            $builder
                ->add('shinai', ShinaiType::class, [
                    'itemOptions' => $itemOptions,
                ]) ;
        }
        if($Product->hasCategoryZekken()){
            $builder
                ->add('zekken', ZekkenType::class, [
                    'itemOptions' => $itemOptions,
                ]) ;
        }
        if($Product->getfreeInputName1()){
            $builder
                ->add('FreeInput1', FreeInput1Type::class, [
                    'itemOptions' => $itemOptions, 
                    'Product'     => $Product,           
                ]) ;
        }
        if($Product->getfreeInputName2()){
            $builder
                ->add('FreeInput2', FreeInput2Type::class, [
                    'itemOptions' => $itemOptions,
                    'Product'     => $Product, 
                ]) ;
        }
        if($Product->getfreeInputName3()){
            $builder
                ->add('FreeInput3', FreeInput3Type::class, [
                    'itemOptions' => $itemOptions,
                    'Product'     => $Product,
                ]) ;
        }




        }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('product',
        );
        $resolver->setDefaults([
            'itemOptions' => null,

        ]);
    }


}
