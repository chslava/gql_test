<?php

namespace Decima\FriendRecommendationsGraphQl\Model\Resolver\Service;

use SwiftOtter\FriendRecommendations\Api\Data\RecommendationListProductInterface;
use SwiftOtter\FriendRecommendations\Api\Data\RecommendationListProductInterfaceFactory;
use SwiftOtter\FriendRecommendations\Api\RecommendationListProductRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;

class RecommendationListProductsService
{
    /**
     * @var RecommendationListProductInterfaceFactory
     */
    private RecommendationListProductInterfaceFactory $recommendationListProductFactory;

    /**
     * @var RecommendationListProductRepositoryInterface
     */
    private RecommendationListProductRepositoryInterface $recommendationListProductRepository;

    /**
     * @param RecommendationListProductInterfaceFactory $recommendationListProductFactory
     * @param RecommendationListProductRepositoryInterface $recommendationListProductRepository
     */
    public function __construct(
        RecommendationListProductInterfaceFactory $recommendationListProductFactory,
        RecommendationListProductRepositoryInterface $recommendationListProductRepository
    ) {
        $this->recommendationListProductFactory = $recommendationListProductFactory;
        $this->recommendationListProductRepository = $recommendationListProductRepository;
    }

    /**
     * @param int $listId
     * @param string[] $skus
     * @return void
     * @throws CouldNotSaveException
     */
    public function execute(int $listId, array $skus): void
    {
        $skus = array_unique($skus);
        foreach ($skus as $sku) {
            $listProduct = $this->recommendationListProductFactory->create();
            $listProduct->setListId($listId);
            $listProduct->setSku($sku);
            $this->recommendationListProductRepository->save($listProduct);
        }
    }
}
