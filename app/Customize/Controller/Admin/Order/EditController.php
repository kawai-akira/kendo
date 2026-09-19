<?php
    /**
	 * @version EC=CUBE4.3
	 * @copyright 株式会社 翔 kakeru.co.jp
	 * @author
	 * 2026年08月06日作成
	 *
	 * app\Controller\Admin\AdminConverterController.php
     *
     *
	 * 
	 *
	 * 							   C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
	 ******************************************************/
namespace Customize\Controller\Admin\Order;

use Doctrine\Common\Collections\ArrayCollection;
#use Eccube\Controller\AbstractController;
#use Eccube\Entity\Master\CustomerStatus;
#use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
#use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\ShoppingException;
#use Eccube\Form\Type\AddCartType;
use Eccube\Form\Type\Admin\OrderType;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Service\OrderHelper;
use Eccube\Service\OrderStateMachine;
use Eccube\Service\PurchaseFlow\Processor\OrderNoProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseException;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\TaxRuleService;
#use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
#use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Customize\Form\Type\ItemOptionType;
use Eccube\Entity\Product;
use Customize\Service\OptionService;

class EditController extends \Eccube\Controller\Admin\Order\EditController
{


    /**
     * @var OrderHelper
     */
    private $orderHelper;

    /**
     * @var array()
     */

    private $ItemOptionForms;

    /** @var OptionService */
    private $OptionService;
    /**
     * EditController constructor.
     *
     * @param TaxRuleService $taxRuleService
     * @param DeviceTypeRepository $deviceTypeRepository
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @param CustomerRepository $customerRepository
     * @param SerializerInterface $serializer
     * @param DeliveryRepository $deliveryRepository
     * @param PurchaseFlow $orderPurchaseFlow
     * @param OrderRepository $orderRepository
     * @param OrderNoProcessor $orderNoProcessor
     * @param OrderItemTypeRepository $orderItemTypeRepository
     * @param OrderStatusRepository $orderStatusRepository
     * @param OrderStateMachine $orderStateMachine
     * @param OrderHelper $orderHelper
     */
    public function __construct(
        TaxRuleService $taxRuleService,
        DeviceTypeRepository $deviceTypeRepository,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        CustomerRepository $customerRepository,
        SerializerInterface $serializer,
        DeliveryRepository $deliveryRepository,
        PurchaseFlow $orderPurchaseFlow,
        OrderRepository $orderRepository,
        OrderNoProcessor $orderNoProcessor,
        OrderItemTypeRepository $orderItemTypeRepository,
        OrderStatusRepository $orderStatusRepository,
        OrderStateMachine $orderStateMachine,
        OrderHelper $orderHelper
        ,OptionService $OptionService
    ) {
        parent::__construct($taxRuleService, $deviceTypeRepository, $productRepository, $categoryRepository,$customerRepository,$serializer,$deliveryRepository,
                 $orderPurchaseFlow,$orderRepository,$orderNoProcessor,$orderItemTypeRepository,$orderStatusRepository,$orderStateMachine,$orderHelper);

        $this->orderHelper = $orderHelper;
        $this->OptionService = $OptionService;      
    }

    /**
     * 受注登録/編集画面.
     *
     * @Route("/%eccube_admin_route%/order/new", name="admin_order_new", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/order/{id}/edit", requirements={"id" = "\d+"}, name="admin_order_edit", methods={"GET", "POST"})
     * @Template("@admin/Order/edit.twig")
     */
    public function index(Request $request, RouterInterface $router, $id = null)
    {
        if (null === $id) {
            // 空のエンティティを作成.
            $TargetOrder = new Order();
            $TargetOrder->addShipping((new Shipping())->setOrder($TargetOrder));

            $preOrderId = $this->orderHelper->createPreOrderId();
            $TargetOrder->setPreOrderId($preOrderId);
        } else {
            $TargetOrder = $this->orderRepository->find($id);
            if (null === $TargetOrder) {
                throw new NotFoundHttpException();
            }
        }

        // 編集前の受注情報を保持
        $OriginOrder = clone $TargetOrder;
        $OriginItems = new ArrayCollection();
        foreach ($TargetOrder->getOrderItems() as $Item) {
            $OriginItems->add($Item);
            
        }

        $builder = $this->formFactory->createBuilder(OrderType::class, $TargetOrder);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'OriginOrder' => $OriginOrder,
                'TargetOrder' => $TargetOrder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_INDEX_INITIALIZE);

