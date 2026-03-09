<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\QuoteItem;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;

class DuplicateRowQuoteItem
{
    private const REGISTRY_KEY = 'share_cart_customer_id';

    /**
     * @param Registry $registry
     */
    public function __construct(
        private Registry $registry
    ) {
    }

    /**
     * Allow duplicate quote items per customer in a group order
     *
     * In a standard cart, Magento merges quantities for the same product.
     * For group orders, each customer's addition should be a separate row.
     *
     * @param Quote $subject
     * @param bool|Item|null $result
     * @param mixed $product
     * @return false|Item|null
     */
    public function afterGetItemByProduct(Quote $subject, $result, $product)
    {
        $customerId = $this->registry->registry(self::REGISTRY_KEY);

        if (!$customerId) {
            return $result;
        }

        /** @var Item[] $items */
        $items = $subject->getItemsCollection()->getItems();

        foreach ($items as $item) {
            if (!$item->isDeleted()
                && $item->getProduct()
                && (int)$item->getProduct()->getStatus() !== ProductStatus::STATUS_DISABLED
                && $item->representProduct($product)
                && (int)$item->getCustomerId() === (int)$customerId
            ) {
                return $item;
            }
        }

        return false;
    }
}
