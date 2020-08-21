<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Email;

/**
 * Model Email TransportBuilder
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * Attach File
     *
     * @param string $file
     * @param string $name
     * @return $this
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function attachFile($file, $name)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Filesystem\DriverInterface $driverFile */
        $driverFile = $om->get(\Magento\Framework\Filesystem\DriverInterface::class);
        /** @var \Magento\Framework\Filesystem\Io\File $ioFile */
        $ioFile = $om->get(\Magento\Framework\Filesystem\Io\File::class);
        $fileInfo = $ioFile->getPathInfo($name);
        if (!empty($file) && $driverFile->isExists($file)) {
            $this->message
                ->createAttachment(
                    $driverFile->fileGetContents($file),
                    \Zend_Mime::TYPE_OCTETSTREAM,
                    \Zend_Mime::DISPOSITION_ATTACHMENT,
                    \Zend_Mime::ENCODING_BASE64,
                    $fileInfo['basename']
                );
        }
        return $this;
    }
}
