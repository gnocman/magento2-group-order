<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\Cart;

use Magento\Checkout\Block\Cart\AbstractCart;
use Magento\Framework\App\Request\Http;
use SmartOSC\GroupOrder\Helper\Data;
use SmartOSC\GroupOrder\ViewModel\CartItemCustomer;

class SetTemplate
{
    private const GROUP_ORDER_CART_PATH = '/grouporder/cart/index';
    private const CART_ITEM_TEMPLATE = 'SmartOSC_GroupOrder::cart/default.phtml';

    /**
     * @param Http $request
     * @param Data $helper
     * @param CartItemCustomer $viewModel
     */
    public function __construct(
        private Http $request,
        private Data $helper,
        private CartItemCustomer $viewModel
    ) {
    }

    /**
     * Override cart item renderer template and inject ViewModel
     *
     * @param AbstractCart $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetItemRenderer(AbstractCart $subject, $result)
    {
        if (str_contains($this->request->getPathInfo(), self::GROUP_ORDER_CART_PATH) & $this->helper->isEnabled()) {
            $result->setTemplate(self::CART_ITEM_TEMPLATE);
            $result->setData('cart_item_customer_view_model', $this->viewModel);
        }

        return $result;
    }
}
