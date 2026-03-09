<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use SmartOSC\GroupOrder\Logger\Logger as GroupOrderLogger;

class QuoteSaveBefore implements ObserverInterface
{
    private const TOKEN_LENGTH = 15;

    /**
     * @param Random $randomDataGenerator
     * @param GroupOrderLogger $logger
     */
    public function __construct(
        private Random $randomDataGenerator,
        private GroupOrderLogger $logger
    ) {
    }

    /**
     * Generate a unique token for the quote if one does not exist
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        $quote = $observer->getEvent()->getQuote();

        if ($quote->getOrderCartToken()) {
            return;
        }

        $token = substr($this->randomDataGenerator->getUniqueHash(), 0, self::TOKEN_LENGTH);
        $quote->setOrderCartToken($token);
        $this->logger->info('Generated token for quote ID ' . $quote->getId() . ': ' . $token);
    }
}
