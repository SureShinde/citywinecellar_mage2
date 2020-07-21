<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RewardsGraphQl
 */


namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\RewardsGraphQl\Model\Rewards\QuoteApplier;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class UseRewardPoints extends AbstractResolver implements ResolverInterface
{
    /**
     * @var QuoteApplier
     */
    private $applier;

    public function __construct(
        ContextResolver $contextResolver,
        QuoteApplier $applier
    ) {
        parent::__construct($contextResolver);

        $this->applier = $applier;
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param \Magento\Framework\GraphQl\Config\Element\Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return string
     * @throws \Exception
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $currentUserId = $this->getCustomerId($context);

        return $this->applier->apply($currentUserId, $args['points']);
    }
}
