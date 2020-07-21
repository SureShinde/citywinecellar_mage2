<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Observer;

use Amasty\Rewards\Model\ReviewManagement;
use Magento\Framework\Event\ObserverInterface;

class ReviewProductSave implements ObserverInterface
{
    /**
     * @var ReviewManagement
     */
    private $reviewManagement;

    public function __construct(
        ReviewManagement $reviewManagement
    ) {
        $this->reviewManagement = $reviewManagement;
    }

    /**
     * Event controller_action_postdispatch_review_product_save
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer->getRequest()->getPostValue();
        $reviewId = $observer->getRequest()->getParam('id');

        if ($data && $reviewId && $data['status_id'] == \Magento\Review\Model\Review::STATUS_APPROVED) {
            $this->reviewManagement->addReviewPoints($reviewId);
        }
    }
}
