<?php
/**
 * @version EC=CUBE4.3
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年09月09日作成
 *
 * app\Customize\Form\Type\Admin\ShopType.php
 *
 *
 * 
 * 　　
 *　
 *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/

namespace Customize\Form\Type\Admin;

use Eccube\Form\DataTransformer;
use Eccube\Common\EccubeConfig;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Form\DataTransformer\EntityToIdTransformer;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\PriceType;
use Eccube\Form\Type\PostalType;
use Eccube\Form\Type\PhoneNumberType;
use Customize\Entity\Shop;
use Eccube\Entity\Member;
use Customize\Entity\Master\ShopStatus;
use Customize\Repository\MemberRepository;

class ShopType extends AbstractType
{

    /**
     * @var EccubeConfig
     */
    private $config;
    /**
     * @var EntityManagerInterface $em
     */
    private $em;


    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var MemberRepository.
     */
    private $MemberRepository;

    public function __construct(EccubeConfig $config
                                ,EntityManagerInterface $em
                                ,ManagerRegistry $doctrine
                                ,MemberRepository $MemberRepository)
    {
        $this->config = $config;
        $this->em = $em;
        $this->doctrine = $doctrine;
        $this->MemberRepository = $MemberRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Shop */
        $Shop = $options['data'];
        /** @var Member  */
        $Member = $Shop->getMember();

        $builder

            #店舗情報
            ->add('shopName', TextType::class, [
                'label' => '店舗名（必須）',
                'required' => true,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->config['eccube_stext_len'],
                    ]),
                    new Assert\NotBlank(),
                ],
            ])
            ->add('shop_image_file', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('shop_image', HiddenType::class, [
                'required' => false,
            ])
            
            ->add('postal_code',  PostalType::class, [
                'required' => true,
            ])
            ->add('address', AddressType::class, [
                'required' => true,
            ])



            ->add('phoneNumber', PhoneNumberType::class, [
                'label' => '電話番号（必須）',
                'required' => true,
	


            ])

            ->add('deliveryFreeAmount', PriceType::class, [
                'label' => '送料無料条件',
                'required' => false,
            ])
            ->add('shopStatus', EntityType::class, [
                'label' => 'ステータス',
		        'required' => false,			
                'class' => Shop::class,			
                'choices' => $this->em->getRepository(ShopStatus::class)->findAll(),			
                'choice_label' => 'name',      			
                'choice_value' => 'id',      			
                'constraints' => [			
                    new Assert\NotBlank(),
                ],
                'placeholder'=>false,		

            ])
            ->add('appeal', TextareaType::class, [
                'label' => 'アピール欄',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->config['eccube_ltext_len'],
                    ]),
                ],
            ])
            ->add('memo', TextareaType::class, [
                'label' => '備考欄',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->config['eccube_ltext_len'],
                    ]),
                ],
            ])
            ->add('productDetailMemo', TextareaType::class, [
                'label' => '商品詳細欄',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->config['eccube_ltext_len'],
                    ]),
                ],
            ])
            ->add('shopUrl', TextType::class, [
                'label' => '店舗リンクURL',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->config['eccube_ltext_len'],
                    ]),
                ],
            ]);

            if($Shop->getId()){
            $builder
                ->add($builder->create('Member', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->em,Member::class
                )));
            }else{
             $builder
                ->add('Member',  EntityType::class,[
                    'label' => '諸会費ステータス',
                    'required' => false,
                    'class' => Member::class,
                    'choices' => $this->MemberRepository->findNewMembers(),
                    'choice_label' => 'name',      
                    'choice_value' => 'id',
                    'placeholder' => 'common.select',
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ]);


            }
    }



    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
          'data_class' => Shop::class,

        ]);
    }


     /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_shop';
    }
}
