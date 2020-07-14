<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Staff\Logout;

/**
 * @api
 */
/**
 * Interface LogoutResultInterface
 * @package Magestore\Webpos\Api\Data\Staff\Logout
 */
interface LogoutResultInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     *
     */
    const MESSAGE = 'message';

    /**
     * Get message
     *
     * @api
     * @return string
     */
    public function getMessage();

    /**
     * Set message
     *
     * @api
     * @param string $message
     * @return LogoutResultInterface
     */
    public function setMessage($message);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Staff\Logout\LogoutResultExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Staff\Logout\LogoutResultExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Staff\Logout\LogoutResultExtensionInterface $extensionAttributes
    );


}
