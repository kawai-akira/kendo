<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 20260\09月23日作成
	 *
	 * app\Customize\Command\CRONCommand.php
     * 　　　　　　　　　　　　　　　　　　　　　　
     *
     * 1 誕生日クーポン処理
     * 2 クーポン終了処理
	 * 
	 *
	 * 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	 ******************************************************/
namespace Customize\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Customize\Service\CouponService;


class CRONCommand extends Command {

    /* cronで実行したい名前を指定します */
    protected static $defaultName = 'eccube:assetrouge.ColeectionFee';

    /**
     * @var CouponService;
     */
    protected $CouponService;

    /**
     * @var BimWebApiOrderService
     */
    protected $BimWebApiOrderService;

    public function __construct(
        CouponService  $CouponService
     //   ,BimWebApiOrderService $BimWebApiOrderService
     ) {
        parent::__construct();
        $this->CouponService = $CouponService;
     //   $this->BimWebApiOrderService = $BimWebApiOrderService;

    }

    protected function configure() {
     # $this->setDescription('Hoge Moge');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->CouponService->BirthdayCoupon();
      //  $this->BimWebApiOrderService->setOrder();
   

    }
}