<?php

namespace Druidvav\EssentialsBundle\Twig;

use DateTime;
use Locale;
use NumberFormatter;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class Currency extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('currency_symbol', [$this, 'currencySymbolFilter']),
            new TwigFilter('to_words', [$this, 'numberToWordsFilter']),
            new TwigFilter('to_parts', [$this, 'numberToPartsFilter']),
            new TwigFilter('percent', [$this, 'percentFilter']),
            new TwigFilter('numberNormalizer', [$this, 'numberNormalizer']),
            new TwigFilter('dateNormalizer', [$this, 'dateNormalizer']),
        ];
    }

    public function currencySymbolFilter($currencyCode, $locale = null): false|string
    {
        if ($locale === null) {
            $locale = Locale::getDefault();
        }
        $formatter = new NumberFormatter($locale.'@currency='.$currencyCode, NumberFormatter::CURRENCY);
        return $formatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
    }

    public function numberToWordsFilter($sum): string
    {
        $intPart = (int) $sum;
        $decimalPart = abs(round(($sum - $intPart) * 100));
        $result = [];
        $var = abs($intPart);

        for ($i = 1; $var > 0; $i++) {
            $result[] = $var % 1000;
            $var = floor($var / 1000);
        }

        $strResult = '';

        if ($sum < 0) {
            $strResult .= 'минус ';
        }

        for ($i = count($result) - 1; $i >= 0; $i--) {
            $strResult .= $this->toWords($result[$i], $i === 1);
            if ($i !== 0) {
                $strResult .= ' '.$this->translator->trans('number.unit.'.pow(1000, $i), [ 'count' => $result[$i] ], 'numbers');
            }
        }

        if ($intPart !== 0) {
            $strResult .= ' '.$this->translator->trans('number.unit.currency', [ 'count' => abs($intPart) ], 'numbers');
        }

        if ($decimalPart > 0) {
            $strResult .= ' '.$this->toWords($decimalPart, true).' '.$this->translator->trans('number.unit.small_currency', [ 'count' => $decimalPart ], 'numbers');
        }

        return $strResult;
    }

    public function numberToPartsFilter($sum): string
    {
        $intPart = (int) $sum;
        $decimalPart = abs(round(($sum - $intPart) * 100));

        return sprintf(
            '%s %s %s %s',
            $intPart,
            $this->translator->trans('number.unit.currency', [ 'count' => $intPart ], 'numbers'),
            str_pad($decimalPart, 2, '0', STR_PAD_LEFT),
            $this->translator->trans('number.unit.small_currency', [ 'count' => $decimalPart ], 'numbers')
        );
    }

    public function percentFilter(string $value, string $default = '-'): string
    {
        if (is_numeric($value)) {
            $value *= 100;
            $value .= ' %';
        } else {
            $value = $default;
        }
        return $value;
    }

    public function numberNormalizer($sum): string
    {
        if (!$sum) {
            return '-';
        }
        return number_format($sum, 2, '.', '');
    }

    public function dateNormalizer($date): string
    {
        if (!$date) {
            return '-';
        }

        if ($date instanceof DateTime) {
            return $date->format('d.m.Y');
        }

        return (new DateTime($date))->format('d.m.Y');
    }

    public function getName(): string
    {
        return 'currency_extension';
    }

    private function toWords($number, bool $isOther = false): string
    {
        $result = '';
        if ($number != 0) {
            $transId = "number.$number";
            if ($isOther && $number < 3) {
                $transId .= '_other';
            }
            $word = $this->translator->trans($transId, [], 'numbers');
            if ($transId == $word) {
                $subNum = substr($number, 1);
                $result .= ' '.$this->toWords($number - $subNum);
                $result .= ' '.$this->toWords($subNum, $isOther);
            } else {
                $result .= $word;
            }
        }

        return $result;
    }
}
