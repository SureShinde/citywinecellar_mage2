<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Controller\Adminhtml\Unconverted;

/**
 * Class \Magestore\Webpos\Controller\Adminhtml\Unconverted\AbstractAction
 */
abstract class AbstractAction extends \Magento\Backend\App\Action
{
    /**
     * Admin resource constant
     */
    const ADMIN_RESOURCE = 'Magestore_Webpos::unconvertedOrder';

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magestore\Webpos\Api\Checkout\PosOrderRepositoryInterface
     */
    protected $posOrderRepository;

    /**
     * AbstractAction constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magestore\Webpos\Api\Checkout\PosOrderRepositoryInterface $posOrderRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magestore\Webpos\Api\Checkout\PosOrderRepositoryInterface $posOrderRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->posOrderRepository = $posOrderRepository;
        parent::__construct($context);
    }
}
