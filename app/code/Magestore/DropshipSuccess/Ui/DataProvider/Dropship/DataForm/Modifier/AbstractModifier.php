<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Ui\DataProvider\Dropship\DataForm\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class AbstractModifier
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractModifier extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier implements ModifierInterface
{
    const FORM_NAME = 'os_dropship_form';
    const DATA_SOURCE_DEFAULT = 'dropship';
    const DATA_SCOPE_PRODUCT = 'data.dropship';
}
