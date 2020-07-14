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
use Magento\Ui\Component\MassAction\Filter;
use Amasty\PromoBanners\Model\ResourceModel\Rule;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var Rule
     */
    private $ruleResource;

    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        Filter $filter,
        Rule $ruleResource
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->ruleResource = $ruleResource;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        try {
            /** @var \Amasty\PromoBanners\Model\Rule $item */
            foreach ($collection->getItems() as $item) {
                $this->ruleResource->delete($item);
            }
            $this->messageManager->addSuccessMessage(__('Rules(s) were successfully deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t delete rule(s) right now. Please review the log and try again. ') . $e->getMessage()
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
