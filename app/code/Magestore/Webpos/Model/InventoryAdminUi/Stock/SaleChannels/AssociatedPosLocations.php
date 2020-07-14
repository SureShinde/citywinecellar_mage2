<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\InventoryAdminUi\Stock\SaleChannels;

class AssociatedPosLocations implements \Magento\Framework\Option\ArrayInterface
{
    protected $options;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Stock constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Return options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $stockId = $this->request->getParam('stock_id');
        if (!$stockId || $stockId < 1) {
            return [];
        }
        if (!$this->options) {
            $this->options = [];
            /** @var \Magestore\Webpos\Model\ResourceModel\Location\Location\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->getSelect()
                ->columns(
                    [
                        'address' => new \Zend_Db_Expr("CONCAT_WS(', ',street, city, country, region, postcode)")
                    ]);
            $collection->addFieldToFilter(\Magestore\Webpos\Api\Data\Location\LocationInterface::STOCK_ID, $stockId);
            /** @var \Magestore\Webpos\Api\Data\Location\LocationInterface $item */
            foreach ($collection as $item) {
                $this->options[] = [
                    'id' => $item->getLocationId(),
                    'name' => $item->getName(),
                    'address' => $item->getData('address'),
                    'href' => $this->urlBuilder->getUrl('webposadmin/location_location/edit', ['id' => $item->getLocationId()])
                ];
            }
        }

        return $this->options;
    }

}