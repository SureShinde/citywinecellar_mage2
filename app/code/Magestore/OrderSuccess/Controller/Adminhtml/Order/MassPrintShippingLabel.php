<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class MassPrintShippingLabel
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Order
 */
class MassPrintShippingLabel extends \Magento\Shipping\Controller\Adminhtml\Order\Shipment\MassPrintShippingLabel
{

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        $labelsContent = [];
        $shipments = $this->shipmentCollectionFactory->create()->setOrderFilter(['in' => $collection->getAllIds()]);

        if ($shipments->getSize()) {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            foreach ($shipments as $shipment) {
                $labelContent = $shipment->getShippingLabel();
                if ($labelContent) {
                    $labelsContent[] = $labelContent;
                }
            }
        }

        if (!empty($labelsContent)) {
            $outputPdf = $this->labelGenerator->combineLabelsPdf($labelsContent);
            return $this->fileFactory->create(
                'ShippingLabels.pdf',
                $outputPdf->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }

        $this->messageManager
            ->addWarningMessage(__('There are no shipping labels related to selected orders.'));
        $this->_redirect($this->getComponentRefererUrl());
    }
}
