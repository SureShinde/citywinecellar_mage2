<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Block\Adminhtml\Pos\Tab;

/**
 * Class SessionDetail
 * @package Magestore\Webpos\Block\Adminhtml\Pos\Tab
 */
class SessionDetail extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magestore_Webpos::pos/session_detail.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface
     */
    protected $shiftRepository;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $localeFormat;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magestore\Webpos\Helper\Shift
     */
    protected $shiftHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface $shiftRepository,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magestore\Webpos\Helper\Shift $shiftHelper,
        array $data = []
    )
    {
        $this->registry = $registry;
        $this->shiftRepository = $shiftRepository;
        $this->localeFormat = $localeFormat;
        $this->storeManager = $context->getStoreManager();
        $this->shiftHelper = $shiftHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magestore\Webpos\Model\Pos\Pos|null
     */
    public function getCurrentPos()
    {
        return $this->registry->registry('current_pos');
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
            return $this->shiftRepository->getCurrentShiftByPosId($pos->getId());
        }
        return null;
    }

    /**
     * @return string
     */
    public function getShiftData()
    {
        $shift = $this->getCurrentShift();
        if (!$shift || !$shift->getId()) {
            $result = [
                "error" => __('There is no session in progress.'),
                "refresh_url" => $this->getUrl("*/*/getShift", ['pos_id' => $this->getCurrentPos()->getPosId()])
            ];
        } else {
            $result = $this->shiftHelper->getShiftData($shift);
        }
        return \Zend_Json::encode($result);
    }

    /**
     * Retrieve webpos configuration
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getPOSConfig()
    {
        $output = [];
        $baseCurrency = $this->storeManager->getStore()->getBaseCurrency();
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency();
        $output['priceFormat'] = $this->localeFormat->getPriceFormat(null, $currentCurrency->getCode());
        $output['basePriceFormat'] = $this->localeFormat->getPriceFormat(null, $baseCurrency->getCode());
        $output['currentCurrencyCode'] = $currentCurrency->getCode();
        $output['baseCurrencyCode'] = $baseCurrency->getCode();
        $output['currentCurrencySymbol'] = $currentCurrency->getCurrencySymbol();
        return $output;
    }
}