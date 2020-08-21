<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Helper;

/**
 * Class Url
 *
 * Used to create url
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Url extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter
     */
    protected $_converter;

    /**
     * @var \Magestore\Storepickup\Model\Factory
     */
    protected $_factory;

    /**
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var array
     */
    protected $_sessionData = null;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $_backendHelperJs;

    /**
     * @var \Magestore\Storepickup\Model\ResourceModel\Store\CollectionFactory
     *
     */
    protected $_storeCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory
     */
    protected $_urlRewriteCollectionFactory;

    /**
     * @var
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\HTTP\Adapter\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    protected $driver;

    /**
     * Url constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magestore\Storepickup\Model\Factory $factory
     * @param \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter
     * @param \Magento\Backend\Helper\Js $backendHelperJs
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param \Magestore\Storepickup\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory
     * @param \Magestore\Storepickup\Model\StoreFactory $storeFactory
     * @param \Magento\Framework\HTTP\Adapter\Curl $curl
     * @param \Magento\Framework\Filesystem\DriverInterface $driver
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\Storepickup\Model\Factory $factory,
        \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter,
        \Magento\Backend\Helper\Js $backendHelperJs,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        \Magestore\Storepickup\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \Magestore\Storepickup\Model\StoreFactory $storeFactory,
        \Magento\Framework\HTTP\Adapter\Curl $curl,
        \Magento\Framework\Filesystem\DriverInterface $driver
    ) {
        parent::__construct($context);
        $this->_factory = $factory;
        $this->_converter = $converter;
        $this->_backendHelperJs = $backendHelperJs;
        $this->_filesystem = $filesystem;
        $this->_backendSession = $backendSession;
        $this->_storeManager = $storeManager;
        $this->_urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->_storeCollectionFactory = $storeCollectionFactory;
        $this->_storeFactory = $storeFactory;
        $this->curl = $curl;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->driver = $driver;
    }

    /**
     * Get response body
     *
     * @param string $url
     * @return string
     */
    public function getResponseBody($url)
    {
        $this->curl->setConfig(['header' => false]);
        $this->curl->write('get', $url);
        $contents = $this->curl->read();
        $this->curl->close();
        if (empty($contents)) {
            try {
                $contents = $this->driver->fileGetContents($url);
            } catch (\Exception $exception) {
                $contents = '';
            }
        }
        return $contents;
    }

    /**
     * Get store view url
     *
     * @param string $storeName
     * @param string $id
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getStoreViewUrl($storeName, $id)
    {
        $allStores = $this->_storeManager->getStores();
        $storepickup = $this->_storeFactory->create()->load($id);

        foreach ($allStores as $_eachStoreId => $val) {
            $rewrite = $this->_urlRewriteCollectionFactory->create()
                ->addFieldToFilter('id_path', $storeName)
                ->addFieldToFilter('store_id', $_eachStoreId)
                ->getFirstItem();

            $request_path1 = $rewrite->getRequestPath();

            if ($storepickup->getUrlIdPath() != $storeName) {
                $storeName = $storepickup->getUrlIdPath();
                $storepickup->save();
            }
        }

        return $this->urlBuilder->getUrl($request_path1, ["_secure" => true]);
    }

    /**
     * Character special
     *
     * @param string $character
     * @return string|string[]
     */
    public function characterSpecial($character)
    {
        if ('"libiconv"' == ICONV_IMPL) {
            $character = iconv('UTF-8', 'ascii//ignore//translit', $character);
        }
        $input = ["ñ", " ", "à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ"
        , "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ", "ì", "í", "ị", "ỉ", "ĩ",
            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ"
        , "ờ", "ớ", "ợ", "ở", "ỡ",
            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
            "ỳ", "ý", "ỵ", "ỷ", "ỹ",
            "đ",
            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă"
        , "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
            "Ì", "Í", "Ị", "Ỉ", "Ĩ",
            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ"
        , "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
            "Đ", "ê", "ù", "à", '.', '-', "'", "[À-Å]", "Æ", "Ç", "[È-Ë]", "/[Ì-Ï]/", "/Ð/", "/Ñ/", "/[Ò-ÖØ]/",
            "/×/", "/[Ù-Ü]/", "/[Ý-ß]/", "/[à-å]/", "/æ/", "/ç/", "/[è-ë]/", "/[ì-ï]/", "/ð/", "/ñ/", "/[ò-öø]/",
            "/÷/", "/[ù-ü]/", "/[ý-ÿ]/", "?"];
        $output = ["n", "-", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a"
        , "a", "a", "a", "a", "a", "a",
            "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e",
            "i", "i", "i", "i", "i",
            "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o"
        , "o", "o", "o", "o", "o",
            "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
            "y", "y", "y", "y", "y",
            "d",
            "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A"
        , "A", "A", "A", "A", "A",
            "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
            "I", "I", "I", "I", "I",
            "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O"
        , "O", "O", "O", "O", "O",
            "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
            "Y", "Y", "Y", "Y", "Y",
            "D", "e", "u", "a", '-', '-', "", "A", "AE", "C", "E", "I", "D", "N", "O", "X", "U", "Y", "a", "ae", "c",
            "e", "i", "d", "n", "o", "x", "u", "y", ""];

        return str_replace($input, $output, $character);
    }
}
