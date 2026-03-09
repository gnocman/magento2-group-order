<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Observer;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;
use SmartOSC\GroupOrder\Logger\Logger as GroupOrderLogger;

class EmailCC implements ObserverInterface
{
    /**
     * @param CheckoutSession $checkoutSession
     * @param CartItemRepositoryInterface $cartItemRepository
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param GroupOrderLogger $logger
     */
    public function __construct(
        private CheckoutSession $checkoutSession,
        private CartItemRepositoryInterface $cartItemRepository,
        private CustomerCollectionFactory $customerCollectionFactory,
        private GroupOrderLogger $logger
    ) {
    }

    /**
     * Collect emails of group order participants and store in session for CC
     *
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = (int)$order->getQuoteId();

        if (!$quoteId) {
            return;
        }

        $quoteItems = $this->cartItemRepository->getList($quoteId);
        $quoteItemsById = [];
        $customerIds = [];

        foreach ($quoteItems as $quoteItem) {
            $id = (int)$quoteItem->getItemId();
            $quoteItemsById[$id] = $quoteItem;
            if ($cId = (int)$quoteItem->getData('customer_id')) {
                $customerIds[] = $cId;
            }
        }

        // Explicitly map customer_id to order items to ensure it is saved.
        foreach ($order->getAllItems() as $orderItem) {
            $quoteItemId = (int)$orderItem->getQuoteItemId();
            if ($quoteItemId && isset($quoteItemsById[$quoteItemId])) {
                $orderItem->setData(
                    'customer_id',
                    (int)$quoteItemsById[$quoteItemId]->getData('customer_id')
                );
            }
        }

        if (empty($customerIds)) {
            return;
        }

        $emailList = $this->customerCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => array_unique($customerIds)])
            ->getColumnValues('email');

        if (!empty($emailList)) {
            $this->logger->info('Setting Email CC for order: ' . json_encode($emailList));
            $this->checkoutSession->setEmailCc(json_encode($emailList));
        }
    }
}
