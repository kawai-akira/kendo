<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\Form\Type\KoteType.php
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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Entity\Master\Sex;


class KoteType extends AbstractType
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
            ->add('kote_size_left_d', TextType::class, [
                'required' => false,
                'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Regex([
                                'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                                'message' => '数値を入力してください'])
                        ],
                    ])
            ->add('kote_size_left_e', TextType::class, [
                        'required' => false,
                        'constraints' => [
                            new Assert\NotBlank(),
                            new Assert\Regex([
                                'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                                'message' => '数値を入力してください'])
                        ],
                    ])
                    ->add('kote_size_left_f', TextType::class, [
                        'required' => false,
                        'constraints' => [
                            new Assert\NotBlank(),
                            new Assert\Regex([
                                'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                                'message' => '数値を入力してください'])
                        ],
                    ])
                    ->add('kote_size_right_d', TextType::class, [
                        'required' => false,
                        'constraints' => [
                            new Assert\NotBlank(),
                            new Assert\Regex([
                                'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                                'message' => '数値を入力してください'])
                        ],
                    ])
                    ->add('kote_size_right_e', TextType::class, [
                        'required' => false,
                        'constraints' => [
                            new Assert\NotBlank(),
                            new Assert\Regex([
                                'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                                'message' => '数値を入力してください'])
                        ],
                    ])
                    ->add('kote_size_right_f', TextType::class, [
                        'required' => false,
                        'constraints' => [
                            new Assert\NotBlank(),
                            new Assert\Regex([
                                'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                                'message' => '数値を入力してください'])
                        ],
                    ])
                    /*
                    ->add('kote_color', 'kote_color', [
                        'required' => false,
                        'constraints' => [
                            new Assert\NotBlank(),
                        ],
                    ])
                    */

                    ->add('kote_etc', TextareaType::class, [
                        'label' => ',小手その他希望',
                        'required' => false,
                    ]);

            }

}