<?php

/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Controller\Adminhtml\Unconverted;

use Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder\Collection;

/**
 * Class \Magestore\Webpos\Controller\Adminhtml\Unconverted\MassConvert
 */
class MassConvert extends AbstractMassAction
{
    /**
     * @var \Magestore\Webpos\Api\Checkout\PosOrderRepositoryInterface
     */
    protected $posOrderRepository;

    /**
     * MassConvert constructor.
     *
     * @param \Magestore\Webpos\Api\Checkout\PosOrderRepositoryInterface $posOrderRepository
     * @param \Magento\Backend\App\Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magestore\Webpos\Api\Checkout\PosOrderRepositoryInterface $posOrderRepository,
        \Magento\Backend\App\Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->posOrderRepository = $posOrderRepository;
        parent::__construct($context, $filter, $collectionFactory);
    }

    /**
     * Mass action
     *
     * @param Collection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\ResultInterface|mixed
     */
    protected function massAction(Collection $collection)
    {
        $orderConverted = 0;
        foreach ($collection as $model) {
            try {
                $this->posOrderRepository->processConvertOrder($model->getIncrementId());
                $orderConverted++;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath($this->getComponentRefererUrl());
                return $resultRedirect;
            }
        }
        if ($orderConverted) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were converted.', $orderConverted));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
