<?php

declare(strict_types=1);

namespace Decima\FriendRecommendationsGraphQl\Model\Resolver\DataProvider;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use SwiftOtter\FriendRecommendations\Api\RecommendationListProductRepositoryInterface;

class RecommendationProductsDataProvider
{
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;

    /**
     * @var RecommendationListProductRepositoryInterface
     */
    private RecommendationListProductRepositoryInterface $recommendationListProductRepository;

    /**
     * @var array
     */
    private array $recommendationListIds = [];

    /**
     * @var array
     */
    private array $recommendationProducts = [];

    /**
     * @var array
     */
    private array $products = [];

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param RecommendationListProductRepositoryInterface $recommendationListProductRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        ProductRepositoryInterface   $productRepository,
        RecommendationListProductRepositoryInterface $recommendationListProductRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->recommendationListProductRepository = $recommendationListProductRepository;
    }

    /**
     * @param int $id
     * @return void
     */
    public function addRecommendationListId(int $id): void
    {
        if (!in_array($id, $this->recommendationListIds)) {
            $this->recommendationListIds[] = $id;
        }
    }

    /**
     * @param int $listId
     * @return array
     */
    public function getRecommendationListProducts(int $listId): array
    {
        $this->fetchRecommendationListSkus();
        return $this->recommendationProducts[$listId] ?? [];
    }

    /**
     * @return void
     */
    private function fetchProducts(): void
    {
        if (empty($this->recommendationListIds) || !empty($this->products)) {
            return;
        }

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter('recommendation_list_ids', $this->recommendationListIds, 'in');
        $products = $this->productRepository->getList($searchCriteriaBuilder->create())->getItems();
        foreach ($products as $product) {
            $this->products[$product->getSku()] = $this->formatProductData($product);
        }
    }

    /**
     * @param ProductInterface $product
     * @return array
     */
    private function formatProductData(ProductInterface $product): array
    {
        return [
            'model' => $product,
            'name' => $product->getName(),
            'sku' => $product->getSku(),
        ];
    }

    /**
     * @return void
     */
    private function fetchRecommendationListSkus(): void
    {
        if (empty($this->recommendationListIds) || !empty($this->recommendationProducts)) {
            return;
        }

        $this->fetchProducts();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter('list_id', $this->recommendationListIds, 'in');
        $listProducts = $this->recommendationListProductRepository->getList($searchCriteriaBuilder->create())->getItems();

        foreach ($listProducts as $product) {
            if (isset($this->products[$product->getSku()])) {
                $this->recommendationProducts[$product->getListId()][] = $this->products[$product->getSku()];
            }
        }
    }
}
