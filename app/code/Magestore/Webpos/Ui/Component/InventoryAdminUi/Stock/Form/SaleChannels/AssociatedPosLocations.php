<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Ui\Component\InventoryAdminUi\Stock\Form\SaleChannels;

use Magento\Ui\Component\Form\Field;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class AssociatedPosLocations
 * @package Magestore\Webpos\Ui\Component\InventoryAdminUi\Stock\Form\SaleChannels
 */
class AssociatedPosLocations extends Field
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    
    /**
     * AssociatedPosLocations constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\App\RequestInterface $request,
        array $components = [],
        array $data = []
    )
    {
        $this->request = $request;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Remove Associated Pos Locations if create new Stock
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $stockId = $this->request->getParam('stock_id');
        if (!$stockId || $stockId < 1) {
            $config['visible'] = false;
        }
        /*if($config && isset($config['options']) && is_array($config['options'])) {
            if(empty($config['options'])) {
                $config['disabled'] = true;
            }
        } else {
            $config['disabled'] = true;
        }*/
        $this->setData('config', $config);
    }
}
