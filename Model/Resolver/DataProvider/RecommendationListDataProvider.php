<?php

declare(strict_types=1);

namespace Decima\FriendRecommendationsGraphQl\Model\Resolver\DataProvider;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use SwiftOtter\FriendRecommendations\Api\RecommendationListRepositoryInterface;
use SwiftOtter\FriendRecommendations\Api\Data\RecommendationListInterface;

class RecommendationListDataProvider
{

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var RecommendationListRepositoryInterface
     */
    private RecommendationListRepositoryInterface $recommendationListRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RecommendationListRepositoryInterface $recommendationListRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RecommendationListRepositoryInterface $recommendationListRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->recommendationListRepository = $recommendationListRepository;
    }

    /**
     * @param string $email
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    public function getRecommendations(string $email): array
    {
        $this->searchCriteriaBuilder->addFilter('email', ['eq' => $email]);
        $recommendations = $this->recommendationListRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        if (empty($recommendations)) {
            throw new GraphQlNoSuchEntityException(__('There is no product recommendations for the email ' .$email ));
        }

        $result = [];
        foreach ($recommendations as $recommendation) {
            $result[] = $this->formatRecommendationData($recommendation);
        }

        return $result;
    }

    /**
     * @param RecommendationListInterface $recommendation
     * @return string[]
     */
    private function formatRecommendationData(RecommendationListInterface $recommendation): array
    {
        return [
            'id' => $recommendation->getId(),
            'friendName' => $recommendation->getFriendName(),
            'title' => $recommendation->getTitle(),
            'note' => $recommendation->getNote()
        ];
    }
}
