<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product\Type;

use Magestore\Giftvoucher\Model\Product\Type\Giftvoucher;

/**
 * Class GiftcardProductTemplateGenerator
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Product\Type
 */
class GiftcardProductTemplateGenerator extends AbstractTypeProductGenerator
{
    const SKU_PATTERN = "pos_giftcard_product_%s";

    /**
     * @inheritDoc
     */
    public function getProductTemplate($productNumber, $additionalData = [])
    {
        $product = parent::getProductTemplate($productNumber, $additionalData);
        $product->setTypeId(Giftvoucher::GIFT_CARD_TYPE);
        $product->setData('gift_card_type', 1);
        $product->setData('gift_template_ids', 1);
        $product->setData('gift_type', 1);
        $product->setData('gift_value', 10);
        $product->setData('gift_price_type', 1);

        return $product;
    }
}
