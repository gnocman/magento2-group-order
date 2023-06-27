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
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class QuoteSaveAfter of SmartOSC\GroupOrder\Observer
 */
class QuoteSaveAfter implements ObserverInterface
{
    /**
     * @var Random
     */
    protected Random $randomDataGenerator;

    /**
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $cartRepository;

    /**
     * QuoteSaveAfter constructor.
     *
     * @param Random $randomDataGenerator
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        Random $randomDataGenerator,
        CartRepositoryInterface $cartRepository
    ) {
        $this->randomDataGenerator = $randomDataGenerator;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Random token
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();

        if (!$quote->getOrderCartToken()) {
            $randomLength = $this->randomDataGenerator->getUniqueHash();
            if (strlen($randomLength) > 15) {
                $randomLength = substr($randomLength, 0, 15);
            }
            $quote->setOrderCartToken($randomLength);

            $this->cartRepository->save($quote);
        }
    }
}
