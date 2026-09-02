<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年08月25日作成
   *
   *　app\Customize\Form\Extension\Admin\ProductTypeExtension.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/

namespace Customize\Form\Extension\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
#use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

use Eccube\Entity\Product;
use Eccube\Form\Type\Admin\ProductType;
use Customize\Entity\Shop;
use Customize\Entity\Master\ProductType as mtbProductType;




class ProductTypeExtension extends AbstractTypeExtension
{


    /**
     * @var EntityManagerInterface $em
     */
    protected $em;


    public function __construct(
        EntityManagerInterface $em


    ) {
        $this->em = $em;
    }


   /**
     * {@inheritdoc}
     */
    /*public function getExtendedType()
    {
    //    return ProductType::class;
    }*/

    /**
     * Return the class of the type being extended.
     */
    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
     
        /** @var Product */
        $Product = $options['data'];
        $Placeholder = $Product->getId() ? false : 'common.select';
        $builder
            ->add('Shop', EntityType::class, [
                'label' => '店舗',				
                'required' => true,				
                'class' => Shop::class,				
                'choices' => $this->em->getRepository(shop::class)->select($Product),				
                'choice_label' => 'shopName',      				
                'choice_value' => 'id',      				
                'constraints' => [				
                    new Assert\NotBlank(),				
                ],
                'placeholder' => $Placeholder,
            ])
            ->add('freeInputName1', TextType::class,[
                 'label' => 'フリー入力項目名1',
                 'required' => true,		

            ])
            ->add('freeInputName2', TextType::class,[
                 'label' => 'フリー入力項目名2',
                 'required' => true,		

            ])
            ->add('freeInputName3', TextType::class,[
                 'label' => 'フリー入力項目名3',
                 'required' => true,		

            ])
            ->add('MetaDescription', TextareaType::class,[
                 'label' => 'フリー入力項目名2',
                 'required' => true,		

            ])
            ->add('MetaKeyword', TextareaType::class,[
                 'label' => 'フリー入力項目名3',
                 'required' => true,		

            ])
            ->add('ItemFeatures', TextareaType::class,[
                 'label' => 'アイテム説明',
                 'required' => true,		
            ])
            ->add('ProductType', EntityType::class, [
                'label' => '店舗',				
                'required' => true,				
                'class' => mtbProductType::class,				
                'choices' => $this->em->getRepository(mtbProductType::class)->findAll(),				
                'choice_label' => 'name',      				
                'choice_value' => 'id',      				
                'constraints' => [				
                    new Assert\NotBlank(),				
                ],				
            ])
            ->add('Material', TextareaType::class,[
                 'label' => '素材',
                 'required' => true,		
            ])	
		    ->add('Weight', TextType::class,[
                 'label' => '重さ(g)',
                 'required' => true,		
            ])
            ->add('StitchType', TextareaType::class,[
                 'label' => '素材',
                 'required' => true,		
            ])
			->add('StitchWidth', TextType::class,[
                 'label' => '重さ(g)',
                 'required' => true,		
            ])
			->add('MenBaseSize', TextType::class,[
                 'label' => '重さ(g)',
                 'required' => true,		
            ])
			
			->add('KoteBaseSize', TextType::class,[
                 'label' => '重さ(g)',
                 'required' => true,		
            ])
			
			->add('TareBaseSize', TextType::class,[
                 'label' => '重さ(g)',
                 'required' => true,		
            ])
			
		    ->add('Utikomi', TextType::class,[
                 'label' => '重さ(g)',
                 'required' => true,		
            ])
            ->add('DouBaseSize', TextType::class,[
                 'label' => '重さ(g)',
                 'required' => true,		
            ])


        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
