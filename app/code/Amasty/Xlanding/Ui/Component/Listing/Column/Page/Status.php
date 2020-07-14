<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Ui\Component\Listing\Column\Page;

use Magento\Framework\Data\OptionSourceInterface;
use Amasty\Xlanding\Model\Page;

class Status implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [
                ['value' => Page::STATUS_ENABLED, 'label' => __('Enabled')],
                ['value' => Page::STATUS_DYNAMIC, 'label' => __('Dynamic Category')],
                ['value' => Page::STATUS_DISABLED, 'label' =>  __('Disabled')]
            ];
        }
        return $this->options;
    }

}
