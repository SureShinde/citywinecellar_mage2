<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Controller\Adminhtml\HistoricalStock;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class MassExport
 * @package Magestore\ReportSuccess\Controller\Adminhtml\HistoricalStock
 */
class MassExport extends \Magestore\ReportSuccess\Controller\Adminhtml\Inventory\Download
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {

        $backupIds = $this->getRequest()->getParam('selected');

        $collection = $this->_objectManager->create('Magestore\ReportSuccess\Model\Fs\Collection');

        if ($backupIds) {
            $collection->addFieldToFilter('name', ['in' => $backupIds]);
        } else {
            $filters = $this->getRequest()->getParam('filters');
            foreach ($filters as $key => $value) {
                if ($key!= 'placeholder'){
                    $collection->addFieldToFilter($key, $value);
                }
            }
        }

        $collection->load();
        $items = $collection->toArray();
        $data = $items['items'];
        $directoryName = 'Historical Stock Report_'. $this->localeDate->date()->format('YmdHis');
        $this->varDirectory->create($directoryName);
        $this->varDirectory->create('compress');
        foreach ($data as $item){
            $this->archive->unpack($this->varDirectory->getAbsolutePath('historical_stock/'.$item['name']), $this->varDirectory->getAbsolutePath($directoryName));
        }
        $this->archive->pack($this->varDirectory->getAbsolutePath($directoryName), $this->varDirectory->getAbsolutePath('compress/'.$directoryName.'.tgz'));
        $this->varDirectory->delete($directoryName);

        $this->fileFactory->create(
            $directoryName.'.tgz',
            [
                'type' => 'filename',
                'value' => 'compress/'.$directoryName.'.tgz',
                'rm' => true
            ],
            DirectoryList::VAR_DIR,
            'application/octet-stream',
            $this->getReportSize($directoryName.'.tgz')
        );
    }

    /**
     * Get file path.
     *
     * @param string $filename
     * @return string
     */
    public function getFilePath($filename)
    {
        return $this->varDirectory->getRelativePath('compress/' . $filename);
    }
}