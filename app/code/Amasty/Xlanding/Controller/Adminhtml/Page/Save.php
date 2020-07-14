<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */

/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Amasty\Xlanding\Controller\Adminhtml\Page;

use Amasty\Xlanding\Api\Data\PageInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use \Amasty\Xlanding\Model\Page;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var \Amasty\Xlanding\Model\PageFactory
     */
    private $pageFactory;

    /**
     * @var \Amasty\Xlanding\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var Page\Product\AdminhtmlDataProvider
     */
    private $dataProvider;

    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        \Amasty\Base\Model\Serializer $serializer,
        \Amasty\Xlanding\Model\PageFactory $pageFactory,
        \Amasty\Xlanding\Model\RuleFactory $ruleFactory,
        \Amasty\Xlanding\Model\Page\Product\AdminhtmlDataProvider $dataProvider
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->serializer = $serializer;
        $this->pageFactory = $pageFactory;
        $this->ruleFactory = $ruleFactory;
        $this->dataProvider = $dataProvider;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Xlanding::page');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $id = $this->getRequest()->getParam('page_id');

        if ($data) {
            $model = $this->pageFactory->create();
            if ($id) {
                $model->load($id);
            }

            $data = $this->validateData($data);
            $model->setData($data);

            $model->setProductPositionData($this->dataProvider->getProductPositionDataByStore());
            $model->setSortOrder($this->dataProvider->getSortOrder());
            $this->dataProvider->clear();

            if ($useConfig = $this->getRequest()->getPost('use_config')) {
                foreach ($useConfig as $attributeCode) {
                    $model->setData($attributeCode, null);
                }
            }

            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit', ['page_id' => $model->getId(), '_current' => true]);
            }

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved this page.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['page_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the page.'));
            }

            $data['store_id'] = $data['stores']; //fastfix;
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['page_id' => $this->getRequest()->getParam('page_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function validateData(array $data)
    {
        if (isset($data['rule']) && isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];

            unset($data['rule']);

            $rule = $this->ruleFactory->create();
            $rule->loadPost($data);

            $data['conditions_serialized'] = $this->serializer->serialize($rule->getConditions()->asArray());
            unset($data['conditions']);
        }

        $metaData = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'meta_') !== false) {
                $metaData[substr($key, strripos($key, '_') + 1)][$key] = $value;
                unset($data[$key]);
            }
        }

        $data['meta_data'] = $this->serializer->serialize($metaData);

        if (($data[PageInterface::LANDING_IS_ACTIVE] ?? Page::STATUS_DISABLED) == Page::STATUS_DYNAMIC
            && !$data[PageInterface::DYNAMIC_CATEGORY_ID]
        ) {
            $data[PageInterface::LANDING_IS_ACTIVE] = Page::STATUS_ENABLED;
            $this->messageManager->addNoticeMessage(__('Display mode is set to Enabled.'));
        }

        return $data;
    }
}
