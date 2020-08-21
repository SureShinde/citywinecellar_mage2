<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Cron;

/**
 * Class SendScheduleEmail
 *
 * Cron Send schedule email
 */
class SendScheduleEmail
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory
     */
    protected $giftvoucherCollectionFactory;

    /**
     * SendScheduleEmail constructor.
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $giftvoucherCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $giftvoucherCollectionFactory
    ) {
        $this->date = $date;
        $this->giftvoucherCollectionFactory = $giftvoucherCollectionFactory;
    }

    /**
     * Execute
     */
    public function execute()
    {
        $collection = $this->giftvoucherCollectionFactory->create();
        $timeSite = date("Y-m-d H:i:s", $this->date->timestamp());
        $collection->addFieldToFilter('is_sent', ['neq' => 1])
            ->addFieldToFilter('day_store', ['notnull' => true])
            ->addFieldToFilter('day_store', ['to' => $timeSite]);
        if (count($collection)) {
            foreach ($collection as $giftCard) {
                $giftCard->save();
                if ($giftCard->sendEmailToRecipient()) {
                    if ($giftCard->getNotifySuccess()) {
                        $giftCard->sendEmailSuccess();
                    }
                }
            }
        }
    }
}
