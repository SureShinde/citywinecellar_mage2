<?php

namespace Magestore\Giftvoucher\Ui\DataProvider\GiftcardCode;

use Magento\Framework\UrlInterface;
use Magestore\Giftvoucher\Model\Status;

class ChangeStatusAction {

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * context
     *
     * @var \Magestore\OrderSuccess\Ui\DataProvider\Context
     */
    protected $context;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var string
     */
    protected $urlPath = 'giftvoucheradmin/giftvoucher/massStatus';


    /**
     * ChangeStatusAction constructor.
     * @param UrlInterface $urlBuilder
     * @param Status $status
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Status $status
    ){
        $this->urlBuilder = $urlBuilder;
        $this->status = $status;
    }

    /**
     * get actions
     *
     * @return array
     */
    public function getActions()
    {
        $actions = [];
        $statusList = $this->status->toOptionArray();
        if(count($statusList) && is_array($statusList)) {
            foreach($statusList as $status) {
                $actions[] = [
                    'type' => $status['value'],
                    'label' => $status['label'],
                    'url' => $this->urlBuilder->getUrl($this->urlPath,
                        [
                            'status' => $status['value']
                        ]),
                    'confirm' => [
                        'title' => __('Change Status'),
                        'message' => __('Are you sure to change selected Giftcode(s) to new status?')
                    ]
                ];
            }
        }
        return $actions;
    }
}