<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年10月14日作成
   *
   * app\Customize\Form\Type\SearchProductCstType.php
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/

namespace Customize\Form\Type;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Form\Type\SearchProductType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Customize\Entity\Shop;
use Eccube\Repository\CategoryRepository;
use Customize\Repository\ProductCategoryRepository;
use Eccube\Entity\Tag;

class SearchProductCstType extends AbstractType
{

    /**
     * @var CategoryRepository 
     */
    protected $CategoryRepository;   
    /**
     * @var EntityManagerInterface $em
     */
    protected $em;
    /**
     * @var ProductCategoryRepository
     */
    private $ProductCategoryRepository;
    /*
     * SearchProductTypeExtention constructor.
     */
    public function __construct(
        EntityManagerInterface $em
        ,CategoryRepository $CategoryRepository
        ,ProductCategoryRepository $ProductCategoryRepository
    ) {
        $this->em = $em;
        $this->CategoryRepository =$CategoryRepository;
        $this->ProductCategoryRepository = $ProductCategoryRepository;
    }    

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ShopId = $options['ShopId'];
        echo $ShopId;
        $placeholder = 'common.select__all_products';
        
        if($ShopId){
            $Shop = $this->em->getRepository(Shop::class)->find($ShopId);
            $placeholder = $Shop->getShopName().'のカテゴリー';
            $Categories = $this->ProductCategoryRepository->getCategoryByShop($Shop);
        }else{
            $Categories = $this->CategoryRepository->getList(null, true);
           
        }
        $builder
            ->add('price01',TextType::class,[
                'label' => '価格1',						
                'required' => false,	
            ])
            ->add('price02',TextType::class,[
                'label' => '価格1',						
                'required' => false,	
            ])

            ->add('Shop', EntityType::class, [
                'label' => '武具店',						
                'required' => false,						
                'class' => Shop::class,						
                'choices' => $this->em->getRepository(Shop::class)->search(),
                'choice_label' => 'ShopName',      						
                'choice_value' => 'id',
                'placeholder' => '全武道具店',   						
            ])
            ->add('category_id', EntityType::class, [
            'class' => 'Eccube\Entity\Category',
            'choice_label' => 'NameWithLevel',
            'choices' => $Categories,
            'placeholder' => $placeholder,
            'required' => false,
        ])
            ->add('Tag', EntityType::class, [
                'label' => 'タグ',						
                'required' => false,						
                'class' => Shop::class,						
                'choices' => $this->em->getRepository(Tag::class)->findAll(),
                'choice_label' => 'name',      						
                'choice_value' => 'id',
                'placeholder' => '全商品',   						
      ]);;					

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'ShopId' => null,
        ]);
    }

					

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'search_product';
    }
}
