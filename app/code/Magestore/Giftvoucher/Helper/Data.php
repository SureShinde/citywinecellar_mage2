<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Tax\Model\Config;
use Magento\Store\Model\Store;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Giftvoucher default helper
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_moduleList;

    protected $_imageUrl;
    protected $_imageName;
    protected $_imageReturn;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Set
     */
    protected $_attributeSet;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currencyModel;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_imageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_taxCalculation;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;
    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $coreFileStorageDatabase;

    /**
     * @var \Magento\Framework\View\Element\Template
     */
    protected $view;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuote;

    protected $filesystemDriver;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Eav\Model\Entity\Attribute\Set $attributeSet
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\Currency $currencyModel
     * @param \Magento\Framework\Locale\ResolverInterface $locale
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param CustomerSession $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Framework\View\Element\Template $view
     * @param ModuleListInterface $moduleList
     * @param \Magento\Framework\Filesystem\DriverInterface $filesystemDriver
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Eav\Model\Entity\Attribute\Set $attributeSet,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currencyModel,
        \Magento\Framework\Locale\ResolverInterface $locale,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\View\Element\Template $view,
        ModuleListInterface $moduleList,
        \Magento\Framework\Filesystem\DriverInterface $filesystemDriver
    ) {
        $this->_objectManager = $objectManager;
        $this->_priceCurrency = $priceCurrency;
        $this->_productFactory = $productFactory;
        $this->_product = $product;
        $this->_customer = $customer;
        $this->_attributeSet= $attributeSet;
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_storeManager = $storeManager;
        $this->_currencyModel = $currencyModel;
        $this->_locale = $locale;
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_appState = $appState;
        $this->messageManager = $messageManager;
        $this->_taxCalculation = $taxCalculation;
        $this->_sessionQuote = $sessionQuote;
        $this->_localeDate = $localeDate;
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->view = $view;
        $this->_moduleList = $moduleList;
        $this->filesystemDriver = $filesystemDriver;
        parent::__construct($context);
    }

    /**
     * Get Gift Card general configuration
     *
     * @param string $code
     * @param int|null $store
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getGeneralConfig($code, $store = null)
    {
        if ($code == 'barcode_enable' || $code == 'barcode_type' || $code == 'logo') {
            return $this->scopeConfig->getValue('giftvoucher/print_voucher/' . $code, 'store', $store);
        }
        return $this->scopeConfig->getValue('giftvoucher/general/' . $code, 'store', $store);
    }

    /**
     * Get Gift Card print-out configuration
     *
     * @param string $code
     * @param int|null $store
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getPrintConfig($code, $store = null)
    {
        return $this->scopeConfig->getValue('giftvoucher/print_voucher/' . $code, 'store', $store);
    }

    /**
     * Get Gift Card interface configuration
     *
     * @param string $code
     * @param int|null $store
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getInterfaceConfig($code, $store = null)
    {
        return $this->scopeConfig->getValue('giftvoucher/interface/' . $code, 'store', $store);
    }

    /**
     * Get Gift Card checkout configuration
     *
     * @param string $code
     * @param int|null $store
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getInterfaceCheckoutConfig($code, $store = null)
    {
        return $this->scopeConfig->getValue('giftvoucher/interface_checkout/' . $code, 'store', $store);
    }

    /**
     * Get Gift Card email configuration
     *
     * @param string $code
     * @param int|null $store
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getEmailConfig($code, $store = null)
    {
        return $this->scopeConfig->getValue('giftvoucher/email/' . $code, 'store', $store);
    }

    /**
     * Get Store Config
     *
     * @param string $code
     * @param null|int|string $store
     * @return mixed
     */
    public function getStoreConfig($code, $store = null)
    {
        return $this->scopeConfig->getValue($code, 'store', $store);
    }

    /**
     * Get Base Dir Media
     *
     * @return \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    public function getBaseDirMedia()
    {
        return $this->_filesystem->getDirectoryRead('media');
    }

    /**
     * Get Base Dir
     *
     * @return \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    public function getBaseDir()
    {
        return $this->_filesystem->getDirectoryRead();
    }

    /**
     * Get Customer Session
     *
     * @return CustomerSession
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * Get Checkout Session
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    /**
     * Returns a gift code string
     *
     * @param string $expression
     * @return string
     * @internal param string $param
     */
    public function calcCode($expression)
    {
        if ($this->isExpression($expression)) {
            return preg_replace_callback('#\[([AN]{1,2})\.([0-9]+)\]#', [$this, 'convertExpression'], $expression);
        } else {
            return $expression;
        }
    }

    /**
     * Convert a expression to the numeric and alphabet
     *
     * @param string $param
     * @return string
     */
    public function convertExpression($param)
    {
        $alphabet = (strpos($param[1], 'A')) === false ? '' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet .= (strpos($param[1], 'N')) === false ? '' : '0123456789';
        return $this->_objectManager->create(\Magento\Framework\Math\Random::class)
            ->getRandomString($param[2], $alphabet);
    }

    /**
     * Check a string whether it is a expression or not
     *
     * @param string $string
     * @return int|boolean
     */
    public function isExpression($string)
    {
        return preg_match('#\[([AN]{1,2})\.([0-9]+)\]#', $string);
    }

    /**
     * Get Gift Card product options configuration
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getGiftVoucherOptions()
    {
        $option = explode(',', $this->getInterfaceCheckoutConfig('display'));
        $result = [];
        foreach ($option as $val) {
            if ($val == 'amount') {
                $result['amount'] = __('Gift Card value');
            }
            if ($val == 'giftcard_template_id') {
                $result['giftcard_template_id'] = __('Gift Card template');
            }
            if ($val == 'customer_name') {
                $result['customer_name'] = __('Sender name');
            }
            if ($val == 'recipient_name') {
                $result['recipient_name'] = __('Recipient name');
            }
            if ($val == 'recipient_email') {
                $result['recipient_email'] = __('Recipient email address');
            }
            if ($val == 'recipient_ship') {
                $result['recipient_ship'] = __('Ship to recipient');
            }
            if ($val == 'recipient_address') {
                $result['recipient_address'] = __('Recipient address');
            }
            if ($val == 'message') {
                $result['message'] = __('Custom message');
            }
            if ($val == 'day_to_send') {
                $result['day_to_send'] = __('Day to send');
            }
            if ($val == 'timezone_to_send') {
                $result['timezone_to_send'] = __('Time zone');
            }
            if ($val == 'giftcard_use_custom_image') {
                $result['giftcard_use_custom_image'] = __('Use custom image');
            }
        }
        return $result;
    }

    /**
     * Get the full Gift Card options
     *
     * @return array
     */
    public function getFullGiftVoucherOptions()
    {
        return [
            'customer_name' => __('Sender Name'),
            'giftcard_template_id' => __('Giftcard Template'),
            'send_friend' => __('Send Gift Card to friend'),
            'recipient_name' => __('Recipient name'),
            'recipient_email' => __('Recipient email'),
            'recipient_ship' => __('Ship to recipient'),
            'recipient_address' => __('Recipient address'),
            'message' => __('Custom message'),
            'day_to_send' => __('Day To Send'),
            'timezone_to_send' => __('Time zone'),
            'email_sender' => __('Email To Sender'),
            'amount' => __('Amount'),
            'giftcard_template_image' => __('Giftcard Image'),
            'giftcard_use_custom_image' => __('Use Custom Image'),
            'notify_success' => __('Notify when the recipient receives Gift Card.')
        ];
    }

    /**
     * Get the hidden gift code
     *
     * @param string $codes
     * @return string
     */
    public function getHiddenCode($codes)
    {
        $prefix = $this->getGeneralConfig('showprefix');
        $listCodes = explode(',', $codes);
        $codes = '';
        foreach ($listCodes as $code) {
            if (!$code) {
                continue;
            }
            $prefixCode = substr($code, 0, $prefix);
            $suffixCode = substr($code, $prefix);
            if ($suffixCode) {
                $hiddenChar = $this->getGeneralConfig('hiddenchar');
                if (!$hiddenChar) {
                    $hiddenChar = 'X';
                } else {
                    $hiddenChar = substr($hiddenChar, 0, 1);
                }
                $suffixCode = preg_replace('#([a-zA-Z0-9\-])#', $hiddenChar, $suffixCode);
            }
            if ($codes) {
                $codes.= ','.$prefixCode . $suffixCode;
            } else {
                $codes = $prefixCode . $suffixCode;
            }
        }
        return $codes;
    }

    /**
     * Check gift codes whether they are available to add or not
     *
     * @return boolean
     */
    public function isAvailableToAddCode()
    {
        $codes = $this->_objectManager->get(\Magestore\Giftvoucher\Model\Session::class)->getCodes()?: [];
        if ($max = $this->getGeneralConfig('maximum')) {
            if (count($codes) >= $max) {
                return false;
            }
        }
        return true;
    }

    /**
     * Is Available To Check Code
     *
     * @return bool
     */
    public function isAvailableToCheckCode()
    {
        $codes = $this->_objectManager->get(\Magestore\Giftvoucher\Model\Session::class)->getCodesInvalid()?: [];
        if ($max = $this->getGeneralConfig('maximum')) {
            if (count($codes) >= $max) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check code can used to checkout or not
     *
     * @param mixed $code
     * @return boolean
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function canUseCode($code)
    {
        if (!$code) {
            return false;
        }
        if (is_string($code)) {
            $code = $this->_objectManager->create(\Magestore\Giftvoucher\Model\Giftvoucher::class)
                ->loadByCode($code);
        }
        if (!($code instanceof \Magestore\Giftvoucher\Model\Giftvoucher)) {
            return false;
        }
        if (!$code->getId()) {
            return false;
        }
        if ($this->_appState->getAreaCode()=='adminhtml') {
            $customerId = $this->_sessionQuote->getCustomerId();
        } else {
            $customerId = $this->getCustomerSession()->getCustomerId();
        }
        $shareCard = (int) $this->getGeneralConfig('share_card');
        if ($shareCard < 1) {
            return true;
        }
        $customersUsed = $code->getCustomerIdsUsed()?: [];
        if ($shareCard > count($customersUsed) || in_array($customerId, $customersUsed)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get store id from quote (fix Magento create order in backend)
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return int
     */
    public function getStoreId($quote)
    {
        if ($this->_appState->getAreaCode() == 'adminhtml') {
            return $this->_sessionQuote->getStoreId();
        }
        return $quote->getStoreId();
    }

    /**
     * Get Allowed Currencies
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllowedCurrencies()
    {
        $allowedCurrencies = $this->_currencyModel->getConfigAllowCurrencies();
        $currencies = $this->_objectManager->create(\Magento\Framework\Locale\Bundle\CurrencyBundle::class)
            ->get($this->_locale->getLocale())['Currencies'];
        $options = [];
        foreach ($currencies as $code => $data) {
            if (!in_array($code, $allowedCurrencies)) {
                continue;
            }
            $options[] = ['label' => $data[1], 'value' => $code];
        }

        return $options;
    }

    /**
     * Upload template image
     *
     * @param string $type
     * @return string
     */
    public function uploadImage($type)
    {
        $image = '';
        try {
            $files = $this->_getRequest()->getFiles($type);
            if (isset($files['error']) && $files['error'] == 0) {
                $uploader = $this->_objectManager->create(
                    \Magento\Framework\File\Uploader::class,
                    ['fileId' => $type]
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $mediaDirectory = $this->_filesystem->getDirectoryRead('media');

                $result = $uploader->save($mediaDirectory->getAbsolutePath('giftvoucher/template/images'));
                unset($result['tmp_name']);
                unset($result['path']);
                $image = $result['file'];
                $this->resizeImage($image);
                $this->customResizeImage($image, 'images');
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $image;
    }

    /**
     * Upload template background image
     *
     * @return string
     * @internal param type $type
     */
    public function uploadTemplateBackground()
    {
        $image = '';
        try {
            $files = $this->_getRequest()->getFiles('background_img');
            if (isset($files['error']) && $files['error'] == 0) {
                $uploader = $this->_objectManager->create(
                    \Magento\Framework\File\Uploader::class,
                    ['fileId' => 'background_img']
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $mediaDirectory = $this->_objectManager->get(\Magento\Framework\Filesystem::class)
                    ->getDirectoryRead(DirectoryList::MEDIA);
                $result = $uploader->save($mediaDirectory->getAbsolutePath('giftvoucher/template/background'));
                unset($result['tmp_name']);
                unset($result['path']);
                $image = $result['file'];
                $this->resizeImage($image, 'background');
                $this->customResizeImage($image, 'background');
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $image;
    }

    /**
     * Delete image
     *
     * @param string $image
     * @return bool
     */
    public function deleteImageFile($image)
    {
        if (!$image) {
            return false;
        }
        try {
            unset($image);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create folder for the Gift Card product image
     *
     * @param string $parent
     * @param string $type
     * @param bool $tmp
     */
    public function createImageFolderHaitv($parent, $type, $tmp = false)
    {
        if ($type !== '') {
            $urlType = $type . '/';
        } else {
            $urlType = '';
        }
        if ($tmp) {
            $imagePath = $this->getBaseDirMedia()->getAbsolutePath('tmp/giftvoucher/images/');
        } else {
            $imagePath = $this->getBaseDirMedia()->getAbsolutePath('giftvoucher/template/'. $parent . '/' . $urlType);
        }
        if (!$this->filesystemDriver->isDirectory($imagePath)) {
            try {
                $this->filesystemDriver->createDirectory($imagePath);
                $this->filesystemDriver->changePermissions($imagePath, 0777);
            } catch (\Exception $e) {
                $this->_logger->info($e->getMessage());
            }
        }
    }

    /**
     * Resize image when admin upload template image
     *
     * @param string $imageName
     * @param string $type
     *
     * @throws \Exception
     */
    public function resizeImage($imageName, $type = 'images')
    {
        if ($imageName) {
            $imageDir = $this->getBaseDirMedia()->getAbsolutePath('giftvoucher/template/'.$type.'/' . $imageName);
            $resizeBarcodeObj = $this->_imageFactory->create();
            $resizeBarcodeObj->open($imageDir);
            $resizeBarcodeObj->getImage();
            $resizeBarcodeObj->constrainOnly(true);
            $resizeBarcodeObj->keepAspectRatio(false);
            $resizeBarcodeObj->keepFrame(false);
            $resizeBarcodeObj->resize(600, 365);
            $resizeBarcodeObj->save();
        }
    }

    /**
     * Resize
     *
     * @param int $width
     * @param null|int $height
     * @return $this
     */
    public function resize($width, $height = null)
    {
        $imageReturn = $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            . "tmp/giftvoucher/cache/" . $this->_imageName;
        $this->_imageReturn = $imageReturn;
        if ($height == null) {
            $height = $width;
        }
        $imageUrl = $this->_filesystem->getDirectoryRead('media')
            ->getAbsolutePath('tmp/giftvoucher/cache'. $this->_imageName);
        $imageObj = $this->_imageFactory->create();
        $imageObj->open($this->_imageUrl);
        $imageObj->constrainOnly(true);
        $imageObj->keepAspectRatio(true);
        $imageObj->keepFrame(true);
        $imageObj->backgroundColor([255, 255, 255]);
        $imageObj->resize($width, $height);
        try {
            $imageObj->save($imageUrl);
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
        return $this;
    }

    /**
     * Custom Resize Image
     *
     * @param string $imageName
     * @param string $imageType
     *
     * @throws \Exception
     */
    public function customResizeImage($imageName, $imageType)
    {
        //$imagePath = Mage::getBaseDir() . str_replace("/", DS, strstr($imagePath, '/media'));
        $imagePath = $this->getBaseDirMedia()->getAbsolutePath('giftvoucher/template/'.$imageType.'/');
        $imageUrl = $imagePath . $imageName;
        if ($this->filesystemDriver->isExists($imageUrl)) {
            //self::createImageFolderHaitv($imageType, 'left');
            //self::createImageFolderHaitv($imageType, 'top');
            if ($imageType == 'images') {
                $imageObj = $this->_imageFactory->create();
                $imageObj->open($imageUrl);
                $imageObj->getImage();
                $imageObj->constrainOnly(true);
                $imageObj->keepAspectRatio(false);
                $imageObj->keepFrame(false);
                $imageObj->resize(600, 190);
                $imageObj->save($imagePath . 'top/' . $imageName);

                $imageObj->resize(250, 365);
                $imageObj->save($imagePath . 'left/' . $imageName);
            } else {
                $imageObj = $this->_imageFactory->create();
                $imageObj->open($imageUrl);
                $imageObj->getImage();
                $imageObj->constrainOnly(true);
                $imageObj->keepAspectRatio(false);
                $imageObj->keepFrame(false);
                $imageObj->resize(600, 175);
                $imageObj->save($imagePath . 'top/' . $imageName);

                $imageObj->resize(350, 365);
                $imageObj->save($imagePath . 'left/' . $imageName);
            }
        }
    }

    /**
     * Get Product Thumbnail
     *
     * @param string $url
     * @param string $filename
     * @param string $urlImage
     *
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductThumbnail($url, $filename, $urlImage)
    {
        $this->_imageUrl = null;
        $this->_imageName = null;
        $this->_imageReturn = null;

        $this->_imageUrl = $url;
        $this->_imageName = $filename;
        $this->_imageReturn = $this->getStoreManager()->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . $urlImage;
        return $this;
    }

    /**
     * Get the rate of items on quote
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param null|int|string $store
     * @return float
     */
    public function getItemRateOnQuote($product, $store)
    {
        //Calculate rate to subtract taxable amount
        $priceIncludesTax =
            (bool) $this->getStoreConfig(\Magento\Tax\Model\Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $store);
        $taxClassId = $product->getTaxClassId();
        if ($taxClassId && $priceIncludesTax) {
            $request = $this->_taxCalculation->getRateRequest(false, false, false, $store);
            $rate = $this->_taxCalculation->getRate($request->setProductClassId($taxClassId));
            return $rate;
        }
        return 0;
    }

    /**
     * Get Checkout Helper
     *
     * @return \Magento\Checkout\Helper\Data
     */
    public function getCheckoutHelper()
    {
        return $this->_objectManager->get(\Magento\Checkout\Helper\Data::class);
    }

    /**
     * Get Object Manager
     *
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * Get Filesystem Driver
     *
     * @return \Magento\Framework\Filesystem\DriverInterface
     */
    public function getFilesystemDriver()
    {
        return $this->filesystemDriver;
    }

    /**
     * Get Store Manager
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * Get rate from a currency with current currency
     *
     * @param string $currencyFrom
     * @param null|string $currencyTo
     *
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRateToCurrentCurrency($currencyFrom, $currencyTo = null)
    {
        $baseCurrency = $this->getStoreManager()->getStore()->getBaseCurrency();
        if ($currencyTo === null) {
            $currencyTo = $this->getStoreManager()->getStore()->getCurrentCurrency();
        }
        $rateCurrencyFrom = $baseCurrency->getRate($currencyFrom);
        $rateCurrencyTo = $baseCurrency->getRate($currencyTo);
        if ($rateCurrencyFrom) {
            return (float)$rateCurrencyTo/$rateCurrencyFrom;
        } else {
            return $rateCurrencyTo;
        }
    }

    /**
     * Get currency format from other currency
     *
     * @param float $amount
     * @param string $currencyFrom
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrencyFormat($amount, $currencyFrom)
    {
        $currentCurrency = $this->getStoreManager()->getStore()->getCurrentCurrency();
        if (is_string($currencyFrom)) {
            if ($currencyFrom == $currentCurrency->getCode()) {
                return $currentCurrency->format($amount);
            }
            $currencyFrom = $this->getObjectManager()->create(\Magento\Directory\Model\Currency::class)
                ->load($currencyFrom);
        } elseif ($currencyFrom === null) {
            return $currentCurrency->format($amount);
        }

        $rate = $this->getRateToCurrentCurrency($currencyFrom);
        return $currentCurrency->format($amount * $rate);
    }

    /**
     * Get Type Of Gift Code
     *
     * @param string $code
     * @return int
     */
    public function getSetIdOfCode($code)
    {
        $codes = $this->_objectManager->create(\Magestore\Giftvoucher\Model\Giftvoucher::class)->loadByCode($code);
        return $codes->getSetId();
    }

    /**
     * Format Date
     *
     * @param string $data
     * @param string $format
     * @return string
     */
    public function formatDate($data, $format = '')
    {
        $format = ($format == '')?'M d,Y H:i:s a':$format;
        return $this->_localeDate->date(new \DateTime($data))->format($format);
    }

    /**
     * Get Version
     *
     * @return mixed
     */
    public function getVersion()
    {
        return $this->_moduleList
            ->getOne($this->_getModuleName())['setup_version'];
    }

    /**
     * Is Rebuild Version
     *
     * @return bool
     */
    public function isRebuildVersion()
    {
        return (version_compare($this->getVersion(), '2.0.0', '<'))?false:true;
    }

    /**
     * Get Image Url By Quote Item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return string
     */
    public function getImageUrlByQuoteItem($item)
    {
        if ($item->getOptionByCode('giftcard_template_image')) {
            $filename = $item->getOptionByCode('giftcard_template_image')->getValue();
        } else {
            $filename = 'default.png';
        }

        $imageUrl = '/giftvoucher/template/images/' . $filename;

        if ($item->getOptionByCode('giftcard_use_custom_image')
            && $item->getOptionByCode('giftcard_use_custom_image')->getValue()) {
            $imageUrl = '/tmp/giftvoucher/images/' . $filename;
        }

        try {
            return $this->getStoreManager()
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                    . $imageUrl;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return '';
        }
    }
}
