<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RewardsGraphQl
 */


namespace Amasty\RewardsGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

abstract class AbstractResolver
{
    /**
     * @var ContextResolver
     */
    private $contextResolver;

    /**
     * @var array
     */
    private $requestedFields = [];

    public function __construct(
        ContextResolver $contextResolver
    ) {
        $this->contextResolver = $contextResolver;
    }

    /**
     * @param ContextInterface $context
     * @return int
     */
    public function getCustomerId($context): int
    {
        return $this->contextResolver->getCustomerId($context);
    }

    /**
     * @param String $field
     * @param ResolveInfo $info
     * @param int $depth
     *
     * @return bool
     */
    public function isFieldRequested(String $field, ResolveInfo $info, int $depth = 10)
    {
        if (!$this->requestedFields) {
            $requestedFields = $info->getFieldSelection($depth);
            $this->requestedFields = $this->arrayWalker($requestedFields);
        }

        return array_search($field, $this->requestedFields) !== false;
    }

    /**
     * @param array $array
     * @param string $path
     * @return array
     */
    private function arrayWalker(array $array, string $path = ''): array
    {
        $parsedData = [];

        foreach ($array as $key => $fields) {
            $currentPath = $path . '/' . $key;
            $parsedData[] = trim($currentPath, '/');

            if (is_array($fields)) {
                $parsedData = array_merge_recursive($parsedData, $this->arrayWalker($fields, $currentPath));
            }
        }

        return $parsedData;
    }
}
