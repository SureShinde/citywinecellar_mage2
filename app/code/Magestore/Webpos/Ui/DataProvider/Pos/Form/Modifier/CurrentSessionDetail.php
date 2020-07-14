<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Pos\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Container;

/**
 * Class SessionDetails
 * @package Magestore\Webpos\Ui\DataProvider\Pos\Form\Modifier
 */
class CurrentSessionDetail extends AbstractModifier
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
     * @var \Magestore\Webpos\Model\Pos\Pos
     */
    protected $currentPos;

    protected $groupLabel = 'Current Session Detail';
    protected $sortOrder = 30;
    protected $groupContainer = 'current_session_detail';

    /**
     * @var \Magestore\Webpos\Model\Config\ConfigRepository
     */
    protected $configRepository;

    /**
     * CurrentSessionDetail constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository
     * @param \Magestore\Webpos\Helper\Shift $shiftHelper
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magestore\Webpos\Model\Config\ConfigRepository $configRepository
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        UrlInterface $urlBuilder,
        \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository,
        \Magestore\Webpos\Helper\Shift $shiftHelper,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magestore\Webpos\Model\Config\ConfigRepository $configRepository
    )
    {
        parent::__construct(
            $objectManager, $registry, $request, $urlBuilder
        );
        $this->shiftRepository = $shiftRepository;
        $this->shiftHelper = $shiftHelper;
        $this->localeCurrency = $localeCurrency;
        $this->configRepository = $configRepository;
    }

    /**
     * Get current pos
     *
     * @return \Magestore\Webpos\Model\Pos\Pos`
     */
    public function getCurrentPos()
    {
        if (!$this->currentPos)
            $this->currentPos = $this->registry->registry('current_pos');
        return $this->currentPos;
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
        $pos = $this->getCurrentPos();
        if ($pos) {
            $data = $pos->getData();
            $this->loadedData[$pos->getId()] = $data;
        }
        return $this->loadedData;
    }

    /**
     * get visible
     *
     * @param
     * @return
     */
    public function getVisible()
    {
        $pos = $this->getCurrentPos();
        if (!$pos || !$pos->getId())
            return false;
        return true;
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
        if ($this->request->getParam('id')) {
            $meta = array_replace_recursive(
                $meta,
                [
                    $this->groupContainer => [
                        'children' => $this->getChildren(),
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __($this->groupLabel),
                                    'autoRender' => true,
                                    'collapsible' => true,
                                    'visible' => $this->getVisible(),
                                    'opened' => false,
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
                                'currency_data' => $this->getCurrencyData(),
                                'denomination_data' => $this->getDenominationData(),
                            ],
                        ],
                    ],
                    'children' => [
                        'no_session' => $this->getTemplateDetail('no-session'),
                        'details' => $this->getTemplateDetail('detail'),
                        'cash_transactions' => $this->getTemplateDetail('cash-transactions'),
                        'cash_adjustment' => $this->getTemplateDetail('cash-adjustment'),
                        'closing_session' => $this->getTemplateDetail('closing-session'),
                        'report_session' => $this->getTemplateDetail('report-session'),
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
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftInterface|null
     */
    public function getCurrentShift()
    {
        $currentShift = $this->registry->registry('current_shift');
        if ($currentShift && $currentShift->getId()) {
            return $currentShift;
        }
        $pos = $this->getCurrentPos();
        if ($pos && $pos->getId()) {
            $currentShift = $this->shiftRepository->getCurrentShiftByPosId($pos->getId());
            $this->registry->register('current_shift', $currentShift, true);
            return $currentShift;
        }
        return null;
    }

    /**
     * @return string
     */
    public function getShiftData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $FormKey = $objectManager->get('Magento\Framework\Data\Form\FormKey');
        $FormKey->getFormKey();

        $shift = $this->getCurrentShift();
        if (!$shift || !$shift->getId()) {
            $result = [
                "error" => __('There is no session in progress.')
            ];
        } else {
            $result = $this->shiftHelper->getShiftDataForAdmin($shift);

            $location = $objectManager->create('Magestore\Webpos\Model\Location\Location')->load($shift->getLocationId());
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
        }
        /** refresh action */
        $result["refresh_url"] = $this->urlBuilder->getUrl(
            "*/*/getShift",
            ['pos_id' => $this->getCurrentPos()->getPosId()]
        );
        /** save Cash adjustment action */
        $result["save_url"] = $this->urlBuilder->getUrl(
            "*/*/saveShift",
            ['form_key' => $FormKey->getFormKey()]
        );
        /** save close shift action */
        $result["save_close_shift_url"] = $this->urlBuilder->getUrl(
            "*/*/saveCloseShift",
            ['form_key' => $FormKey->getFormKey()]
        );

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
        if($shift && $shift->getShiftId()) {
            $currentCurrency = $this->localeCurrency->getCurrency($shift->getShiftCurrencyCode());
            $baseCurrency = $this->localeCurrency->getCurrency($shift->getBaseCurrencyCode());
            $currentCurrencyCode = $shift->getShiftCurrencyCode();
            $baseCurrencyCode = $shift->getBaseCurrencyCode();
            $currentCurrencySymbol = $currentCurrency->getSymbol();
            $baseCurrencySymbol = $baseCurrency->getSymbol();
        } else {
            /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
            $storeManager = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $currentCurrency = $storeManager->getStore()->getCurrentCurrency();
            $baseCurrency = $storeManager->getStore()->getBaseCurrency();
            $currentCurrencyCode = $currentCurrency->getCode();
            $baseCurrencyCode = $baseCurrency->getCode();
            $currentCurrencySymbol = $currentCurrency->getCurrencySymbol();
            $baseCurrencySymbol = $baseCurrency->getCurrencySymbol();
        }
        /** @var \Magento\Framework\Locale\FormatInterface $localeFormat */
        $localeFormat = $this->objectManager->get('Magento\Framework\Locale\FormatInterface');
        $output['currentCurrencyCode'] = $currentCurrencyCode;
        $output['baseCurrencyCode'] = $baseCurrencyCode;
        $output['currentCurrencySymbol'] = $currentCurrencySymbol;
        $output['baseCurrencySymbol'] = $baseCurrencySymbol;
        $output['priceFormat'] = $localeFormat->getPriceFormat(null, $currentCurrencyCode);
        $output['basePriceFormat'] = $localeFormat->getPriceFormat(null, $baseCurrencyCode);
        return \Zend_Json::encode($output);
    }

    /**
     * @return array
     */
    public function getDenominationData(){
        $output = array();
        $denominations = $this->configRepository->getDenominations();
        if($denominations) {
            foreach ($denominations as $denomination) {
                $output['denomination'][] = $denomination->getData();
            }
        }
        $denomination = \Zend_Json::encode($output);
        return $denomination;
    }
}