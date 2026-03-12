<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Observer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SmartOSC\GroupOrder\Helper\Data;

class QuoteItemSaveBefore implements ObserverInterface
{
    /**
     * @param CustomerSession $customerSession
     * @param Data $helper
     */
    public function __construct(
        private CustomerSession $customerSession,
        private Data $helper
    ) {
    }

    /**
     * Persist the current customer ID on the quote item before save
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if (!$this->helper->isEnabled()) {
            return;
        }

        $quoteItem = $observer->getEvent()->getItem();

        // Avoid overwriting ownership of items already in the group cart.
        if ($quoteItem->getData('customer_id')) {
            return;
        }

        $customerId = (int)$this->customerSession->getCustomerId();

        if (!$customerId) {
            return;
        }

        $quoteItem->setData('customer_id', $customerId);
    }
}
