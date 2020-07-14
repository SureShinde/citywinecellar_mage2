<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Model;

use Magestore\BarcodeSuccess\Api\Data\BarcodeTemplateInterface;

class Template  extends \Magento\Framework\Model\AbstractModel implements BarcodeTemplateInterface
{
    /**
     * @var \Magestore\BarcodeSuccess\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magestore\BarcodeSuccess\Model\Source\TemplateType
     */
    protected $sourceType;

    /**
     * Template constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\BarcodeSuccess\Helper\Data $helper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\BarcodeSuccess\Helper\Data $helper,
        \Magestore\BarcodeSuccess\Model\Source\TemplateType $sourceType,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->helper = $helper;
        $this->sourceType = $sourceType;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\BarcodeSuccess\Model\ResourceModel\Template');
    }

    /**
     * Get template id
     *
     * @return string
     */
    public function getTemplateId(){
        return $this->_getData(self::TEMPLATE_ID);
    }

    /**
     * Set template id
     *
     * @param string $id
     * @return $this
     */
    public function setTemplateId($id){
        return $this->setData(self::TEMPLATE_ID, $id);
    }

    /**
     * Get template type
     *
     * @return string
     */
    public function getType(){
        return $this->_getData(self::TYPE);
    }

    /**
     * Set template type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type){
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get template type
     *
     * @return string
     */
    public function getName(){
        return $this->_getData(self::NAME);
    }

    /**
     * Set template type
     *
     * @param string $name
     * @return $this
     */
    public function setName($name){
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get template priority
     *
     * @return string
     */
    public function getPriority(){
        return $this->_getData(self::PRIORITY);
    }

    /**
     * Set template priority
     *
     * @param string $priority
     * @return $this
     */
    public function setPriority($priority){
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * Get template status
     *
     * @return string
     */
    public function getStatus(){
        return $this->_getData(self::STATUS);
    }

    /**
     * Set template status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get template symbology
     *
     * @return string
     */
    public function getSymbology(){
        return $this->_getData(self::SYMBOLOGY);
    }

    /**
     * Set template symbology
     *
     * @param string $symbology
     * @return $this
     */
    public function setSymbology($symbology){
        return $this->setData(self::SYMBOLOGY, $symbology);
    }

    /**
     * Get template mesurementUnit
     *
     * @return string
     */
    public function getMesurementUnit(){
        return $this->_getData(self::MEASUREMENT_UNIT);
    }

    /**
     * Set template mesurementUnit
     *
     * @param string $mesurementUnit
     * @return $this
     */
    public function setMesurementUnit($mesurementUnit){
        return $this->setData(self::MEASUREMENT_UNIT, $mesurementUnit);
    }

    /**
     * Get template labelPerRow
     *
     * @return string
     */
    public function getLabelPerRow(){
        return $this->_getData(self::LABEL_PER_ROW);
    }

    /**
     * Set template labelPerRow
     *
     * @param string $labelPerRow
     * @return $this
     */
    public function setLabelPerRow($labelPerRow){
        return $this->setData(self::LABEL_PER_ROW, $labelPerRow);
    }

    /**
     * Get template labelWidth
     *
     * @return string
     */
    public function getLabelWidth(){
        return $this->_getData(self::LABEL_WIDTH);
    }

    /**
     * Set template labelWidth
     *
     * @param string $labelWidth
     * @return $this
     */
    public function setLabelWidth($labelWidth){
        return $this->setData(self::LABEL_WIDTH, $labelWidth);
    }

    /**
     * Get template labelHeight
     *
     * @return string
     */
    public function getLabelHeight(){
        return $this->_getData(self::LABEL_HEIGHT);
    }

    /**
     * Set template labelHeight
     *
     * @param string $labelHeight
     * @return $this
     */
    public function setLabelHeight($labelHeight){
        return $this->setData(self::LABEL_HEIGHT, $labelHeight);
    }

    /**
     * Get template $paperWidth
     *
     * @return string
     */
    public function getPaperWidth(){
        return $this->_getData(self::PAPER_WIDTH);
    }

    /**
     * Set template paerWidth
     *
     * @param string $paperWidth
     * @return $this
     */
    public function setPaperWidth($paperWidth){
        return $this->setData(self::PAPER_WIDTH, $paperWidth);
    }

    /**
     * Get template paperHeight
     *
     * @return string
     */
    public function getPaperHeight(){
        return $this->_getData(self::PAPER_HEIGHT);
    }

    /**
     * Set template paperHeight
     *
     * @param string $paperHeight
     * @return $this
     */
    public function setPaperHeight($paperHeight){
        return $this->setData(self::PAPER_HEIGHT, $paperHeight);
    }

    /**
     * Get template topMargin
     *
     * @return string
     */
    public function getTopMargin(){
        return $this->_getData(self::TOP_MARGIN);
    }

    /**
     * Set template topMargin
     *
     * @param string $topMargin
     * @return $this
     */
    public function setTopMargin($topMargin){
        return $this->setData(self::TOP_MARGIN, $topMargin);
    }

    /**
     * Get template bottomMargin
     *
     * @return string
     */
    public function getBottomMargin(){
        return $this->_getData(self::BOTTOM_MARGIN);
    }

    /**
     * Set template bottomMargin
     *
     * @param string $bottomMargin
     * @return $this
     */
    public function setBottomMargin($bottomMargin){
        return $this->setData(self::BOTTOM_MARGIN, $bottomMargin);
    }

    /**
     * Get template leftMargin
     *
     * @return string
     */
    public function getLeftMargin(){
        return $this->_getData(self::LEFT_MARGIN);
    }

    /**
     * Set template leftMargin
     *
     * @param string $leftMargin
     * @return $this
     */
    public function setLeftMargin($leftMargin){
        return $this->setData(self::LEFT_MARGIN, $leftMargin);
    }

    /**
     * Get template rightMargin
     *
     * @return string
     */
    public function getRightMargin(){
        return $this->_getData(self::RIGHT_MARGIN);
    }

    /**
     * Set template rightMargin
     *
     * @param string $rightMargin
     * @return $this
     */
    public function setRightMargin($rightMargin){
        return $this->setData(self::RIGHT_MARGIN, $rightMargin);
    }

    /**
     * Get template preview data
     *
     * @return string
     */
    public function getPreviewData(){
        $preview = [];
        $preview['url'] = $this->helper->getBackendUrl('barcodesuccess/template/preview');
        $preview['default'] = $this->sourceType->getDefaultData();
        return \Zend_Json::encode($preview);
    }
}