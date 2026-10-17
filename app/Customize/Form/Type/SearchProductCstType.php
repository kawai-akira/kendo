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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Customize\Entity\Shop;
use Eccube\Repository\CategoryRepository;
use Customize\Repository\ProductCategoryRepository;
use Eccube\Entity\Tag;
use Customize\Repository\ProductTagRepository;
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

    /**
     * @var ProductTagRepository
     */
    private $ProductTagRepository;
    /*
     * SearchProductTypeExtention constructor.
     */
    public function __construct(
        EntityManagerInterface $em
        ,CategoryRepository $CategoryRepository
        ,ProductCategoryRepository $ProductCategoryRepository
        ,ProductTagRepository $ProductTagRepository
    ) {
        $this->em = $em;
        $this->CategoryRepository =$CategoryRepository;
        $this->ProductCategoryRepository = $ProductCategoryRepository;
        $this->ProductTagRepository = $ProductTagRepository;
        }    

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ?Shop */
        $ShopId = $options['ShopId'];

        $CatPlaceholder = 'common.select__all_products';
        $TgPlaceholder  = '全の商品特性';
        
        if($ShopId){
            /** @var Shop */
            $Shop = $this->em->getRepository(Shop::class)->find($ShopId);
            $CatPlaceholder = $Shop->getShopName().'のカテゴリー';
            $Categories = $this->ProductCategoryRepository->getCategoryByShop($Shop);
            $Tags = $this->ProductTagRepository->getTagByShop($Shop);
            $TgPlaceholder = $Shop->getShopName().'の商品特性';

           
        }else{
            $Categories = $this->CategoryRepository->getList(null, true);
            //$Tags = $this->em->getRepository(Tag::class)->findBy([],['sort_no'=>'DESC']);
            $Tags = $this->ProductTagRepository->getTagByShop();
        }

        if (count($Tags) < 1 ){$TgPlaceholder ='商品特性はお選びいただけません';}
        $builder
            ->add('min',TextType::class,[
                'label' => '価格1',						
                'required' => false,	
            ])
            ->add('max',TextType::class,[
                'label' => '価格1',						
                'required' => false,	
            ])
            ->add('name', SearchType::class, [
            'required' => false,
            'attr' => [
                'maxlength' => 50,
            ],
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
            'placeholder' => $CatPlaceholder,
            'required' => false,
        ])
            ->add('Tag', EntityType::class, [
                'label' => 'タグ',						
                'required' => false,						
                'class' => Shop::class,						
                'choices' => $Tags,
                'choice_label' => 'name',      						
                'choice_value' => 'id',
                'placeholder' => $TgPlaceholder ,   						
      ]);				

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
