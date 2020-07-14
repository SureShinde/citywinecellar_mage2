<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 07/06/2016
 * Time: 09:21
 */

namespace Magestore\Webpos\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface MessageInterface extends ExtensibleDataInterface
{
    /*#@+
     * Constants defined for keys of data array
     */
    const MESSAGE = "message";



    /**
     *  message
     * @return string|null
     */
    public function getMessage();


    /**
     * Set message
     *
     * @param string $message
     * @return MessageInterface
     */
    public function setMessage($message);

}