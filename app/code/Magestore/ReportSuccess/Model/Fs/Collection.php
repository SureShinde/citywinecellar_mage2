<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Model\Fs;
/**
 * Class Collection
 * @package Magestore\ReportSuccess\Model\Fs
 */
class Collection extends \Magento\Backup\Model\Fs\Collection
{
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Magento\Backup\Helper\Data $backupData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Backup\Model\Backup $backup
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Magento\Backup\Helper\Data $backupData,
        \Magento\Framework\Filesystem $filesystem,
        \Magestore\ReportSuccess\Model\Backup $backup
    ) {
        parent::__construct($entityFactory, $backupData, $filesystem, $backup);

    }
    /**
     * Folder, where all backups are stored
     *
     * @var string
     */
    protected $_path = 'historical_stock';

    /**
     * Get backup-specific data from model for each row
     *
     * @param string $filename
     * @return array
     */
    public function _generateRow($filename)
    {
        $row = parent::_generateRow($filename);
        foreach ($this->_backup->load(
            $row['basename'],
            $this->_varDirectory->getAbsolutePath($this->_path)
        )->getData() as $key => $value) {
            $row[$key] = $value;
        }

        $row['size'] = $this->_varDirectory->stat($this->_varDirectory->getRelativePath($filename))['size'];
        if (isset($row['display_name']) && $row['display_name'] == '') {
            $row['display_name'] = 'WebSetupWizard';
        }
        $row['id'] = $row['time'] . '_' . $row['type'] . (isset($row['display_name']) ? $row['display_name'] : '');

        return $row;
    }
}
