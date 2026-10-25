<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年10月25日作成
	 *
	 * app\Customize\Event\MasterdataSubscriber.php
     *
     *
	 * 
	 *
	 * 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	 ******************************************************/

    namespace Customize\Event;

use Eccube\Event\EventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
#use Symfony\Component\Validator\Constraints\Callback;
#use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Customize\Service\CommonService;


class MasterdataSubscriber implements EventSubscriberInterface
{

    private CommonService $CommonService;

    public function __construct(CommonService $CommonService)
    {
       $this->CommonService = $CommonService;
    }
    public static function getSubscribedEvents(): array
    {
        return [
            // コアクラスの定数、または内部文字列「admin.setting.system.masterdata.index.initialize」を指定
            \Eccube\Event\EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_INDEX_INITIALIZE => 'onMasterdataInitialize',
        ];
    }

    // ★ここの型ヒントが「EventArgs」になっているか確認してください
    public function onMasterdataInitialize(EventArgs $event)
    {
        // コントローラーから渡されている builder を取得
        $builder = $event->getArgument('builder');

        if ($builder && $builder->has('masterdata')) {

            $options = $builder->get('masterdata')->getOptions();
            $addMtbs = $this->CommonService->getYaml('AddMtbForCstomize.yaml');

            $options['choices'] = array_merge($options['choices'],$addMtbs);

            $builder->add('masterdata', ChoiceType::class, $options);
        }
    }
}