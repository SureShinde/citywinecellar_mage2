<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\ResourceModel\Staff\Staff\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 * @package Magestore\Appadmin\Model\ResourceModel\Staff\Staff\Grid
 * @category    Magestore
 * @package     Magestore_Appadmin
 * @module      Appadmin
 * @author      Magestore Developer
 */
class Collection extends SearchResult
{


    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    /**
     * @var \Magestore\Webpos\Model\Source\Adminhtml\Location
     */
    protected $location;
    /**
     * @var \Magestore\Appadmin\Model\Source\Adminhtml\Role
     */
    protected $role;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Collection constructor.
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @param \Magestore\Appadmin\Model\Source\Adminhtml\Role $role
     * @param \Magestore\Webpos\Model\Source\Adminhtml\Location $location
     * @param \Magento\Framework\App\RequestInterface $request
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable,
        $resourceModel,
        \Magestore\Appadmin\Model\Source\Adminhtml\Role $role,
        \Magestore\Webpos\Model\Source\Adminhtml\Location $location,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->role = $role;
        $this->location = $location;
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }


    public function getData()
    {
        $data = parent::getData();
        if (($this->request->getActionName() == 'gridToCsv') || ($this->request->getActionName() == 'gridToXml')) {
            $options = array(
                self::STATUS_ENABLED => __('Enabled'),
                self::STATUS_DISABLED => __('Disabled')
            );
            $locationOptions = $this->location->getOptionArray();
            $roleOptions = $this->role->getOptionArray();
            foreach ($data as &$item) {
                if ($item['status']) {
                    $item['status'] = $options[$item['status']];
                }
                if ($item['location_ids']) {
                    $locationArray = explode(',', $item['location_ids']);
                    $locationNameArray = array();
                    foreach ($locationArray as $locationId) {
                        if (isset($locationOptions[$locationId])) {
                            $locationName = $locationOptions[$locationId];
                            $locationNameArray[] = $locationName;
                        }
                    }
                    $item['location_ids'] = implode(',', $locationNameArray);
                }
                if ($item['role_id']) {
                    $item['role_id'] = $roleOptions[$item['role_id']];
                }
            }
        }
        foreach ($data as &$staff) {
            if (!$staff['role_id']) {
                $staff['role_id'] = 0;
            }
        }
        return $data;
    }
}