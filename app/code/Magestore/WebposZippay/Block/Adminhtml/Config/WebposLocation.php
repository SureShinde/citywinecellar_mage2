<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Block\Adminhtml\Config;
class WebposLocation extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    public $locationRepository;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        \Magento\Backend\Block\Context $context,
        array $data = array()
    )
    {
        $this->registry = $registry;
        $this->locationRepository = $locationRepository;
        parent::__construct($context, $data);
    }


    /**
     * @return array
     */
    public function toOptionArray()
    {
        $allLocation = $this->locationRepository->getAllLocation();
        $allLocationArray = [];
        foreach ($allLocation as $location) {
            $allLocationArray[] = ['label' => $location->getName(), 'value' => $location->getLocationId()];
        }
        return $allLocationArray;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $allLocation = $this->locationRepository->getAllLocation();
        $allLocationArray = [];
        foreach ($allLocation as $location) {
            $allLocationArray[$location->getLocationId()] = $location->getName();
        }
        return $allLocationArray;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }


    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $options = $this->registry->registry('webpos_location_options');

            if (!$options) {
                $options = $this->getOptionArray();
                $this->registry->register('webpos_location_options', $options);
            }

            foreach ($options as $webposLocationId => $webposLocationName) {
                $this->addOption($webposLocationId, addslashes($webposLocationName));
            }

        }
        return parent::_toHtml();
    }

}