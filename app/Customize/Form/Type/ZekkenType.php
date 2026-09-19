<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年09月03日作成
 *
 * app\Customize\Form\Type\ZekkenType.php
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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;


class ZekkenType extends AbstractType
{




    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $itemOption = $options['itemOptions'];

        $Values =  $itemOption['zekken'] ?? null ;

        $builder
                ->add('zekken_etc', TextareaType::class, [
                    'label' => 'その他希望',
                    'required' => false,
                    'data' => $Values['zekken_etc'] ?? null,
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