<?php
/**
 * Created by PhpStorm.
 * User: HiepDD
 * Date: 1/18/2017
 * Time: 9:25 AM
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;


class InlineEdit extends
    \Magento\Backend\App\Action
{
    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    protected $resultJsonFactory;

    /** @var \Magento\Framework\Api\DataObjectHelper */
    protected $dataObjectHelper;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * InlineEdit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataObjectHelper  = $dataObjectHelper;
        $this->logger            = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $model     = $this->_objectManager->create('Magestore\BarcodeSuccess\Model\Barcode');
        $postItems = $this->getRequest()->getParam('items', []);
        if ( !($this->getRequest()->getParam('isAjax') && count($postItems)) ) {
            return $resultJson->setData([
                                            'messages' => [__('Please correct the data sent.')],
                                            'error'    => true,
                                        ]);
        }
        foreach ( array_keys($postItems) as $barcodeId ) {
            $data = $postItems[$barcodeId];
            /** Check barcode first */
            $model->load($data['barcode'], \Magestore\BarcodeSuccess\Model\Barcode::BARCODE);
            if ( $model->getId() && $model->getId() != $barcodeId ) {
                return $resultJson->setData([
                                                'messages' => [__('The Barcode already exist.')],
                                                'error'    => true,
                                            ]);
            }
            /** Check SKU then */
            $productCollection = $this->_objectManager->create('Magento\Catalog\Model\Product')->getCollection()
                                                      ->addFieldToFilter('sku', $data['product_sku']);
            if ( !$productCollection->getSize() ) {
                return $resultJson->setData([
                                                'messages' => [__("The Product SKU doesn't exist.")],
                                                'error'    => true,
                                            ]);
            }
            $model->addData($data);
            $model->setId($barcodeId);
            $model->save();
        }

        return $resultJson->setData([
                                        'messages' => $this->getErrorMessages(),
                                        'error'    => $this->isErrorExists(),

                                    ]);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_BarcodeSuccess::manage_barcode');
    }


    /**
     * Get array with errors
     *
     * @return array
     */
    public function getErrorMessages()
    {
        $messages = [];
        foreach ( $this->getMessageManager()->getMessages()->getItems() as $error ) {
            $messages[] = $error->getText();
        }
        return $messages;
    }

    /**
     * Check if errors exists
     *
     * @return bool
     */
    public function isErrorExists()
    {
        return (bool)$this->getMessageManager()->getMessages(true)->getCount();
    }
}