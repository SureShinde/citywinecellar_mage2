<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Observer\Controller\Action\Predispatch;

use Magento\Framework\Event\Observer;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magestore\PaymentOffline\Model\Source\Adminhtml\IconType;

/**
 * Action predispatch AdminhtmlSystemConfigSave
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AdminhtmlSystemConfigSave implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;
    /**
     * @var \Magestore\PaymentOffline\Service\PaymentOfflineService
     */
    protected $paymentOfflineService;

    /**
     * @var \Magestore\PaymentOffline\Helper\Data
     */
    protected $helperData;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * AdminhtmlSystemConfigSave constructor.
     *
     * @param \Magento\Backend\Model\Session $session
     * @param DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magestore\PaymentOffline\Service\PaymentOfflineService $paymentOfflineService
     * @param \Magestore\PaymentOffline\Helper\Data $helperData
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem,
        \Magestore\PaymentOffline\Service\PaymentOfflineService $paymentOfflineService,
        \Magestore\PaymentOffline\Helper\Data $helperData,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        $this->session = $session;
        $this->directoryList = $directoryList;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->filesystem = $filesystem;
        $this->paymentOfflineService = $paymentOfflineService;
        $this->helperData = $helperData;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer) //phpcs:disable
    {
        $controllerAction = $observer->getControllerAction();
        if ($controllerAction->getRequest()->getParam('section') == 'webpos') {
            $groups = $this->_getGroupsForSavePaymentOffline($controllerAction);
            if ($groups) {
                $payments = $groups['payment']['groups'];
                if ($payments) {
                    foreach ($payments as $key => $payment) {
                        if (strpos($key, 'wpo') !== false && strpos($key, 'wpo') === 0) {
                            $paymentData = [];
                            $paymentData['payment_code'] = $key;
                            foreach ($payment['fields'] as $pkey => $value) {
                                $paymentData[$pkey] = $value['value'];
                            }
                            $result = $this->addIconPathToPayment($payment, $paymentData);
                            if ($result !== null) {
                                return $result;
                            }

                            if (count($paymentData)) {
                                $this->paymentOfflineService->createPaymentOffline($paymentData);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Add Icon Path To Payment
     *
     * @param array $payment
     * @param array $paymentData
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function addIconPathToPayment($payment, &$paymentData)
    {
        unset($paymentData['icon_path']);
        if (isset($payment['fields']['icon_type']['value'])
            && $payment['fields']['icon_type']['value'] == IconType::USE_SUGGEST) {
            if (isset($payment['fields']['icon_default']['value'])) {
                $paymentData['icon_path'] = $payment['fields']['icon_default']['value'];
            }
        } else {
            if (isset($payment['fields']['icon_path']) && $payment['fields']['icon_path']['value']
                && $payment['fields']['icon_path']['value']['tmp_name']
                && $payment['fields']['icon_path']['value']['name']) {
                $file = [];
                $iconPath = $this->helperData->getIconPath();
                $file['tmp_name'] = $payment['fields']['icon_path']['value']['tmp_name'];
                $file['name'] = $payment['fields']['icon_path']['value']['name'];
                $om = \Magento\Framework\App\ObjectManager::getInstance();
                /** @var \Magento\Framework\Filesystem\Io\File $ioFile */
                $ioFile = $om->get(\Magento\Framework\Filesystem\Io\File::class);
                $fileInfo = $ioFile->getPathInfo($payment['fields']['icon_path']['value']['name']);
                $ext = $fileInfo['extension'] ?: '';
                $allowed = ['jpg', 'jpeg', 'svg', 'png'];
                $uploader = $this->objectManager->create(
                    \Magento\MediaStorage\Model\File\Uploader::class,
                    ['fileId' => $file]
                );

                $uploader->setAllowedExtensions($allowed);
                if (!in_array($ext, $allowed)) {
                    $this->messageManager->addErrorMessage(
                        'File upload failed. Please choose a jpeg, png, or svg file and try again.'
                    );
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setPath('*/*/', ['section' => 'webpos']);

                    return $resultRedirect;
                }
                $uploader->setAllowRenameFiles(true);
                $result = $uploader->save($iconPath);
                $paymentData['icon_path'] = $result['name'];
            }
            if (isset($payment['fields']['icon_path']['delete'])) {
                $paymentData['icon_path'] = '';
            }
        }
        return null;
    }

    /**
     * Get groups for save
     *
     * @param \Magento\Framework\App\Action\Action $controllerAction
     * @return mixed
     */
    protected function _getGroupsForSavePaymentOffline($controllerAction)
    {
        $groups = $controllerAction->getRequest()->getPost('payment_offline');
        $files = $controllerAction->getRequest()->getFiles('payment_offline');

        if ($files && is_array($files)) {
            /**
             * Carefully merge $_FILES and $_POST information
             * None of '+=' or 'array_merge_recursive' can do this correct
             */
            foreach ($files as $groupName => $group) {
                $data = $this->_processNestedGroups($group);
                if (!empty($data)) {
                    if (!empty($groups[$groupName])) {
                        $groups[$groupName] = array_merge_recursive((array)$groups[$groupName], $data);
                    } else {
                        $groups[$groupName] = $data;
                    }
                }
            }
        }
        return $groups;
    }

    /**
     * Process nested groups
     *
     * @param mixed $group
     * @return array
     */
    protected function _processNestedGroups($group)
    {
        $data = [];

        if (isset($group['fields']) && is_array($group['fields'])) {
            foreach ($group['fields'] as $fieldName => $field) {
                if (!empty($field['value'])) {
                    $data['fields'][$fieldName] = ['value' => $field['value']];
                }
            }
        }

        if (isset($group['groups']) && is_array($group['groups'])) {
            foreach ($group['groups'] as $groupName => $groupData) {
                $nestedGroup = $this->_processNestedGroups($groupData);
                if (!empty($nestedGroup)) {
                    $data['groups'][$groupName] = $nestedGroup;
                }
            }
        }

        return $data;
    }
}
