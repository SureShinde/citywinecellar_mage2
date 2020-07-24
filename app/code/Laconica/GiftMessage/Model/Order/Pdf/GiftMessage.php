<?php

namespace Laconica\GiftMessage\Model\Order\Pdf;

use Magento\AdminNotification\Block\ToolbarEntry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\GiftMessage\Model\MessageFactory;
use Magento\Payment\Helper\Data;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Pdf\AbstractPdf;
use Magento\Sales\Model\Order\Pdf\Config;
use Magento\Sales\Model\Order\Pdf\ItemsFactory;
use Magento\Sales\Model\Order\Pdf\Total\Factory;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Pdf;
use Zend_Pdf_Color_GrayScale;
use Zend_Pdf_Exception;
use Zend_Pdf_Image;
use Zend_Pdf_Page;
use Zend_Pdf_Style;

class GiftMessage extends AbstractPdf
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var MessageFactory
     */
    protected $giftMessageFactory;

    /**
     * @var ToolbarEntry
     */
    protected $toolbarEntry;

    /**
     * GiftMessage constructor.
     * @param Data $paymentData
     * @param StringUtils $string
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     * @param Config $pdfConfig
     * @param Factory $pdfTotalFactory
     * @param ItemsFactory $pdfItemsFactory
     * @param TimezoneInterface $localeDate
     * @param StateInterface $inlineTranslation
     * @param Renderer $addressRenderer
     * @param StoreManagerInterface $storeManager
     * @param ResolverInterface $localeResolver
     * @param MessageFactory $giftMessageFactory
     * @param ToolbarEntry $toolbarEntry
     * @param array $data
     */
    public function __construct(
        Data $paymentData,
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem,
        Config $pdfConfig,
        Factory $pdfTotalFactory,
        ItemsFactory $pdfItemsFactory,
        TimezoneInterface $localeDate,
        StateInterface $inlineTranslation,
        Renderer $addressRenderer,
        StoreManagerInterface $storeManager,
        ResolverInterface $localeResolver,
        MessageFactory $giftMessageFactory,
        ToolbarEntry $toolbarEntry,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->localeResolver = $localeResolver;
        $this->giftMessageFactory = $giftMessageFactory;
        $this->toolbarEntry = $toolbarEntry;
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $data
        );
    }

    /**
     * @param Collection|array $orders
     * @return bool|Zend_Pdf
     * @throws LocalizedException
     * @throws Zend_Pdf_Exception
     */
    public function getPdf($orders = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('giftmessage');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        $exists = false;

        foreach ($orders->getItems() as $order) {
            if (!$order->getGiftMessageId()) {
                continue;
            }
            $exists = true;
            if ($order->getStoreId()) {
                $this->localeResolver->emulate($order->getStoreId());
                $this->storeManager->setCurrentStore($order->getStoreId());
            }
            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;

            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                true
            );

            /* Add image */
            $this->insertLogo($page, $order->getStore());
            $this->y -= 10;

            $page->drawLine(30, $this->y, 570, $this->y);
            $this->y -= 60;

            $this->_printGiftMessage($order, $page);

            /* Add table */
            $this->_drawHeader($page);

            $this->_setFontRegular($page, 10);

            $page = $this->_printItems($order, $page);

            $this->_printFooter($order, $page);

            if ($order->getStoreId()) {
                $this->localeResolver->revert();
            }
        }

        $this->_afterGetPdf();

        return !$exists ? $exists : $pdf;
    }

    /**
     * Insert order to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param Order $order
     * @param bool $putOrderId
     * @throws LocalizedException
     * @throws Zend_Pdf_Exception
     */
    protected function insertOrder(&$page, $order, $putOrderId = true)
    {
        $this->y = $this->y ? $this->y : 805;
        $top = $this->y+20;

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);

        if ($putOrderId) {
            $page->drawText(
                __('Order # ') . $order->getRealOrderId(), 30, ($top -= 30), 'UTF-8'
            );
        }

        $this->y -= 40;
        $date = date('D, m/d/y', strtotime($order->getCreatedAtStoreDate()));
        $lineBlock = [
            'lines' => [
                0 => [
                    [
                        'text' => __('ORDER PLACED'),
                        'feed' => 30,
                        'align' => 'left',
                        'font_size' => 9,
                    ],
                    [
                        'text' => __('BY CUSTOMER'),
                        'feed' => 120,
                        'align' => 'left',
                        'font_size' => 9,

                    ],
                ],
                1 => [
                    [
                        'text' => $date,
                        'feed' => 30,
                        'align' => 'left',
                        'font' => 'bold',
                        'font_size' => 10,
                    ],
                    [
                        'text' => $order->getCustomerName(),
                        'feed' => 120,
                        'align' => 'left',
                        'font' => 'bold',
                        'font_size' => 10,

                    ],
                ],
                2 => [
                    [
                        'text' => $order->getBillingAddress()->getTelephone(),
                        'feed' => 120,
                        'align' => 'left',
                        'font' => 'bold',
                        'font_size' => 10,

                    ],
                ],
            ],
            'height' => 12
        ];

        $page = $this->drawLineBlocks($page, array($lineBlock));

    }

    /**
     * @param $order
     * @param $page
     * @return mixed
     * @throws Zend_Pdf_Exception
     */
    protected function _printGiftMessage($order, $page)
    {
        $message = $this->giftMessageFactory->create()->load($order->getGiftMessageId());
        if (!$message->getId()) {
            return $page;
        }

        $fontSize = 16;
        $font = $this->_setFontBold($page, 16);
        $storeName = $this->_scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE);
        $text = __('Dear ') . $message->getRecipient();

        $maxWidth = 300;
        $feed = 150;

        foreach ($this->_splitTextByWidth($text, $maxWidth, $font, $fontSize) as $row) {
            $center = $this->getAlignCenter($row, $feed, $maxWidth, $font, $fontSize);
            $page->drawText($row, $center, $this->y, 'UTF-8');
            $this->y -= $fontSize + 2;
        }

        $this->y -= 42;
        $this->insertGiftIcon($page);
        $this->y -= 60;

        $text = __('You have been given the gift of ') . $storeName;
        $center = $this->getAlignCenter($text, $feed, $maxWidth, $font, $fontSize);
        $page->drawText($text, $center, $this->y, 'UTF-8');
        $this->y -= 18;

        $text = __('by ') . $message->getSender() . '!';
        $center = $this->getAlignCenter($text, $feed, $maxWidth, $font, $fontSize);
        $page->drawText($text, $center, $this->y, 'UTF-8');

        $this->y -= 60;
        $font = $this->_setFontItalic($page, 14);

        foreach ($this->_splitTextByWidth($message->getMessage(), $maxWidth, $font, $fontSize) as $row) {
            $center = $this->getAlignCenter($row, $feed, $maxWidth + 20, $font, $fontSize);
            $page->drawText($row, $center, $this->y, 'UTF-8');
            $this->y -= $fontSize + 2;
        }

        $this->y -= 40;

        return $page;
    }

    /**
     * @param $text
     * @param int $maxWidth
     * @param $font
     * @param $fontSize
     * @return array
     */
    protected function _splitTextByWidth($text, $maxWidth, $font, $fontSize)
    {
        $rows = [];
        $text = $this->toolbarEntry->stripTags($text);
        $text = preg_replace("/[\n\r]/", ' ', $text);
        $text = preg_replace('/\s\s+/', ' ', $text);
        $text_width = $this->widthForStringUsingFontSize($text, $font, $fontSize);
        if ($text_width <= $maxWidth) {
            return [0 => $text];
        }
        $part = '';
        $words = explode(' ', $text);
        $total = count($words);
        foreach ($words as $key => $word) {
            $part .= ' ' . $word;
            $part_width = $this->widthForStringUsingFontSize($part . ' ', $font, $fontSize);
            if ($part_width < $maxWidth && ($total > ($key + 1))) {
                continue;
            } else {
                $rows[] = $part;
                $part = '';
            }
        }

        return $rows;
    }

    /**
     * @param $page
     * @throws Zend_Pdf_Exception
     */
    protected function insertGiftIcon(&$page)
    {
        $image = $this->_mediaDirectory->getAbsolutePath() . '/sales/store/icon/gift.png';
        if (is_file($image)) {
            $image = Zend_Pdf_Image::imageWithPath($image);
            $widthLimit = 32; //half of the page width
            $heightLimit = 32; //assuming the image is not a "skyscraper"
            $width = $image->getPixelWidth();
            $height = $image->getPixelHeight();

            //preserving aspect ratio (proportions)
            $ratio = $width / $height;
            if ($ratio > 1 && $width > $widthLimit) {
                $width = $widthLimit;
                $height = $width / $ratio;
            } elseif ($ratio < 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width = $height * $ratio;
            } elseif ($ratio == 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width = $widthLimit;
            }

            $top = $this->y - 15;
            $x1 = 280;
            $x2 = $x1 + $width;

            //coordinates after transformation are rounded by Zend
            $page->drawImage($image, $x1, $top, $x2, ($top + $height));
        }
    }

    /**
     * @param Zend_Pdf_Page $page
     * @throws LocalizedException
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        //columns headers
        $lines = [];
        $lines[0][] = [
            'text' => __('Qty'),
            'feed' => 50,
            'font' => 'italic'
        ];

        $lines[0][] = [
            'text' => __('Product'),
            'feed' => 100,
            'font' => 'italic'
        ];

        $lineBlock = [
            'lines' => $lines,
            'height' => 5
        ];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);

        $page->drawLine(30, $this->y, 570, $this->y);
        $this->y -= 20;


    }

    /**
     * @param $order
     * @param $page
     * @return Zend_Pdf_Page
     */
    protected function _printItems($order, $page)
    {
        /* Add body */
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($this->y < 15) {
                $page = $this->newPage(['table_header' => true]);
            }

            /* Draw item */
            $item->setQty($item->getQtyOrdered());
            $item->setOrderItem($item);
            $page = $this->_drawItem($item, $page, $order);
        }

        return $page;
    }

    /**
     * @param $order
     * @param $page
     * @throws LocalizedException
     */
    protected function _printFooter($order, $page)
    {
        $storeName = $this->_scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE);
        $store_email = $this->_scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
        $streetLine1 = $this->_scopeConfig->getValue('general/store_information/street_line1', ScopeInterface::SCOPE_STORE);
        $streetLine2 = $this->_scopeConfig->getValue('general/store_information/street_line2', ScopeInterface::SCOPE_STORE);
        $city = $this->_scopeConfig->getValue('general/store_information/city', ScopeInterface::SCOPE_STORE);
        $postcode = $this->_scopeConfig->getValue('general/store_information/postcode', ScopeInterface::SCOPE_STORE);
        $streetLine2 .= ', ' . $city . ', ' . $postcode;
        $this->y -= 10;
        $top = $this->y;
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->drawRectangle(30, $top, 580, $top - 80, Zend_Pdf_Page::SHAPE_DRAW_STROKE);

        $this->y -= 30;
        $lineBlock = [
            'lines' => [
                0 => [
                    [
                        'text' => __('Liquor and wine delivered fast!'),
                        'feed' => 60,
                        'font' => 'bold',
                        'font_size' => 18
                    ],
                ],
                1 => [
                    [
                        'text' => __('Get $10 off your first order with code GIFTED'),
                        'feed' => 60,
                        'font' => 'bold',
                        'font_size' => 18
                    ],
                ],
            ],
            'height' => 30
        ];

        $this->drawLineBlocks($page, [$lineBlock]);

        $this->y -= 15;
        $lineBlock = [
            'lines' => [
                0 => [
                    [
                        'text' => __("Need Help? Call {$storeName}!"),
                        'feed' => 30,
                    ],
                ],
                1 => [
                    [

                        'text' => $this->_scopeConfig->getValue('general/store_information/phone', ScopeInterface::SCOPE_STORE),
                        'feed' => 30,
                        'font' => 'bold',
                        'font_size' => 16
                    ],
                ],
                2 => [
                    [
                        'text' => $store_email,
                        'feed' => 30,
                        'font' => 'bold'
                    ],
                ],
                3 => [
                    [
                        'text' => $streetLine1,
                        'feed' => 30,
                        'font' => 'bold'
                    ],
                ],
                4 => [
                    [
                        'text' => $streetLine2,
                        'feed' => 30,
                        'font' => 'bold'
                    ],
                ],
            ],
            'height' => 15
        ];
        $this->drawLineBlocks($page, [$lineBlock]);
    }
}
