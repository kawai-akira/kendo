<?php
/**
 * @version EC=CUBE4.3
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年10月20日作成
 *
 * pp\Customize\Form\Type\Front\KenJopypaginationType.php
 *                                                
 *  
 *
 *
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Form\Type;

#use Doctrine\ORM\EntityManagerInterface;
#use Eccube\Entity\Master\ProductListMax;
#use Eccube\Entity\Master\ProductListOrderBy;
#use Eccube\Form\Type\Master\ProductListMaxType;
#use Eccube\Form\Type\Master\ProductListOrderByType;
#use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
#use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
#use Symfony\Component\OptionsResolver\OptionsResolver;

class KenJopypaginationType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('pageno', HiddenType::class, [
            'data' => 1
        ])
            ->add('disp_number', HiddenType::class, [
            'data'=>10
        ])
            ->add('holiderr', HiddenType::class, []);
        /*
        $builder->add('orderby', ProductListOrderByType::class, [
            'label' => false,
            'choices' => $this->entityManager->getRepository(ProductListOrderBy::class)->findBy([], ['sort_no' => 'ASC']),
        ]);*/
    
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'search_product';
    }
}