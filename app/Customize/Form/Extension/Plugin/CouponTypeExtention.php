<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年09月18日作成
   *
   *　app\Customize\Form\Extension\Plugin\CouponTypeExtention.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/

namespace Customize\Form\Extension\Plugin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Plugin\Coupon42\Form\Type\CouponType;
use Customize\Entity\Shop;

class CouponTypeExtention extends AbstractTypeExtension
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Shop', EntityType::class, [
                'label' => '店舗',				
                'required' => false,				
                'class' => Shop::class,				
                'choices' => $this->em->getRepository(Shop::class)->select(),					
                'choice_label' => 'shopName',      				
                'choice_value' => 'id',      				
                'placeholder' => 'plugin_coupon.admin.select' ,
            ])
            ;
    }



    /**
     * Return the class of the type being extended.
     */
    public static function getExtendedTypes(): iterable
    {
        return [CouponType::class];
    }
}