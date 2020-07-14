<?php

namespace Magestore\Webpos\Model\Integration;

class GiftcardRepository implements \Magestore\Webpos\Api\Integration\GiftcardRepositoryInterface {
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Sales\Api\OrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    protected $refundOrderItemService;

    /**
     * GiftcardRepository constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
    ) {
        $this->objectManager = $objectManager;
        $this->orderItemRepository = $orderItemRepository;
        $this->refundOrderItemService = $objectManager->get('Magestore\Giftvoucher\Api\Sales\RefundOrderItemServiceInterface');
    }

    /**
     * @inheritdoc
     */
    public function checkGiftCardRefund($listGiftCardRefund) {
        $canRefund = 1;
        foreach ($listGiftCardRefund as $giftcardRefundRequest) {
            $orderItemId = $this->orderItemRepository->get($giftcardRefundRequest->getOrderItemId());
            $maximumQtyRefund = $this->refundOrderItemService->getGiftCardQtyToRefund($orderItemId);
            if($maximumQtyRefund < $giftcardRefundRequest->getQty()) {
                $canRefund = 0;
                break;
            }
        }
        return $canRefund;
    }
}