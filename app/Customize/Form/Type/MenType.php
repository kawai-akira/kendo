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
#use Doctrine\ORM\EntityManager;
#use Doctrine\Persistence\ManagerRegistry;
#use Dom\Entity;
#use Eccube\Common\EccubeConfig;
#use Eccube\Entity\CartItem;
use Eccube\Entity\ProductClass;
use Eccube\Form\Type\Master\SexType;
use Eccube\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
#use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

#use Symfony\Component\Form\FormInterface;
#use Symfony\Component\Form\FormView;
#use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
#use Symfony\Component\Validator\Context\ExecutionContext;
use Eccube\Entity\Master\Sex;


class MenType extends AbstractType
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
            ->add('men_size_a', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                        'message' => '数値を入力してください'])
                ],
            ])
            ->add('men_size_b', TextType::class, [
               'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                        'message' => '数値を入力してください'])
                ],
            ])
            ->add('men_size_c', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                        'message' => '数値を入力してください'])
                ],
            ])

         //2021/05/18 kakeru
            ->add('men_etc', TextareaType::class, [
                'label' => 'その他希望',
     		    'required' => false,
            ]);
            /*
            ->add('men_color', 'men_color', [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ),
            ));
            */
            }

}