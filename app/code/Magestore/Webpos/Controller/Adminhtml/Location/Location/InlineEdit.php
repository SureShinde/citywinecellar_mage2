<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Location\Location;
/**
 * class \Magestore\Webpos\Controller\Adminhtml\Location\Location\Index
 *
 * List staff
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Location\Location
 * @module      Webpos
 * @author      Magestore Developer
 */
class InlineEdit extends \Magestore\Webpos\Controller\Adminhtml\Location\Location
{
    protected $resource = 'Magestore_Webpos::location_edit';

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultData = [];
        $data = $this->getRequest()->getParam('items', []);

        if (!($this->getRequest()->getParam('isAjax') && count($data))) {
            $resultData = [
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ];
        } else {
            foreach ($data as $id => $item) {
                $location = $this->locationRepository->getById($id);
                $location->addData($item);
                $this->locationRepository->save($location);
            }
            $resultData = [
                'messages' => $this->getErrorMessages(),
                'error' => $this->isErrorExists(),

            ];
        }

        return $this->jsonResult($resultData);
    }

    /**
     * @return array
     */
    public function getErrorMessages()
    {
        $messages = [];
        foreach ($this->getMessageManager()->getMessages()->getItems() as $error) {
            $messages[] = $error->getText();
        }
        return $messages;
    }

    /**
     * @return bool
     */
    public function isErrorExists()
    {
        return (bool)$this->getMessageManager()->getMessages(true)->getCount();
    }
}