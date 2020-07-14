<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Website;
use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * Class WebsiteInformationRepository
 * @package Magestore\Webpos\Model\Website
 */
class WebsiteInformationRepository implements \Magestore\Webpos\Api\Website\WebsiteInformationRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Api\Data\Website\WebsiteInformationInterface
     */
    protected $websiteInformation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    protected $urlBuilder;
    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $webposFileStorageHelper;

    protected $fileSystem;

    /**
     * Media Directory
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $mediaDirectory;

    protected $template;

    /**
     * WebsiteInformationRepository constructor.
     * @param \Magestore\Webpos\Api\Data\Website\WebsiteInformationInterface $websiteInformation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Website\WebsiteInformationInterface $websiteInformation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\MediaStorage\Helper\File\Storage\Database $webposFileStorageHelper,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\View\Element\Template $template
    ){
        $this->websiteInformation = $websiteInformation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->webposFileStorageHelper = $webposFileStorageHelper;
        $this->fileSystem = $fileSystem;
        $this->template = $template;
    }

    /**
     * get all config
     *
     * @return \Magestore\Webpos\Api\Data\Website\WebsiteInformationInterface
     */
    public function getInformation() {
        $image = $this->scopeConfig->getValue('webpos/general/webpos_logo');
        if ($image) {
            $imageUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'webpos/logo/' . $image;
        } else {
            $imageUrl = $this->getStoreLogoUrl();
        }
        $this->websiteInformation->setLogoUrl($imageUrl);
        return $this->websiteInformation;
    }

    /**
     * @return string
     */
    public function getStoreLogoUrl()
    {
        $uploadFolderName = \Magento\Config\Model\Config\Backend\Image\Logo::UPLOAD_DIR;
        $webposLogoPath = $this->scopeConfig->getValue(
            'design/header/logo_src',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $path = $uploadFolderName . '/' . $webposLogoPath;
        $logoUrl = $this->urlBuilder
                ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;
        if ($webposLogoPath !== null && $this->isFile($path)) {
            $url = $logoUrl;
        } else {
            $url = '';
        }
        return $url;
    }

    /**
     * @param $filename
     * @return bool
     */
    public function isFile($filename)
    {
        if ($this->webposFileStorageHelper->checkDbUsage() && !$this->getMediaDirectory()->isFile($filename)) {
            $this->webposFileStorageHelper->saveFileToFilesystem($filename);
        }

        return $this->getMediaDirectory()->isFile($filename);
    }

    /**
     * Get media directory
     *
     * @return \Magento\Framework\Filesystem\Directory\Read
     */
    public function getMediaDirectory()
    {
        if (!$this->mediaDirectory) {
            $this->mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }
}