<?php
namespace Magestore\Webpos\Plugin\Block\Adminhtml\Order\View;

/**
 * Class Info
 * @package Magestore\Webpos\Plugin\Block\Adminhtml\Order\View
 */
class Info extends \Magento\Sales\Block\Adminhtml\Order\View\Info
{
    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeToHtml(\Magento\Sales\Block\Adminhtml\Order\View\Info $subject) {

        if (!$subject->getParentBlock()) {
            $order = $subject->getOrder();
        } else {
            $order = $subject->getParentBlock()->getOrder();
        }
        $posId = $order->getPosId();
        if ($posId) {
            $subject->setTemplate('Magestore_Webpos::sales/order/view/info.phtml');
        }
    }
}