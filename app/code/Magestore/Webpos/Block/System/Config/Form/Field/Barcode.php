<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Magestore\Webpos\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class Barcode
 *
 * Use to check condition of show barcode field
 */
class Barcode extends Field
{
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $webposHelper;

    /**
     * Barcode constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magestore\Webpos\Helper\Data $webposHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magestore\Webpos\Helper\Data $webposHelper,
        array $data = []
    ) {
        $this->webposHelper = $webposHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->webposHelper->isEnabledBarcodeManagement()) {
            return '';
        } else {
            return parent::render($element);
        }
    }
}
