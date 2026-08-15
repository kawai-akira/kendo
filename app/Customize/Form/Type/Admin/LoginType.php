<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年08月13日作成
	 *
	 * app\Customize\Form\Type\Admin\LoginType.php
     *
     *
	 * 
	 *
	 * 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	 ******************************************************/

namespace Customize\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Eccube\Session\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LoginType extends \Eccube\Form\Type\Admin\LoginType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var Session
     */
    protected $session;

    public function __construct(
        EccubeConfig $eccubeConfig,
        Session $session
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('login_id', TextType::class, [
            'attr' => [
                'maxlength' => $this->eccubeConfig['eccube_id_max_len'],
            ],
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'data' => $this->session->get('_security.last_username'),
        ]);
        $builder->add('password', PasswordType::class, [
            'attr' => [
                'maxlength' => $this->eccubeConfig['eccube_password_max_len'],
            ],
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_login';
    }
}
