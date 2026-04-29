<?php

namespace Druidvav\EssentialsBundle\Twig;

use DateTime;
use IntlDateFormatter;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Basic extends AbstractExtension
{
    protected TranslatorInterface $translator;
    protected string $gruntAssetManifestPath = '';

    /**
     * @required
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function setGruntAssetManifestPath(string $gruntAssetManifestPath)
    {
        $this->gruntAssetManifestPath = $gruntAssetManifestPath;
    }

    public function getFilters(): array
    {
        return array(
            new TwigFilter('format_date_interval', array($this, 'formatDateInterval')),
            new TwigFilter('format_date_smart', array($this, 'formatDateSmart')),
            new TwigFilter('format_date_pattern', array($this, 'formatDatePattern')),
        );
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('array_print', array($this, 'arrayPrint')),
            new TwigFunction('grunt_asset', array($this, 'gruntAsset')),
            new TwigFunction('cdn_asset', array($this, 'cdnAsset')),
            new TwigFunction('get_locale', array($this, 'getLocale')),
            new TwigFunction('is_locale', array($this, 'isLocale')),
        );
    }

    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }

    public function isLocale($locale): bool
    {
        return $this->translator->getLocale() == $locale;
    }

    public function formatDateInterval($date, $format = ''): string
    {
        if (is_object($date) && is_a($date, 'DateTime')) {
            /* @var $date DateTime */
            $date = $date->getTimestamp();
        } elseif (!is_numeric($date)) {
            $date = strtotime($date);
        }

        $diff = $date - time();
        $direction = $diff < 0 ? 'ago' : 'in';
        if (abs($diff) <= 3600) {
            $diffInt = ceil(abs($diff) / 60);

            return $this->translator->trans('diff.'.$direction.'.minute', ['%count%' => $diffInt], 'date');
        } elseif (abs($diff) <= 36 * 3600) {
            $diffInt = ceil(abs($diff) / 3600);

            return $this->translator->trans('diff.'.$direction.'.hour', ['%count%' => $diffInt], 'date');
        }

        return $this->formatDateSmart($date, $format);
    }

    public function formatDateSmart($date, $format = ''): string
    {
        if (!is_object($date) || !is_a($date, 'DateTime')) {
            $date = new DateTime($date);
        }

        $locale = $this->translator->getLocale();
        $currentYear = $date->format('Y') == date('Y');
        $dateMode = 'long' == $format || $currentYear ? IntlDateFormatter::LONG : IntlDateFormatter::MEDIUM;
        $formatter = IntlDateFormatter::create($locale, $dateMode, IntlDateFormatter::NONE);
        $pattern = $formatter->getPattern();

        if ($currentYear) {
            $pattern = trim(str_replace([', y', 'y', '\'г\'.', 'թ.'], '', $pattern));
        } else {
            $pattern = str_replace(['\'г\'.', 'թ.'], '', $pattern);
        }

        $formatter->setPattern($pattern);
        $formatter->setTimeZone($date->getTimezone());

        return trim($formatter->format($date), " \t\n\r\0\x0B\xC2\xA0\xE2\x80\xAF");
    }

    public function formatDatePattern($date, $pattern)
    {
        if (!is_object($date) || !is_a($date, 'DateTime')) {
            $date = new DateTime($date);
        }

        $locale = $this->translator->getLocale();
        $formatter = IntlDateFormatter::create($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        $formatter->setPattern($pattern);
        $formatter->setTimeZone($date->getTimezone());

        return $formatter->format($date);
    }

    public function arrayPrint($string)
    {
        return print_r($string, true);
    }

    public function gruntAsset($string): string
    {
        $assetsFilename = $this->gruntAssetManifestPath;
        if (file_exists($assetsFilename)) {
            $data = file_get_contents($assetsFilename);
            if (empty($data)) {
                return '/'.$string;
            }
            $assets = json_decode($data, true);
            if (empty($assets)) {
                return '/'.$string;
            }
            foreach ($assets as $asset) {
                if ($asset['originalPath'] == $string) {
                    return '/'.$asset['versionedPath'];
                }
            }
        }

        return '/'.$string;
    }
}
