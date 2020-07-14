<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Content\Post;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\View\Element\Template;

/**
 * Class Helpful
 */
class Helpful extends \Magento\Framework\View\Element\Template
{
    const VOTED_CLASS_NAME = '-voted';

    /**
     * @var string
     */
    protected $_template = 'Amasty_Blog::post/helpful.phtml';

    /**
     * @var array
     */
    private $vote;

    /**
     * @var array
     */
    private $voteByCurrentIp;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;

    /**
     * @var \Amasty\Blog\Api\VoteRepositoryInterface
     */
    private $voteRepository;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Amasty\Blog\Api\VoteRepositoryInterface $voteRepository,
        RemoteAddress $remoteAddress,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->formKey = $formKey;
        $this->voteRepository = $voteRepository;
        $this->remoteAddress = $remoteAddress;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return array
     */
    private function getVotedByIp()
    {
        if (!$this->voteByCurrentIp) {
            $this->voteByCurrentIp = $this->voteRepository->getVotesCount(
                $this->getReview()->getId(),
                $this->remoteAddress->getRemoteAddress()
            );
        }

        return $this->voteByCurrentIp;
    }

    /**
     * @return \Amasty\Blog\Model\Posts
     */
    public function getPost()
    {
        return $this->getData('post');
    }

    /**
     * @return int
     */
    public function getPlusReview()
    {
        $vote = $this->getVote();

        return $vote['plus'];
    }

    /**
     * @return int
     */
    public function getMinusReview()
    {
        $vote = $this->getVote();

        return $vote['minus'];
    }

    /**
     * @return array
     */
    private function getVote()
    {
        if (!$this->vote) {
            $this->vote = $this->voteRepository->getVotesCount($this->getReview()->getId());
        }

        return $this->vote;
    }

    /**
     * @return bool
     */
    public function isPlusVoted()
    {
        $voted = $this->getVotedByIp();

        return $voted['plus'] > 0;
    }

    /**
     * @return bool
     */
    public function isMinusVoted()
    {
        $voted = $this->getVotedByIp();

        return $voted['minus'] > 0;
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        return $this->jsonEncoder->encode([
            'url' => $this->getUrl('amblog/ajax/vote')
        ]);
    }

    /**
     * @return string
     */
    public function getPlusVotedClass()
    {
        $result = '';
        if ($this->isPlusVoted()) {
            $result = self::VOTED_CLASS_NAME;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getMinusVotedClass()
    {
        $result = '';
        if ($this->isMinusVoted()) {
            $result = self::VOTED_CLASS_NAME;
        }

        return $result;
    }
}
