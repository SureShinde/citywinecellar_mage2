<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Block\Adminhtml;

/**
 * Class AbstractBlock
 * @package Magestore\ReportSuccess\Block\Adminhtml
 */
class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface 
     */
    protected $_objectManager;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    public $_moduleManager;

    /**
     * AbstractBlock constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    )
    {
        $this->_objectManager = $objectManager;
        $this->_messageManager = $messageManager;
        $this->_storeManager = $context->getStoreManager();
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_authorization = $authorization;
        $this->_moduleManager = $moduleManager;
        parent::__construct($context, $data);
    }
    /**
     * 
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager() {
        return $this->_objectManager;
    }
}
