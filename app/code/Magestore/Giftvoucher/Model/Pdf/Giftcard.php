<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Pdf;

/**
 * Class Giftcard
 *
 * Pdf gift card model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Giftcard extends \Magento\Framework\DataObject
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directorylist;

    /**
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    protected $fileDriver;

    protected $y;
    /**
     * @var \Zend_Pdf
     */
    protected $_pdf;

    /**
     * Giftcard constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directorylist
     * @param \Magento\Framework\Filesystem\DriverInterface $fileDriver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist,
        \Magento\Framework\Filesystem\DriverInterface $fileDriver,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_directorylist = $directorylist;
        $this->fileDriver = $fileDriver;
        parent::__construct(
            $data
        );
    }

    /**
     * Get Pdf
     *
     * @param array $giftvoucherIds
     *
     * @return \Zend_Pdf
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Zend_Pdf_Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * phpcs:disable Generic.Metrics.NestingLevel
     */
    public function getPdf($giftvoucherIds)
    {
        if ($giftvoucherIds) {
            $pdf = new \Zend_Pdf();
            $this->_setPdf($pdf);
            $style = new \Zend_Pdf_Style();
            $this->_setFontBold($style, 10);

            $giftvoucherIds = array_chunk($giftvoucherIds, 3);

            foreach ($giftvoucherIds as $giftvouchers) {
                $page = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
                $pdf->pages[] = $page;
                $this->y = 790;
                foreach ($giftvouchers as $giftvoucherId) {
                    $giftvoucher = $this->_objectManager->create(\Magestore\Giftvoucher\Model\Giftvoucher::class)
                        ->load($giftvoucherId);
                    $gifttemplate = $this->_objectManager->create(\Magestore\Giftvoucher\Model\GiftTemplate::class)
                        ->load($giftvoucher['giftcard_template_id']);

                    // resize the width image to 300px
                    if ($gifttemplate && $gifttemplate['design_pattern'] != 4) {
                        if ($giftvoucher->getId()) {
                            $newImgWidth = ($page->getWidth() - 300) / 2;

                            $images = $this->_objectManager->get(\Magestore\Giftvoucher\Helper\Drawgiftcard::class)
                                ->getImagesInFolder($giftvoucher['gift_code']);
                            if (isset($images[0]) && $this->fileDriver->isFile($images[0])) {
                                $image = \Zend_Pdf_Image::imageWithPath($images[0]);
                                $page->drawImage($image, $newImgWidth, $this->y - 183, $newImgWidth + 300, $this->y);
                            }
                        }
                    } else {
                        if ($giftvoucher->getId()) {
                            $newImgWidth = ($page->getWidth() - 300) / 2;
                            $images = $this->_objectManager->get(\Magestore\Giftvoucher\Helper\Drawgiftcard::class)
                                ->getImagesInFolder($giftvoucher['gift_code']);
                            if ($giftvoucher['message'] && $giftvoucher['message'] != '') {
                                if (isset($images[0]) && $this->fileDriver->isFile($images[0])) {
                                    $image = \Zend_Pdf_Image::imageWithPath($images[0]);
                                    $page->drawImage(
                                        $image,
                                        $newImgWidth,
                                        $this->y - 265,
                                        $newImgWidth + 300,
                                        $this->y
                                    );
                                }
                            } else {
                                if (isset($images[0]) && $this->fileDriver->isFile($images[0])) {
                                    $image = \Zend_Pdf_Image::imageWithPath($images[0]);
                                    $page->drawImage(
                                        $image,
                                        $newImgWidth,
                                        $this->y - 219,
                                        $newImgWidth + 300,
                                        $this->y
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        return $pdf;
    }

    /**
     * Set PDF object
     *
     * @param \Zend_Pdf $pdf
     *
     * @return $this
     */
    public function _setPdf(\Zend_Pdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Set font as bold
     *
     * @param \Zend_Pdf_Page $object
     * @param int $size
     *
     * @return \Zend_Pdf_Resource_Font
     * @throws \Zend_Pdf_Exception
     */
    public function _setFontBold($object, $size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithPath(
            $this->_directorylist->getRoot() . '/lib/internal/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf'
        );
        $object->setFont($font, $size);
        return $font;
    }
}
