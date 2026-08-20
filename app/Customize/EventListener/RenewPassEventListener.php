<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年08月13日作成
	 *
	 *app\Customize\EventListener\RenewPassEventListener.php
     *
     *
	 * 
	 *
	 * 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	 ******************************************************/
    namespace Customize\EventListener;

    use Symfony\Component\Security\Http\Event\CheckPassportEvent;
    use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
    use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    use Carbon\Carbon;
    use Doctrine\ORM\EntityManagerInterface;
    use Eccube\Entity\Customer;
    use Eccube\Entity\Member;
    use Eccube\Util\StringUtil;
    use Eccube\Common\EccubeConfig;
    use Customize\Service\MailService;   


    class RenewPassEventListener{


    /**
     * @var EntityManagerInterface $em
     */
    protected $em;
    /**
     * @var eccubeConfig $eccubeConfig
     */

    private $eccubeConfig;
    /**
     * @var MailService $MailService
     */
    private $MailService;

    /**
     * @var UrlGeneratorInterface
     */
    private $UrlGenerator;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $em
     * @param EccubeConfig $eccubeConfig
     * @param MailService $MailService
     * @param UrlGeneratorInterface UrlGenerator
     */
    public function __construct(
             EntityManagerInterface $em
            ,EccubeConfig $eccubeConfig
            ,MailService $MailService
            ,UrlGeneratorInterface $UrlGenerator
            )
    {
        $this->em = $em;
        $this->eccubeConfig = $eccubeConfig;
        $this->MailService = $MailService;
        $this->UrlGenerator = $UrlGenerator;
    }


    /**
     * 会員（フロント）用
     */
    public function onCustomerCheckPassport(CheckPassportEvent $event)
    {  
        $passport = $event->getPassport();  

        /** @var Customer */  
        $Customer = $passport->getUser();

        #会員を取得できなければ
        if (!$Customer instanceof Customer) {
            return ;
        }
    
        /** @var Customer */  
        #パスワードが空でなければ
        if(!empty($Customer->getPassword())){
            return ;
        }

        /** @var Customer */  
        /** @var UserBadge $badge */
        $badge = $passport->getBadge(UserBadge::class);
        $loginId = $badge->getUserIdentifier();

        $expire = $Customer->getResetExpire();

        #パスワード変更開始
        log_info('CustomerRenewPassEventListener' ,['LoginId'=>$Customer->getId(),'expire'=> $expire]);

        #expire 時間内
        if($expire){ // DateTime
            $now = Carbon::now();
                
            if (!$now->greaterThan($expire)) {
                $minutes = Carbon::now()->diffInMinutes($expire);
                // ★ ログイン画面へ戻る
                throw new CustomUserMessageAuthenticationException(
                   trans('customer_login_mail_message02')
                );
                return ;
            }

        }
        #パスワーと変更処理
        $Customer->setResetKey($this->em->getRepository(Customer::class)->getUniqueResetKey())
                 ->setResetExpire(new \DateTime('+'.$this->eccubeConfig['eccube_customer_reset_expire'].' min'));
        $this->em->persist($Customer);			
        $this->em->flush();
        #メールの送信
        $resetUrl = $this->UrlGenerator->generate(
                'forgot_reset', 
                ['reset_key' => $Customer->getResetKey()], 
                UrlGeneratorInterface::ABSOLUTE_URL // メール用なので絶対URLにする
            );
        
        $this->MailService->sendPasswordResetNotificationMail($Customer,$resetUrl);
       
        // ★ ログイン画面へ戻す
            throw new CustomUserMessageAuthenticationException(
                trans('customer_login_mail_message01')
            );


    }

    /**
     * 管理者（管理画面）用
     */
    public function onAdminCheckPassport(CheckPassportEvent $event)
    {
        $passport = $event->getPassport();  

        /** @var Member */  
        $Member = $passport->getUser();

        #メンバーが取得できなければ
        if (!$Member instanceof Member) {
            return ;
        }  

        #パスワードが空でなければ
        if(!empty($Member->getPassword())){
            return ;
        }

        /** @var UserBadge $badge */
        $badge = $passport->getBadge(UserBadge::class);
        $loginId = $badge->getUserIdentifier();

        #ログインIDが　メールでなければ
        if (!filter_var($loginId, FILTER_VALIDATE_EMAIL)){return ;}

        $expire = $Member->getResetExpire();

        #パスワード変更開始
        log_info('MemberRenewPassEventListener' ,['LoginId'=>$Member->getId(),'expire'=> $expire]);
         
        #expire 時間内
        if($expire){ // DateTime
            $now = Carbon::now();
                
            if (!$now->greaterThan($expire)) {
                $minutes = Carbon::now()->diffInMinutes($expire);
                // ★ ログイン画面へ戻る
                throw new CustomUserMessageAuthenticationException(
                   trans('admin_login_mail_message02',['{Minutes}'=>(int)$minutes + 1  ])
                );
                return ;
            }

        }
        #パスワーと変更処理
        $Member->setResetKey($this->getUniqueResetKey())
               ->setResetExpire(new \DateTime('+'.$this->eccubeConfig['eccube_customer_reset_expire'].' min'));
        $this->em->persist($Member);			
        $this->em->flush();
        #メールの送信
        $this->MailService->sendAdminRenuwMail($Member);
       
        // ★ ログイン画面へ戻す
            throw new CustomUserMessageAuthenticationException(
                trans('admin_login_mail_message01')
            );
        
    }

    /**
     * ユニークなパスワードリセットキーを返す ADMIN 
     *
     * @return string
     */
    public function getUniqueResetKey()
    {
        do {
            $key = StringUtil::random(32);
            $Member = $this->em->getRepository(Member::class)->findOneBy(['reset_key' => $key]);
        } while ($Member);

        return $key;
    }



    }