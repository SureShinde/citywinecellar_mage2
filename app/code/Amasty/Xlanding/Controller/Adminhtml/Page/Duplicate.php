<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Controller\Adminhtml\Page;

use Amasty\Xlanding\Model\Page;
use Magento\Backend\App\Action\Context;

class Duplicate extends \Magento\Backend\App\Action
{

    private $pageRepository;

    public function __construct(
        Context $context,
        \Amasty\Xlanding\Api\PageRepositoryInterface $pageRepository
    ) {
        parent::__construct($context);
        $this->pageRepository = $pageRepository;
    }

    /**
     * Duplicate action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('page_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                $model = $this->pageRepository->getById($id);

                $model->setPageId(null)
                    ->setTitle($model->getTitle() . '-duplicate')
                    ->setIdentifier($model->getIdentifier() . '-duplicate')
                    ->setCreationTime(null)
                    ->setUpdateTime(null)
                    ->setDynamicCategoryId(null)
                    ->setIsActive(Page::STATUS_ACTIVE);

                $this->pageRepository->save($model);

                $this->messageManager->addSuccess(__('The page has been duplicated.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->messageManager->addError(__('We can\'t find a page to duplicate.'));

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Xlanding::page');
    }
}
