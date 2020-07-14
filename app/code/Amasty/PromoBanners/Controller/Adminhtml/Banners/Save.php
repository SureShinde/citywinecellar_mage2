<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */

namespace Amasty\PromoBanners\Controller\Adminhtml\Banners;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    /** @var \Amasty\PromoBanners\Model\Rule $_ruleModel */
    protected $_ruleModel;

    /** @var \Magento\MediaStorage\Model\File\Uploader $_uploaderFactory */
    protected $_uploaderFactory;
    protected $_filesystem;
    protected $_localFilter;
    protected $_localDate;
    protected $_jsHelper;
    protected $_timezone;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\PromoBanners\Model\Rule $ruleModel,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\LocalizedToNormalized $localFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localDate,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        parent::__construct($context);
        $this->_ruleModel = $ruleModel;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_filesystem = $filesystem;
        $this->_localFilter = $localFilter;
        $this->_localDate = $localDate;
        $this->_jsHelper = $jsHelper;
        $this->_timezone = $timezone;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->messageManager->addErrorMessage('Unable to find an option to save');
            $this->_redirect('*/*/edit', ['id' => $this->_ruleModel->getId()]);
        } else {

            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            if (isset($data['rule']['actions'])) {
                $data['actions'] = $data['rule']['actions'];
            }
            if (isset($data['segments_ids']) && is_array($data['segments_ids'])) {
                $data['segments'] = implode(',', $data['segments_ids']);
            } else {
                $data['segments'] = '';
            }

            unset($data['rule']);
            unset($data['segments_ids']);

            $path = $this->_filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath(
                'amasty/ampromobanners/'
            );
            $field = 'banner_img';
            if (!empty($this->getRequest()->getFiles($field)['name'])) {
                try {
                    $uploader = $this->_uploaderFactory->create(['fileId' => $field]);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setFilesDispersion(false);
                    $uploader->setAllowRenameFiles(false);

                    $fileName = $this->getRequest()->getFiles($field)['name'];
                    $result = $uploader->save($path, "banner_$id" . '_' . $fileName);
                    $fileName = $result['file'];
                    $data[$field] = $fileName;
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }

            try {
                $this->_ruleModel->setData($data)->loadPost($data)->setId($id);
                $this->_ruleModel->setFromDate($data['from_date']);
                $this->_ruleModel->setToDate($data['to_date']);

                $this->prepareForSave();

                $this->_ruleModel->getResource()->save($this->_ruleModel);

                $this->_getSession()->setFormData(false);

                $msg = __('Banner has been successfully saved');
                $this->messageManager->addSuccessMessage($msg);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $this->_ruleModel->getId()]);
                } else {
                    $this->_redirect('*/*');
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $id]);
            }
            return;
        }
    }

    public function prepareForSave()
    {
        $fields = ['stores', 'cust_groups', 'cats', 'banner_position'];
        foreach ($fields as $f) {
            // convert data from array to string
            $val = $this->_ruleModel->getData($f);

            if (is_array($val)) {
                $this->_ruleModel->setData($f, implode(',', $val));
            } else {
                $this->_ruleModel->setData($f, '');
            }
        }

        return true;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_PromoBanners::ampromobanners');
    }
}
