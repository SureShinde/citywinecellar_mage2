<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Categories;

use Amasty\Blog\Api\Data\CategoryInterface;
use Amasty\Blog\Model\Source\CategoryStatus;

/**
 * Class
 */
class MassActivate extends AbstractMassAction
{
    /**
     * @param CategoryInterface $category
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function itemAction($category)
    {
        try {
            $category->setStatus(CategoryStatus::STATUS_ENABLED);
            $this->getRepository()->save($category);
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
