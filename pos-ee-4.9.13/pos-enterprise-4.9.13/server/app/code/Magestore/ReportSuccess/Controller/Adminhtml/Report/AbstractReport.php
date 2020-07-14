<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Controller\Adminhtml\Report;

use Magento\Framework\View\Element\BlockInterface;

/**
 * Class AbstractReport
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Report
 */
abstract class AbstractReport extends \Magento\Backend\App\Action
{
    /**
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * AbstractReport constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_moduleManager = $moduleManager;
        $this->_messageManager = $context->getMessageManager();
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_ReportSuccess::report');
    }

    /**
     *
     * @param string $class
     * @param string $name
     * @param string $template
     * @return BlockInterface|string type
     */
    public function createBlock($class, $name = '', $template = "")
    {
        try {
            $block = $this->_view->getLayout()->createBlock($class, $name);
            if ($block && $template != "") {
                $block->setTemplate($template);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $block;
    }
}