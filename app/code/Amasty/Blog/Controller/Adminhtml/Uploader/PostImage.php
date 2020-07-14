<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Uploader;

use Magento\Framework\Controller\ResultFactory;
use Amasty\Blog\Api\Data\PostInterface;

/**
 * Class PostImage
 */
class PostImage extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Amasty_Blog::posts';

    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    private $imageUploader;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Upload file controller action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $imageField = '';
            foreach ($this->getRequest()->getFiles() as $key => $file) {
                $imageField = $key;
                break;
            }
            $result = $this->imageUploader->saveFileToTmpDir($imageField);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
