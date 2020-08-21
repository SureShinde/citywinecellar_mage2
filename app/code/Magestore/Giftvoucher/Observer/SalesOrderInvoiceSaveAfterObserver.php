<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer;

/**
 * Class SalesOrderInvoiceSaveAfterObserver
 *
 * Sales Order Invoice - Save After Observer
 */
class SalesOrderInvoiceSaveAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $orderFactory;

    protected $giftvoucherFactory;

    protected $helperData;

    protected $messageManager;

    /**
     * SalesOrderInvoiceSaveAfterObserver constructor.
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucher
     * @param \Magestore\Giftvoucher\Helper\Data $helperData
     * @param \Magento\Framework\Message\ManagerInterface $message
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucher,
        \Magestore\Giftvoucher\Helper\Data $helperData,
        \Magento\Framework\Message\ManagerInterface $message
    ) {
        $this->orderFactory = $orderFactory;
        $this->giftvoucherFactory = $giftvoucher;
        $this->helperData = $helperData;
        $this->messageManager = $message;
    }

    /**
     * Process Gift Card data after invoice is saved
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer) // phpcs:disable Generic.Metrics.NestingLevel
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        $order = $this->orderFactory->create()->load($order->getId());

        foreach ($invoice->getAllItems() as $itemCredit) {
            $item = $order->getItemById($itemCredit->getOrderItemId());
            if (isset($item) && $item != null) {
                if ($item->getProductType() != 'giftvoucher') {
                    continue;
                }

                if ($item->getQuoteItemId()) {
                    $giftVouchers = $this->giftvoucherFactory->create()->getCollection()
                        ->addItemFilter($item->getQuoteItemId());
                } else {
                    $giftVouchers = $this->giftvoucherFactory->create()->getCollection()
                        ->addItemFilter($item->getId(), true);
                }
                $itemQtyInvoice = $itemCredit->getQty();
                foreach ($giftVouchers as $giftVoucher) {
                    if ($giftVoucher->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_PENDING) {
                        $giftVoucher->addData([
                            'status' => \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE,
                            'comments' => __('Active when order is complete'),
                            'amount' => $giftVoucher->getBalance(),
                            'action' => \Magestore\Giftvoucher\Model\Actions::ACTIONS_UPDATE,
                            'order_increment_id'    => $order->getIncrementId()
                        ])->setIncludeHistory(true);
                        try {
                            if ($giftVoucher->getDayToSend() && strtotime($giftVoucher->getDayToSend()) > time()
                            ) {
                                $giftVoucher->setData('dont_send_email_to_recipient', 1);
                            }
                            $giftVoucher->save();
                            if ($this->helperData->getEmailConfig('enable', $order->getStoreId())) {
                                $giftVoucher->setIncludeHistory(false);
                                if ($giftVoucher->getRecipientEmail()) {
                                    if ($giftVoucher->sendEmailToRecipient() && $giftVoucher->getNotifySuccess()) {
                                        $giftVoucher->sendEmailSuccess();
                                    }
                                } else {
                                    $giftVoucher->sendEmail();
                                }
                            }
                        } catch (\Exception $e) {
                            $this->messageManager->addErrorMessage($e->getMessage());
                        }
                        $itemQtyInvoice--;
                        if (!$itemQtyInvoice) {
                            break;
                        }
                    }
                }
            }
        }
    }
}
