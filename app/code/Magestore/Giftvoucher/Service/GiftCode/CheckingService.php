<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Service\GiftCode;

use Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface;

/**
 * Class CheckingService
 *
 * Gift code checking service
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class CheckingService implements \Magestore\Giftvoucher\Api\GiftCode\CheckingServiceInterface
{
    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magestore\Giftvoucher\Model\Session
     */
    protected $session;

    /**
     * @var \Magestore\Giftvoucher\Model\Giftvoucher
     */
    protected $giftvoucherFactory;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magestore\Giftvoucher\Model\Status
     */
    protected $statusFactory;

    /**
     * CheckingService constructor.
     *
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param \Magestore\Giftvoucher\Model\Session $session
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magestore\Giftvoucher\Model\StatusFactory $statusFactory
     */
    public function __construct(
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magestore\Giftvoucher\Model\Session $session,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magestore\Giftvoucher\Model\StatusFactory $statusFactory
    ) {
        $this->helper = $helper;
        $this->session = $session;
        $this->giftvoucherFactory = $giftvoucherFactory;
        $this->currencyFactory = $currencyFactory;
        $this->statusFactory = $statusFactory;
    }

    /**
     * @inheritDoc
     */
    public function check($code)
    {
        $result = [
            ResponseInterface::ERRORS => [],
            ResponseInterface::SUCCESS => [],
            ResponseInterface::NOTICES => []
        ];
        $max = $this->helper->getGeneralConfig('maximum');
        if ($code) {
            $giftVoucher = $this->giftvoucherFactory->create()->loadByCode($code);
            $codes = $this->session->getCodesInvalid();
            if (!$giftVoucher->getId()) {
                $codes[] = $code;
                $codes = array_unique($codes);
                $this->session->setCodesInvalid($codes);
            }
            if (!$this->helper->isAvailableToCheckCode()) {
                $result[ResponseInterface::ERRORS][] = __(
                    'The maximum number of times to enter the invalid gift codes is %1!',
                    $max
                );
            } else {
                if (!$giftVoucher->getId()) {
                    $errorMessage = __('Invalid gift code. ');
                    if ($max) {
                        $errorMessage .=
                            __(
                                'You have %1 time(s) remaining to check your Gift Card code.',
                                $max - count($codes)
                            );
                    }
                    $result[ResponseInterface::ERRORS][] = $errorMessage;
                }
            }
        } else {
            if (!$this->helper->isAvailableToCheckCode()) {
                $result[ResponseInterface::ERRORS][] = __(
                    'The maximum number of times to enter the invalid gift codes is %1!',
                    $max
                );
            }
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getCodeData($code, $formated = false)
    {
        $data = [
            'code' => '',
            'balance' => '',
            'description' => '',
            'status' => '',
            'expired_at' => '',
        ];
        if ($code) {
            $result = $this->check($code);
            if (empty($result[ResponseInterface::ERRORS])) {
                $giftVoucher = $this->giftvoucherFactory->create()->loadByCode($code);
                $currency = $this->currencyFactory->create()->load($giftVoucher->getCurrency());
                $statusArray = $this->statusFactory->create()->getOptionArray();
                switch ($formated) {
                    case true:
                        $data = [
                            'code' => $this->helper->getHiddenCode($code),
                            'balance' => $currency->format($giftVoucher->getBalance(), [], false),
                            'description' => $giftVoucher->getDescription(),
                            'status' => $statusArray[$giftVoucher->getStatus()],
                            'expired_at' => $this->helper->formatDate($giftVoucher->getExpiredAt(), 'M d, Y')
                        ];
                        break;
                    case false:
                        $data = [
                            'code' => $code,
                            'balance' => $giftVoucher->getBalance(),
                            'description' => $giftVoucher->getDescription(),
                            'status' => $giftVoucher->getStatus(),
                            'expired_at' => $giftVoucher->getExpiredAt()
                        ];
                        break;
                }
            }
        }
        return $data;
    }
}
