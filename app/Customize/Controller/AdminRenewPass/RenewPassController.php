<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年08月14日作成
	 *
	 * app\Customize\Controller\AdminRenewPass\RenewPassController.php
     *
     *
	 * 
	 *
	 * 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	 ******************************************************/
    namespace Customize\Controller\AdminRenewPass;

    use Carbon\Carbon;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Eccube\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    use Symfony\Component\Validator\Constraints as Assert;
    use Symfony\Component\Validator\Validator\ValidatorInterface;
    use Eccube\Entity\Member;
    use Customize\Repository\MemberRepository;
    use Customize\Form\Type\Flont\RenewPassType;



class RenewPassController extends \Eccube\Controller\AbstractController
{

    const renew_session = 'admin_renew_pass_session';

 
    /**
     * @var ValidatorInterface
     */
       private $Validator;

    /**
     * @var MemberRepository $MemberRepository
     */
        private $MemberRepository;
    /**
     *  @var UserPasswordHasherInterface $passwordHasher
     */
        private $passwordHasher;




    /**
     * @param MemberRepository $MemberRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @param ValidatorInterface $Validator
     */    

    
    public function __construct(MemberRepository $MemberRepository
                    ,UserPasswordHasherInterface $passwordHasher
                    ,ValidatorInterface $Validator
    )
    {
        $this->MemberRepository = $MemberRepository;
        $this->passwordHasher = $passwordHasher;
        $this->Validator = $Validator;

    }


   /**
     * 管理者のパスワードをリセットする.
     *
     * @Route("/admin/RenewPass/{reset_key}", name="admin_renew_pass_index", methods={"GET", "POST"})
     * @Template("AdminRenewPass/index.twig")
     */
    public function index(Request $request,$reset_key)
    {


        $errors = $this->Validator->validate(
        $reset_key,
            [
                new Assert\NotBlank(),
                new Assert\Regex(
                    [
                        'pattern' => '/^[a-zA-Z0-9]+$/',
                    ]
                ),
            ]
        );

        if (count($errors) > 0) {
            // リセットキーに異常がある場合
            throw $this->createNotFoundException();
        }


        
        /** @var Member */
        $Member = $this->MemberRepository->getRenewPass($reset_key);

        if ($Member  == false){
            throw $this->createNotFoundException();
        }

        $expire = $Member->getResetExpire(); // DateTime
        $now = Carbon::now();
        $expireFlg = false;



       
        if ($now->greaterThan($expire)) {
            $expireFlg = true;
            $this->session->remove(self::renew_session);
        }
        
        $form  = $this->createForm(RenewPassType::class);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            if($form->get('login_id')->getData() == $Member->getLoginId()){

                $salt = $Member->getSalt();
                $password = $form->get('change_password')->getData();
                $password = $this->passwordHasher->hashPassword($Member, $password);

                $Member
                    ->setPassword($password)
                    ->setSalt($salt)
                    ->setResetKey(null)
                    ->setResetExpire(null);

                $this->entityManager->persist($Member);
                $this->entityManager->flush();
                $this->session->set(self::renew_session,'success');
                
                return $this->redirectToRoute('admin_renew_pass_complete');


            }

        }

        $eCount = ($this->session->get(self::renew_session) ?? 0 ) ;


        if ($eCount > $this->eccubeConfig['admin_renew_pass_count_max']){
            $Member
                ->setResetKey(null)
                ->setResetExpire(null);
            //    $this->entityManager->persist($Member);
            //    $this->entityManager->flush();

            return $this->redirectToRoute('admin_renew_pass_complete');
        }
       

        $eCount++;
        $this->session->set(self::renew_session, $eCount);

        return[
            'form' => $form->createView(),
            'expireFlg' => $expireFlg,
            'reset_key' => $reset_key,
        ];
    }

    /**
     * 管理者のパスワードをリセットする.
     *
     * @Route("/admin/RenewPass_complete", name="admin_renew_pass_complete", methods={"GET"})
     * @Template("AdminRenewPass/complete.twig")
     */
    public function complete(Request $request){
    
        $flg = $this->session->get(self::renew_session);
            
        if (is_null($flg)){
            throw $this->createNotFoundException();
        }

        $this->session->remove(self::renew_session) ;

        return[

            'flg' => $flg,
        ];



    }
}