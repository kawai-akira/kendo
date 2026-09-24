<?php
/**
 * @version EC=CUBE4.3系
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年09月23日作成
 *
 * app\Customize\Service\CouponService.php
 *
 *
 * クーポンサービス
 *
 * 
 * 　     
 *                          C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/
namespace Customize\Service;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Customer;
use Customize\Repository\CustomerRepository;
//use Doctrine\ORM\EntityManagerInterface;
use Plugin\Coupon42\Entity\Coupon;
use Plugin\MailMagazine42\Entity\MailMagazineSendHistory;
use Plugin\Coupon42\Service\CouponService as plCouponService;
use Eccube\Common\EccubeConfig;
use Customize\Service\MailService;

class CouponService{

    /**
     * @var EntityManagerInterface;
     */
    private $em;

    /**
     * @var CustomerRepository
     */
    Public $CustomerRepository;

    /**
     * @var plCouponService
     */
    public $plCouponService;

    /**
     * @var EccubeConfig
     */
     private $EccubeConfig;
 
    /**
     * @var MailService
     */ 
    private $MailService;

    public function __construct(
         EntityManagerInterface $EntityManager   
        ,CustomerRepository $CustomerRepository
        ,plCouponService $plCouponService
        ,EccubeConfig $EccubeConfig
        ,MailService $MailService
        )
    {
        $this->em = $EntityManager;
        $this->CustomerRepository = $CustomerRepository;
        $this->plCouponService =$plCouponService;
        $this->EccubeConfig =$EccubeConfig;
        $this->MailService = $MailService; 
        }   

    public function BirthdayCoupon(){
        
        $startDate   = Carbon::now();
        $TargetMonth = Carbon::now()->startOfMonth()->addMonth();
        $EndDay    = $TargetMonth->copy()->endOfMonth();

        $Cuatomers = $this->CustomerRepository->findByBirth($TargetMonth->format('m'));

        log_info('バースデイクーポン作成',['ターゲット月' => $TargetMonth, '誕生日会員件数' =>count($Cuatomers) ]);

        $errorCount =0 ;
        $completeCount = 0;
        $searchDatas = [];
        $subject = null;
        $body    = null;

        foreach ($Cuatomers as $Customer){
          

            $couponName ='バースデイ クーポン'.  $TargetMonth->format('Y/m').'分 '. $Customer->getName01() . ' ' . $Customer->getName02() ."({$Customer->getId()})" ;
            $couponCd = $this->plCouponService->generateCouponCd();

            $Coupon = new Coupon();
            $Coupon->setCouponCd($couponCd)
                   ->setCouponType(3) #全商品
                   ->setCouponName($couponName)
                   ->setCouponMember(false) #会員のみ
                   ->setDiscountType(2) #値引き
                   ->setCouponUseTime(1)
                   ->setDiscountPrice(null)
                   ->setDiscountRate($this->EccubeConfig['coupon_birthday_discountRate'])
                   ->setEnableFlag(1)
                   ->setAvailableFromDate($TargetMonth)
                   ->setAvailableToDate($EndDay)
                   ->setVisible(1)
                   ->setCouponMember(0)
                   ->setCouponLowerLimit(null)
                   ->setCouponRelease(1) #発行枚数
                   ->setShop(null);

            $this->em->persist($Coupon);
            $this->em->flush();

            
            list($subject,$body) = $this->MailService->sendBirthdayMail($Customer,$Coupon);
            if(is_null($subject)){
                $errorCount ++;
                $Status = 'error';

            }else{
               $subject =  $subject;
               $body    =  $body;
               $Status  = 'Success';
               $completeCount ++;

            };
            $searchDatas[] =['customer'=>$Customer->getId(),'email'=>$Customer->getEmail(),'birthday'=>$Customer->getBirth()->format('Y/m/d'),'CouponId' =>$Coupon->getId()];
            log_info('バースデイクーポン詳細',['会員ID' => $Customer->getId(),'email'=>$Customer->getEmail(), 'CouponId' =>$Coupon->getId(),'status'=>$Status ]);
 
        }

            $searchData =  base64_encode(serialize(['BirthdayCouponMailmage'=>$searchDatas]));
            $History = new MailMagazineSendHistory();
            $History->setSubject($subject)
                ->setBody($body)
                ->setHtmlBody(null)
                ->setSendCount(Count($Cuatomers))
                ->setCompleteCount($completeCount)
                ->setErrorCount($errorCount)
                ->setStartDate($startDate)
                ->setEndDate(Carbon::now())
                ->setSearchData($searchData);
            
            $this->em->persist($History);
            $this->em->flush();
              
        log_info('バースデイクーポン作成 完了');
    }
}




