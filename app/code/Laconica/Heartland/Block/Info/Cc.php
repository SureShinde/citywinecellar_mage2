<?php

namespace Laconica\Heartland\Block\Info;

class Cc extends \Magento\Payment\Block\Info\Cc
{
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $data = $transport->getData();
        if ($this->getInfo()->getData('cc_cid_status')) {
            $cvvArr = explode(':', $this->getInfo()->getData('cc_cid_status'));
            $data[(string)__('CVV Status')] = end($cvvArr);
        }
        if ($this->getInfo()->getData('cc_avs_status')) {
            $avsArr = explode(':', $this->getInfo()->getData('cc_avs_status'));
            $data[(string)__('AVS Status')] = end($avsArr);
        }
        return $transport->setData(array_merge($data, $transport->getData()));
    }
}