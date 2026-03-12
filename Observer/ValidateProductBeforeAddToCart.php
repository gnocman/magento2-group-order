<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Observer;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\QuoteFactory;
use SmartOSC\GroupOrder\Helper\Data;

class ValidateProductBeforeAddToCart implements ObserverInterface
{
    /**
     * @param CustomerSession $customerSession
     * @param ManagerInterface $messageManager
     * @param QuoteFactory $quoteFactory
     * @param Cart $cart
     * @param Data $helper
     */
    public function __construct(
        private CustomerSession $customerSession,
        private ManagerInterface $messageManager,
        private QuoteFactory $quoteFactory,
        private Cart $cart,
        private Data $helper
    ) {
    }

    /**
     * Validate customer login and cart state before adding product in group order context
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if (!$this->helper->isEnabled()) {
            return;
        }

        $token = (string)$observer->getEvent()->getRequest()->getParam('key');

        if (!$token) {
            return;
        }

        if (!$this->customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('You must be logged in to add items.'));
            $observer->getRequest()->setParam('product', false);
            return;
        }

        $quote = $this->quoteFactory->create()->load($token, 'order_cart_token');
        if (!$quote->getId() || !$quote->getIsActive()) {
            $this->messageManager->addErrorMessage(
                __("You can't Group Order now because the URL link is out of date.")
            );
            $observer->getRequest()->setParam('product', false);
            return;
        }

        $this->cart->setQuote($quote);
    }
}
