<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\Form\Type\DouType.php
 *
 * 
 *
 *
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/

namespace Customize\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;


class DouType extends AbstractType
{



    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
            ->add('dou_size_bust', TextType::class, [
                'required' =>false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.?[0-9]+|)$/',
                        'message' => '数値を入力してください'])
                ],
            ])
            ->add('dou_size_waist', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.?[0-9]+|)$/',
                        'message' => '数値を入力してください'])
                ],
            ])
            ->add('dou_size_g', TextType::class, [
                'required' =>false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.?[0-9]+|)$/',
                        'message' => '数値を入力してください'])
                ],
            ])

            /*
            ->add('dou_color', 'dou_color', [
                'required' =>false,
                'constraints' => [
                    new Assert\NotBlank(),
                ),
            ])
            */


            ->add('dou_etc', TextareaType::class, [
                'label' => '胴のその他希望',
                'required' => false,
            ]);
            }
            





}