<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class ConvertToCsv
 *
 * Used to create Convert To Csv
 */
class ConvertToCsv
{
    /**
     * @var DirectoryList
     */
    protected $directory;

    /**
     * @var MetadataProvider
     */
    protected $metadataProvider;

    /**
     * @var int|null
     */
    protected $pageSize = null;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * ConvertToCsv constructor.
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param int $pageSize
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        $pageSize = 200
    ) {
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->metadataProvider = $metadataProvider;
        $this->pageSize = $pageSize;
    }

    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCsvFile()
    {
        $component = $this->filter->getComponent();

        $name = sha1(microtime());
        $file = 'export/' . $component->getName() . $name . '.csv';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();
        $fields = $this->metadataProvider->getFields($component);
        $options = $this->metadataProvider->getOptions();

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $reportHeaders = $this->metadataProvider->getReportHeaders($component);
        if (!empty($reportHeaders)) {
            foreach ($reportHeaders as $reportHeader) {
                $stream->writeCsv($reportHeader);
            }
        }

        $stream->writeCsv($this->metadataProvider->getHeaders($component));
        $i = 1;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize($this->pageSize);
        $totalCount = (int)$dataProvider->getSearchResult()->getTotalCount();
        while ($totalCount > 0) {
            $items = $dataProvider->getSearchResult()->getItems();
            $datasource = new \Magento\Framework\DataObject(
                [
                    'items' => $items
                ]
            );
            if (!$this->metadataProvider->isExportRawValue($component)) {
                $this->renderValues($component, $datasource, $options);
            }
            foreach ($datasource->getItems() as $item) {
                $this->metadataProvider->convertDate($item, $component->getName());
                $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
            }
            $searchCriteria->setCurrentPage(++$i);
            $totalCount = $totalCount - $this->pageSize;
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

    /**
     * Render value
     *
     * @param UiComponentInterface $component
     * @param \Magento\Framework\DataObject $datasource
     * @param mixed $options
     * @return \Magento\Framework\DataObject
     */
    public function renderValues(UiComponentInterface $component, \Magento\Framework\DataObject $datasource, &$options)
    {
        $fieldsComponent = $this->metadataProvider->getFieldsComponent($component);
        $dataSourceData = [
            'data' => [
                'items' => $datasource->getItems()
            ]
        ];
        if (!empty($fieldsComponent)) {
            foreach ($fieldsComponent as $field) {
                $this->metadataProvider->getFieldOptions($field, $options);
                $dataSourceData = $field->prepareDataSource($dataSourceData);
            }
        }
        $datasource->setItems($dataSourceData['data']['items']);
        return $datasource;
    }

    /**
     * Get metadata provider
     *
     * @return MetadataProvider
     */
    public function getMetaDataProvider()
    {
        return $this->metadataProvider;
    }
}
