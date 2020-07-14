<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Controller\Adminhtml\Product;

use Amasty\Xlanding\Api\PageRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_Xlanding::page';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Amasty\Xlanding\Model\Page\Product\Cache
     */
    private $dataProvider;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Amasty\Xlanding\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Amasty\Xlanding\Model\PageFactory
     */
    private $pageFactory;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\Xlanding\Model\Page\Product\AdminhtmlDataProvider $dataProvider,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Amasty\Xlanding\Model\RuleFactory $ruleFactory,
        \Amasty\Xlanding\Model\PageFactory $pageFactory,
        \Amasty\Xlanding\Api\PageRepositoryInterface $pageRepository,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataProvider = $dataProvider;
        $this->ruleFactory = $ruleFactory;
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
        $this->registry = $registry;
        $this->serializer = $serializer;
    }

    /**
     * @return \Amasty\Xlanding\Model\Page
     */
    private function initPage()
    {
        $pageId = $this->getRequest()->getParam('page_id', false);

        try {
            $model = $this->pageRepository->getById($pageId);
        } catch (NoSuchEntityException $e) {
            $model = $this->pageFactory->create();
        }

        $this->registry->register('amasty_xlanding_page', $model);

        return $model;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $this->initPage();

        $storeId = $this->getRequest()->getParam('store_id', $this->dataProvider->getStoreId());
        $this->dataProvider->setStoreId($storeId);

        $topProductData = $this->getRequest()->getParam('top_product_data', []);
        if (!empty($topProductData)) {
            $this->dataProvider->resortPositionData($topProductData['source_position'], 0);
            $this->dataProvider->setProductPositionData([$topProductData['entity_id'] => 0]);
        }

        $automaticProducData = $this->getRequest()->getParam('automatic_product_data', []);
        if (!empty($automaticProducData)) {
            $this->dataProvider->unsetProductPositionData($automaticProducData['entity_id']);
            $position = $this->dataProvider->getCurrentProductPosition($automaticProducData['entity_id']);
            $this->dataProvider->resortPositionData($automaticProducData['source_position'], $position);
        }

        $ruleData = $this->getRequest()->getParam('rule', []);
        if (isset($ruleData['conditions'])) {
            $rule = $this->ruleFactory->create();
            $rule->loadPost($ruleData);

            $serializedConditions = $this->serializer->serialize($rule->getConditions()->asArray());
            $this->dataProvider->setSerializedRuleConditions($serializedConditions);
        }

        $positions = $this->getRequest()->getParam('positions', []);
        $sortOrder = $this->getRequest()->getParam('sort_order', false);

        $this->dataProvider->setProductPositionData($positions);
        $this->dataProvider->setSortOrder($sortOrder);

        $resultJson->setData([]);
        return $resultJson;
    }
}
