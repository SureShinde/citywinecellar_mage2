<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff\Data\Logout;

/**
 * Class LoginResult
 * @package Magestore\Webpos\Model\Staff\Data\Logout
 */
class LogoutResult extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Staff\Logout\LogoutResultInterface
{

    /**
     * Get message
     *
     * @api
     * @return string
     */
    public function getMessage() {
        return $this->getData(self::MESSAGE);
    }

    /**
     * Set message
     *
     * @api
     * @param string $message
     * @return $this
     */
    public function setMessage($message) {
        return $this->setData(self::MESSAGE, $message);
    }
    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Staff\Logout\LogoutResultExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

}