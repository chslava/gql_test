<?php

declare(strict_types=1);

namespace Decima\FriendRecommendationsGraphQl\Model\Resolver;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Decima\FriendRecommendationsGraphQl\Model\Resolver\DataProvider\RecommendationListDataProvider;

class RecommendationList implements ResolverInterface
{
    /**
     * @var RecommendationListDataProvider
     */
    private RecommendationListDataProvider $recommendationListDataProvider;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param RecommendationListDataProvider $recommendationListDataProvider
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        RecommendationListDataProvider $recommendationListDataProvider
    ) {
        $this->recommendationListDataProvider = $recommendationListDataProvider;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return Value|array|mixed
     * @throws GraphQlAuthorizationException
     * @throws GraphQlInputException
     * @throws GraphQlNoSuchEntityException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $currentUserId = $context->getUserId();

        try {
            $customer = $this->customerRepository->getById($currentUserId);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(
                __('Customer with id "%customer_id" does not exist.', ['customer_id' => $currentUserId]),
                $e
            );
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return $this->recommendationListDataProvider->getRecommendations($customer->getEmail());
    }
}
