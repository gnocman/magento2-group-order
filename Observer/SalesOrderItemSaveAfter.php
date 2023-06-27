<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Sales\Model\ResourceModel\Order\Item;

class SalesOrderItemSaveAfter implements ObserverInterface
{
    /**
     * @var ItemFactory
     */
    private ItemFactory $quoteItemFactory;
    /**
     * @var Item
     */
    private Item $salesOrderItemResourceModel;

    /**
     * @param ItemFactory $quoteItemFactory
     * @param Item $salesOrderItemResourceModel
     */
    public function __construct(
        ItemFactory $quoteItemFactory,
        Item $salesOrderItemResourceModel
    ) {
        $this->quoteItemFactory = $quoteItemFactory;
        $this->salesOrderItemResourceModel = $salesOrderItemResourceModel;
    }

    /**
     * Set name of customer $quoteItem to $orderItem
     *
     * @param Observer $observer
     * @return void
     * @throws AlreadyExistsException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $salesOrderItem = $observer->getEvent()->getItem();
        $quoteItemId = $salesOrderItem->getQuoteItemId();

        $quoteItem = $this->quoteItemFactory->create()->load($quoteItemId, 'item_id');

        $salesOrderItem->setData('name_customer_add_to_cart', $quoteItem->getData('name_customer_add_to_cart'));

        $this->salesOrderItemResourceModel->save($salesOrderItem);
    }
}
