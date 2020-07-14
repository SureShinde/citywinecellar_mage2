<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Tags;

use Amasty\Blog\Api\Data\TagInterface;
use Amasty\Blog\Controller\Adminhtml\Traits\SaveTrait;
use Amasty\Blog\Model\Tag;

class Save extends \Amasty\Blog\Controller\Adminhtml\Tags
{
    use SaveTrait;

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->getRequest()->getPostValue();
            $id = (int)$this->getRequest()->getParam('tag_id');
            try {
                $model = $this->getTagRepository()->getTagModel();

                $inputFilter = new \Zend_Filter_Input([], [], $data);
                $data = $inputFilter->getUnescaped();
                if ($id) {
                    $model = $this->getTagRepository()->getById($id);
                    $data = $this->retrieveItemContent($data, $model);
                }

                $model->addData($data);
                $this->_getSession()->setPageData($model->getData());
                $this->getTagRepository()->save($model);
                $this->getMessageManager()->addSuccessMessage(__('You saved the item.'));
                $this->_getSession()->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', [
                        'id' => $model->getId(),
                        'store' => (int)$this->getRequest()->getParam('store_id', 0)
                    ]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->getMessageManager()->addErrorMessage($e->getMessage());
                $this->getDataPersistor()->set(Tag::PERSISTENT_NAME, $data);
                if (!empty($id)) {
                    $this->_redirect('*/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('*/*/new');
                }

                return;
            } catch (\Exception $e) {
                $this->getMessageManager()->addErrorMessage(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->getLogger()->critical($e);
                $this->_getSession()->setPageData($data);
                $this->_redirect('*/*/edit', ['id' => $id]);

                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * @return array
     */
    private function getFieldsByStore()
    {
        return TagInterface::FIELDS_BY_STORE;
    }
}
