<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Repository;

use Amasty\Blog\Api\Data;
use Amasty\Blog\Api\Data\VoteInterface;
use Amasty\Blog\Api\VoteRepositoryInterface;
use Amasty\Blog\Model\Vote;
use Amasty\Blog\Model\VoteFactory;
use Amasty\Blog\Model\ResourceModel\Vote as ResourceVote;
use Amasty\Blog\Model\ResourceModel\Vote\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class VoteRepository
 */
class VoteRepository implements VoteRepositoryInterface
{
    /**
     * @var array
     */
    private $vote = [];

    /**
     * @var ResourceVote
     */
    private $voteResource;

    /**
     * @var VoteFactory
     */
    private $voteFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        ResourceVote $voteResource,
        VoteFactory $voteFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->voteResource = $voteResource;
        $this->voteFactory = $voteFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Data\VoteInterface $vote)
    {
        if ($vote->getVoteId()) {
            $vote = $this->get($vote->getVoteId())->addData($vote->getData());
        }

        try {
            $this->voteResource->save($vote);
            $this->vote[$vote->getVoteId()] = $vote;
        } catch (\Exception $e) {
            if ($vote->getVoteId()) {
                throw new CouldNotSaveException(
                    __('Unable to save vote with ID %1. Error: %2', [$vote->getVoteId(), $e->getMessage()])
                );
            }
            throw new CouldNotSaveException(__('Unable to save new vote. Error: %1', $e->getMessage()));
        }

        return $vote;
    }

    /**
     * {@inheritdoc}
     */
    public function get($voteId)
    {
        if (!isset($this->vote[$voteId])) {
            /** @var Vote $vote */
            $vote = $this->voteFactory->create();
            $this->voteResource->load($vote, $voteId);
            if (!$vote->getVoteId()) {
                throw new NoSuchEntityException(__('Vote with specified ID "%1" not found.', $voteId));
            }
            $this->vote[$voteId] = $vote;
        }

        return $this->vote[$voteId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByIdAndIp($postId, $ip)
    {
        /** @var Vote $vote */
        $vote = $this->voteFactory->create();

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(VoteInterface::POST_ID, $postId)
            ->addFieldToFilter(VoteInterface::IP, $ip);
        $collection->getSelect()->limit(1);

        if ($collection->getSize() > 0) {
            $vote = $collection->getFirstItem();
        }

        return $vote;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(VoteInterface $vote)
    {
        try {
            $this->voteResource->delete($vote);
            unset($this->vote[$vote->getVoteId()]);
        } catch (\Exception $e) {
            if ($vote->getVoteId()) {
                throw new CouldNotDeleteException(
                    __('Unable to remove vote with ID %1. Error: %2', [$vote->getVoteId(), $e->getMessage()])
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove vote. Error: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($voteId)
    {
        $model = $this->get($voteId);
        $this->delete($model);
        return true;
    }

    /**
     * @param $postId
     * @param null $ip
     * @return array
     */
    public function getVotesCount($postId, $ip = null)
    {
        $result = [
            'plus' => 0,
            'minus' => 0
        ];

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(VoteInterface::POST_ID, $postId);
        if ($ip) {
            $collection->addFieldToFilter(VoteInterface::IP, $ip);
        }

        foreach ($collection as $vote) {
            if ($vote->getType() == '1') {
                $result['plus'] = ++$result['plus'];
            } else {
                $result['minus'] = ++$result['minus'];
            }
        }

        return $result;
    }
}
