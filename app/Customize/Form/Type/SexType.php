<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\Form\Type\Mentype.php
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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Entity\Master\Sex;


class SexType extends AbstractType
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



        $builder

            
            ->add('sex', EntityType::class, [
                'required' => false,
                'class' => sex::class,				
                'choices' => $this->em->getRepository(sex::class)->findBy(['id'=>[1,2]]),				
                'choice_label' => 'name',      				
                'choice_value' => 'id',   
                'multiple' => false,
                'expanded' => true,
                'placeholder' => false,
        	    'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]);

            }

}