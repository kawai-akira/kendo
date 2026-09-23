<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年09月21日作成
   *
   * app\Customize\Form\Type\Plugin\RecommendProductType.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/

namespace Customize\Form\Type\Plugin;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Form\DataTransformer\EntityToIdTransformer;
use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvents;
use Eccube\Form\DataTransformer;

use Eccube\Entity\Category;
use Customize\Entity\Master\RecommendKbn;
use Customize\Entity\Master\RecommendType;
/**
 * Class RecommendProductType.
 */
class RecommendProductType extends \Plugin\Recommend42\Form\Type\RecommendProductType
{
    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * RecommendProductType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EccubeConfig $eccubeConfig, EntityManagerInterface $entityManager)
    {

        parent::__construct($eccubeConfig,$entityManager);
        $this->eccubeConfig = $eccubeConfig;
        $this->em = $entityManager;
    }

    /**
     * Build config type form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        parent::buildForm($builder,$options);
        $builder
            ->add($builder
                ->create('type', HiddenType::class, [								
                    'data_class' => null,
                    'mapped' => false,							
                ])			
                ->addModelTransformer(new EntityToIdTransformer($this->em, RecommendType::class))
            )
            ->add($builder->create('Category', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->em,Category::class
            )))	
            ->add($builder->create('RecommendKbn', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->em,RecommendKbn::class
            )))

        ;
   
    }



    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_recommend';
    }
}
