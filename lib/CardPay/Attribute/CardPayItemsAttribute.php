<?php

namespace CardPay\Attribute;

use CardPay\Core\CardPayItem;
use CardPay\Exception\CardPayValidationException;

trait CardPayItemsAttribute
{
    private $items;

    public function setItems(array $items)
    {
        foreach ($items as $item) {
            if (!($item instanceof CardPayItem)) {
                throw new CardPayValidationException("One of Items is not a item object");
            }
        }

        $this->items = $items;

        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }
}