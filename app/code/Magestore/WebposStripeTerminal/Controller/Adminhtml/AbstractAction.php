<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripeTerminal\Controller\Adminhtml;

/**
 * Class AbstractAction
 * @package Magestore\WebposStripeTerminal\Controller\Adminhtml
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magestore\WebposStripeTerminal\Api\StripeTerminalServiceInterface
     */
    protected $service;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magestore\WebposStripe\Helper\Data
     */
    protected $helper;

    /**
     * AbstractAction constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magestore\WebposStripeTerminal\Api\StripeTerminalServiceInterface $service
     * @param \Magestore\WebposStripeTerminal\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magestore\WebposStripeTerminal\Api\StripeTerminalServiceInterface $service,
        \Magestore\WebposStripeTerminal\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->service = $service;
        $this->helper = $helper;
        $this->resultFactory = $context->getResultFactory();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createJsonResult($data)
    {
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        return $resultJson->setData($data);
    }
}