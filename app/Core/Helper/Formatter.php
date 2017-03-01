<?php

namespace UniMan\Core\Helper;

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
        if ($number < pow(1024, 2)) {
            return $this->formatNumber($number / 1024, 1) . ' kB';
        }
        if ($number < pow(1024, 3)) {
            return $this->formatNumber($number / pow(1024, 2), 1) . ' MB';
        }
        if ($number < pow(1024, 4)) {
            return $this->formatNumber($number / pow(1024, 3), 1) . ' GB';
        }
        return $this->formatNumber($number / pow(1024, 4), 1) . ' TB';
    }

    public function formatTime($number)
    {
        if ($number < 60) {
            return $number . ' s';
        }
        if ($number < 3600) {
            return date('i:s', $number);
        }
        return floor($number / 3600) . date('\h i\m s\s', $number);
    }
}
