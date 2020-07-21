<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RewardsGraphQl
 */


namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\Rewards\Api\CatalogHighlightManagementInterface;
use Amasty\Rewards\Api\Data\HighlightInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class HighlightCategory extends AbstractResolver implements ResolverInterface
{
    /**
     * @var CatalogHighlightManagementInterface
     */
    private $highlightManagement;

    public function __construct(
        ContextResolver $contextResolver,
        CatalogHighlightManagementInterface $highlightManagement
    ) {
        parent::__construct($contextResolver);

        $this->highlightManagement = $highlightManagement;
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return HighlightInterface
     * @throws \Exception
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $this->getCustomerId($context);
        $attributes = isset($args['attributes']) ? $args['attributes'] : null;

        return $this->highlightManagement->getHighlightForCategory($args['productId'], $customerId, $attributes);
    }
}
