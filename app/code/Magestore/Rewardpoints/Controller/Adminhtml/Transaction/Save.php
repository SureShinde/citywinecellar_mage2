<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace  Magestore\Rewardpoints\Controller\Adminhtml\Transaction;

use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Transaction save controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Customer\Controller\Adminhtml\Index implements HttpPostActionInterface
{
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $modelCustomer;

    /**
     * @var \Magestore\Rewardpoints\Helper\Action
     */
    protected $helperAction;

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->modelCustomer = $objectManager->create(\Magento\Customer\Model\Customer::class);
        $this->helperAction = $objectManager->create(\Magestore\Rewardpoints\Helper\Action::class);
        if ($this->getRequest()->isPost()) {

            try {
                $request = $this->getRequest();
                $customer = $this->modelCustomer->load($request->getPost('customer_id'));
                if (!$customer->getId()) {
                    $this->messageManager->addError(
                        __('Not found customer to create transaction.')
                    );
                }

                $transaction = $this->helperAction->addTransaction(
                    'admin',
                    $customer,
                    new \Magento\Framework\DataObject([
                        'point_amount'  => $request->getPost('point_amount'),
                        'title'         => $request->getPost('title'),
                        'expiration_day'=> (int)$request->getPost('expiration_day'),
                    ])
                );

                if (!$transaction->getId()) {
                    $this->messageManager->addError(
                        __('Cannot create transaction, please recheck form information.')
                    );
                    return $this->_redirect('*/*/new');

                }
                $this->messageManager->addSuccess(
                    __('Transaction has been created successfully.')
                );
                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', ['id' => $transaction->getId()]);
                }
                return $this->_redirect('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setFormData($request->getPost());
                return $this->_redirect('*/*/edit', ['id' => $request->getParam('id')]);
            }
        }
        $this->messageManager->addError(
            __('Unable to find item to save')
        );
        return $this->_redirect('*/*/');
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Rewardpoints::Manage_transaction');
    }
}
