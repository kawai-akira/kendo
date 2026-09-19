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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;


class KoteType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $itemOption = $options['itemOptions'];

        $Values =  $itemOption['kote'] ?? null ;


        $builder
            ->add('kote_size_left_d', TextType::class, [
                'required' => false,
                'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Regex([
                                'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                                'message' => '数値を入力してください'])
                        ],

                'data' => $Values['kote_size_left_d'] ?? null,
            ])
            ->add('kote_size_left_e', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Regex([
                            'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                            'message' => '数値を入力してください'])
                    ],
                    'data' => $Values['kote_size_left_e'] ?? null,
                ])
            ->add('kote_size_left_f', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                        'message' => '数値を入力してください'])
                ],
                'data' => $Values['kote_size_left_f'] ?? null,
                ])
            ->add('kote_size_right_d', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                        'message' => '数値を入力してください'])
                ],
                'data' => $Values['kote_size_right_d'] ?? null,
            ])
            ->add('kote_size_right_e', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                        'message' => '数値を入力してください'])
                ],
                'data' => $Values['kote_size_right_e'] ?? null,
            ])
            ->add('kote_size_right_f', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                        'message' => '数値を入力してください'])
                ],
                'data' => $Values['kote_size_right_f'] ?? null,
            ])
            ->add('kote_etc', TextareaType::class, [
                'label' => ',小手その他希望',
                'required' => false,
                    'data' => $Values['kote_etc'] ?? null,
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