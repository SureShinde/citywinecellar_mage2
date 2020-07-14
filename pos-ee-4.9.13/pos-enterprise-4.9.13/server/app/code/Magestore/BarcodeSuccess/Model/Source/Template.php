<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magestore\BarcodeSuccess\Model\ResourceModel\Template\Collection as BarcodeTemplateCollection;
use Magestore\BarcodeSuccess\Model\Source\Status;

/**
 * Class TemplateType
 * @package Magestore\BarcodeSuccess\Model\Source\TemplateType
 */

class Template implements OptionSourceInterface
{
    /**
     * @var BarcodeTemplateCollection
     */
    protected $collection;
    
    /**
     * @var array
     */
    protected $options;

    /**
     * TemplateType constructor.
     * @param BarcodeTemplateCollection $collection
     */
    public function __construct(
        BarcodeTemplateCollection $collection
    ) {
        $this->collection = $collection;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if(!$this->options) {
//            $availableOptions = [];
            $this->options = [];
            $collection = $this->collection;
            $collection->addFieldToFilter('status', Status::ACTIVE);
            if ($collection->getSize() > 0) {
                foreach ($collection as $template) {
                    $this->options[] = [
                        'label' => $template->getName(),
                        'value' => $template->getTemplateId(),
                    ];
                }
            }
        }
        return $this->options;
    }
}
