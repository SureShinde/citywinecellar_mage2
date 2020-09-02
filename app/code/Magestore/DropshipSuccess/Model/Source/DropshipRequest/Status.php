<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\Source\DropshipRequest;

use Magento\Framework\Data\OptionSourceInterface;
use Magestore\DropshipSuccess\Model\DropshipRequestFactory;
use Magestore\DropshipSuccess\Model\Source\AbstractSource;

/**
 * Class IsActive
 */
class Status extends AbstractSource implements OptionSourceInterface
{

    /**
     * @var DropshipRequestFactory
     */
    protected $dropshipRequestFactory;

    /**
     * Status constructor.
     * @param DropshipRequestFactory $dropshipRequestFactory
     */
    public function __construct(
        DropshipRequestFactory $dropshipRequestFactory
    ) {
        $this->dropshipRequestFactory = $dropshipRequestFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->dropshipRequestFactory->create()->getStatusOption();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
