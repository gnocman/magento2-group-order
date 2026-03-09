<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OrderSaveBefore implements ObserverInterface
{
    /**
     * Transfer order_cart_token from quote to order before it's saved
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if ($quote && $quote->getOrderCartToken()) {
            $order->setOrderCartToken($quote->getOrderCartToken());
        }
    }
}
