<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\DataProvider;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class PrintActions
 * @package Magestore\OrderSuccess\Ui\DataProvider
 */
class PrintActions
{
    const NO_SHIP_PRINT_ACTION = [
        'pickrequest',
        'needship',
        'needverify'
    ];

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    protected $printAllUrl = 'sales/order/pdfdocs';

    /**
     * @var string
     */
    protected $printInvoiceUrl = 'sales/order/pdfinvoices';

    /**
     * @var string
     */
    protected $printShipmentUrl = 'sales/order/pdfshipments';

    /**
     * @var string
     */
    protected $printCreditmemosUrl = 'sales/order/pdfcreditmemos';

    /**
     * @var string
     */
    protected $printShippingLabelUrl = 'ordersuccess/order/massPrintShippingLabel';

    /**
     * PrintActions constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder,
        RequestInterface $request
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
    }

    /**
     * get actions
     *
     * @return array
     */
    public function getActions($posistion)
    {
        $actions = [
            [
                'type' => 'pdfinvoices_order',
                'label' => __('Print Invoices'),
                'url' => $this->urlBuilder->getUrl($this->printInvoiceUrl,
                    [
                        'order_position' => $posistion
                    ]),
            ],
            [
                'type' => 'pdfcreditmemos_order',
                'label' => __('Print Credit Memos'),
                'url' => $this->urlBuilder->getUrl($this->printCreditmemosUrl,
                    [
                        'order_position' => $posistion
                    ]),
            ]
        ];
        if (!in_array(strtolower($this->request->getControllerName()), self::NO_SHIP_PRINT_ACTION)) {
            $actions = array_merge($actions, [
                [
                    'type' => 'pdfshipments_order',
                    'label' => __('Print Packing Slips'),
                    'url' => $this->urlBuilder->getUrl($this->printShipmentUrl,
                        [
                            'order_position' => $posistion
                        ]),
                ],
                [
                    'type' => 'print_shipping_label',
                    'label' => __('Print Shipping Labels'),
                    'url' => $this->urlBuilder->getUrl($this->printShippingLabelUrl,
                        [
                            'order_position' => $posistion
                        ]),
                ],
            ]);
        }
        $actions = array_merge($actions, [
            [
            'type' => 'pdfdocs_order',
            'label' => __('Print All'),
            'url' => $this->urlBuilder->getUrl($this->printAllUrl,
                [
                    'order_position' => $posistion
                ]),
            ]
        ]);
        return $actions;
    }
}