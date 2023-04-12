<?php

declare(strict_types=1);

namespace Decima\FriendRecommendationsGraphQl\Model\Resolver\RecommendationList;

use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

class Identity implements IdentityInterface
{
    public const CACHE_TAG = 'frl';

    /**
     * @param array $resolvedData
     * @return string[]
     */
    public function getIdentities(array $resolvedData) : array
    {
        $identities = [];

        foreach ($resolvedData as $listData) {
            if (isset($listData['id'])) {
                $identities[] = self::CACHE_TAG . '_' . $listData['id'];
            }
        }

        return $identities;
    }
}
