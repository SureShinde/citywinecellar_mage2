<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Adapter\DataMapper;

use Magento\AdvancedSearch\Model\Adapter\DataMapper\AdditionalFieldsProvider as DefaultAdditionalFieldsProvider;
use Magento\AdvancedSearch\Model\Adapter\DataMapper\AdditionalFieldsProviderInterface;

/**
 * Provide additional fields for data mapper during search indexer
 *
 * Must return array with the following format: [[product id] => [field name1 => value1, ...], ...]
 */
class AdditionalFieldsProvider extends DefaultAdditionalFieldsProvider implements AdditionalFieldsProviderInterface
{
}
