<?php

declare(strict_types=1);

namespace Decima\FriendRecommendationsGraphQl\Model\Resolver\RecommendationList;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Decima\FriendRecommendationsGraphQl\Model\Resolver\DataProvider\RecommendationProductsDataProvider;

class RecommendationListProducts implements ResolverInterface
{
    /**
     * @var ValueFactory
     */
    private ValueFactory $valueFactory;

    /**
     * @var RecommendationProductsDataProvider
     */
    private RecommendationProductsDataProvider $productsDataProvider;

    /**
     * @param RecommendationProductsDataProvider $productsDataProvider
     * @param ValueFactory $valueFactory
     */
    public function __construct(
        RecommendationProductsDataProvider $productsDataProvider,
        ValueFactory $valueFactory
    ) {
        $this->valueFactory = $valueFactory;
        $this->productsDataProvider = $productsDataProvider;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws \Exception
     * @return Value|mixed
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $this->productsDataProvider->addRecommendationListId((int) $value['id']);
        $result = function () use ($value) {
            return $this->productsDataProvider->getRecommendationListProducts((int) $value['id']);
        };

        return $this->valueFactory->create($result);
    }
}