        $form = $builder->getForm();
        $optionForms = $this->setItemOptionFrom($TargetOrder->getItems());

        $form->handleRequest($request);
        $purchaseContext = new PurchaseContext($OriginOrder, $OriginOrder->getCustomer());

        foreach ($TargetOrder->getOrderItems() as $orderItem) {
            if ($orderItem->getTaxDisplayType() == null) {
                $orderItem->setTaxDisplayType($this->orderHelper->getTaxDisplayType($orderItem->getOrderItemType()));
                
            }
        }


        if ($form->isSubmitted() && $form['OrderItems']->isValid()) {
            $event = new EventArgs(
                [
                    'builder' => $builder,
                    'OriginOrder' => $OriginOrder,
                    'TargetOrder' => $TargetOrder,
                    'PurchaseContext' => $purchaseContext,
                ],
                $request
            );


            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_INDEX_PROGRESS);

            $flowResult = $this->purchaseFlow->validate($TargetOrder, $purchaseContext);

            if ($flowResult->hasWarning()) {
                foreach ($flowResult->getWarning() as $warning) {
                    $this->addWarning($warning->getMessage(), 'admin');
                }
            }

            if ($flowResult->hasError()) {
                foreach ($flowResult->getErrors() as $error) {
                    $this->addError($error->getMessage(), 'admin');
                }
            }

            
            // 登録ボタン押下
            switch ($request->get('mode')) {
                case 'register':
                    log_info('受注登録開始', [$TargetOrder->getId()]);

                    #オプションの確認
                    list($optionForms,$OpFlg)  =  $this->ValidationOrderItem($request,$optionForms);

                    if (!$flowResult->hasError() && $form->isValid()) {
                      
                        if($OpFlg){    

                            try {
                                $this->purchaseFlow->prepare($TargetOrder, $purchaseContext);
                                $this->purchaseFlow->commit($TargetOrder, $purchaseContext);
                            } catch (PurchaseException $e) {
                                $this->addError($e->getMessage(), 'admin');
                                break;
                            }

                            $OldStatus = $OriginOrder->getOrderStatus();
                            $NewStatus = $TargetOrder->getOrderStatus();

                            // ステータスが変更されている場合はステートマシンを実行.
                            if ($TargetOrder->getId() && $OldStatus->getId() != $NewStatus->getId()) {
                                // 発送済に変更された場合は, 発送日をセットする.
                                if ($NewStatus->getId() == OrderStatus::DELIVERED) {
                                    $TargetOrder->getShippings()->map(function (Shipping $Shipping) {
                                        if (!$Shipping->isShipped()) {
                                            $Shipping->setShippingDate(new \DateTime());
                                        }
                                    });
                                }
                                // ステートマシンでステータスは更新されるので, 古いステータスに戻す.
                                $TargetOrder->setOrderStatus($OldStatus);
                                try {
                                    // FormTypeでステータスの遷移チェックは行っているのでapplyのみ実行.
                                    $this->orderStateMachine->apply($TargetOrder, $NewStatus);
                                } catch (ShoppingException $e) {
                                    $this->addError($e->getMessage(), 'admin');
                                    break;
                                }
                            }

                            $this->RegistOrderItem($TargetOrder,$optionForms);

                            $this->entityManager->persist($TargetOrder);
                            $this->entityManager->flush();

                            foreach ($OriginItems as $Item) {
                                if ($TargetOrder->getOrderItems()->contains($Item) === false) {
                                    $this->entityManager->remove($Item);
                                }
                            }
                            $this->entityManager->flush();

                            // 新規登録時はMySQL対応のためflushしてから採番
                            $this->orderNoProcessor->process($TargetOrder, $purchaseContext);
                            $this->entityManager->flush();


                            // 会員の場合、購入回数、購入金額などを更新
                            if ($Customer = $TargetOrder->getCustomer()) {
                                $this->orderRepository->updateOrderSummary($Customer);
                                $this->entityManager->flush();
                            }

                            $event = new EventArgs(
                                [
                                    'form' => $form,
                                    'OriginOrder' => $OriginOrder,
                                    'TargetOrder' => $TargetOrder,
                                    'Customer' => $Customer,
                                ],
                                $request
                            );
                            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_INDEX_COMPLETE);


                            $this->addSuccess('admin.common.save_complete', 'admin');

                            log_info('受注登録完了', [$TargetOrder->getId()]);

                            if ($returnLink = $form->get('return_link')->getData()) {
                                try {
                                    // $returnLinkはpathの形式で渡される. pathが存在するかをルータでチェックする.
                                    $pattern = '/^'.preg_quote($request->getBasePath(), '/').'/';
                                    $returnLink = preg_replace($pattern, '', $returnLink);
                                    $result = $router->match($returnLink);
                                    // パラメータのみ抽出
                                    $params = array_filter($result, function ($key) {
                                        return 0 !== \strpos($key, '_');
                                    }, ARRAY_FILTER_USE_KEY);

                                    // pathからurlを再構築してリダイレクト.
                                    return $this->redirectToRoute($result['_route'], $params);
                                } catch (\Exception $e) {
                                    // マッチしない場合はログ出力してスキップ.
                                    log_warning('URLの形式が不正です。');
                                }
                            }

                            return $this->redirectToRoute('admin_order_edit', ['id' => $TargetOrder->getId()]);
                        }
                    }

