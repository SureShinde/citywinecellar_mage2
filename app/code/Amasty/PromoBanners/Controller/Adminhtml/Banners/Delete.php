<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Controller\Adminhtml\Banners;

use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
use Amasty\PromoBanners\Model\ResourceModel\Rule\CollectionFactory;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        try {
            /** @var $collection \Amasty\PromoBanners\Model\ResourceModel\Rule\Collection */
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('id', ['eq' => $id]);
            $collection->walk('delete');
            $this->messageManager->addSuccessMessage(__('Banner(s) were successfully deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t delete Banner(s) right now. Please review the log and try again. ') . $e->getMessage()
            );

            $this->logger->critical($e);
        }

        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_PromoBanners::ampromobanners');
    }
}
