<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Ui\Component\Listing\Columns\InventoryTransfer\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ReceiveActions
 * @package Magestore\TransferStock\Ui\Component\Listing\Columns\InventoryTransfer\Columns
 */
class ReceiveActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;


    /**
     * @var string
     */
    protected $editActionLabel = 'View';

    /**
     * @var string
     */
    protected $exportActionLabel = 'Export';

    /**
     * @var string
     */
    protected $_viewUrl = 'transferstock/inventorytransfer_receive/view';

    /**
     * @var string
     */
    protected $_exportUrl = 'transferstock/inventorytransfer_receive/export';

    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['receive_id'])) {
                    $item[$name]['view'] = [
                        'href' => $this->urlBuilder->getUrl($this->_viewUrl, ['id' => $item['receive_id']]),
                        'label' => __($this->editActionLabel)
                    ];
                    $item[$name]['export'] = [
                        'href' => $this->urlBuilder->getUrl($this->_exportUrl, ['id' => $item['receive_id']]),
                        'label' => __($this->exportActionLabel)
                    ];
                }
            }
        }

        return $dataSource;
    }
}
