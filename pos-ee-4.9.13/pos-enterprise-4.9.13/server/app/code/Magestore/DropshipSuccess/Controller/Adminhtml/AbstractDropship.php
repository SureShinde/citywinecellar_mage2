<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\Adminhtml;

use Magento\Framework\Registry;
use Magestore\DropshipSuccess\Api\DropshipRequestRepositoryInterface;
use Magestore\DropshipSuccess\Model\Locator\LocatorInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
/**
 * Class AbstractDropship
 * @package Magestore\DropshipSuccess\Controller\Adminhtml
 */
abstract class AbstractDropship extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var DropshipRequestRepositoryInterface
     */
    protected $dropshipRequestRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * AbstractDropship constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param LocatorInterface $locator
     * @param DropshipRequestRepositoryInterface $dropshipRequestRepository
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        LocatorInterface $locator,
        DropshipRequestRepositoryInterface $dropshipRequestRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_dateFilter = $dateFilter;
        $this->dataPersistor = $dataPersistor;
        $this->_fileFactory = $fileFactory;
        $this->locator = $locator;
        $this->dropshipRequestRepository = $dropshipRequestRepository;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * @return bool|\Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface
     */
    public function _initDropshipRequest()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            $dropshipRequest = $this->dropshipRequestRepository->getById($id);
            $orderId = $dropshipRequest->getOrderId();
            try {
                $order = $this->orderRepository->get($orderId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This order no longer exists.'));
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
                return false;
            } catch (InputException $e) {
                $this->messageManager->addErrorMessage(__('This order no longer exists.'));
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
                return false;
            }
            $this->_coreRegistry->register('current_order', $order);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('This dropship request no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->_coreRegistry->register(\Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface::CURRENT_DROPSHIP_REQUEST, $dropshipRequest);
        return $dropshipRequest;
    }
}
