<?php

declare(strict_types=1);

namespace Decima\FriendRecommendationsGraphQl\Model\Resolver\RecommendationList\RecommendationListProducts;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\ImageFactory;
use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\Image\Placeholder as PlaceholderProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ThumbnailUrl implements ResolverInterface
{
    /**
     * @var ImageFactory
     */
    private ImageFactory $productImageFactory;

    /**
     * @var PlaceholderProvider
     */
    private PlaceholderProvider $placeholderProvider;

    /**
     * @var string
     */
    private string $placeholder;

    /**
     * @param ImageFactory $productImageFactory
     * @param PlaceholderProvider $placeholderProvider
     */
    public function __construct(
        ImageFactory $productImageFactory,
        PlaceholderProvider $placeholderProvider
    ) {
        $this->productImageFactory = $productImageFactory;
        $this->placeholderProvider = $placeholderProvider;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed|Value
     * @throws LocalizedException
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var Product $product */
        $product = $value['model'];
        return $this->getImageUrl($product->getData('thumbnail'));
    }

    /**
     * @param string|null $imagePath
     * @return string
     */
    private function getImageUrl(?string $imagePath): string
    {
        if (empty($imagePath) && !empty($this->placeholder)) {
            return $this->placeholder;
        }
        $image = $this->productImageFactory->create();
        $image->setDestinationSubdir('thumbnail')
            ->setBaseFile($imagePath);

        if ($image->isBaseFilePlaceholder()) {
            $this->placeholder = $this->placeholderProvider->getPlaceholder('thumbnail');
            return $this->placeholder;
        }

        return $image->getUrl();
    }
}
