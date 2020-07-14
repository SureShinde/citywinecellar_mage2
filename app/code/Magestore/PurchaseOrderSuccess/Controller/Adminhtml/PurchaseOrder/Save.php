<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\CollectionFactory;

/**
 * Class \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Save
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::save_purchase_order';

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    /**
     * @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface
     */
    protected $supplierRepository;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService
     */
    protected $purchaseService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService
     */
    protected $itemService;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     */
    protected $dateFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;
    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Save constructor.
     *
     * @param \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context
     * @param \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository
     * @param \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseService
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService $itemService
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateFilter
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService $itemService,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        parent::__construct($context);
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->supplierRepository = $supplierRepository;
        $this->purchaseService = $purchaseService;
        $this->itemService = $itemService;
        $this->dateFilter = $dateFilter;
        $this->localeDate = $localeDate;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Quotation grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $filterValues = [];
        if ($params['purchased_at']) {
            // Magestore/pos-pro#76 : Error when saving current date
            if ($this->localeDate->date()->format('Y-m-d') != $params['purchased_at']) {
                $filterValues['purchased_at'] = $this->dateFilter;
            }
        }
        if (array_key_exists('started_at', $params) && $params['started_at']) {
            $filterValues['started_at'] = $this->dateFilter;
        }
        if (array_key_exists('expected_at', $params) && $params['expected_at']) {
            $filterValues['expected_at'] = $this->dateFilter;
        }
        $inputFilter = new \Zend_Filter_Input(
            $filterValues,
            [],
            $params
        );
        $params = $inputFilter->getUnescaped();
        $id = (isset($params['purchase_order_id']) && $params['purchase_order_id'] > 0)
            ? $params['purchase_order_id'] : null;
        $type = (isset($params['type']) && $params['type'] > 0) ? $params['type'] : null;
        if (!$type) {
            return $this->redirectGrid(1, __('This item does not exist.'));
        }
        $typeLabel = $this->getTypeLabel($type);
        if ($id) {
            try {
                $purchaseOrder = $this->purchaseOrderRepository->get($id, $typeLabel);
                if ($purchaseOrder->getType() != $type) {
                    $message = $this->purchaseOrderRepository->getNotFoundExeptionMessage($typeLabel);
                    return $this->redirectGrid($type, $message);
                }
            } catch (\Exception $e) {
                return $this->redirectGrid($type, $e->getMessage());
            }
        } else {
            $purchaseOrder = $this->_purchaseOrderFactory->create();
        }
        $purchaseOrder->addData($params)->setId($id);
        $canSendEmail = $purchaseOrder->canSendEmail();
        try {
            $purchaseOrder = $this->purchaseOrderRepository->save($purchaseOrder);
            $productsData = $this->itemService->processUpdateProductParams($params);
            if (!empty($productsData)) {
                $this->itemService->updateProductDataToPurchaseOrder($purchaseOrder, $productsData);
            } else {
                $purchaseOrderItemCollection = $this->collectionFactory->create()
                    ->addFieldToFilter('purchase_order_id', $id);
                $totalOrderQty = 0;
                foreach ($purchaseOrderItemCollection as $purchaseOrderItem) {
                    $totalOrderQty = $totalOrderQty + $purchaseOrderItem->getData('qty_orderred');
                }
                try {
                    $purchaseOrder->setTotalQtyOrderred($totalOrderQty);
                    $this->purchaseOrderRepository->save($purchaseOrder);
                } catch (\Exception $e) {
                    throw $e;
                }
            }
            $this->purchaseService->updatePurchaseTotal($purchaseOrder);
        } catch (\Exception $e) {
            return $this->redirectForm(
                $type,
                $id,
                $e->getMessage(),
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
        }
        if ($this->getRequest()->getParam('isConfirm') == 'true') {
            $resultForward = $this->_resultForwardFactory->create();
            $resultForward->setParams($this->getRequest()->getParams());
            if ($type == TYPE::TYPE_QUOTATION) {
                return $resultForward->forward('confirmQuotation');
            } elseif ($type == TYPE::TYPE_PURCHASE_ORDER) {
                return $resultForward->forward('confirm');
            }
        }
        if ($this->getRequest()->getParam('isRevert') == 'true') {
            $resultForward = $this->_resultForwardFactory->create();
            $resultForward->setParams($this->getRequest()->getParams());
            if ($type == TYPE::TYPE_QUOTATION) {
                return $resultForward->forward('revertQuotation');
            }
        }
        if ($this->getRequest()->getParam('convert') == 'true') {
            $resultForward = $this->_resultForwardFactory->create();
            $resultForward->setController('quotation');
            $resultForward->setParams($this->getRequest()->getParams());
            return $resultForward->forward('convert');
        }

        if ($canSendEmail || (isset($params['sendEmail']) && $params['sendEmail'] == 'true')) {
            $supplier = $this->supplierRepository->getById($params['supplier_id']);
            $this->_registry->register('current_purchase_order', $purchaseOrder);
            $this->_registry->register('current_purchase_order_supplier', $supplier);
            $sendSuccess = $this->purchaseService->sendEmailToSupplier($purchaseOrder, $supplier);
            if ((isset($params['sendEmail']) && $params['sendEmail'] == 'true')) {
                if ($sendSuccess) {
                    return $this->redirectForm($type, $id, __('An email has been sent to supplier'));
                } else {
                    return $this->redirectForm(
                        $type,
                        $id,
                        __('Could not send email to supplier'),
                        \Magento\Framework\Message\MessageInterface::TYPE_ERROR
                    );
                }
            }
        }
        return $this->redirectForm($type, $purchaseOrder->getPurchaseOrderId(), __('%1 has been saved.', $typeLabel));
    }
}
