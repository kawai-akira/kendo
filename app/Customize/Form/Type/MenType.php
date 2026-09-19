<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\Form\Type\MenType.php
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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;




class MenType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $itemOption = $options['itemOptions'];

        $Values =  $itemOption['men'] ?? null ;

        $builder
            ->add('men_size_a', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                        'message' => '数値を入力してください'])
                ],
                'data' => $Values['men_size_a'] ?? null,
            ])
            ->add('men_size_b', TextType::class, [
               'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                        'message' => '数値を入力してください'])
                ],
                'data' => $Values['men_size_b'] ?? null,
            ])
            ->add('men_size_c', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.[0-9]+)?$/',
                        'message' => '数値を入力してください'])
                ],
                'data' => $Values['men_size_c'] ?? null,
            ])
           ->add('men_etc', TextareaType::class, [
                'label' => '面その他希望',
     		    'required' => false,
                'data' => $Values['men_etc'] ?? null,
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