<?php

/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Controller\Adminhtml\Unconverted;

use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder\CollectionFactory;
use Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder\Collection;

/**
 * Class \Magestore\Webpos\Controller\Adminhtml\Unconverted\AbstractMassAction
 */
abstract class AbstractMassAction extends \Magento\Backend\App\Action
{
    /**
     * Admin resource constant
     */
    const ADMIN_RESOURCE = 'Magestore_Webpos::unconvertedOrder';

    /**
     * @var string
     */
    protected $redirectUrl = '*/*/index';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * AbstractMassAction constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Mass action abstract
     *
     * @return $this
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Get Component Referer Url
     *
     * @return string
     */
    public function getComponentRefererUrl()
    {
        return '*/*/index';
    }

    /**
     * Mass action abstract
     *
     * @param Collection $collection
     * @return mixed
     */
    abstract protected function massAction(Collection $collection);
}
