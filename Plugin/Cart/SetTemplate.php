<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\Cart;

use Magento\Checkout\Block\Cart\AbstractCart;
use Magento\Framework\App\Request\Http;

class SetTemplate
{
    private const GROUP_ORDER_CART_PATH = '/grouporder/cart/index';
    private const CART_ITEM_TEMPLATE = 'SmartOSC_GroupOrder::cart/default.phtml';

    /**
     * @param Http $request
     */
    public function __construct(private Http $request)
    {
    }

    /**
     * Override cart item renderer template on group order cart page
     *
     * @param AbstractCart $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetItemRenderer(AbstractCart $subject, $result)
    {
        if (str_contains($this->request->getPathInfo(), self::GROUP_ORDER_CART_PATH)) {
            $result->setTemplate(self::CART_ITEM_TEMPLATE);
        }

        return $result;
    }
}
