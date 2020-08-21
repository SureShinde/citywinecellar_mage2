<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Ui\DataProvider\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Form;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier as CoreAbstractModifier;

/**
 * Class AbstractModifier
 *
 * Modifier Abstract
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class AbstractModifier extends CoreAbstractModifier implements ModifierInterface
{
    /**
     * Collapsible
     *
     * @var string
     */
    protected $_collapsible = true;

    /**
     * Group Container
     *
     * @var string
     */
    protected $_visible = true;

    /**
     * Group Container
     *
     * @var string
     */
    protected $_opened = true;

    /**
     * Sort Sales
     *
     * @var string
     */
    protected $_sortOrder = '1';

    /**
     * Modifier Config
     *
     * @var array
     */
    protected $_modifierConfig = [];

    /**
     * Group Label
     *
     * @var string
     */
    protected $_groupLabel;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface
     */
    protected $adjustStockManagement;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * is required
     *
     * @var boolean
     */
    protected $isRequried = true;

    const TMPL_INPUT = 'ui/form/element/input';
    const TMPL_TEXTAREA = 'ui/form/element/textarea';
    const TMPL_SELECT = 'ui/form/element/select';
    const TMPL_DATE = 'ui/form/element/date';
    const TMPL_TEXT_LABEL = 'Magestore_AdjustStock/form/element/text';
    const TMPL_TEXTAREA_LABEL = 'Magestore_AdjustStock/form/element/textarea';
    const TMPL_SELECT_LABEL = 'Magestore_AdjustStock/form/element/selectlabel';

    /**
     * AbstractModifier constructor.
     *
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface $adjustStockManagement
     * @param array $modifierConfig
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface $adjustStockManagement,
        array $modifierConfig = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->adjustStockManagement = $adjustStockManagement;
        $this->_modifierConfig = array_replace_recursive($this->_modifierConfig, $modifierConfig);
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     */
    public function setVisible($visible)
    {
        $this->_visible = $visible;
    }

    /**
     * Get visible
     *
     * @return int|bool
     */
    public function getVisible()
    {
        return $this->_visible;
    }

    /**
     * Get visible
     *
     * @return bool|int
     */
    public function getVisibleImage()
    {
        return $this->adjustStockManagement->isShowThumbnail();
    }

    /**
     * Set opened
     *
     * @param boolean $opened
     */
    public function setOpened($opened)
    {
        $this->_opened = $opened;
    }

    /**
     * Get opened
     *
     * @return boolean|int
     */
    public function getOpened()
    {
        return $this->_opened;
    }

    /**
     * Set collapsible
     *
     * @param boolean $collapsible
     */
    public function setCollapsible($collapsible)
    {
        $this->_collapsible = $collapsible;
    }

    /**
     * Get collapsible
     *
     * @return boolean|int
     */
    public function getCollapsible()
    {
        return $this->_collapsible;
    }

    /**
     * Set group label
     *
     * @param boolean $groupLabel
     */
    public function setGroupLabel($groupLabel)
    {
        $this->_groupLabel = $groupLabel;
    }

    /**
     * Get group label
     *
     * @return string
     */
    public function getGroupLabel()
    {
        return $this->_groupLabel;
    }

    /**
     * Set sort order
     *
     * @param int $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->_sortOrder = $sortOrder;
    }

    /**
     * Get is required
     *
     * @return boolean|int
     */
    public function getIsRequired()
    {
        return $this->isRequried;
    }

    /**
     * Set is required
     *
     * @param boolean $isRequired
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequried = $isRequired;
    }

    /**
     * Get sort order
     *
     * @return int|string
     */
    public function getSortOrder()
    {
        return $this->_sortOrder;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get modify tmpl
     *
     * @param string $type
     * @return string
     */
    public function getModifyTmpl($type)
    {
        switch ($type) {
            case 'input':
                return static::TMPL_INPUT;
            case 'date':
                return static::TMPL_DATE;
            case 'textarea':
                return static::TMPL_TEXTAREA;
            case 'select':
                return static::TMPL_SELECT;
            default:
                return static::TMPL_INPUT;
        }
    }

    /**
     * Returns text column configuration for the dynamic grid
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    public function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Form\Field::NAME,
                        'formElement' => Form\Element\Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'dataType' => Form\Element\DataType\Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
        return $column;
    }
}
