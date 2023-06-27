<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\Cart;

use Magento\Framework\App\Request\Http;

class SetTemplate
{
    /**
     * @var Http
     */
    protected Http $request;

    /**
     * @param Http $request
     */
    public function __construct(
        Http $request
    ) {
        $this->request = $request;
    }

    /**
     * Plugin afterGetItemRenderer
     *
     * @param \Magento\Checkout\Block\Cart\AbstractCart $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetItemRenderer(\Magento\Checkout\Block\Cart\AbstractCart $subject, $result)
    {
        if (str_contains($this->request->getPathInfo(), '/sharecart/cart/index')) {
            $result->setTemplate('SmartOSC_GroupOrder::cart/default.phtml');
        }

        return $result;
    }
}
