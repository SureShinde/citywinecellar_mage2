<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Posts;

use Amasty\Blog\Api\Data\PostInterface;
use Amasty\Blog\Model\Source\PostStatus;

/**
 * Class MassInactivate
 */
class MassInactivate extends AbstractMassAction
{
    /**
     * @param PostInterface $post
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function itemAction($post)
    {
        try {
            $post->setStatus(PostStatus::STATUS_DISABLED);
            $this->getRepository()->save($post);
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
