<?php

namespace Laconica\Storepickup\Controller\Tips;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Layout;

class Update extends Action
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Update constructor.
     * @param Context $context
     * @param Session $checkoutSession
     */
    public function __construct(
        Context $context,
        Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Layout
     * @throws Exception
     */
    public function execute()
    {
        $tips = $this->getRequest()->getParam('tips');
        if ($tips >= 0) {
            $quote = $this->checkoutSession->getQuote();
            if ($quote) {
                $quote->setTips($tips);
                $quote->save();
            }
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData('ok');
        return $resultJson;
    }
}
