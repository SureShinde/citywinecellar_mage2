<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Giftvoucher Index UploadImageAjax Action
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class UploadImageAjax extends \Magestore\Giftvoucher\Controller\Action implements HttpPostActionInterface
{

    /**
     * Upload images action
     */
    public function execute()
    {
        $fileRequest = $this->getRequest()->getFiles();
        $result = [];
        if (isset($fileRequest['templateimage'])) {
            try {
                $uploader = $this->_objectManager->create(
                    \Magento\Framework\File\Uploader::class,
                    ['fileId' => 'templateimage']
                );
                $uploader->setAllowedExtensions(['jpg','jpeg','gif','png']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $this->getHelper()->createImageFolderHaitv('', '', true);
                $fileName = $fileRequest['templateimage']['name'];
                $result = $uploader->save(
                    $this->getFileSystem()->getDirectoryRead('media')->getAbsolutePath('tmp/giftvoucher/images')
                );
                $result['url'] = $this->_storeManager->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                    . 'tmp/giftvoucher/images/' . $result['file'];

                $result['filename']= $fileName;
                $result['sucess'] = true;
            } catch (\Exception $e) {
                $result['sucess'] = false;
                $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
            }
        } else {
            $this->messageManager->addError(__('Image Saving Error!'));
            $result['sucess'] = false;
            $result = ['error' => __('Image Saving Error!')];
        }
        $this->getResponse()->setBody(
            $this->_objectManager->create(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
    }
}
