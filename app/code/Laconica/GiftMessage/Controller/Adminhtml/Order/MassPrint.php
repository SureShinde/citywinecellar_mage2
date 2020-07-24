<?php

namespace Laconica\GiftMessage\Controller\Adminhtml\Order;

use Exception;
use Laconica\GiftMessage\Model\Order\Pdf\GiftMessage;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Psr\Log\LoggerInterface;
use Zend_Pdf_Exception;

class MassPrint extends Action
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var GiftMessage
     */
    protected $giftMessage;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * MassPrint constructor.
     * @param CollectionFactory $collectionFactory
     * @param GiftMessage $giftMessage
     * @param FileFactory $fileFactory
     * @param LoggerInterface $logger
     * @param Action\Context $context
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        GiftMessage $giftMessage,
        FileFactory $fileFactory,
        LoggerInterface $logger,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->giftMessage = $giftMessage;
        $this->fileFactory = $fileFactory;
        $this->logger = $logger;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Zend_Pdf_Exception
     * @throws Exception
     */
    public function execute()
    {
        $selectedIds = implode(',', $this->getRequest()->getParam('selected'));
        $orderCollection = $this->collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $selectedIds]);

        try {
            $pdf = $this->giftMessage->getPdf($orderCollection);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
            $this->messageManager->addErrorMessage(__('Could not create pdf: ' . $exception->getMessage()));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->_redirect->getRefererUrl());
        }
        if (!$pdf) {
            $this->messageManager->addErrorMessage(__('There are no printable gift messages related to selected orders'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->_redirect->getRefererUrl());
        }

        $fileContent = ['type' => 'string', 'value' => $pdf->render(), 'rm' => true];

        return $this->fileFactory->create(
            sprintf('giftMessages%s.pdf', date('Y-m-d_H-i-s')),
            $fileContent,
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
