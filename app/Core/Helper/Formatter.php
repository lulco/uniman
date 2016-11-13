<?php

namespace Adminerng\Core\Helper;

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

    public function formatSize($number)
    {
        if ($number < 1024) {
            return $this->formatNumber($number) . ' B';
        }
        if ($number < 1024 * 1024) {
            return $this->formatNumber($number / 1024, 1) . ' kB';
        }
        if ($number < 1024 * 1024 * 1024) {
            return $this->formatNumber($number / 1024 / 1024, 1) . ' MB';
        }
        if ($number < 1024 * 1024 * 1024 * 1024) {
            return $this->formatNumber($number / 1024 / 1024 / 1024, 1) . ' GB';
        }
        return $this->formatNumber($number / 1024 / 1024 / 1024 / 1024, 1) . ' TB';
    }
}
