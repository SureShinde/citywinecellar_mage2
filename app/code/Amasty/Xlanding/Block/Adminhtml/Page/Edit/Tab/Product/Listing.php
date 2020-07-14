<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Page\Edit\Tab\Product;

class Listing extends \Magento\Backend\Block\Widget\Grid
{
    const IMAGE_WIDTH = 130;
    const IMAGE_HEIGHT = 130;

    /**
     * Collection object
     *
     * @var \Magento\Framework\Data\Collection
     */
    private $collection;

    /**
     * Catalog image
     *
     * @var \Magento\Catalog\Helper\Image
     */
    private $catalogImage = null;

    /**
     * @var \Amasty\Xlanding\Model\Page\Product\AdminhtmlDataProvider
     */
    private $dataProvider;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $usableAttributes = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $attributeFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Helper\Image $catalogImage,
        \Amasty\Xlanding\Model\Page\Product\AdminhtmlDataProvider $dataProvider,
        \Amasty\Xlanding\Block\Adminhtml\Widget\Attribute\Factory $attributeFactory,
        array $data = []
    ) {
        $this->catalogImage = $catalogImage;
        $this->dataProvider = $dataProvider;
        $this->scopeConfig = $context->getScopeConfig();
        $this->attributeFactory = $attributeFactory;
        parent::__construct($context, $backendHelper, $data);
        $this->setTemplate('Amasty_Xlanding::page/product/listing.phtml');
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('position');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * @return \Magento\Catalog\Helper\Image
     */
    public function getImageHelper()
    {
        return $this->catalogImage;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getImageUrl($product)
    {
        $image = $this->getImageHelper()
            ->init($product, 'small_image', ['type' => 'small_image'])
            ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
        return $image->getUrl();
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->dataProvider->getProductCollection();

        $this->setCollection($collection);

        $this->_preparePage();

        $idx = ($collection->getCurPage() * $collection->getPageSize()) - $collection->getPageSize();

        foreach ($collection as $item) {
            $item->setPosition($idx);
            if (array_key_exists($item->getId(), $this->dataProvider->getProductPositionData())) {
                $item->setIsManual(true);
            }

            $idx++;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getCollection()->getCurPage();
    }

    /**
     * @return int
     */
    public function getLastPageNumber()
    {
        if ($this->getCollection()->count()) {
            return $this->getCollection()->getLastPageNumber();
        }
        return (int)$this->_defaultPage;
    }

    /**
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getParam($this->getVarNamePage(), $this->_defaultPage) == $this->_defaultPage;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->getCollection()->getPageSize();
    }

    /**
     * Set collection object
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return void
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * get collection object
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Retrieve column by id
     *
     * @param string $columnId
     * @return \Magento\Framework\View\Element\AbstractBlock|bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getColumn($columnId)
    {
        return false;
    }

    /**
     * Retrieve list of grid columns
     *
     * @return array
     */
    public function getColumns()
    {
        return [];
    }

    /**
     * Process column filtration values
     *
     * @param mixed $data
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _setFilterValues($data)
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('amasty_xlanding/product/listing', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function getUsableAttributes()
    {
        if ($this->usableAttributes == null) {
            $this->usableAttributes = ['name', 'price', 'stock'];
        }
        return $this->usableAttributes;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAttributesToDisplay($product)
    {
        $attributeCodes = $this->getUsableAttributes();
        $availableAttributes = $product->getTypeInstance()->getSetAttributes($product);
        $availableFields = array_keys($product->getData());
        $filteredAttributes = [];

        foreach ($attributeCodes as $code) {
            $renderer = $this->attributeFactory->create($code);

            if ($code == 'price') {
                $attributeObject = $availableAttributes[$code];
                $filteredAttributes[] = $renderer->addData([
                    'label' => $attributeObject->getFrontend()->getLabel(),
                    'value' => $product->getFormatedPrice()
                ]);
            } elseif (isset($availableAttributes[$code])) {
                $attributeObject = $availableAttributes[$code];
                $filteredAttributes[] = $renderer->addData([
                    'label' => $attributeObject->getFrontend()->getLabel(),
                    'value' => $product->getData($code)
                ]);
            } elseif (in_array($code, $availableFields)) {
                $filteredAttributes[] = $renderer->addData([
                    'label' => ucwords($code),
                    'value' => $product->getData($code)
                ]);
            }
        }

        return $filteredAttributes;
    }

    /**
     * @return null
     */
    public function getMainButtonsHtml()
    {
        return null;
    }
}
