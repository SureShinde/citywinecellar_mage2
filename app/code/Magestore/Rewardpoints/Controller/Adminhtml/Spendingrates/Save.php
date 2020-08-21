<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace  Magestore\Rewardpoints\Controller\Adminhtml\Spendingrates;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Save Spending rates controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Customer\Controller\Adminhtml\Index implements HttpPostActionInterface
{
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $model = $this->_objectManager->create(\Magestore\Rewardpoints\Model\Rate::class);
        $request = $this->getRequest()->getPostValue();
        if (isset($request['rewardpoints_spendingrates']) && $request['rewardpoints_spendingrates']) {
            $data = $request['rewardpoints_spendingrates'];
            if (isset($data['website_ids']) && $data['website_ids']) {
                $data['website_ids'] = implode(',', $data['website_ids']);
            }
            if (isset($data['customer_group_ids']) && $data['customer_group_ids']) {
                $data['customer_group_ids'] = implode(',', $data['customer_group_ids']);
            }
            $data['direction'] = \Magestore\Rewardpoints\Model\Rate::POINT_TO_MONEY;
            $model->setData($data);
            if (isset($data['rate_id']) && $data['rate_id']) {
                $model->setId($data['rate_id']);
            }
            try {
                $model->save();
                if ($model->getId()) {
                    $this->messageManager->addSuccess(__('Spending rate was successfully saved'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addSuccess(__('Unable to find item to save'));
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        if ($this->getRequest()->getParam('back')) {
            return $this->_redirect('*/*/edit', ['id' => $model->getId()]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Rewardpoints::Spending_Rates');
    }
}
