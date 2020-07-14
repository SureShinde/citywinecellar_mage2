<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Block\Adminhtml\Banners\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Amasty\PromoBanners\Model\Rule;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\UrlInterface;

class Content extends Generic implements TabInterface
{
    protected $_position;
    protected $_categoryColFactory;
    protected $_blockCollection;
    protected $_maxWidth = 4;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Amasty\PromoBanners\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $rendererFieldset;

    /**
     * @var \Magento\Rule\Block\Actions
     */
    protected $actions;

    /**
     * @var Yesno
     */
    private $yesno;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\PromoBanners\Model\Source\Position $position,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryColFactory,
        \Magento\Cms\Model\ResourceModel\Block\Collection $blockCollection,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Amasty\PromoBanners\Helper\Data $helper,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Magento\Rule\Block\Actions $actions,
        Yesno $yesno,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_position = $position;
        $this->_categoryColFactory = $categoryColFactory;
        $this->_blockCollection = $blockCollection;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->helper = $helper;
        $this->rendererFieldset = $rendererFieldset;
        $this->actions = $actions;
        $this->yesno = $yesno;
    }

    protected $_statuses;

    public function getTabLabel()
    {
        return __('Banner Content');
    }

    public function getTabTitle()
    {
        return __('Banner Content');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getNProductWidthOptions()
    {
        $maxWidth = $this->_scopeConfig->getValue('ampromobanners/general/max_banner_width');
        if (isset($maxWidth)) {
            $this->_maxWidth = $maxWidth;
        }
        $arr = [];
        for ($i = 1; $i <= $this->_maxWidth; $i++) {
            $arr[$i] = $i;
        }
        return $arr;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_promo_banner');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'ampromobanners_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                "{$htmlIdPrefix}after_n_product_row",
                'after_n_product_row'
            )->addFieldMap(
                "{$htmlIdPrefix}n_product_width",
                'n_product_width'
            )->addFieldMap(
                "{$htmlIdPrefix}banner_position",
                'banner_position'
            )->addFieldDependence(
                'after_n_product_row',
                'banner_position',
                Rule::POS_AMONG_PRODUCTS
            )->addFieldDependence(
                'n_product_width',
                'banner_position',
                Rule::POS_AMONG_PRODUCTS
            )
        );

        $this->setForm($form);

        $fieldset = $form->addFieldset('position', ['legend' => __('Banner Position and Type')]);
        $fieldset->addField(
            'banner_position',
            'multiselect',
            [
                'label' => __('Position'),
                'name' => 'banner_position',
                'values' => $this->_position->getPositionMulti(),
                'note' => __('Where to display this banner'),
                'after_element_html' => '
                <div class="note" id="note-sidebar" style="display: none; font-style: italic;">'.
                    __('Please note that position of sidebars depends on the theme layout. </br> By default Sidebar Main is situated on the left side of category pages.')
                .'</div>
                '
            ]
        );

        $fieldset->addField(
            'after_n_product_row',
            'text',
            [
                'label' => __('Product line number'),
                'name'  => 'after_n_product_row',
                'class' => 'validate-number required-entry'
            ]
        );


        $fieldset->addField(
            'n_product_width',
            'select',
            [
                'label'   => __('Banner width (number of products)'),
                'class'   => 'validate-number required-entry',
                'name'    => 'n_product_width',
                'options' => $this->getNProductWidthOptions()
            ]
        );

        $fieldset->addField(
            'cats',
            'multiselect',
            [
                'label'  => __('Categories'),
                'name'   => 'cats',
                'values' => $this->getTree(),
                'note'   => __('Leave empty to show in all categories. Not applicable for `Above Cart` position.'),
            ]
        );

        $fieldset->addField(
            'show_on_products',
            'textarea',
            [
                'label' => __('Product SKUs'),
                'name'  => 'show_on_products',
                'style' => 'width:500px; height:50px;',
                'note'  => __(
                    'Comma separted list of product SKUs where this banner should be displayed. Leave field blank to display banner on all products'
                ),
            ]
        );

        $fieldset->addField(
            'show_on_search',
            'textarea',
            [
                'label' => __('Search keywords'),
                'name'  => 'show_on_search',
                'style' => 'width:500px; height:50px;',
                'note'  => __(
                    'Provide search keywords one per line. Leave empty to show banner on all catalog search pages'
                ),
            ]
        );

        $fieldset->addField(
            'banner_type',
            'select',
            [
                'label'    => __('Banner Type'),
                'title'    => __('Banner Type'),
                'name'     => 'banner_type',
                'required' => true,
                'options'  => $this->getBannerTypes(),
                'note'     => __('What kind of content banner will display'),
            ]
        );

        $fieldset->addField(
            'show_products',
            'select',
            [
                'label'    => __('Show Products'),
                'title'    => __('Show Products'),
                'name'     => 'show_products',
                'required' => true,
                'options'  => $this->yesno->toArray(),
                'note'     => __('Choose Yes to show selected products below banner'),
            ]
        );


        $fieldset = $form->addFieldset('image', ['legend' => __('Image')]);

        $fieldset->addField(
            'banner_img',
            'file',
            [
                'label'              => __('Image'),
                'name'               => 'banner_img',
                'after_element_html' => $this->getImageHtml($model->getBannerImg()),
            ]
        );

        $fieldset->addField(
            'banner_link',
            'text',
            [
                'label' => __('Link'),
                'name'  => 'banner_link',
            ]
        );

        $fieldset->addField(
            'banner_title',
            'text',
            [
                'label' => __('Title'),
                'name'  => 'banner_title',
            ]
        );

        $cmsBlocks = $this->_blockCollection;
        $values = [[
                       'value' => '',
                       'label' => '',
                   ]];

        foreach ($cmsBlocks as $block) {
            $values[] = [
                'value' => $block->getIdentifier(),
                'label' => $block->getTitle(),
            ];
        }

        $fieldset = $form->addFieldset('cms', ['legend' => __('CMS Block')]);
        $fieldset->addField(
            'cms_block',
            'select',
            [
                'name'   => 'cms_block',
                'label'  => __('Use CMS block'),
                'title'  => __('Use CMS block'),
                'values' => $values,
            ]
        );

        $fieldset = $form->addFieldset('html', ['legend' => __('HTML Text')]);
        $fieldset->addField(
            'html_text',
            'editor', [
                'name'   => 'html_text',
                'label'  => __('HTML Text'),
                'title'  => __('HTML Text'),
                'style'  => 'width:700px; height:500px;',
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );

        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('*/*/newProductCondHtml', ['form' => 'ampromobanners_banner_productcond_fieldset'])
        );

        $fieldset = $form->addFieldset(
            'banner_productcond_fieldset',
            [
                'legend' => __(
                    'Product attributes'
                )
            ]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'actions',
            'text',
            [
                'name' => 'actions',
                'label' => __('Conditions'),
                'title' => __('Conditions')
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->actions
        );

        $form->setValues($model->getData());

        return parent::_prepareForm();
    }

    protected function getTree()
    {
        $rootId = $this->_storeManager->getStore(0)->getRootCategoryId();
        $tree = [];
        $collection = $this->_categoryColFactory->create()->addNameToResult();

        $pos = [];
        foreach ($collection as $cat) {
            $path = explode('/', $cat->getPath());
            if ((!$rootId || in_array($rootId, $path)) && $cat->getLevel()) {
                $tree[$cat->getId()] = [
                    'label' => str_repeat('--', $cat->getLevel()) . $cat->getName(),
                    'value' => $cat->getId(),
                    'path' => $path,
                ];
            }
            $pos[$cat->getId()] = $cat->getPosition();
        }

        foreach ($tree as $catId => $cat) {
            $order = [];
            foreach ($cat['path'] as $id) {
                $order[] = isset($pos[$id]) ? $pos[$id] : 0;
            }
            $tree[$catId]['order'] = $order;
        }

        usort($tree, [$this, 'compare']);
        array_unshift($tree, ['value' => '', 'label' => '']);

        return $tree;
    }

    /**
     * Compares category data. Must be public as used as a callback value
     *
     * @param array $a
     * @param array $b
     * @return int 0, 1 , or -1
     */
    public function compare($a, $b)
    {
        foreach ($a['path'] as $i => $id) {
            if (!isset($b['path'][$i])) {
                // B path is shorther then A, and values before were equal
                return 1;
            }
            if ($id != $b['path'][$i]) {
                // compare category positions at the same level
                $p = isset($a['order'][$i]) ? $a['order'][$i] : 0;
                $p2 = isset($b['order'][$i]) ? $b['order'][$i] : 0;
                return ($p < $p2) ? -1 : 1;
            }
        }
        // B path is longer or equal then A, and values before were equal
        return ($a['value'] == $b['value']) ? 0 : -1;
    }

    protected function getBannerTypes()
    {
        return [
            Rule::TYPE_IMAGE => __('Image'),
            Rule::TYPE_CMS => __('CMS Block'),
            Rule::TYPE_HTML => __('HTML text'),
        ];
    }

    private function getImageHtml($img)
    {
        $html = '';
        if ($img) {
            $mediaUrl = $this->_storeManager->getStore(0)->getBaseUrl(UrlInterface::URL_TYPE_MEDIA, false);
            $html .= '<p style="margin-top: 5px">';
            $html .= '<img src="' . $mediaUrl . 'amasty/ampromobanners/' . $img . '" alt=""/>';
            $html .= '</p>';
        }
        return $html;
    }
}
