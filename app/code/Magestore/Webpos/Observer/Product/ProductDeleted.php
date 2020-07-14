<?php

namespace Magestore\Webpos\Observer\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ProductDeleted implements ObserverInterface
{
    /**
     * @var \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface
     */
    protected $productDeletedRepository;

    /**
     * ProductDeleted constructor.
     * @param \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface $productDeletedRepository
     */
    public function __construct(
        \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface $productDeletedRepository
    )
    {
        $this->productDeletedRepository = $productDeletedRepository;
    }

    public function execute(EventObserver $observer)
    {
        $productId = $observer->getProduct()->getId();
        if ($productId) {
            $this->productDeletedRepository->insertByProductId($productId);
        }
    }
}