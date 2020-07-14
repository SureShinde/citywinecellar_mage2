<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model;

/**
 * Class Backup
 * @package Magestore\ReportSuccess\Model
 */
class Backup extends \Magento\Backup\Model\Backup
{
    /**
     * @var
     */
    protected $fileHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * Backup constructor.
     * @param \Magestore\ReportSuccess\Helper\File $helper
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param array $data
     */
    public function __construct(
        \Magestore\ReportSuccess\Helper\File $helper,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        $data = []
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($helper, $localeResolver, $authSession, $encryptor, $filesystem, $data);
    }

    /**
     * @param string $fileName
     * @param string $filePath
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function load($fileName, $filePath)
    {
        $backupData = $this->_helper->extractDataFromFilename($fileName);

        $this->addData(
            [
                'id' => $filePath . '/' . $fileName,
                'time' => (int)$backupData->getTime(),
                'path' => $filePath,
                'extension' => $backupData->getExtension(),
                'display_name' => $backupData->getDisplayName(),
                'name' => $backupData->getName(),
                'date_object' => $this->dateTime->date(null, $backupData->getTime()),
                'type' => $backupData->getType()
            ]
        );



        return $this;
    }


}