<?php

declare(strict_types=1);

namespace Decima\FriendRecommendationsGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Decima\FriendRecommendationsGraphQl\Model\Resolver\Service\RecommendationListService;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class CreateRecommendationList implements ResolverInterface
{
    private RecommendationListService $recommendationListService;

    public function __construct(RecommendationListService $recommendationListService)
    {
        $this->recommendationListService = $recommendationListService;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return Value|mixed
     * @throws GraphQlInputException
     * @throws CouldNotSaveException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args['email'])
            || !isset($args['friendName'])
            || !isset($args['productSkus'])) {
            throw new GraphQlInputException(__('The required list data was not provided.'));
        }

        return $this->recommendationListService->execute(
            $args['email'],
            $args['friendName'],
            $args['productSkus'],
            $args['title'] ?? null,
            $args['note'] ?? null
        );
    }
}
