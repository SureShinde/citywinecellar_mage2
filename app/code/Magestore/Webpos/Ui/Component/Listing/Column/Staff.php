<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Staff
 * @package Magestore\Webpos\Ui\Component\Listing\Column
 */
class Staff implements OptionSourceInterface
{
    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\Collection
     */
    protected $staffCollection;
    /**
     * Construct
     *
     * @param \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\Collection $staffCollection
     */
    public function __construct(
        \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\Collection $staffCollection
    ) {
        $this->staffCollection = $staffCollection;
    }
    /**
     * Get product type labels array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $staffs = $this->staffCollection
            ->setOrder(\Magestore\Appadmin\Api\Data\Staff\StaffInterface::NAME, 'ASC')
            ->loadData();
        if (count($staffs) > 0) {
            foreach ($staffs as $staff) {
                $options[$staff->getId()] = (string) $staff->getUsername();
            }
        }
        return $options;
    }
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
    /**
     * Get product type labels array for option element
     *
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }
}