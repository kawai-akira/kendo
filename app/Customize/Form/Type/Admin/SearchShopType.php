<?php
/**
 * @version EC=CUBE4.3
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年09月09日作成
 *
 * app\Customize\Form\Type\Admin\SearchShopType.php
 *
 *
 * 
 * 　　
 *　
 *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Master\Pref;
use Customize\Entity\Master\ShopStatus;
use Symfony\Component\Validator\Constraints as Assert;
use Customize\Service\CommonService;




class SearchShopType extends AbstractType
{

    /**
     * @var CommonService
     */
    private $CommonService;
    /**
     * @var EntityManagerInterface $em
     */
    private $em;



    public function __construct(
                CommonService $CommonService
                ,EntityManagerInterface $em
    )
    {
        $this->CommonService = $CommonService;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('id', TextType::class, [
                'label' => 'ID',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->CommonService->getConfig('stext_len')]),
                ],
            ])
            ->add('shopName', TextType::class, [
                'label' => '店舗名',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->CommonService->getConfig('stext_len')]),
                ],
            ])
            ->add('pref', EntityType::class, [
                'label' => '都道府県',
                'required' => false,
                'class' => Pref::class,				
                'choices' => $this->em->getRepository(Pref::class)->findAll(),				
                'choice_label' => 'name',      				
                'choice_value' => 'id',      				
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^[\d-]+$/u",
                        'message' => 'form.type.admin.nottelstyle',
                    ])
                ]
        
            ])
            ->add('ShopStatus', EntityType::class, [
                'label' => 'ステータス',
                'required' => false,
                'class' => Pref::class,				
                'choices' => $this->em->getRepository(ShopStatus::class)->findAll(),				
                'choice_label' => 'name',      				
                'choice_value' => 'id',
                'multiple' => true,
                'expanded' => true,
                'placeholder' => false,
                'data'        => $this->em->getRepository(ShopStatus::class)->findBy(['id'=>1]),

            ])
            ->add('create_date_start', DateType::class, [
                'label' => 'admin.common.create_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\Range([
                        'min'=> '0003-01-01',
                        'minMessage' => 'form_error.out_of_range',
                    ]),
                ],
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_create_date_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('create_date_end', DateType::class, [
                'label' => 'admin.common.create_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\Range([
                        'min'=> '0003-01-01',
                        'minMessage' => 'form_error.out_of_range',
                    ]),
                ],
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_create_date_end',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
            ->add('sortkey', HiddenType::class, [
                'label' => 'admin.list.sort.key',
                'required' => false,
            ])
            ->add('sorttype', HiddenType::class, [
                'label' => 'admin.list.sort.type',
                'required' => false,
            ])

       /*     ->add('sort', 'choice', [
                'label' => '並べ替え',
                'required' => true,
                'choices' => [
                    'id#asc' => 'ID昇順',
                    'id#desc' => 'ID降順',
                    'create_date#asc' => '登録日昇順',
                    'create_date#desc' => '登録日降順',
                    'status#asc' => 'ステータス昇順',
                    'status#desc' => 'ステータス降順',
                ),
            ))*/
 ;
        
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_search_shop';
    }
}
