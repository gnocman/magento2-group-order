<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\QuoteItem;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Framework\Registry;

class DuplicateRowQuoteItem
{
    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * Plugin afterGetItemByProduct
     *
     * @param Quote $subject
     * @param bool $result
     * @param $product
     * @return false|Item|mixed
     */
    public function afterGetItemByProduct(Quote $subject, $result, $product)
    {
        if (!$this->registry->registry('share_cart_has_token')) {
            return $result;
        }

        $customerId = $this->registry->registry('share_cart_customer_id');

        $this->registry->unregister('share_cart_customer_id');
        $this->registry->unregister('share_cart_has_token');

        /** @var Item[] $items */
        $items = $subject->getItemsCollection()->getItems();

        foreach ($items as $item) {
            if (!$item->isDeleted()
                && $item->getProduct()
                && $item->getProduct()->getStatus() !== Status::STATUS_DISABLED
                && $item->representProduct($product)
                && $item->getCustomerId() == $customerId
            ) {
                return $item;
            }
        }
        return false;
    }
}
