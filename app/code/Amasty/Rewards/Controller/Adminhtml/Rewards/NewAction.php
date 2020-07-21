<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Controller\Adminhtml\Rewards;

use Amasty\Rewards\Api\Data\ExpirationArgumentsInterface;
use Amasty\Rewards\Api\Data\ExpirationArgumentsInterfaceFactory;
use Amasty\Rewards\Api\RewardsProviderInterface;
use Amasty\Rewards\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\LayoutFactory;

class NewAction extends \Amasty\Rewards\Controller\Adminhtml\Rewards
{
    /**
     * @var RewardsProviderInterface
     */
    private $rewardsProvider;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var ExpirationArgumentsInterfaceFactory
     */
    private $expirationArgFactory;

    public function __construct(
        Context $context,
        ExpirationArgumentsInterfaceFactory $expirationArgFactory,
        RewardsProviderInterface $rewardsProvider,
        LayoutFactory $layoutFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);

        $this->expirationArgFactory = $expirationArgFactory;
        $this->rewardsProvider = $rewardsProvider;
        $this->layoutFactory = $layoutFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $hasError = false;
        $amount = $this->getRequest()->getParam('amount');
        $customerId = $this->getRequest()->getParam('customer_id');
        $comment = $this->getRequest()->getParam('comment');
        $action = $this->getRequest()->getParam('action');

        try {
            switch ($action) {
                case 'add':
                    $expiration = $this->getRequest()->getParam('expiration');
                    /** @var ExpirationArgumentsInterface $expire */
                    $expire = $this->expirationArgFactory->create();
                    $expire->setIsExpire(!empty($expiration[ExpirationArgumentsInterface::IS_EXPIRE]));

                    if (isset($expiration[ExpirationArgumentsInterface::DAYS])) {
                        $expire->setDays($expiration[ExpirationArgumentsInterface::DAYS]);
                    }

                    $this->rewardsProvider->addPoints($amount, $customerId, Data::ADMIN_ACTION, $comment, $expire);

                    break;
                case 'deduct':
                    $this->rewardsProvider->deductPoints($amount, $customerId, Data::ADMIN_ACTION, $comment);
            }
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $hasError = true;
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong. Please review the error log.')
            );

            $hasError = true;
        }

        if ($this->getRequest()->getPost('return_session_messages_only')) {
            /** @var $block \Magento\Framework\View\Element\Messages */
            $block = $this->layoutFactory->create()->getMessagesBlock();
            $block->setMessages($this->messageManager->getMessages(true));
            $body = [
                'messages' => $block->getGroupedHtml(),
                'error' => $hasError
            ];

            return $this->resultJsonFactory->create()->setData($body);
        }
    }
}
