<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Model\ResourceModel\Order\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderGridCollection;

class Collection extends OrderGridCollection
{
    /**
     * @inheritdoc
     */
    protected function _renderFiltersBefore()
    {
        $this->getSelect()->where('order_cart_token IS NOT NULL');
        parent::_renderFiltersBefore();
    }
}
