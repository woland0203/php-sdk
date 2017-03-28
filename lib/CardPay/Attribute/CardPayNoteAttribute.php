<?php

namespace CardPay\Attribute;

use CardPay\Helper\CardPayStringHelper;
use CardPay\Validation\CardPayStringValidator;

trait CardPayNoteAttribute
{
    private $note;

    public function setNote($note)
    {
        CardPayStringValidator::validate($note, "Note", 1, 100);

        $this->note = CardPayStringHelper::normalizeString($note);

        return $this;
    }

    public function getNote()
    {
        return $this->note;
    }
}