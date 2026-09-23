<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\Form\Type\Plugin\RecommendSearchType.php
 *
 * 
 *
 *
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/

namespace Customize\Form\Type\Plugin;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Eccube\Entity\Category;
use Customize\Entity\Master\RecommendKbn;
use Customize\Entity\Master\RecommendType;

class RecommendSearchType extends AbstractType
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

        $type = $this->em->getRepository(RecommendType::class)->find(RecommendType::CATEGORY);
        $Category = null ;
        $Kbn =null;
        if ($options['type']){
            $type = $this->em->getRepository(RecommendType::class)->find($options['type']);
            $id = $options['id'];
            if (RecommendType::CATEGORY == $options['type']){
                $Category = $this->em->getRepository(Category::class)->find($id);
            }else{
                $Kbn  = $this->em->getRepository(RecommendKbn::class)->find($id);
            }   
        }
        $builder
            ->add('type', EntityType::class, [
                'label' => 'タイプ',
                'required' => true,				
                'class' => RecommendType::class,				
                'choices' => $this->em->getRepository(RecommendType::class)->findAll(),					
                'choice_label' => 'name',      				
                'choice_value' => 'id',
                'multiple' => false,
                'expanded' => true,
                'placeholder' => false ,       				
                'data' => $type,
            ])
            ->add('Category', ChoiceType::class, [
                'choice_label' => 'Name',
                'multiple' => false,
                'expanded' => false,
                'choices' => $this->em->getRepository(Category::class)->getList(null, true),
                'choice_value' => function (?Category $Category = null) {
                    return $Category ? $Category->getId() : null;
                },
                'placeholder' => 'admin.common.select' ,
                'data' => $Category,
            ])
            ->add('RecommendKbn', EntityType::class, [
                'label' => '区分',				
                'required' => false,				
                'class' => RecommendKbn::class,				
                'choices' => $this->em->getRepository(RecommendKbn::class)->findAll(),					
                'choice_label' => 'name',      				
                'choice_value' => 'id',      				
                'placeholder' => 'admin.common.select' ,
                'data' => $Kbn,
            ]);
                
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => null,
            'id'   => null,

        ]);
    }



    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_search_shop';
    }
}