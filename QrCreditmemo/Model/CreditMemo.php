<?php

namespace ThemeFactory\QrCreditmemo\Model;

use ThemeFactory\QrCreditmemo\Api\CreditMemoInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory;

class CreditMemo implements CreditMemoInterface
{
    /**
     * @var \Magento\Sales\Model\Order\CreditmemoRepository
     */
    protected $creditMemoRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    protected $orderFactory;
    protected $creditmemoCollectionFactory;

    public function __construct(
        \Magento\Sales\Model\Order\CreditmemoRepository $creditMemoRepository,
        OrderRepositoryInterface $orderRepository,
        OrderFactory $orderFactory,
        CollectionFactory $creditmemoCollectionFactory
    ) {
        $this->creditMemoRepository = $creditMemoRepository;
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
    }

    /**
     * Get credit memo incrementId by OrderNumber
     *
     * @param string $orderNumber
     * @return array
     */
    public function getCreditMemoNumberByOrderNumber($orderNumber)
{
    $order = $this->orderFactory->create()->loadByIncrementId($orderNumber);
    if ($order && $order->getId()) {
        $order_id = $order->getId();
        $creditmemoCollection = $this->creditmemoCollectionFactory->create();
        $creditmemoCollection->addFieldToFilter('order_id', $order_id);
        $creditmemo = $creditmemoCollection->getFirstItem();
        if ($creditmemo && $creditmemo->getId()) {
            return $creditmemo->getIncrementId();
        }
    }
    return null;
}


}
