<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Pos;


class InlineEdit extends \Magestore\Webpos\Controller\Adminhtml\Pos\AbstractAction
{


    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);

            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $posId) {
                    $pos = $this->posRepository->getById($posId);
                    try {
                        $pos->setData(array_merge($pos->getData(), $postItems[$posId]));
                        $this->posRepository->save($pos);
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithPosId(
                            $pos,
                            __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * @param $pos
     * @param $errorText
     * @return string
     */
    public function getErrorWithPosId( $pos, $errorText)
    {
        return '[Pos ID: ' . $pos->getId() . '] ' . $errorText;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::pos');
    }

}
