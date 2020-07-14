<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\ContentType\Block;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class
 */
class Metadata extends \Magento\Backend\App\AbstractAction
{
    /**
     * @var \Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        try {
            $collection = $this->collectionFactory->create();
            $widgets = $collection
                ->addFieldToSelect(['title', 'instance_type', 'widget_parameters'])
                ->addFieldToFilter('instance_id', ['eq' => $params['instance_id']])
                ->load();
            $result = $widgets->getFirstItem()->toArray();
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
