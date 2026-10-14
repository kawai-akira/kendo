<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年10月13日作成
   *
   * app\Customize\Form\Extension\Front\SearchProductTypeExtention.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/

namespace Customize\Form\Extension\Front;

use Symfony\Component\Form\AbstractTypeExtension;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Form\Type\SearchProductType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Customize\Entity\Shop;
use Eccube\Entity\Tag;
class SearchProductTypeExtention extends AbstractTypeExtension
{


       
    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    /*
     * SearchProductTypeExtention constructor.
     */
    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }    

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            
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
                'choices' => $this->em->getRepository(Shop::class)->findBy(['ShopStatus'=>1],['sort_no'=>'ASC','id'=>'ASC']),
                'choice_label' => 'ShopName',      						
                'choice_value' => 'id',
                'placeholder' => '全武道具店',   						
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
     * Return the class of the type being extended.
     */
    public static function getExtendedTypes(): iterable
    {
        return [SearchProductType::class];
    }
}
