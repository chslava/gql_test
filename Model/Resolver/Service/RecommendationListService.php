<?php

declare(strict_types=1);

namespace Decima\FriendRecommendationsGraphQl\Model\Resolver\Service;

use SwiftOtter\FriendRecommendations\Api\Data\RecommendationListInterface;
use SwiftOtter\FriendRecommendations\Api\Data\RecommendationListInterfaceFactory;
use SwiftOtter\FriendRecommendations\Api\RecommendationListRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;

class RecommendationListService
{
    /**
     * @var RecommendationListRepositoryInterface
     */
    private RecommendationListRepositoryInterface $recommendationListRepository;

    /**
     * @var RecommendationListInterfaceFactory
     */
    private RecommendationListInterfaceFactory $recommendationListFactory;

    /**
     * @var RecommendationListProductsService
     */
    private RecommendationListProductsService $recommendationListProductsService;

    /**
     * @param RecommendationListRepositoryInterface $recommendationListRepository
     * @param RecommendationListInterfaceFactory $recommendationListFactory
     * @param RecommendationListProductsService $recommendationListProductsService
     */
    public function __construct(
        RecommendationListRepositoryInterface $recommendationListRepository,
        RecommendationListInterfaceFactory $recommendationListFactory,
        RecommendationListProductsService $recommendationListProductsService
    ) {
        $this->recommendationListRepository = $recommendationListRepository;
        $this->recommendationListFactory = $recommendationListFactory;
        $this->recommendationListProductsService = $recommendationListProductsService;
    }

    /**
     * @param string $email
     * @param string $friendName
     * @param array $productSkus
     * @param string|null $title
     * @param string|null $note
     * @return string[]
     * @throws CouldNotSaveException
     */
    public function execute(
        string $email,
        string $friendName,
        array $productSkus,
        ?string $title = null,
        ?string $note = null
    ): array {
        $recommendationList = $this->recommendationListFactory->create();
        $recommendationList->setEmail($email);
        $recommendationList->setFriendName($friendName);

        if (null !== $title) {
            $recommendationList->setTitle($title);
        }

        if (null !== $note) {
            $recommendationList->setNote($note);
        }

        $recommendationListSaved = $this->recommendationListRepository->save($recommendationList);
        $this->recommendationListProductsService->execute((int) $recommendationListSaved->getId(), $productSkus);

        return $this->formatRecommendationListData($recommendationListSaved);
    }

    /**
     * @param RecommendationListInterface $recommendationList
     * @return string[]
     */
    private function formatRecommendationListData(RecommendationListInterface $recommendationList): array
    {
        return [
            'email' => $recommendationList->getEmail(),
            'friendName' => $recommendationList->getFriendName(),
            'title' => $recommendationList->getTitle(),
            'note' => $recommendationList->getNote()
        ];
    }
}
