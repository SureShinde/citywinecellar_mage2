<?php

/**
 * Magestore.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Megamenu
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Block\Adminhtml\Transaction\TabContent\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Tab Add new tab
 */
class AddNew extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * AddNew constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_storeManager= $context->getStoreManager();
        $this->_systemStore =$systemStore;
    }

    /**
     * @inheritDoc
     */
    public function getTabLabel()
    {
        return __('Transaction information');
    }

    /**
     * @inheritDoc
     */
    public function getTabTitle()
    {
        return __('Transaction information');
    }

    /**
     * @inheritDoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isHidden()
    {
        return false;
    }
}
