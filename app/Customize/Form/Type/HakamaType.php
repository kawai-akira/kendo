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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;


class HakamaType extends AbstractType
{




    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
                ->add('hakama_size_waist', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Regex([
                            'pattern' => '/^[0-9]+(\.?[0-9]+|)$/',
                            'message' => '数値を入力してください'])
                    ],
                ])
                ->add('hakama_size_length', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Regex([
                            'pattern' => '/^[0-9]+(\.?[0-9]+|)$/',
                            'message' => '数値を入力してください'])
                    ],
                ])

                ->add('hakama_etc', TextareaType::class, [
                    'label' => 'その他希望',
                    'required' => false,
                ]);
        }
           

}