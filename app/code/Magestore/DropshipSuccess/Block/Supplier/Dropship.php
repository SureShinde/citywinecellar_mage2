<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Supplier;

/**
 * Sales order history block
 */
class Dropship extends AbstractSupplier
{
    /**
     * @var string
     */
    protected $_template = 'supplier/dropship.phtml';

    /**
     * @var
     */
    protected $dropships;

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getDropships()
    {
        if (!($supplierId = $this->supplierSession->getSupplierId())) {
            return false;
        }
        if (!$this->dropships) {
            /** @var \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Collection $dropshipCollection */
            $this->dropships = $this->dropshipRequestService->getDropshipRequestBySupplierId($supplierId);
        }
        return $this->dropships;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getDropships()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'supplier.dropship.pager'
            )->setCollection(
                $this->getDropships()
            );
            $this->setChild('pager', $pager);
            $this->getDropships()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param $dropship
     * @return string
     */
    public function getViewUrl($dropship)
    {
        return $this->getUrl('dropship/dropshipRequest/viewDropship', ['dropship_id' => $dropship->getId()]);
    }

    /**
     * @return array
     */
    public function getDropshipStatus()
    {
        return $this->dropshipRequestInterface->getStatusOption();
    }
}
