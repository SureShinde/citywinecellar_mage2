<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Ajax;

use Amasty\Blog\Api\VoteRepositoryInterface;
use Amasty\Blog\Model\Vote as VoteModel;
use Amasty\Blog\Model\VoteFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Vote
 */
class Vote extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var VoteRepositoryInterface
     */
    private $voteRepository;

    /**
     * @var VoteFactory
     */
    private $voteFactory;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        VoteFactory $voteFactory,
        VoteRepositoryInterface $voteRepository,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->logger = $logger;
        $this->jsonEncoder = $jsonEncoder;
        $this->remoteAddress = $remoteAddress;
        $this->voteRepository = $voteRepository;
        $this->voteFactory = $voteFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $message = [
            'error' => __('Sorry. There is a problem with your Vote Request.')
        ];

        if ($this->getRequest()->isPost() && $this->getRequest()->isAjax()) {
            try {
                $this->validateFormKey();

                $type = $this->getRequest()->getParam('type');
                $postId = (int)$this->getRequest()->getParam('post');
                if ($postId > 0 && in_array($type, ['plus', 'minus', 'update'])) {
                    $ip = $this->remoteAddress->getRemoteAddress();

                    if ($type != 'update') {
                        $type = ($type == 'plus') ? '1' : '0';

                        /** @var VoteModel $model */
                        $model = $this->voteRepository->getByIdAndIp($postId, $ip);
                        $modelType = $model->getType();
                        if ($model->getVoteId()) {
                            $this->voteRepository->delete($model);
                        }

                        if ($modelType === null || $modelType != $type) {
                            $model = $this->voteFactory->create();
                            $model->setIp($ip);
                            $model->setPostId($postId);
                            $model->setType($type);
                            $this->voteRepository->save($model);
                        }
                    }

                    $votesForPost = $this->voteRepository->getVotesCount($postId);
                    $voted = $this->voteRepository->getVotesCount($postId, $ip);
                    $message = [
                        'success' => __('Success.'),
                        'data' => $votesForPost,
                        'voted' => $voted
                    ];
                }
            } catch (LocalizedException $e) {
                $message = ['error' => $e->getMessage()];
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        $resultPage = $this->resultJsonFactory->create();
        $resultPage->setHttpResponseCode(200);
        $resultPage->setData($message);
        return $resultPage;
    }

    /**
     * @throws LocalizedException
     */
    private function validateFormKey()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new LocalizedException(
                __('Form key is not valid. Please try to reload the page.')
            );
        }
    }
}
