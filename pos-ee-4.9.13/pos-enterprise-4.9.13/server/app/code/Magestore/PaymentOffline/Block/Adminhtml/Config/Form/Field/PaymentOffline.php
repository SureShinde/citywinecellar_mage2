<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Magestore\PaymentOffline\Block\Adminhtml\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;

class PaymentOffline extends Field
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('config/form/field/payment_offline.phtml');
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     *
     */
    public function getDefaultTemplate() {
        return preg_replace("/[\n\r]/","",$this->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('Magestore_PaymentOffline::config/form/field/default_template.phtml')
            ->toHtml());
    }

    /**
     * @return mixed
     */
    public function getTemplateUrl()
    {
        return $this->getUrl('paymentofflineadmin/paymentOffline/getTemplate');
    }

    /**
     * @return mixed
     */
    public function getExistedPaymentOffline()
    {
        //Get Object Manager Instance
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magestore\PaymentOffline\Service\PaymentOfflineService $paymentOfflineService */
        $paymentOfflineService = $objectManager->create('Magestore\PaymentOffline\Service\PaymentOfflineService');
        return $paymentOfflineService->getExistedPaymentOffline();
    }

    public function removePaymentUrl()
    {
        return $this->getUrl('paymentofflineadmin/paymentOffline/removePayment');
    }
}
