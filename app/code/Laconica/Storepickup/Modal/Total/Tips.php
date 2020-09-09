<?php

namespace Laconica\Storepickup\Modal\Total;

use Laconica\Storepickup\Helper\Data;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

class Tips extends AbstractTotal
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var int
     */
    protected $value = 0;

    /**
     * Fee constructor.
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $tips = $quote->getTips();
        if ($tips > 0) {
            $this->value = $tips;
            $total->setBaseTips($tips);

            $total->addTotalAmount(Data::TIPS_CODE, $tips);
            $total->addBaseTotalAmount(Data::TIPS_CODE, $tips);
        }

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote $quote
     * @param Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Quote $quote, Total $total)
    {
        return [
            'code' => Data::TIPS_CODE,
            'title' => $this->helper->getConfigTitle(),
            'value' => $this->value
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->helper->getConfigTitle();
    }
}
