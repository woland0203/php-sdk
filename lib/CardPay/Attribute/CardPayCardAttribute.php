<?php

namespace CardPay\Attribute;

use CardPay\Core\CardPayCard;
use CardPay\Exception\CardPayValidationException;

trait CardPayCardAttribute
{
    private $card;

    public function setCard($card)
    {
        if (!($card instanceof CardPayCard)) {
            throw new CardPayValidationException("Card is not a card object");
        }

        $this->card = $card;

        return $this;
    }

    /**
     * @return CardPayCard
     */
    public function getCard()
    {
        return $this->card;
    }
}