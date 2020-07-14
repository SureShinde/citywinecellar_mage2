<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Index;

/**
 * Class
 */
class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Amasty\Blog\Api\ViewRepositoryInterface
     */
    private $viewRepository;

    /**
     * @var \Magento\Store\App\Response\Redirect
     */
    private $redirect;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Amasty\Blog\Api\ViewRepositoryInterface $viewRepository
    ) {
        parent::__construct($context);
        $this->viewRepository = $viewRepository;
    }

    public function execute()
    {
        $postId = (int)$this->getRequest()->getParam("post_id");
        if ($postId) {
            $this->viewRepository->create($postId);
        }
    }
}
