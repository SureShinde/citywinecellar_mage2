<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Controller\Adminhtml\Banners;

class Edit extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;
    protected $resultForwardFactory;
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var \Amasty\PromoBanners\Model\Rule $model */
        $model = $this->_objectManager->create('Amasty\PromoBanners\Model\Rule');

        if ($id) {
            $model->getResource()->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('Record does not exist.'));
                $this->_redirect('ampromobanners/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_amasty_promo_banner', $model);
        $this->_view->loadLayout();
        $this->_setActiveMenu('Amasty_PromoBanners::ampromobanners')
            ->_addBreadcrumb(__('Banners'), __('Banners'));
        if ($model->getId()) {
            $title = __('Edit Banner `%1`', $model->getRuleName());
        } else {
            $title = __("Add new Banner");
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);

        $this->_view->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_PromoBanners::ampromobanners');
    }
}
