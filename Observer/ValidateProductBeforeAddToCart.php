<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Checkout\Model\Cart;

class ValidateProductBeforeAddToCart implements ObserverInterface
{
    /**
     * @var Session
     */
    protected Session $customerSession;
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @var Cart
     */
    private Cart $cart;
    /**
     * @var QuoteFactory
     */
    private QuoteFactory $quoteFactory;

    /**
     * @param Session $customerSession
     * @param ManagerInterface $messageManager
     * @param QuoteFactory $quoteFactory
     * @param Cart $cart
     */
    public function __construct(
        Session $customerSession,
        ManagerInterface $messageManager,
        QuoteFactory $quoteFactory,
        Cart $cart
    ) {
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->cart = $cart;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Check customer login add to cart
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $token = $observer->getEvent()->getRequest()->getParam('key');

        if ($token) {
            if (!$this->customerSession->isLoggedIn()) {
                $this->messageManager->addErrorMessage('You must be logged in to add items.');
                $observer->getRequest()->setParam('product', false);
                return;
            }

            if ($this->cart->getItemsCount() === 0) {
                $this->messageManager->addSuccessMessage(
                    'You added Item to your shopping cart because your shopping cart is empty.'
                );
                return;
            }

            $quote = $this->quoteFactory->create()->load($token, 'order_cart_token');
            if (!$quote->getId()) {
                throw new LocalizedException(__('Invalid quote token.'));
            }

            $this->cart->setQuote($quote);
        }
    }
}