                    break;
                default:
                    break;
            }
        }

        // 会員検索フォーム
        $builder = $this->formFactory
            ->createBuilder(SearchCustomerType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'OriginOrder' => $OriginOrder,
                'TargetOrder' => $TargetOrder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_INITIALIZE);

        $searchCustomerModalForm = $builder->getForm();

        // 商品検索フォーム
        $builder = $this->formFactory
            ->createBuilder(SearchProductType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'OriginOrder' => $OriginOrder,
                'TargetOrder' => $TargetOrder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_INITIALIZE);

        $searchProductModalForm = $builder->getForm();

        // 配送業者のお届け時間
        $times = [];
        $deliveries = $this->deliveryRepository->findAll();
        foreach ($deliveries as $Delivery) {
            $deliveryTimes = $Delivery->getDeliveryTimes();
            foreach ($deliveryTimes as $DeliveryTime) {
                $times[$Delivery->getId()][$DeliveryTime->getId()] = $DeliveryTime->getDeliveryTime();
            }
        }




        return [
            'form' => $form->createView(),
            'searchCustomerModalForm' => $searchCustomerModalForm->createView(),
            'searchProductModalForm' => $searchProductModalForm->createView(),
            'Order' => $TargetOrder,
            'id' => $id,
            'shippingDeliveryTimes' => $this->serializer->serialize($times, 'json'),
            'OptionForms' => $this->OPcreateView($optionForms),
        ];
    }

    /**
     * @param array<OrderItem> $OrderItems
     * @return array $OptionForms
     */
    private function setItemOptionFrom($OrderItems){

            $OptionForms = [];
            foreach ($OrderItems as $OrderItem){        
                if(1 != $OrderItem->getOrderItemType()->getId()){continue;}    
                $id =   $OrderItem->getId();
                $builder = $this->formFactory->createNamedBuilder('OrdeOption_'.$id,ItemOptionType::class,null,['product'=>$OrderItem->getProduct(),'itemOptions'=>$OrderItem->getOptions()]);
                $OptionForms[$id] = $builder->getForm();
                //$OptionForms[$id] = $OptionForm ->createView();
                }


            return $OptionForms;     

   }

   /**
    * Undocumented function
    * @param Request $request
    * @param array $OrderItems
    * @return $reFlg
    */
   private function ValidationOrderItem(Request $request ,$optionForms ){
            $reFlg = true;
            foreach ($optionForms as $id => $optionForm){
            
                $optionForm->handleRequest($request);
                $optionForms[$id] = $optionForm;
                

                if ($optionForm->isSubmitted() && $optionForm->isValid()) {
                }else{
                   $reFlg = false; 
                }
            }

         

            return [$optionForms ,$reFlg];         

    }
               


    private function RegistOrderItem(Order $Order ,array $OptionForms){

            foreach ($Order->getOrderItems() as $OrderItem){
                if(!$OrderItem->isProduct()){continue;}
                if(!$OrderForm = $OptionForms[$OrderItem->getId()] ?? null){continue;}

                $Options = $this->OptionService->serializer($OrderForm->getData()) ; #//= $this->serializer->serialize(, 'json',['json_encode_options' => JSON_UNESCAPED_UNICODE]);

                $OrderItem->setOptions($Options);
    
            }
    }

    /**
     * @param array $optionForms
     * @return array $Re
     */
    private function OPcreateView($optionForms){

        
        $Re = []; 
        foreach ($optionForms as $id =>  $optionForm){
            $Re[$id] = $optionForm ->createView();    
        }

        return $Re ;            
    }
}
