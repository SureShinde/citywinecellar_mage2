<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Repository;

use Amasty\Blog\Api\Data\PostInterface;
use Amasty\Blog\Api\Data\ViewInterface;
use Amasty\Blog\Api\ViewRepositoryInterface;
use Amasty\Blog\Model\ViewFactory;
use Amasty\Blog\Model\ResourceModel\View\CollectionFactory;
use Amasty\Blog\Model\ResourceModel\View as ViewResource;
use Magento\Customer\Model\Session\Proxy as CustomerSessionProxy;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ViewRepository implements ViewRepositoryInterface
{
    /**
     * @var ViewFactory
     */
    private $viewFactory;

    /**
     * @var ViewResource
     */
    private $viewResource;

    /**
     * @var ViewInterface[]|null
     */
    private $views;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var CollectionFactory
     */
    private $viewCollectionFactory;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    public function __construct(
        ViewFactory $viewFactory,
        ViewResource $viewResource,
        CollectionFactory $viewCollectionFactory,
        StoreManagerInterface $storeManager,
        RemoteAddress $remoteAddress,
        \Magento\Customer\Model\SessionFactory $sessionFactory
    ) {
        $this->viewFactory = $viewFactory;
        $this->viewResource = $viewResource;
        $this->viewCollectionFactory = $viewCollectionFactory;
        $this->storeManager = $storeManager;
        $this->remoteAddress = $remoteAddress;
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * @param int $postId
     *
     * @return int
     */
    public function getViewCountByPostId($postId)
    {
        return $this->viewCollectionFactory->create()
            ->addFieldToFilter(PostInterface::POST_ID, $postId)
            ->getSize();
    }

    /**
     * @param int $postId
     * @param null $refererUrl
     *
     * @return bool
     */
    public function create($postId, $refererUrl = null)
    {
        $viewModel = $this->viewFactory->create();
        $this->viewResource->loadByPostAndSession($viewModel, $postId, $this->getCustomerSession()->getSessionId());
        if (!$viewModel->getId()) {
            try {
                $now = new \Zend_Date();
                $customerId = $this->getCustomerSession()->isLoggedIn()
                    ? $this->getCustomerSession()->getCustomerId()
                    : null;
                $viewModel
                    ->setPostId($postId)
                    ->setCustomerId($customerId)
                    ->setSessionId($this->getCustomerSession()->getSessionId())
                    ->setRemoteAddr($this->remoteAddress->getRemoteAddress(true))
                    ->setStoreId($this->storeManager->getStore()->getId())
                    ->setCreatedAt($now->toString(DateTime::DATETIME_INTERNAL_FORMAT))
                    ->setRefererUrl($refererUrl);
                $this->save($viewModel);
            } catch (\Exception $e) {
                null;// Do nothing
            }
        }

        return true;
    }

    /**
     * @param ViewInterface $view
     *
     * @return ViewInterface
     * @throws CouldNotSaveException
     */
    public function save(ViewInterface $view)
    {
        try {
            if ($view->getId()) {
                $view = $this->getById($view->getId())->addData($view->getData());
            }
            $this->viewResource->save($view);
            unset($this->views[$view->getId()]);
        } catch (\Exception $e) {
            if ($view->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save view with ID %1. Error: %2',
                        [$view->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new view. Error: %1', $e->getMessage()));
        }

        return $view;
    }

    /**
     * @param int $viewId
     *
     * @return ViewInterface
     * @throws NoSuchEntityException
     */
    public function getById($viewId)
    {
        if (!isset($this->views[$viewId])) {
            /** @var \Amasty\Blog\Model\View $view */
            $view = $this->viewFactory->create();
            $this->viewResource->load($view, $viewId);
            if (!$view->getId()) {
                throw new NoSuchEntityException(__('View with specified ID "%1" not found.', $viewId));
            }
            $this->views[$viewId] = $view;
        }

        return $this->views[$viewId];
    }

    /**
     * @param ViewInterface $view
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ViewInterface $view)
    {
        try {
            $this->viewResource->delete($view);
            unset($this->views[$view->getId()]);
        } catch (\Exception $e) {
            if ($view->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove view with ID %1. Error: %2',
                        [$view->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove view. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $viewId
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($viewId)
    {
        $viewModel = $this->getById($viewId);
        $this->delete($viewModel);

        return true;
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    private function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }
}
