<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Block\Cart;

use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Checkout\Helper\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory;

class Grid extends \Magento\Checkout\Block\Cart\Grid
{
    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Session $checkoutSession
     * @param Url $catalogUrlBuilder
     * @param Cart $cartHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param CollectionFactory $itemCollectionFactory
     * @param JoinProcessorInterface $joinProcessor
     * @param QuoteFactory $quoteFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        Session $checkoutSession,
        Url $catalogUrlBuilder,
        Cart $cartHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        CollectionFactory $itemCollectionFactory,
        JoinProcessorInterface $joinProcessor,
        QuoteFactory $quoteFactory,
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
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->catalogUrlBuilder = $catalogUrlBuilder;
        $this->cartHelper = $cartHelper;
        $this->httpContext = $httpContext;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->joinProcessor = $joinProcessor;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Load loadQuoteByToken
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function loadQuoteByToken()
    {
        $token = $this->getRequest()->getParam('key') ?? '';
        if ($token) {
            return $this->quoteFactory->create()->load($token, 'order_cart_token');
        } else {
            return $this->quoteFactory->create();
        }
    }
}
