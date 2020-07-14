<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Amasty\PromoBanners\Model\Source\Active;

class Status implements OptionSourceInterface
{

    private $options;

    /**
     * @var Active
     */
    private $active;

    public function __construct(Active $active)
    {
        $this->active = $active;
    }

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            foreach ($this->active->toOptionArray() as $value => $label) {
                $this->options[] = [
                    'value' => $value,
                    'label' => $label
                ];
            }
        }

        return $this->options;
    }
}
