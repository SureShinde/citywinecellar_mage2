<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Controller\Adminhtml\Banners;


class NewProductCondHtml extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->newConditions('actions');
    }

    public function newConditions($prefix)
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->_objectManager->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->_objectManager->create('Magento\CatalogRule\Model\Rule'))
            ->setPrefix($prefix);
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof \Magento\Rule\Model\Condition\AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Amasty_PromoBanners::ampromobanners')
            ->_addBreadcrumb(__('Promo Banners'), __('Promo Banners'));
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_PromoBanners::ampromobanners');
    }
}

