<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Block;

use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Checkout\Block\Cart;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;

class QuoteUrl extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    /**
     * @param Context $context
     * @param CatalogSession $catalogSession
     * @param CatalogConfig $catalogConfig
     * @param ToolbarModel $toolbarModel
     * @param EncoderInterface $urlEncoder
     * @param ProductList $productListHelper
     * @param PostHelper $postDataHelper
     * @param Cart $checkoutCart
     * @param UrlInterface $urlBuilder
     * @param ToolbarMemorizer $toolbarMemorizer
     * @param HttpContext $httpContext
     * @param FormKey $formKey
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Math\Random $random
     * @param array $data
     */
    public function __construct(
        Context $context,
        CatalogSession $catalogSession,
        CatalogConfig $catalogConfig,
        ToolbarModel $toolbarModel,
        EncoderInterface $urlEncoder,
        ProductList $productListHelper,
        PostHelper $postDataHelper,
        private Cart $checkoutCart,
        private UrlInterface $urlBuilder,
        ToolbarMemorizer $toolbarMemorizer,
        HttpContext $httpContext,
        FormKey $formKey,
        private \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        private \Magento\Framework\Math\Random $random,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $catalogSession,
            $catalogConfig,
            $toolbarModel,
            $urlEncoder,
            $productListHelper,
            $postDataHelper,
            $data
        );
        $this->toolbarMemorizer = $toolbarMemorizer;
        $this->httpContext = $httpContext;
        $this->formKey = $formKey;
    }

    /**
     * Get the group order share URL for the current category
     *
     * @return string
     */
    public function getQuoteUrl(): string
    {
        $subCategoryId = (int)$this->getRequest()->getParam('id');
        $quote = $this->checkoutCart->getQuote();

        $token = $quote->getOrderCartToken();
        if (!$token) {
            try {
                $token = substr($this->random->getUniqueHash(), 0, 15);
                $quote->setOrderCartToken($token);
                $this->quoteRepository->save($quote);
            } catch (\Exception $e) {
                // If it fails to save an empty quote, fallback to what we generated.
                // The QuoteSaveBefore observer will also handle it when quote is finally saved.
            }
        }

        return $this->urlBuilder->getUrl(
            'grouporder',
            [
                'key' => $token,
                'sub' => $subCategoryId
            ]
        );
    }
}
