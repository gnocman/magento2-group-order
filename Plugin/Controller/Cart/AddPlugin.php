<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\Controller\Cart;

use Magento\Checkout\Controller\Cart\Add as AddToCartController;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Quote\Model\QuoteRepository\SaveHandler;
use SmartOSC\GroupOrder\Helper\Data;
use SmartOSC\GroupOrder\Logger\Logger;

class AddPlugin
{
    public function __construct(
        private Registry $registry,
        private CustomerSession $customerSession,
        private SaveHandler $saveHandler,
        private Data $helper,
        private CheckoutSession $checkoutSession,
        private Context $context,
        private Logger $logger
    ) {
    }

    /**
     * @param AddToCartController $subject
     * @param callable $proceed
     * @return ResponseInterface|ResultInterface
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundExecute(AddToCartController $subject, callable $proceed)
    {
        if (!$this->helper->isEnabled()) {
            return $proceed();
        }

        $token = $subject->getRequest()->getParam('key');

        // Register customer ID for the QuoteItemSaveBefore observer
        if (!$this->registry->registry('share_cart_customer_id')) {
            $this->registry->register('share_cart_customer_id', $this->customerSession->getCustomerId());
        }

        try {
            $result = $proceed();

            // Handle custom saving logic if token is present
            if ($token && $subject->getRequest()->isPost()) {
                // If the quote hasn't errored out during addProduct, do a forced saveHandler
                // although the core logic already calls cart->save() which triggers quote save.
                // We keep it as per the original preference fallback
                $cart = $this->checkoutSession->getQuote();
                if ($cart && !$cart->getHasError()) {
                    $this->saveHandler->save($cart);
                }
            }

            // Fix the redirect destination if it successfully added
            if ($token && $result instanceof \Magento\Framework\Controller\Result\Redirect) {
                // Redirect back to group order cart if token is present
                $redirectUrl = $this->context->getUrl()->getUrl('grouporder/cart/index', ['key' => $token]);
                $result->setUrl($redirectUrl);
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error('Error in Add to Cart Plugin: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->registry->unregister('share_cart_customer_id');
        }
    }
}
