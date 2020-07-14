<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Api\Data;

/**
 * @api
 */
interface BarcodeTemplateInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const TEMPLATE_ID = 'template_id';

    const TYPE = 'type';

    const NAME = 'name';

    const PRIORITY = 'priority';

    const STATUS = 'status';

    const SYMBOLOGY = 'symbology';

    const MEASUREMENT_UNIT = 'measurement_unit';

    const LABEL_PER_ROW = 'label_per_row';

    const PAPER_WIDTH = 'paper_width';

    const PAPER_HEIGHT = 'paper_height';

    const LABEL_WIDTH = 'label_width';

    const LABEL_HEIGHT = 'label_height';

    const TOP_MARGIN = 'top_margin';

    const RIGHT_MARGIN = 'right_margin';

    const BOTTOM_MARGIN = 'bottom_margin';

    const LEFT_MARGIN = 'left_margin';

    const PREVIEW = 'preview';

    /**#@-*/

    /**
     * Get template id
     *
     * @return string
     */
    public function getTemplateId();

    /**
     * Set template id
     *
     * @param string $id
     * @return $this
     */
    public function setTemplateId($id);

    /**
     * Get template type
     *
     * @return string
     */
    public function getType();

    /**
     * Set template type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get template type
     *
     * @return string
     */
    public function getName();

    /**
     * Set template type
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get template priority
     *
     * @return string
     */
    public function getPriority();

    /**
     * Set template priority
     *
     * @param string $priority
     * @return $this
     */
    public function setPriority($priority);

    /**
     * Get template status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set template status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get template symbology
     *
     * @return string
     */
    public function getSymbology();

    /**
     * Set template symbology
     *
     * @param string $symbology
     * @return $this
     */
    public function setSymbology($symbology);

    /**
     * Get template mesurementUnit
     *
     * @return string
     */
    public function getMesurementUnit();

    /**
     * Set template mesurementUnit
     *
     * @param string $mesurementUnit
     * @return $this
     */
    public function setMesurementUnit($mesurementUnit);

    /**
     * Get template labelPerRow
     *
     * @return string
     */
    public function getLabelPerRow();

    /**
     * Set template labelPerRow
     *
     * @param string $labelPerRow
     * @return $this
     */
    public function setLabelPerRow($labelPerRow);

    /**
     * Get template labelWidth
     *
     * @return string
     */
    public function getLabelWidth();

    /**
     * Set template labelWidth
     *
     * @param string $labelWidth
     * @return $this
     */
    public function setLabelWidth($labelWidth);

    /**
     * Get template labelHeight
     *
     * @return string
     */
    public function getLabelHeight();

    /**
     * Set template labelHeight
     *
     * @param string $labelHeight
     * @return $this
     */
    public function setLabelHeight($labelHeight);

    /**
     * Get template $paperWidth
     *
     * @return string
     */
    public function getPaperWidth();

    /**
     * Set template paerWidth
     *
     * @param string $paperWidth
     * @return $this
     */
    public function setPaperWidth($paperWidth);

    /**
     * Get template paperHeight
     *
     * @return string
     */
    public function getPaperHeight();

    /**
     * Set template paperHeight
     *
     * @param string $paperHeight
     * @return $this
     */
    public function setPaperHeight($paperHeight);

    /**
     * Get template topMargin
     *
     * @return string
     */
    public function getTopMargin();

    /**
     * Set template topMargin
     *
     * @param string $topMargin
     * @return $this
     */
    public function setTopMargin($topMargin);

    /**
     * Get template bottomMargin
     *
     * @return string
     */
    public function getBottomMargin();

    /**
     * Set template bottomMargin
     *
     * @param string $bottomMargin
     * @return $this
     */
    public function setBottomMargin($bottomMargin);

    /**
     * Get template leftMargin
     *
     * @return string
     */
    public function getLeftMargin();

    /**
     * Set template leftMargin
     *
     * @param string $leftMargin
     * @return $this
     */
    public function setLeftMargin($leftMargin);

    /**
     * Get template rightMargin
     *
     * @return string
     */
    public function getRightMargin();

    /**
     * Set template rightMargin
     *
     * @param string $rightMargin
     * @return $this
     */
    public function setRightMargin($rightMargin);

    /**
     * Get template preview data
     *
     * @return string
     */
    public function getPreviewData();
}
