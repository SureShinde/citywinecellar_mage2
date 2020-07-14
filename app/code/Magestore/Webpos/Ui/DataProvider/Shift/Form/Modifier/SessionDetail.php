<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Shift\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Container;

/**
 * Class SessionDetails
 * @package Magestore\Webpos\Ui\DataProvider\Pos\Form\Modifier
 */
class SessionDetail extends \Magestore\Webpos\Ui\DataProvider\Form\Modifier\AbstractModifier
{
    /**
     * @var \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface
     */
    protected $shiftRepository;

    /**
     * @var \Magestore\Webpos\Helper\Shift
     */
    protected $shiftHelper;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var \Magestore\Webpos\Model\Shift\Shift
     */
    protected $currentShift;
    /**
     * @var \Magestore\Webpos\Model\Location\LocationFactory
     */
    protected $locationFactory;

    protected $sortOrder = 10;
    protected $groupContainer = 'session_detail';

    /**
     * ClosedSession constructor.
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        UrlInterface $urlBuilder,
        \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository,
        \Magestore\Webpos\Helper\Shift $shiftHelper,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magestore\Webpos\Model\Location\LocationFactory $locationFactory
    )
    {
        parent::__construct(
            $objectManager, $registry, $request, $urlBuilder
        );
        $this->shiftRepository = $shiftRepository;
        $this->shiftHelper = $shiftHelper;
        $this->localeCurrency = $localeCurrency;
        $this->locationFactory = $locationFactory;
    }

    /**
     * Get current shift
     * 
     * @return \Magestore\Webpos\Model\Shift\Shift|mixed
     */
    public function getCurrentShift()
    {
        if (!$this->currentShift)
            $this->currentShift = $this->registry->registry('current_shift');
        return $this->currentShift;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $this->loadedData = [];
        $shift = $this->getCurrentShift();
        if ($shift) {
            $data = $shift->getData();
            $this->loadedData[$shift->getId()] = $data;
        }
        return $this->loadedData;
    }

    /**
     * modify data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if ($this->request->getParam('id')){
            $meta = array_replace_recursive(
                $meta,
                [
                    $this->groupContainer => [
                        'children' => $this->getChildren(),
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => "",
                                    'autoRender' => true,
                                    'collapsible' => true,
                                    'visible' => true,
                                    'opened' => true,
                                    'componentType' => Form\Fieldset::NAME,
                                    'sortOrder' => $this->sortOrder
                                ],
                            ],
                        ],
                    ],
                ]
            );
            return $meta;
        } else {
            return $meta;
        }
    }

    /**
     * Retrieve child meta configuration
     *
     * @return array
     */
    public function getChildren()
    {
        $children = [
            'current_session_form' => $this->getCurrentSessionForm(),
        ];
        return $children;
    }

    public function getCurrentSessionForm()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'sortOrder' => 10,

                    ],
                ],
            ],
            'children' => [
                /*'html_content' => [
                    'arguments' => [
                        'data' => [
                            'type' => 'html_content',
                            'name' => 'html_content',
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/form/components/html',
                                'content' => \Magento\Framework\App\ObjectManager::getInstance()
                                    ->create('Magestore\Webpos\Block\Adminhtml\Pos\Tab\SessionDetail')
                                    ->toHtml()
                            ]
                        ]
                    ]
                ],*/
                'details' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magestore_Webpos/js/view/pos/session',
                                'session_data' => $this->getShiftData(),
                                'currency_data' => $this->getCurrencyData()
                            ],
                        ],
                    ],
                    'children' => [
                        'no_session' => $this->getTemplateDetail('no-session'),
                        'details' => $this->getTemplateDetail('detail'),
                        'cash_transactions' => $this->getTemplateDetail('cash-transactions'),
                        'report_session' => $this->getTemplateDetail('report-session')
                    ]
                ]
            ]
        ];
    }

    public function getTemplateDetail($fileName)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'component' => 'Magestore_Webpos/js/view/pos/session/' . $fileName,
                    ],
                ],
            ]
        ];
    }

    /**
     * @return string
     */
    public function getShiftData()
    {
        $shift = $this->getCurrentShift();
        $result = $this->shiftHelper->getShiftDataForAdmin($shift);
        $result["refresh_url"] = $this->urlBuilder->getUrl(
            "*/pos/getShift",
            ['id' => $shift->getShiftId()]
        );
        $location = $this->locationFactory->create()->load($shift->getLocationId());
        if ($location->getId()){
            $addressArray = [];
            if ($location->getStreet()){
                $addressArray[] = $location->getStreet();
            }
            if ($location->getRegion()){
                $addressArray[] = $location->getRegion();
            }
            if ($location->getCity()){
                $addressArray[] = $location->getCity();
            }
            if ($location->getPostcode()){
                $addressArray[] = $location->getPostcode();
            }
            if ($location->getCountry()){
                $addressArray[] = $location->getCountry();
            }
            $result['location_address'] = implode(', ', $addressArray);
        }
        return \Zend_Json::encode($result);
    }

    /**
     * Get currency data
     * 
     * @return string
     */
    public function getCurrencyData()
    {
        $output = [];
        $shift = $this->getCurrentShift();
        $currentCurrency = $this->localeCurrency->getCurrency($shift->getShiftCurrencyCode());
        $baseCurrency = $this->localeCurrency->getCurrency($shift->getBaseCurrencyCode());
        /** @var \Magento\Framework\Locale\FormatInterface $localeFormat */
        $localeFormat = $this->objectManager->get('Magento\Framework\Locale\FormatInterface');
        $output['currentCurrencyCode'] = $shift->getShiftCurrencyCode();
        $output['baseCurrencyCode'] = $shift->getBaseCurrencyCode();
        $output['currentCurrencySymbol'] = $currentCurrency->getSymbol();
        $output['baseCurrencySymbol'] = $baseCurrency->getSymbol();
        $output['priceFormat'] = $localeFormat->getPriceFormat(null, $shift->getShiftCurrencyCode());
        $output['basePriceFormat'] = $localeFormat->getPriceFormat(null, $shift->getBaseCurrencyCode());
        return \Zend_Json::encode($output);
    }
}