<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\Form\Type\Front\AddCartType.php
 *
 * 
 *
 *
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Form\Type\Front;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\CartItem;
use Eccube\Entity\ProductClass;
use Eccube\Form\DataTransformer\EntityToIdTransformer;
use Eccube\Repository\ProductClassRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;
use Customize\Form\Type\MenType;
use Customize\Form\Type\KoteType;
use Customize\Form\Type\DouType;
use Customize\Form\Type\TareType;
use Customize\Form\Type\SexType;
use Customize\Form\Type\HeightType;
use Customize\Form\Type\FreeInput1Type;
use Customize\Form\Type\FreeInput2Type;
use Customize\Form\Type\FreeInput3Type;
#class AddCartType extends \Eccube\Form\Type\AddCartType
class AddCartType extends AbstractType
{

    /**
     * @var EccubeConfig
     */
    private $config;
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

        /**
     * @var \Eccube\Entity\Product
     */
    protected $Product = null;


//public function __construct(ManagerRegistry $doctrine, EccubeConfig $config)
    public function __construct(ManagerRegistry $doctrine,EccubeConfig $config)
    {
        //parent::__construct($doctrine, $config) ;  
        $this->doctrine = $doctrine;
        $this->config = $config;

    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        //parent::buildForm($builder,$options);
        /** @var \Eccube\Entity\Product $Product */
        $Product = $options['product'];
        $this->Product = $Product;
        $ProductClasses = $Product->getProductClasses();


        $builder
            ->add('product_id', HiddenType::class, [
                'data' => $Product->getId(),
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex(['pattern' => '/^\d+$/']),
                ], ]);



        if($Product->hasCategorySex()){
            $builder
                ->add('sex', SexType::class, [
            ]) ;
        }

        if($Product->hasCategoryHeight()){
            $builder
                ->add('height', HeightType::class, [
            ]) ;
        }
        if($Product->hasCategoryMen()){
           
            $builder
                ->add('men', Mentype::class, [
            ]) ;
        }
        if($Product->hasCategoryKote()){
            $builder
                ->add('kote', KoteType::class, [
            ]) ;

        }
        if($Product->hasCategoryDou()){
            $builder
                ->add('dou', DouType::class, [
            ]) ;

        }

        if($Product->hasCategoryTare()){
            $builder
                ->add('tare', TareType::class, [
            ]) ;

        }
        if($Product->getfreeInputName1()){
            $builder
                ->add('FreeInput1', FreeInput1Type::class, [
            ]) ;
        }
        if($Product->getfreeInputName2()){
            $builder
                ->add('FreeInput2', FreeInput2Type::class, [
            ]) ;
        }
        if($Product->getfreeInputName3()){
            $builder
                ->add('FreeInput3', FreeInput3Type::class, [
            ]) ;
        }

        

                $builder
                    ->create('ProductClass', HiddenType::class, [
                        'data_class' => null,
                        'data' => $Product->hasProductClass() ? null : $ProductClasses->first(),
                        'constraints' => [
                            new Assert\NotBlank(),
                        ],
                    ])
                    ->addModelTransformer(new EntityToIdTransformer($this->doctrine->getManager(), ProductClass::class));

        if ($Product->getStockFind()) {
            $builder
                ->add('quantity', IntegerType::class, [
                    'data' => 1,
                    'attr' => [
                        'min' => 1,
                        'maxlength' => $this->config['eccube_int_len'],
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\GreaterThanOrEqual([
                            'value' => 1,
                        ]),
                        new Assert\Regex(['pattern' => '/^\d+$/']),
                    ],
                ]);
            if ($Product && $Product->getProductClasses()) {
                if (!is_null($Product->getClassName1())) {
                    $builder->add('classcategory_id1', ChoiceType::class, [
                        'label' => $Product->getClassName1(),
                        'choices' => ['common.select' => '__unselected'] + $Product->getClassCategories1AsFlip(),
                        'mapped' => false,
                        'constraints' => [
                            new Assert\NotBlank(),
                            new Assert\NotEqualTo([
                            'value' => '__unselected',
                            'message' => 'form_error.not_selected',
                        ])
                        ]
                    ]);
        
                }
                if (!is_null($Product->getClassName2())) {
                    $builder->add('classcategory_id2', ChoiceType::class, [
                        'label' => $Product->getClassName2(),
                        'choices' => ['common.select' => '__unselected'],
                        'mapped' => false,
                        'constraints' => [
                            new Assert\NotBlank(),
                            new Assert\NotEqualTo([
                            'value' => '__unselected',
                             'message' => 'form_error.not_selected',
                        ])
                        ]

                    ]);
                }
            }
        }    

 /*           $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($Product) {
                $data = $event->getData();
                $form = $event->getForm();
                if (isset($data['classcategory_id1']) && !is_null($Product->getClassName2())) {
                    if ($data['classcategory_id1']) {
                        $form->add('classcategory_id2', ChoiceType::class, [
                            'label' => $Product->getClassName2(),
                            'choices' => ['common.select' => '__unselected'] + $Product->getClassCategories2AsFlip($data['classcategory_id1']),
                            'mapped' => false,
                        ]);
                    }
                }
            });
*/;

//            $dispatcher = $builder->getEventDispatcher();
//            foreach ($dispatcher->getListeners(FormEvents::POST_SUBMIT) as $listener) {
//                $dispatcher->removeListener(FormEvents::POST_SUBMIT, $listener);
           /* $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {

                $event->stopPropagation();
                return;
                / ** @var CartItem $CartItem * /
                $CartItem = $event->getData();
                /*$ProductClass = $CartItem->getProductClass();
                // FIXME 価格の設定箇所、ここでいいのか
                if ($ProductClass) {
                    $CartItem
                        ->setProductClass($ProductClass)
                        ->setPrice($ProductClass->getPrice02IncTax());
                }
            });*/
        //}
            ;
        }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('product');
        $resolver->setDefaults([
   //       'data_class' => CartItem::class,
            'id_add_product_id' => true,
            //'constraints' => [
            //    new Assert\Callback([$this, 'validate']),
                // FIXME new Assert\Callback(array($this, 'validate')),
            //],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'add_cart';
    }
}
