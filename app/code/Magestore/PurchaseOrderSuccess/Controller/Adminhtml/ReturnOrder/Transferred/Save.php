<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\Transferred;

use Magento\Framework\Message\MessageInterface;

/**
 * Controller transfer returnorder
 *
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class Save extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\ReturnOrder\AbstractAction
{
    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\Transferred\TransferredService
     */
    protected $transferredService;
    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\MultiSourceInventory\SourceManagementInterface
     */
    protected $sourceManagement;
    /**
     * @var array
     */
    protected $transferred_items = [];

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $this->initVariable();

        $params = $this->getRequest()->getParams();
        if ($this->localeDate->date()->format('Y-m-d') != $params['transferred_at']) {
            $filterValues = ['transferred_at' => $this->dateFilter];
            $inputFilter = new \Zend_Filter_Input(
                $filterValues,
                [],
                $params
            );
            $params = $inputFilter->getUnescaped();
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$params['return_id']) {
            $this->messageManager->addErrorMessage(__('Please select a return order to transfer received product'));
            return $resultRedirect->setPath('*/returnOrder/');
        }
        if (!isset($params['dynamic_grid'])) {
            $this->messageManager->addErrorMessage(__('Please transfer at least one product.'));
            return $resultRedirect->setPath('*/returnOrder/view', ['id' => $params['return_id']]);
        }
        $transferredData = $this->transferredService->processTransferredData($params['dynamic_grid']);

        if (!empty($transferredData)) {
            $dataError = $this->modifyTransferredData($transferredData, $params);
            if (count($dataError['gt_available_qty'])) {
                $arrProductName = implode(', ', $dataError['gt_available_qty']);
                $this->messageManager->addErrorMessage(
                    __('Quantity transferred must be less or equal than available quantity.')
                );
                $this->messageManager->addErrorMessage(
                    __('Have ' . count($dataError['gt_available_qty']) . ' products are not valid: ' . $arrProductName)
                );
                return $resultRedirect->setPath('*/returnOrder/view', ['id' => $params['return_id']]);
            }
            if (count($dataError['gt_warehouse_qty'])) {
                $arrProductName = implode(', ', $dataError['gt_warehouse_qty']);
                $this->messageManager->addErrorMessage(
                    __('Quantity transferred must be less or equal than quantity on source.')
                );
                $this->messageManager->addErrorMessage(
                    __('Have ' . count($dataError['gt_warehouse_qty']) . ' products are not valid: ' . $arrProductName)
                );
                return $resultRedirect->setPath('*/returnOrder/view', ['id' => $params['return_id']]);
            }
        }

        if (empty($transferredData)) {
            $this->messageManager->addErrorMessage(__('Please transfer at least one product qty.'));
        } else {
            try {
                $user = $this->_auth->getUser();
                $transferStockItemData = $this->returnService->transferProducts(
                    $transferredData,
                    $params,
                    $user->getUserName()
                );

                if (!empty($transferStockItemData)) {
                    if (isset($params['is_decrease_stock']) && $params['is_decrease_stock'] == 1) {
                        $this->transferredService->subtractStockFromCatalog($this->transferred_items, $params);
                    }
                }
                $this->messageManager->addSuccessMessage(__('Transfer product(s) successfully.'));
                return $resultRedirect->setPath('*/returnOrder/view', ['id' => $params['return_id']]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/returnOrder/view', ['id' => $params['return_id']]);
    }

    /**
     * Init Variable
     */
    private function initVariable()
    {
        $this->transferredService = $this->_objectManager->get(
            \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\Transferred\TransferredService::class
        );
        $this->sourceManagement = $this->_objectManager->get(
            \Magestore\PurchaseOrderSuccess\Api\MultiSourceInventory\SourceManagementInterface::class
        );
    }

    /**
     * Modify Transferred Data
     *
     * @param array $transferredData
     * @param array $params
     * @return array[]
     */
    public function modifyTransferredData($transferredData, $params)
    {
        $dataError = [
            'gt_available_qty' => [],
            'gt_warehouse_qty' => []
        ];
        if (count($transferredData)) {
            foreach ($transferredData as $item) {
                if ((int)$item['available_qty'] < (int)$item['transferred_qty']) {
                    $dataError['gt_available_qty'][] = $item['product_name'];
                    continue;
                }

                if (isset($params['is_decrease_stock']) && $params['is_decrease_stock'] == 1) {
                    $totalQtyInSource = $this->itemService->getTotalQtyProductInSource(
                        $params['warehouse_id'],
                        $item['product_sku']
                    );
                    if ($totalQtyInSource < $item['transferred_qty']) {
                        $dataError['gt_warehouse_qty'][] = $item['product_name'];
                        continue;
                    }
                }

                $this->transferred_items[$item['product_sku']] = $item['transferred_qty'];
            }
        }
        return $dataError;
    }
}
