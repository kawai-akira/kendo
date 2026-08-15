<?php

   /**
    * @version EC-CUBE4.3
    * @copyright 株式会社 翔 kakeru.co.jp
    *
    * 2026年08月13日作成
    *
    * app\Customize\Service\MailService.php
    * 
    *
    * SQL文を作成する サイトがデッキしだい削除する
    *
    *
    *                                        ≡≡≡┏(＾o＾)┛
    *****************************************************/
namespace Customize\Service;

use Doctrine\ORM\NonUniqueResultException;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
#use Eccube\Entity\MailHistory;
#use Eccube\Entity\MailTemplate;
#use Eccube\Entity\Order;
#use Eccube\Entity\OrderItem;
#use Eccube\Entity\Shipping;
#use Eccube\Event\EccubeEvents;
#use Eccube\Event\EventArgs;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\MailHistoryRepository;
use Eccube\Repository\MailTemplateRepository;
#use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
#use Twig\Error\LoaderError;
#use Twig\Error\RuntimeError;
#use Twig\Error\SyntaxError;
use Eccube\Entity\Member;

class MailService extends \Eccube\Service\MailService
{

const AdminForgotMail = 10;
    /**
     * MailService constructor.
     *
     * @param MailerInterface $mailer
     * @param MailTemplateRepository $mailTemplateRepository
     * @param MailHistoryRepository $mailHistoryRepository
     * @param BaseInfoRepository $baseInfoRepository
     * @param EventDispatcherInterface $eventDispatcher
     * @param \Twig\Environment $twig
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        MailerInterface $mailer,
        MailTemplateRepository $mailTemplateRepository,
        MailHistoryRepository $mailHistoryRepository,
        BaseInfoRepository $baseInfoRepository,
        EventDispatcherInterface $eventDispatcher,
        \Twig\Environment $twig,
        EccubeConfig $eccubeConfig,
    ) {
        parent::__construct(
            $mailer,$mailTemplateRepository,$mailHistoryRepository,$baseInfoRepository,$eventDispatcher,$twig,$eccubeConfig,

        );
    }



    /**
     * Send password reset admin mail.
     *
     * @param $Member 管理者
     * 
     */
    public function sendAdminRenuwMail(member $Member)
    {
        log_info('パスワード再発行メール送信開始');

        $MailTemplate = $this->mailTemplateRepository->find(self::AdminForgotMail);
        $body = $this->twig->render($MailTemplate->getFileName(), [
            'BaseInfo' => $this->BaseInfo,
            'Member' => $Member,
            'expire' => $this->eccubeConfig['eccube_customer_reset_expire'],
        ]);



        $message = (new Email())
            ->subject('['.$this->BaseInfo->getShopName().'] '.$MailTemplate->getMailSubject())
            ->from(new Address($this->BaseInfo->getEmail01(), $this->BaseInfo->getShopName()))
            ->to($this->convertRFCViolatingEmail($Member->getLoginId()))
            ->bcc($this->BaseInfo->getEmail01())
            ->replyTo($this->BaseInfo->getEmail03())
            ->returnPath($this->BaseInfo->getEmail04());

        // HTMLテンプレートが存在する場合
        $htmlFileName = $this->getHtmlTemplate($MailTemplate->getFileName());
        if (!is_null($htmlFileName)) {
            $htmlBody = $this->twig->render($htmlFileName, [
                'BaseInfo' => $this->BaseInfo,
                'Member' => $Member,
                'expire' => $this->eccubeConfig['eccube_customer_reset_expire'],
            ]);

            $message
                ->text($body)
                ->html($htmlBody);
        } else {
            $message->text($body);
        }


        try {
            $this->mailer->send($message);
            log_info('パスワード再発行メール送信完了');
        } catch (TransportExceptionInterface $e) {
            log_critical($e->getMessage());
        }
    }







}
