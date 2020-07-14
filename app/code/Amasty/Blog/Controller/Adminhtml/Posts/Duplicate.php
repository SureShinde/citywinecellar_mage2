<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Posts;

use Amasty\Blog\Api\Data\PostInterface;

class Duplicate extends \Amasty\Blog\Controller\Adminhtml\Posts
{
    /**
     * @return bool|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if (!$id) {
            $this->getMessageManager()->addErrorMessage(__('Please select a post to duplicate.'));

            return $this->_redirect('*/*');
        }
        try {
            $repository = $this->getPostRepository();
            $post = $repository->getById($id);

            $post->setPostId(null);
            $post->setStatus(0);
            $post->setTitle(__('Copy of ') . $post->getTitle());
            $post->setUrlKey($post->getUrlKey() . random_int(1, 1000));
            $this->copyImages($post);
            $repository->save($post);

            $this->getMessageManager()->addSuccessMessage(__('The post has been duplicated.'));

            return $this->_redirect('*/*/edit', ['id' => $post->getId()]);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
            $this->_redirect('*/*');

            return false;
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage(
                __('Something went wrong while saving the item data. Please review the error log.')
            );
            $this->getLogger()->critical($e);
            $this->_redirect('*/*');

            return false;
        }
    }

    /**
     * @param PostInterface $post
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function copyImages(PostInterface $post)
    {
        $postThumbnail = $post->getPostThumbnail();
        if ($postThumbnail) {
            $postThumbnail = $this->getImageProcessor()->copy($postThumbnail);
            $post->setPostThumbnail($postThumbnail);
            $post->setOrigData(PostInterface::POST_THUMBNAIL, $postThumbnail);
        }

        $listThumbnail = $post->getListThumbnail();
        if ($listThumbnail) {
            $listThumbnail = $this->getImageProcessor()->copy($listThumbnail);
            $post->setListThumbnail($listThumbnail);
            $post->setOrigData(PostInterface::LIST_THUMBNAIL, $listThumbnail);
        }
    }
}
