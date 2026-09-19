<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\Form\Type\FreeInput3Type.php
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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Entity\Product;

class FreeInput3Type extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $itemOption = $options['itemOptions'];

        $Values =  $itemOption['FreeInput3'] ?? null ;
        /** @var product */
        $Product = $options['Product'];

        $builder
            ->add('value', TextType::class, [
                'label' => $Values['name'] ?? 'フリーインプト3',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'data' => $Values['value'] ?? null,
                
            ]) 
            ->add('name', HiddenType::class, [
                'data' => $Values['name'] ?? $Product->getFreeInputName3(),
            ]);

            }

     /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'itemOptions' => null,
            'Product'     => null,
        ]);
    }


}