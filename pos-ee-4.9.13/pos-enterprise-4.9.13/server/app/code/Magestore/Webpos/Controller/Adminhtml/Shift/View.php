<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Shift;

/**
 * Class View
 * @package Magestore\Webpos\Controller\Adminhtml\Shift
 */
class View extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface
     */
    protected $shiftRepository;

    public function __construct(
        \Magestore\Webpos\Controller\Adminhtml\Pos\Context $context,
        \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $context->getResultPageFactory();
        $this->coreRegistry = $context->getCoreRegistry();
        $this->shiftRepository = $shiftRepository;
    }

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $posId = $this->getRequest()->getParam('pos_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $shift = $this->shiftRepository->get($id);
            if (!$shift->getShiftId()) {
                $this->messageManager->addErrorMessage(__('This session no longer exists.'));
                if ($posId) {
                    return $resultRedirect->setPath('webposadmin/pos/edit', ['_current' => true, 'id' => $posId]);
                } else {
                    return $resultRedirect->setPath('webposadmin/pos/', ['_current' => true]);
                }
            } else {
                $this->coreRegistry->register('current_shift', $shift);
                $resultPage = $this->resultPageFactory->create();
                $pageTitle = $shift->getShiftIncrementId();
                $resultPage->getConfig()->getTitle()->prepend($pageTitle);
                return $resultPage;
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please choose one session first'));
            return $resultRedirect->setPath('webposadmin/pos/', ['_current' => true]);
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::pos');
    }


}