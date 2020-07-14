<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Ui\Component\Listing\Columns\DropshipRequest;

use Magento\Framework\Data\OptionSourceInterface;
use Magestore\DropshipSuccess\Model\Source\DropshipRequest\Status as RequestStatus;

/**
 * Class Options
 */
class Status implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var RequestStatus
     */
    protected $requestStatus;

    /**
     * Constructor
     *
     * @param RequestStatus $requestStatus
     */
    public function __construct(RequestStatus $requestStatus)
    {
        $this->requestStatus = $requestStatus;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->requestStatus->toOptionArray();
        }
//        \Zend_Debug::dump($this->options);die();
        return $this->options;
    }
}
