<?php
/**
 * @version EC=CUBE4.3
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月06日作成
 *
 * app\Customize\Form\Type\Admin\ConverterType.php
 *
 *
 * 
 * 　　
 *　
 *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * Class TimerType
 */
class ConverterType  extends AbstractType
{


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('mode',HiddenType::class,[
                        'data' => 'regist',
                    ]);
   }
 



    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'converter';
    }

   
}