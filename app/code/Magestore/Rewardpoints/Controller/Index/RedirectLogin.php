<?php
namespace Magestore\Rewardpoints\Controller\Index;

class RedirectLogin extends \Magestore\Rewardpoints\Controller\AbstractAction
{
    public function execute()
    {
        if (!$this->_customerSessionFactory->create()->isLoggedIn()) {
            $url =$this->getRequest()->getParam('redirect');
            if($url){
                $url =  urldecode($this->getRequest()->getParam('redirect'));
            }else{
                $url = $this->_sessionManager->getData('redirect');
            }
            if (strpos($url, 'checkout/onepage') === true) {
                $url = $this->getUrl('checkout/onepage/index');
            }
            $this->_customerSessionFactory->create()->setAfterAuthUrl($url);
        }
        $this->_redirect('customer/account/login');
    }
}
