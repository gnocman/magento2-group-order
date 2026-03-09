<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Block\Cart;

use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory;

class Grid extends \Magento\Checkout\Block\Cart\Grid
{
    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CheckoutSession $checkoutSession
     * @param Url $catalogUrlBuilder
     * @param CartHelper $cartHelper
     * @param HttpContext $httpContext
     * @param CollectionFactory $itemCollectionFactory
     * @param JoinProcessorInterface $joinProcessor
     * @param QuoteFactory $quoteFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CheckoutSession $checkoutSession,
        Url $catalogUrlBuilder,
        CartHelper $cartHelper,
        HttpContext $httpContext,
        CollectionFactory $itemCollectionFactory,
        JoinProcessorInterface $joinProcessor,
        private QuoteFactory $quoteFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $catalogUrlBuilder,
            $cartHelper,
            $httpContext,
            $itemCollectionFactory,
            $joinProcessor,
            $data
        );
    }

    /**
     * Load a quote by the group order token parameter
     *
     * @return Quote
     */
    public function loadQuoteByToken(): Quote
    {
        $token = (string)($this->getRequest()->getParam('key') ?? '');

        if ($token !== '') {
            return $this->quoteFactory->create()->load($token, 'order_cart_token');
        }

        return $this->quoteFactory->create();
    }
}
