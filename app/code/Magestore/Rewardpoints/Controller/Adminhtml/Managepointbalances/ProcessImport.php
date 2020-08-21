<?php
namespace Magestore\Rewardpoints\Controller\Adminhtml\Managepointbalances;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Process Import Controller
 */
class ProcessImport extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $ioFile;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * ProcessImport constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\Io\File $ioFile
    ) {
        $this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->storeManager = $storeManager;
        $this->ioFile = $ioFile;
        parent::__construct($context);
    }

    /**
     * Import action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /* @var \Magento\MediaStorage\Model\File\Uploader $uploader */
        $uploader = $this->_objectManager->create(
            \Magento\MediaStorage\Model\File\Uploader::class,
            ['fileId' => 'filecsv']
        );
        $files = $uploader->validateFile();

        if (isset($files['name']) && $files['name'] != '') {
            try {
                if ($uploader->getFileExtension() !='csv') {
                    $this->messageManager->addError(
                        __('Does not support the %1 file', $uploader->getFileExtension())
                    );
                    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setPath('rewardpoints/managepointbalances/import');
                    return $resultRedirect;
                }
                $file = $this->getRequest()->getFiles('filecsv');
                $dataFile = $this->csvProcessor->getData($file['tmp_name']);
                $customerData = [];
                $fields = [];
                foreach ($dataFile as $row => $cols) {
                    if ($row == 0) {
                        $fields = $cols;
                    } else {
                        $customerData[] = array_combine($fields, $cols);
                    }
                }

                if (isset($customerData) && count($customerData)) {
                    $cnt = $this->_updateCustomer($customerData);
                    $cntNot = count($customerData) - $cnt;
                    $successMessage = __('Imported total %1 customer point balance(s)', $cnt);
                    if ($cntNot) {
                        $successMessage .= "</br>";
                        $successMessage .= __("There are %1 emails which don't belong to any accounts.", $cntNot);
                    }
                    $this->messageManager->addSuccess($successMessage);

                    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setPath('rewardpoints/managepointbalances/index');
                    return $resultRedirect;
                } else {
                    $this->messageManager->addError(__('Point balance imported'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $this->messageManager->addError(__('No uploaded files'));
        }

        /* @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('rewardpoints/managepointbalances/import');
        return $resultRedirect;
    }

    /**
     * Update Customer
     *
     * @param array $customerData
     *
     * @return int|void
     */
    public function _updateCustomer($customerData)
    {
        $collection = [];
        $website = $this->_objectManager->create(\Magento\Config\Model\Config\Source\Website::class)
            ->toOptionArray();
        $website[] = [
            'value' => 0,
            'label' => 'Admin'
        ];

        foreach ($customerData as $key => $value) {
            $website_id = $this->storeManager->getDefaultStoreView()->getWebsiteId();
            foreach ($website as $key => $id) {
                if ($id['label'] == $value['Website']) {
                    $website_id = $id['value'];
                    break;
                }
            }
            $email = $value['Email'];
            $pointBalance = $value['Point Change'];
            $expireAfter = $value['Points expire after'];
            $customerExist = $this->_checkCustomer($email, $website_id);
            if (!$customerExist || !$customerExist->getId()) {
                continue;
            }
            $customerExist->setPointBalance($pointBalance)
                ->setExpireAfter($expireAfter);
            $collection[] = $customerExist;
        }
        $this->_objectManager->create(\Magestore\Rewardpoints\Model\ResourceModel\Transaction::class)
            ->importPointFromCsv($collection);
        return count($collection);
    }

    /**
     * Check customer exist by email
     *
     * @param string $email
     * @param int $website_id
     * @return \Magento\Customer\Model\Customer
     */
    public function _checkCustomer($email, $website_id = 1)
    {
        return $this->_objectManager->create(\Magento\Customer\Model\Customer::class)
            ->setWebsiteId($website_id)
            ->loadByEmail($email);
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Rewardpoints::Manage_Point_Balances');
    }
}
