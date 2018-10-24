<?php

namespace Druidvav\EssentialsBundle\Twig;

use Symfony\Component\Translation\Translator;
use Twig\TwigFilter;

class Currency extends \Twig_Extension
{
    private $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getFilters()
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

    /**
     * @param string $currencyCode
     * @param string $locale
     * @return bool|string
     */
    public function currencySymbolFilter($currencyCode, $locale = null)
    {
        if ($locale === null) {
            $locale = \Locale::getDefault();
        }
        $formatter = new \NumberFormatter($locale.'@currency='.$currencyCode, \NumberFormatter::CURRENCY);

        return $formatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
    }

    /**
     * @param string|float $sum
     * @return string
     */
    public function numberToWordsFilter($sum)
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
                $strResult .= ' '.$this->translator->transChoice('number.unit.'.pow(1000, $i), $result[$i], [], 'numbers');
            }
        }

        if ($intPart !== 0) {
            $strResult .= ' '.$this->translator->transChoice('number.unit.currency', abs($intPart), [], 'numbers');
        }

        if ($decimalPart > 0) {
            $strResult .= ' '.$this->toWords($decimalPart, true).' '.$this->translator->transChoice('number.unit.small_currency', $decimalPart, [], 'numbers');
        }

        return $strResult;
    }

    /**
     * @param string|float $sum
     * @return string
     */
    public function numberToPartsFilter($sum)
    {
        $intPart = (int) $sum;
        $decimalPart = abs(round(($sum - $intPart) * 100));

        return sprintf(
            '%s %s %s %s',
            $intPart,
            $this->translator->transChoice('number.unit.currency', $intPart, [], 'numbers'),
            str_pad($decimalPart, 2, '0', STR_PAD_LEFT),
            $this->translator->transChoice('number.unit.small_currency', $decimalPart, [], 'numbers')
        );
    }

    /**
     * @param string $value
     * @param string $default
     * @return bool|string
     */
    public function percentFilter($value, $default = '-')
    {
        if (is_numeric($value)) {
            $value *= 100;
            $value .= ' %';
        } else {
            $value = $default;
        }

        return $value;
    }

    /**
     * @param mixed $sum
     * @return string
     */
    public function numberNormalizer($sum)
    {
        if (!$sum) {
            return '-';
        }

        return number_format($sum, 2, '.', '');
    }

    /**
     * @param mixed $date
     * @return string
     */
    public function dateNormalizer($date)
    {
        if (!$date) {
            return '-';
        }

        if ($date instanceof \DateTime) {
            return $date->format('d.m.Y');
        }

        return (new \DateTime($date))->format('d.m.Y');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'currency_extension';
    }

    /**
     * @param      $number
     * @param bool $isOther
     * @return string
     */
    private function toWords($number, $isOther = false)
    {
        $result = '';
        if ($number != 0) {
            $transId = "number.{$number}";
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
