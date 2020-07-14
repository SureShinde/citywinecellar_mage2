<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Pricinglist;

use Magento\Backend\App\Action\Context;
use Magestore\SupplierSuccess\Api\SupplierPricingListRepositoryInterface as SupplierPricingListRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface;

class InlineEdit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::supplier_pricinglist_edit';

    /**
     * @var SupplierPricingListRepositoryInterface
     */
    protected $supplierPricingListRepositoryInterface;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param BlockRepository $blockRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        SupplierPricingListRepositoryInterface $supplierPricingListRepositoryInterface,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->supplierPricingListRepositoryInterface = $supplierPricingListRepositoryInterface;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);

            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $pricingListId) {
                    /** @var \Magestore\SupplierSuccess\Model\Supplier\PricingList $pricingList */
                    $pricingList = $this->supplierPricingListRepositoryInterface->getById($pricingListId);
                    try {
                        $data = $postItems[$pricingListId];
                        if (!isset($data['start_date'])
                            || !isset($data['end_date'])
                            || $data['start_date'] == 'Invalid date'
                            || $data['end_date'] == 'Invalid date') {
                            $supplierPricinglistId = $data['supplier_pricinglist_id'];
                            /** @var \Magestore\SupplierSuccess\Model\Supplier\PricingList $supplierPricingList */
                            $supplierPricingList = $this->supplierPricingListRepositoryInterface->getById($supplierPricinglistId);
                            if (!isset($data['start_date']) || $data['start_date'] == 'Invalid date') {
                                $postItems[$pricingListId]['start_date'] = $supplierPricingList->getStartDate();
                            }
                            if (!isset($data['end_date']) || $data['end_date'] == 'Invalid date') {
                                $postItems[$pricingListId]['end_date'] = $supplierPricingList->getEndDate();
                            }
                        }
                        $pricingList->setData(array_merge($pricingList->getData(), $postItems[$pricingListId]));
                        $this->supplierPricingListRepositoryInterface->save($pricingList);
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithBlockId(
                            $pricingList,
                            __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * @param SupplierPricingListInterface $pricingListInterface
     * @param $errorText
     * @return string
     */
    public function getErrorWithBlockId(SupplierPricingListInterface $pricingListInterface, $errorText)
    {
        return '[Pricelist ID: ' . $pricingListInterface->getId() . '] ' . $errorText;
    }
}
