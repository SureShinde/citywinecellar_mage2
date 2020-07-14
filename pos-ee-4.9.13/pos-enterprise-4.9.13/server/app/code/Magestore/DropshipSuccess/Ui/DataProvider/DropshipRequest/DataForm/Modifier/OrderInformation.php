<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Ui\DataProvider\DropshipRequest\DataForm\Modifier;

use Magento\Ui\Component\Form;
use Magento\Ui\Component\Form\Field;

/**
 * Data provider for Configurable panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderInformation extends AbstractModifier
{
    /**
     * modify data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        $data = array_replace_recursive(
            $data,
            $this->getData()
        );
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $this->loadedData = [];
//        $supplier = $this->locator->getSupplier();
//        if ($supplier && $supplier->getId()) {
//            $this->loadedData[$supplier->getId()] = $supplier->getData();
//        }
        return $this->loadedData;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            $this->getOrderInformation($meta)
        );
        return $meta;
    }

    /**
     * @param $meta
     * @return mixed
     */
    public function getOrderInformation($meta)
    {
        $meta['order_information']['arguments']['data']['config'] = [
            'label' => __('Sales Information'),
            'collapsible' => true,
            'visible' => true,
            'opened' => true,
            'dataScope' => 'data',
            'componentType' => Form\Fieldset::NAME
        ];
        $meta['order_information']['children'] = $this->getOrderInformationChildren();
        return $meta;
    }

    /**
     * @return array
     */
    public function getOrderInformationChildren()
    {
        $children = [
            'information' => $this->getOrderInformationContainer()
        ];
        return $children;
    }

    /**
     * @param $lable
     * @param $componentType
     * @param $visible
     * @param $dataType
     * @param $formElement
     * @param $validation
     * @param $notice
     * @return array
     */
    public function getOrderInformationContainer()
    {
        $container = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'container',
                        'visible' => true,
                        'dataType' => 'container'
                    ]
                ]
            ],
            'children' => [
                'html_content' => [
                    'arguments' => [
                        'data' => [
                            'type' => 'html_content',
                            'name' => 'html_content',
                            'config' => [
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/html',
                                'content' => \Magento\Framework\App\ObjectManager::getInstance()->create(
                                    'Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab\OrderInformation'
                                )->toHtml()
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $container;
    }
}
