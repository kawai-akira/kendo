<?php
  /**
   * @version EC=CUBE4.3
   * @copyright 株式会社 翔 kakeru.co.jp
   * @author
   * 2026年09月18日作成
   *
   *　app\Customize\Form\Extension\Admin\ShippingTypeExtention.php
   *
   * 
   *
   * 
   *                               C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
   ******************************************************/

namespace Customize\Form\Extension\Admin;

use Symfony\Component\Form\AbstractTypeExtension;
use Eccube\Form\Type\Admin\ShippingType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Common\EccubeConfig;

class ShippingTypeExtention extends AbstractTypeExtension
{


    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

        public function __construct(
           EccubeConfig $eccubeConfig
        )
        {
             $this->eccubeConfig = $eccubeConfig;
        }



    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tracking_number', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_mtext_len'],
                    ]),
#                    new Assert\Regex([
#                        'pattern' => '/^[0-9a-zA-Z-]+$/u',
#                        'message' => 'form_error.graph_and_hyphen_only',
#                    ]),
                ],
            ])
            ;
    }
        /**
     * Return the class of the type being extended.
     */
    public static function getExtendedTypes(): iterable
    {
        return [ShippingType::class];
    }
}
