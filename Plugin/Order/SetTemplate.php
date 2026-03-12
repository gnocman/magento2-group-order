<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\Order;

use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer;
use SmartOSC\GroupOrder\Helper\Data;
use SmartOSC\GroupOrder\ViewModel\CartItemCustomer;

class SetTemplate
{
    private const ORDER_ITEM_TEMPLATE = 'SmartOSC_GroupOrder::order/default.phtml';

    /**
     * @param Data $helper
     * @param CartItemCustomer $viewModel
     */
    public function __construct(
        private Data $helper,
        private CartItemCustomer $viewModel
    ) {
    }

    /**
     * Override order item renderer template and inject ViewModel
     *
     * @param DefaultRenderer $subject
     * @return void
     */
    public function beforeToHtml(DefaultRenderer $subject)
    {
        if ($this->helper->isEnabled()) {
            $subject->setTemplate(self::ORDER_ITEM_TEMPLATE);
            $subject->setData('cart_item_customer_view_model', $this->viewModel);
        }
    }
}
