<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年08月13日作成
	 *
	 * app\Customize\Controller\Admin\AdminController.php
     *
     *
	 * 
	 *
	 * 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	 ******************************************************/

namespace Customize\Controller\Admin;

use Carbon\Carbon;
#use Doctrine\Common\Collections\Criteria;
#use Doctrine\ORM\NoResultException;
#use Doctrine\ORM\Query\ResultSetMapping;
#use Eccube\Controller\AbstractController;
#use Eccube\Entity\Master\CustomerStatus;
use Eccube\Entity\Master\OrderStatus;
#use Eccube\Entity\Master\ProductStatus;
#use Eccube\Entity\ProductStock;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
#use Eccube\Exception\PluginApiException;
use Eccube\Form\Type\Admin\ChangePasswordType;
use Eccube\Form\Type\Admin\LoginType;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\MemberRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Service\PluginApiService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends \Eccube\Controller\Admin\AdminController
{


    /**
     * @var array 売り上げ状況用受注状況
     */
    private $excludes = [OrderStatus::CANCEL, OrderStatus::PENDING, OrderStatus::PROCESSING, OrderStatus::RETURNED];

    /**
     * AdminController constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param AuthenticationUtils $helper
     * @param MemberRepository $memberRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @param OrderRepository $orderRepository
     * @param OrderStatusRepository $orderStatusRepository
     * @param CustomerRepository $custmerRepository
     * @param ProductRepository $productRepository
     * @param PluginApiService $pluginApiService
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        AuthenticationUtils $helper,
        MemberRepository $memberRepository,
        UserPasswordHasherInterface $passwordHasher,
        OrderRepository $orderRepository,
        OrderStatusRepository $orderStatusRepository,
        CustomerRepository $custmerRepository,
        ProductRepository $productRepository,
        PluginApiService $pluginApiService
    ) {
        parent::__construct($authorizationChecker,$helper,$memberRepository,$passwordHasher,$orderRepository,
        $orderStatusRepository,$custmerRepository,$productRepository,$pluginApiService);
    }

    /**
     * @Route("/%eccube_admin_route%/login", name="admin_login", methods={"GET", "POST"})
     * @Template("@admin/login.twig")
     */
    public function login(Request $request)
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_homepage');
        }

        /** @var \Symfony\Component\Form\FormInterface $form */
        $builder = $this->formFactory->createNamedBuilder('', LoginType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ADMIM_LOGIN_INITIALIZE);

        $form = $builder->getForm();
        return [
            'error' => $this->helper->getLastAuthenticationError(),
            'form' => $form->createView(),
        ];
    }

    /**
     * パスワード変更画面
     *
     * @Route("/%eccube_admin_route%/change_password", name="admin_change_password", methods={"GET", "POST"})
     * @Template("@admin/change_password.twig")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function changePassword(Request $request)
    {
        $builder = $this->formFactory
            ->createBuilder(ChangePasswordType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ADMIM_CHANGE_PASSWORD_INITIALIZE);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Member = $this->getUser();
            $salt = $Member->getSalt();
            $password = $form->get('change_password')->getData();
            $password = $this->passwordHasher->hashPassword($Member, $password);

            $Member
                ->setPassword($password)
                ->setSalt($salt);

            $this->memberRepository->save($Member);

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Member' => $Member,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ADMIN_CHANGE_PASSWORD_COMPLETE);

            $this->addSuccess('admin.change_password.password_changed', 'admin');

            return $this->redirectToRoute('admin_change_password');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    
}
