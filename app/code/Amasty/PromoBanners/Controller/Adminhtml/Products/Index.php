<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Controller\Adminhtml\Products;

use Magento\Backend\App\Action;

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);

        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->_coreRegistry = $registry;
    }

    public function execute()
    {
        /** @var \Amasty\PromoBanners\Model\Rule $rule */
        $rule = $this->_objectManager->create('Amasty\PromoBanners\Model\Rule');
        if ($rid = (int)$this->_request->getParam('id')) {
            $rule->getResource()->load($rule, $rid, 'id');
        }
        $this->_coreRegistry->register('ampromobanners_current_rule', $rule);

        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('ampromobanners.allowed_products.grid')
            ->setAllowedProducts($this->getRequest()->getPost('ampromobanners_allowed', null));

        return $resultLayout;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_PromoBanners::ampromobanners');
    }
}
