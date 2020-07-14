<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Ui\Component\Listing\Columns\AdjustStock\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class Scroll
 * @package Magestore\ReportSuccess\Ui\Component\Listing\Columns
 */
class Source extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * Source constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->_storeManager = $storeManager;
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
        $this->sourceRepository = $sourceRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function addScrollToField($html) {
        return '<div style="max-height: 85px;overflow-y: auto;">
            '.$html.'
        </div>';
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {

                $poArray = [];
                if ($item[$fieldName]) {
                    $item[$fieldName] = explode(',', $item[$fieldName]);
                    foreach ($item[$fieldName] as $sourceCode) {
                        $sourceModel = $this->sourceRepository->get($sourceCode);
                        $poArray[] = $sourceModel->getName() . ' (' . $sourceModel->getSourceCode() . ')';
                    }
                    $item[$fieldName] = $this->addScrollToField(implode('</br>', $poArray));
                }

            }
        }
        return $dataSource;
    }
}
