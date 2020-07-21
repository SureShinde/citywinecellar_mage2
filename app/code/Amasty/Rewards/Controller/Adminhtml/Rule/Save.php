<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Rewards\Controller\Adminhtml\Rule;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends \Amasty\Rewards\Controller\Adminhtml\Rule
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\Rewards\Model\RuleFactory $rewardsRuleFactory,
        \Amasty\Rewards\Api\RuleRepositoryInterface $ruleRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->productRepository = $productRepository;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context, $coreRegistry, $rewardsRuleFactory, $ruleRepository);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->getRequest()->getPostValue();
            $rewardRuleId = (int)$this->getRequest()->getParam('id');

            try {
                /** @var \Amasty\Rewards\Model\Rule $model */
                $model = $this->rewardsRuleFactory->create();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();

                $data['promo_skus'] = $data['promo_skus'] ? $this->preparePromoSkus($data['promo_skus']) : null;

                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                unset($data['rule']);

                $model->setId($rewardRuleId);
                $model->addData($data);
                $model->loadPost($data);

                $this->ruleRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the rule.'));
                $this->dataPersistor->clear(\Amasty\Rewards\Model\ConstantRegistryInterface::FORM_NAMESPACE);
                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                }
                return $this->_redirect('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->dataPersistor->set(\Amasty\Rewards\Model\ConstantRegistryInterface::FORM_NAMESPACE, $data);
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($rewardRuleId) {
                    return $this->_redirect('*/*/edit', ['id' => $rewardRuleId]);
                }

                return $this->_redirect('*/*/new');
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * @param string $promoSkus
     * @return string
     */
    private function preparePromoSkus($promoSkus)
    {
        $promoSkus = \array_map(function ($sku) {
            $sku = trim($sku);
            try {
                $this->productRepository->get($sku);
            } catch (NoSuchEntityException $e) {
                throw new NoSuchEntityException(__('Product with specified SKU "%1" not found.', $sku), $e);
            }
            return $sku;
        }, \explode(',', $promoSkus));

        $promoSkus = \array_unique($promoSkus);
        $promoSkus = implode(',', $promoSkus);

        return $promoSkus;
    }
}
