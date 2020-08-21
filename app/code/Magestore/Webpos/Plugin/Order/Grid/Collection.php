<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Plugin\Order\Grid;

/**
 * Plugin order grid Collection
 */
class Collection
{
    /**
     * @var \Magestore\Webpos\Model\Source\Adminhtml\Location
     */
    protected $locationSource;

    protected $locationSourceOptions;

    /**
     * Collection constructor.
     *
     * @param \Magestore\Webpos\Model\Source\Adminhtml\Location $locationSource
     */
    public function __construct(
        \Magestore\Webpos\Model\Source\Adminhtml\Location $locationSource
    ) {
        $this->locationSource = $locationSource;
    }

    /**
     * After get data
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $subject
     * @param array $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $subject,
        $result
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $requestInterface = $objectManager->get(\Magento\Framework\App\RequestInterface::class);
        if (($requestInterface->getActionName() == 'gridToCsv')
            || ($requestInterface->getActionName() == 'gridToXml')) {
            $options = $this->getLocationSourceOptions();
            $optionsFulfill = ['0' => 'No', '1' => 'Yes'];
            foreach ($result as &$item) {
                if ($item['pos_location_id']) {
                    $item['pos_location_id'] = $options[$item['pos_location_id']];
                }
                if (isset($item['pos_fulfill_online'])) {
                    $item['pos_fulfill_online'] = $optionsFulfill[$item['pos_fulfill_online']];
                }
            }
        }

        return $result;
    }

    /**
     * Get Location Source Options
     *
     * @return array
     */
    public function getLocationSourceOptions()
    {
        if (!$this->locationSourceOptions) {
            $this->locationSourceOptions = $this->locationSource->getOptionArray();
        }
        return $this->locationSourceOptions;
    }
}
