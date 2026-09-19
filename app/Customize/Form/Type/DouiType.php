<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\Form\Type\DouiType.php
 *
 * 
 *
 *
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/

namespace Customize\Form\Type;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Customize\Entity\Master\DouiType as dType;
use Customize\Entity\Master\DouiHope;


class DouiType extends AbstractType
{


    /**
     * @var EntityManagerInterface $em
     */
    protected $em;


    public function __construct(
        EntityManagerInterface $em
        ){
            $this->em = $em;
        }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $itemOption = $options['itemOptions'];
        $Values =  $itemOption['doui'] ?? null ;

        $doui_type = $this->em->getRepository(dType::class)->find($Values['doui_type'] ?? null);
        $doui_hope = $this->em->getRepository(DouiHope::class)->find($Values['doui_hope'] ?? null);



        $builder
            ->add('doui_type', EntityType::class,[
                'required' => false,
                'class' => dType::class,				
                'choices' => $this->em->getRepository(dType::class)->findAll(),				
                'choice_label' => 'name',      				
                'choice_value' => 'id',   
                'placeholder' => 'common.select',

                'constraints' =>[
                    new Assert\NotBlank(),
                ],
                'data' => $doui_type,
            ])
            ->add('doui_hope', EntityType::class,[
                'required' => false,
                'class' => DouiHope::class,				
                'choices' => $this->em->getRepository(DouiHope::class)->findAll(),				
                'choice_label' => 'name',      				
                'choice_value' => 'id',   
                'placeholder' => 'common.select',
                'constraints' =>[
                    new Assert\NotBlank(),
                ],
                'data' => $doui_hope,
            ])
            ->add('doui_etc', TextareaType::class,[
                'label' => '道衣その他希望',
                'required' => false,
                'data' => $Values['doui_etc'] ?? null,
            ]);
    }

     /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'itemOptions' => null,

        ]);
    }


}