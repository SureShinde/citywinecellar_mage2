<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RewardsGraphQl
 */


namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\Rewards\Api\Data\HighlightInterface;
use Amasty\Rewards\Api\GuestHighlightManagementInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GuestHighlight implements ResolverInterface
{
    /**
     * @var GuestHighlightManagementInterface
     */
    private $guestHighlightManagement;

    public function __construct(GuestHighlightManagementInterface $guestHighlightManagement)
    {
        $this->guestHighlightManagement = $guestHighlightManagement;
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
        return $this->guestHighlightManagement->getHighlight($args['page']);
    }
}
