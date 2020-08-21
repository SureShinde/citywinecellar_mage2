<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Controller\Adminhtml;

/**
 * Class AbstractAction
 *
 * Used to create Abstract Action
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $resource = "";

    /**
     * AbstractAction constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->resultFactory = $context->getResultFactory();
    }

    /**
     * Create json result
     *
     * @param array $data
     * @return mixed
     */
    public function createJsonResult($data)
    {
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        return $resultJson->setData($data);
    }

    /**
     * Create page result
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function createPageResult()
    {
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
    }

    /**
     * Create redirect result
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function createRedirectResult()
    {
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
    }

    /**
     * Create forward result
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function createForwardResult()
    {
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
    }

    /**
     * Is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this->resource);
    }
}
