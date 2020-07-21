<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RewardsGraphQl
 */


namespace Amasty\RewardsGraphQl\Model\Resolver;

use Amasty\RewardsGraphQl\Model\Rewards\DataProvider;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Rewards extends AbstractResolver implements ResolverInterface
{
    /**
     * @var DataProvider
     */
    private $dataProvider;

    public function __construct(
        ContextResolver $contextResolver,
        DataProvider $dataProvider
    ) {
        parent::__construct($contextResolver);

        $this->dataProvider = $dataProvider;
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
     * @return array
     * @throws \Exception
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $currentUserId = $this->getCustomerId($context);

        return [
            'balance' => $this->isFieldRequested('balance', $info)
                ? $this->dataProvider->getCustomerBalance($currentUserId) : 0,
            'history' => [
                'total_count' => $this->isFieldRequested('history/total_count', $info)
                    ? $this->dataProvider->getTotalHistoryRecordsCount($currentUserId) : 0,
            ],
            'highlight' => []
        ];
    }
}
