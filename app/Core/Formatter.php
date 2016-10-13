<?php

namespace Adminerng\Core;

use Nette\Localization\ITranslator;

class Formatter
{
    private $translator;

    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    public function formatNumber($number, $decimals = 0)
    {
        return number_format($number, $decimals, $this->translator->translate('core.formatter.number_format.decimal_point'), $this->translator->translate('core.formatter.number_format.thousands_separator'));
    }
}