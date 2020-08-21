<?php 
namespace Ayasoftware\EnhancedConfigurable\Block;
    class Version extends \Magento\Config\Block\System\Config\Form\Field
    {
     /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

        const MODULE_NAME = 'Ayasoftware_EnhancedConfigurable';
        public function __construct(
       \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $_objectManager
        ) 
        {
             $this->_objectManager = $_objectManager;
             parent::__construct($context);
           
        }
         /**
          * get module version
         @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
         */
        protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
        {
            $html = $this->_objectManager->get('Magento\Framework\Module\ModuleList')->getOne(self::MODULE_NAME);
            return $html['setup_version'];
        }
    }