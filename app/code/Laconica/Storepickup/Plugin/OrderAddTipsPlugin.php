<?php

namespace Laconica\Storepickup\Plugin;

use Laconica\Storepickup\Helper\Data;
use Magento\Framework\DataObject;
use Magento\Sales\Block\Order\Totals;

class OrderAddTipsPlugin
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * AddFeeToPdf constructor.
     * @param Data $dataHelper
     */
    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param Totals $subject
     * @param mixed ...$area
     * @return array
     */
    public function beforeGetTotals(Totals $subject, ...$area)
    {
        if ($subject->getTotal('subtotal')) {
            $tips = $subject->getOrder()->getTips();
            if ($tips > 0) {

                $data = new DataObject(
                    [
                        'code' => Data::TIPS_CODE,
                        'value' => $tips,
                        'base_value' => $tips,
                        'label' => $this->dataHelper->getConfigTitle(),
                    ]
                );
                $subject->addTotal($data, 'subtotal');
            }
        }
        return $area;
    }
}
