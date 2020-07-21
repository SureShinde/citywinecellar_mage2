<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Email;

class Expiring extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'email/expiring.phtml';

    /**
     * @return string
     */
    public function getExpirationString()
    {
        return $this->getData('expiration_string');
    }

    /**
     * @return array
     */
    public function getExpirationRows()
    {
        return $this->getData('deadlines') ?: [];
    }

    public function getFilledString($params)
    {
        $string = $this->getExpirationString();

        $string = str_replace('$amount', (float) $params['points'], $string);
        $string = str_replace('$earn_date', $params['earn_date'], $string);
        $string = str_replace('$days_left', $params['days_left'], $string);
        $string = str_replace('$expiration_date', $params['expiration_date'], $string);

        return $string;
    }
}
