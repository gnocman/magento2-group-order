<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Cart\RequestQuantityProcessor;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Filter\LocalizedToNormalized;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use Magento\Framework\Registry;
use Magento\Quote\Model\QuoteRepository\SaveHandler;
use Magento\Store\Model\StoreManagerInterface;
use SmartOSC\GroupOrder\Helper\Data;
use SmartOSC\GroupOrder\Logger\Logger as GroupOrderLogger;

/**
 * Controller for processing add to cart action for Group Order.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Checkout\Controller\Cart\Add
{
    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param CheckoutSession $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param SaveHandler $saveHandler
     * @param Registry $registry
     * @param CustomerSession $customerSession
     * @param RequestQuantityProcessor $quantityProcessor
     * @param Escaper $escaper
     * @param GroupOrderLogger $logger
     * @param LocaleResolver $localeResolver
     * @param Data $helper
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        CheckoutSession $checkoutSession,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        private SaveHandler $saveHandler,
        private Registry $registry,
        private CustomerSession $customerSession,
        private RequestQuantityProcessor $quantityProcessor,
        private Escaper $escaper,
        private GroupOrderLogger $logger,
        private LocaleResolver $localeResolver,
        private Data $helper
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository
        );
    }

    /**
     * Add product to shopping cart action
     *
     * @return ResponseInterface|ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()) {
            return parent::execute();
        }

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__('Your session has expired'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();
        $this->logger->info('Add to Cart Params: ' . json_encode($params));

        try {
            if (isset($params['qty'])) {
                $filter = new LocalizedToNormalized(['locale' => $this->localeResolver->getLocale()]);
                $params['qty'] = $this->quantityProcessor->prepareQuantity($params['qty']);
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            if (!$product) {
                return $this->goBack();
            }

            $this->registry->register('share_cart_customer_id', $this->customerSession->getCustomerId());

            $this->cart->addProduct($product, $params);

            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $token = $this->getRequest()->getParam('key');
            if ($token) {
                $this->saveHandler->save($this->cart->getQuote());
            } else {
                $this->cart->save();
            }

            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );

            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if ($this->shouldRedirectToCart()) {
                    $this->messageManager->addSuccessMessage(
                        __('You added %1 to your shopping cart.', $product->getName())
                    );
                } else {
                    $this->messageManager->addComplexSuccessMessage(
                        'addCartSuccessMessage',
                        [
                            'product_name' => $product->getName(),
                            'cart_url' => $this->getCartUrl(),
                        ]
                    );
                }

                if ($this->cart->getQuote()->getHasError()) {
                    foreach ($this->cart->getQuote()->getErrors() as $error) {
                        $this->messageManager->addErrorMessage($error->getText());
                    }
                }

                return $this->goBack(null, $product);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage(
                    $this->escaper->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage(
                        $this->escaper->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);
            if (!$url) {
                $url = $this->_redirect->getRedirectUrl($this->getCartUrl());
            }

            return $this->goBack($url);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __("We can't add this item to your shopping cart right now.")
            );
            $this->logger->error('Error adding product to cart: ' . $e->getMessage());
            $this->logger->critical($e);
            return $this->goBack();
        } finally {
            $this->registry->unregister('share_cart_customer_id');
        }

        return $this->getResponse();
    }

    /**
     * Returns cart URL — redirects to group order cart page if token is present
     *
     * @return string
     */
    private function getCartUrl(): string
    {
        $token = $this->getRequest()->getParam('key');

        if ($token) {
            return $this->_url->getUrl('grouporder/cart/index', ['key' => $token]);
        }

        return $this->_url->getUrl('checkout/cart', ['_secure' => true]);
    }

    /**
     * Check if redirect to cart is configured
     *
     * @return bool
     */
    private function shouldRedirectToCart(): bool
    {
        return $this->_scopeConfig->isSetFlag(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
