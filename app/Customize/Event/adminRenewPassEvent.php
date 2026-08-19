<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年08月13日作成
	 *
	 * app\Customize\Event\adminRenewPassEvent.php
     *
     *
	 * 
	 *
	 * 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	 ******************************************************/
namespace Customize\Event;

use Carbon\Carbon;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Eccube\Entity\Member;
use Eccube\Util\StringUtil;
use Eccube\Common\EccubeConfig;
use Customize\Service\MailService;



class AdminRenewPassEvent implements EventSubscriberInterface
{
    

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
     * Undocumented function
     *
     * @param EntityManagerInterface $em
     * @param EccubeConfig $eccubeConfig
     * @param MailService $MailService
     */
    public function __construct(EntityManagerInterface $em
            ,EccubeConfig $eccubeConfig
            ,MailService $MailService
            )
    {
        $this->em = $em;
        $this->eccubeConfig = $eccubeConfig;
        $this->MailService = $MailService;
    }

    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => 'onCheckPassport',
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event)
    {
        $passport = $event->getPassport();

        // 管理画面ログイン以外は無視
        if (!$passport->hasBadge(UserBadge::class)) {
            return;
        }

        /** @var UserBadge $badge */
        $badge = $passport->getBadge(UserBadge::class);

        // ★ login_id を取得
        $loginId = $badge->getUserIdentifier();
        log_info('adminRenewPassEventId' ,['LOGIN_ID'=>$loginId]);


        /**  @var Member */
        $Member = $badge->getUser();
        if(!$Member){
           return ;
        }

        log_info('adminRenewPassEventMId' ,['MENBER_ID'=>$Member->getId()]);

        // PASS が NULL の場合
        if (empty($Member->getPassword())) {
           if (!filter_var($loginId, FILTER_VALIDATE_EMAIL)){return ;}
            log_info('adminRenewPassEventMail' ,['LOGIN_ID'=>$loginId]);
            $Message= "";
            $expireFlg = false;
            if($expire = $Member->getResetExpire()){ // DateTime
                $now = Carbon::now();
                   
       
                if (!$now->greaterThan($expire)) {
                    $expireFlg = true;
                    $minutes = Carbon::now()->diffInMinutes($expire);
                    $Message = trans('admin_login_mail_message02',['{Minutes}'=>(int)$minutes + 1  ]);
                    
                }

            }
            
            if (false == $expireFlg){
                $Message = trans('admin_login_mail_message01');
                $Member
                        ->setResetKey($this->getUniqueResetKey())
                        ->setResetExpire(new \DateTime('+'.$this->eccubeConfig['eccube_customer_reset_expire'].' min'));
                $this->em->persist($Member);			
                $this->em->flush();
                #メールの送信
                $this->MailService->sendAdminRenuwMail($Member);
            }
            log_info('adminRenewPassEvent3' ,['message'=>$Message ]);
            // ★ ログイン画面へ戻す
            throw new CustomUserMessageAuthenticationException(
                $Message
            );
        }
    }

    /**
     * ユニークなパスワードリセットキーを返す
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



