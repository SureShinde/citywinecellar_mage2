<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Product;

/**
 * Class Delete
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Product
 */
class Delete extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * Delete constructor.
     *
     * @param \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context
     * @param \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface $itemRepository
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface $itemRepository,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        parent::__construct($context);
        $this->itemRepository = $itemRepository;
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * Save product to purchase order
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        try{
            $this->itemRepository->deleteById($this->getRequest()->getParam('id'));
            $resultRaw->setContents($this->getRequest()->getParam('product_id'));
        }catch (\Exception $e){
            $resultRaw->setContents(0);
        }
        return $resultRaw;
    }
}