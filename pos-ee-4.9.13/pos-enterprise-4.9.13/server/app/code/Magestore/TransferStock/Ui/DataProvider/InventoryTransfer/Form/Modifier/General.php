<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\TransferStock\Ui\DataProvider\InventoryTransfer\Form\Modifier;

use Magento\Ui\Component\Form\Field;

/**
 * Class Related
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class General extends \Magestore\TransferStock\Ui\DataProvider\Form\Modifier\AbstractModifier
{

    protected $_opened = false;
    protected $_groupContainer = 'general_information';
    protected $_groupLabel = 'General Information';
    protected $_sortOrder = 100;

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $this->loadedData = [];
        return $this->loadedData;
    }

    /**
     * get opened
     *
     * @param
     * @return
     */
    public function getOpened()
    {
        return true;
    }

    /**
     * get opened
     *
     * @param
     * @return
     */
    public function getSortOrder()
    {
        return '5';
    }

    /**
     * get is required
     *
     * @param
     * @return boolean
     */
    public function getIsRequired()
    {
        return false;
    }

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
     * @param $meta
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareMeta($meta)
    {
        $meta = array_replace_recursive($meta,
            $this->getFieldsMap()
        );

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                $this->_groupContainer => [
                    'children' => $this->getGeneralChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->getGroupLabel()),
                                'collapsible' => $this->getCollapsible(),
                                'dataScope' => 'data',
                                'visible' => $this->getVisible(),
                                'opened' => $this->getOpened(),
                                'componentType' => 'fieldset',
                                'sortOrder' => $this->getSortOrder()
                            ],
                        ],
                    ],
                ],
            ]
        );
        $meta = $this->prepareMeta($meta);
        return $meta;
    }

    /**
     * Retrieve child meta configuration
     *
     * @return array
     */
    public function getGeneralChildren()
    {
        $children = [
            'reason' => $this->getReasonField()
        ];
        return $children;
    }
    /**
     * Reason field
     *
     * @return array
     */
    public function getReasonField()
    {
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'field',
                        'dataType' => 'string',
                        'formElement' => 'textarea',
                        'elementTmpl' => $this->getModifyTmpl('textarea'),
                        'required' => $this->getIsRequired(),
                        'label' =>__('Reason'),
                        'sortOrder' => 50,
                        'validation' => [
                        ],
                    ],
                ],
            ],
        ];
        return $field;
    }


    /**
     * @return array
     */
    public function getFieldsMap()
    {
        return [
            'general_information' =>
                [
                    'reason',
                ],
        ];
    }
}
