<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Controller\Adminhtml\PaymentOffline;
use Magento\Backend\App\Action;


class GetTemplate extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * GetTemplate constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory

    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
    * GetTemplate action
    *
    * @return \Magento\Framework\Controller\ResultInterface
    */
    public function execute()
    {
        $resultPage = $this->resultPageFactory ->create();
        $result = preg_replace("/[\n\r]/","",$resultPage->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('Magestore_PaymentOffline::config/form/field/default_template.phtml')
            ->toHtml());

        $this->getResponse()->setBody($result);
    }
}