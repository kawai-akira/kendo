<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\Form\Type\HeightType.php
 *
 * 
 *
 *　身長
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/

namespace Customize\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;


class HeightType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $itemOption = $options['itemOptions'];

        
        $Values =  $itemOption['height'] ?? null ;

        $builder
            ->add('height', TextType::class, [
                'required' =>false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+(\.?[0-9]+|)$/',
                        'message' => '数値を入力してください'])
                ],
                'data'=>$Values['height'] ?? null, 
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