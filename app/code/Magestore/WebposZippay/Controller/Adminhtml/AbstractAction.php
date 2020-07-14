<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Controller\Adminhtml;

/**
 * Class AbstractAction
 * @package Magestore\WebposZippay\Controller\Adminhtml
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magestore\WebposZippay\Api\ZippayServiceInterface
     */
    protected $zippayService;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magestore\WebposZippay\Helper\Data
     */
    protected $helper;

    /**
     * AbstractAction constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magestore\WebposZippay\Api\ZippayServiceInterface $zippayService
     * @param \Magestore\WebposZippay\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magestore\WebposZippay\Api\ZippayServiceInterface $zippayService,
        \Magestore\WebposZippay\Helper\Data $helper
    ){
        parent::__construct($context);
        $this->zippayService = $zippayService;
        $this->helper = $helper;
        $this->resultFactory = $context->getResultFactory();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createJsonResult($data){
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        return $resultJson->setData($data);
    }
}