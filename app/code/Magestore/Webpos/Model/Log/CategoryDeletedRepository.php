<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Log;
/**
 * Class CategoryDeletedRepository
 * @package Magestore\Webpos\Model\Log
 */
class CategoryDeletedRepository implements \Magestore\Webpos\Api\Log\CategoryDeletedRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface
     */
    protected $categoryDeleted;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted
     */
    protected $categoryDeletedResource;

    /**
     * CategoryDeletedRepository constructor.
     * @param \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface $categoryDeleted
     * @param \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted $categoryDeletedResource
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface $categoryDeleted,
        \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted $categoryDeletedResource
    )
    {
        $this->categoryDeleted = $categoryDeleted;
        $this->categoryDeletedResource = $categoryDeletedResource;
    }
    /**
     * @param \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface $categoryDeleted
     * @return \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface $categoryDeleted){
        try {
            $this->categoryDeletedResource->save($categoryDeleted);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save category deleted'));
        }
        return $categoryDeleted;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface $categoryDeleted
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface $categoryDeleted){
        return $this->deleteById($categoryDeleted->getId());
    }

    /**
     * @param int $categoryId
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($categoryId){
        try {
            $categoryDeleted = $this->getById($categoryId);
            $this->categoryDeletedResource->delete($categoryDeleted);
            return true;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__('Unable to delete category deleted'));
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getById($categoryId) {
        $categoryDeleted = $this->categoryDeleted;
        $this->categoryDeletedResource->load($categoryDeleted, $categoryId);
        if (!$categoryDeleted->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Category with id "%1" does not exist.', $categoryId));
        } else {
            return $categoryDeleted;
        }
    }
}
