<?php
declare(strict_types=1);

namespace Tinkr\CheckoutSuccess\Controller\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\ScopeInterface;

class Success implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /** @var RequestInterface  */
    protected $request;

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /** @var CollectionFactory  */
    protected $orderCollectionFactory;

    /** @var OrderRepository  */
    protected $orderRepository;

    /** @var ManagerInterface  */
    protected $messageManager;

    /** @var Session  */
    protected $checkoutSession;

    /** @var ForwardFactory  */
    protected $forwardFactory;

    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $orderCollectionFactory
     * @param OrderRepository $orderRepository
     * @param ManagerInterface $messageManager
     * @param Session $checkoutSession
     * @param ForwardFactory $forwardFactory
     */
    public function __construct(
        PageFactory $resultPageFactory,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $orderCollectionFactory,
        OrderRepository $orderRepository,
        ManagerInterface $messageManager,
        Session $checkoutSession,
        ForwardFactory $forwardFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->forwardFactory = $forwardFactory;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $isEnabled = $this->scopeConfig->getValue(
            'checkout_success/general/enabled',
            ScopeInterface::SCOPE_STORE
        );

        if ($isEnabled) {
            //check parameter for orderId
            $orderId = $this->request->getParam('order_id');

            //if no parameter was provided check config for order_id
            if (!$orderId) {
                $orderId = $this->scopeConfig->getValue(
                    'checkout_success/general/order_id',
                    ScopeInterface::SCOPE_STORE
                );
            }

            /** @var Order $order */
            try {
                //order repository will deal automatically with bad parameter/configuration
                $order = $this->orderRepository->get($orderId);
            } catch (InputException|NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            if (isset($order) && $order->getId()) {
                //see: \Magento\Quote\Model\QuoteManagement::placeOrder
                $this->checkoutSession->setLastOrderId($order->getId());
                $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
                return $this->resultPageFactory->create();
            }
        }

        //if something failed, redirect to 404
        $resultForward = $this->forwardFactory->create();
        $resultForward->setController('index');
        $resultForward->forward('defaultNoRoute');
        return $resultForward;

    }
}

