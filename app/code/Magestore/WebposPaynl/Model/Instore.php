<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposPaynl\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magestore\WebposPaynl\Model\Config;

/**
 * Description of Instore
 */
class Instore extends \Magestore\WebposPaynl\Model\Paymentmethod\Paymentmethod // phpstan:ignore
{
    protected $_code = 'paynl_payment_instore';

    /**
     * StartTransaction
     *
     * @param Order $quote
     * @param float $total
     * @param string $currency
     * @param int $bankId
     * @return mixed
     */
    public function startTransaction($quote, $total, $currency, $bankId)
    {
        $transaction = $this->doStartTransaction($quote, $total, $currency, $bankId);
        $bankId = '';
        $instorePayment = \Paynl\Instore::payment( // phpstan:ignore
            [
                'transactionId' => $transaction->getTransactionId(),
                'terminalId' => $bankId
            ]
        );

        return $instorePayment->getRedirectUrl();
    }

    /**
     * GetBanks
     *
     * @return array|mixed
     */
    public function getBanks()
    {
        $cache = $this->getCache();
        $cacheName = 'paynl_terminals_' . $this->getPaymentOptionId();
        $banksJson = $cache->load($cacheName);
        if ($banksJson) {
            $banks = json_decode($banksJson);
        } else {
            $banks = [];
            try {
                $config = new Config($this->_scopeConfig);

                $config->configureSDK();

                $terminals = \Paynl\Instore::getAllTerminals(); // phpstan:ignore
                $terminals = $terminals->getList();

                foreach ($terminals as $terminal) {
                    $terminal['visibleName'] = $terminal['name'];
                    array_push($banks, $terminal);
                }
                $cache->save(json_encode($banks), $cacheName);
            } catch (\Paynl\Error\Error $e) { // phpstan:ignore
                // Probably instore is not activated, no terminals present
                \Magento\Framework\App\ObjectManager::getInstance()
                    ->get(\Psr\Log\LoggerInterface::class)
                    ->info($e->getMessage());
            }
        }
        array_unshift(
            $banks,
            [
                'id' => '',
                'name' => __('Choose the pin terminal'),
                'visibleName' => __('Choose the pin terminal')
            ]
        );
        return $banks;
    }

    /**
     * GetCache
     *
     * @return \Magento\Framework\App\CacheInterface
     */
    private function getCache()
    {
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\CacheInterface $cache */
        $cache = $om->get(\Magento\Framework\App\CacheInterface::class);
        return $cache;
    }
}
