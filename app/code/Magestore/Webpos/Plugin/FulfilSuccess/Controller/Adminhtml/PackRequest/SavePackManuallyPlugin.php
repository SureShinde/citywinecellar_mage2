<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Plugin\FulfilSuccess\Controller\Adminhtml\PackRequest;


class SavePackManuallyPlugin
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * SavePackManuallyPlugin constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ){
        $this->objectManager = $objectManager;
    }

    /**
     *  trigger re sync order
     *
     * @param \Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest\SavePackManually $subject
     * @param @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect $result
     * @return boolean
     */
    public function afterExecute(
        \Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest\SavePackManually $subject,
        $result
    ) {
        $data = $subject->getRequest()->getParams();
        if (empty($data['order_id'])) {
            return $result;
        }

        try {
            /** @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository */
            $orderRepository = $this->objectManager->create('Magento\Sales\Api\OrderRepositoryInterface');
            /** @var \Magento\Sales\Api\Data\OrderInterface $order */
            $order = $orderRepository->get($data['order_id']);
            $order->setUpdatedAt(null);
            $orderRepository->save($order);
        } catch (\Exception $exception) {

        }
        return $result;
    }
}