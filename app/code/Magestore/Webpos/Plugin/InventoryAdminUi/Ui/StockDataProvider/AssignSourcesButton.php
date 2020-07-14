<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magestore\Webpos\Plugin\InventoryAdminUi\Ui\StockDataProvider;

/**
 * Customize stock form. Add note for assign source button
 *
 * Class AssignSourcesButton
 * @package Magestore\Webpos\Plugin\InventoryAdminUi\Ui\StockDataProvider
 */
class AssignSourcesButton
{
    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * AssignSourcesButton constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->locationRepository = $locationRepository;
        $this->request = $request;
    }

    /**
     * @param \Magento\InventoryAdminUi\Ui\DataProvider\StockDataProvider $subject
     * @param array $meta
     * @return array
     */
    public function afterGetMeta($subject, array $meta)
    {
        $stockId = $this->request->getParam('stock_id');
        if ('inventory_stock_form_data_source' === $subject->getName()) {
            if ($stockId > 0) {
                $totalLocation = $this->locationRepository->getCountLocationByStockId($stockId);
                if ($totalLocation > 0) {
                    $meta = array_replace_recursive($meta, [
                        'sources' => [
                            'children' => [
                                'assign_sources_container' => [
                                    'children' => [
                                        'assign_sources_button' => [
                                            'arguments' => [
                                                'data' => [
                                                    'config' => [
                                                        'notice' => __(
                                                            "Notice that in order to assign this Stock to POS Location, one Stock can only be mapped to a Source."
                                                        )
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ], $meta);
                }
            }
        }
        return $meta;
    }
}
