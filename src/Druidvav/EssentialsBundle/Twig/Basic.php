<?php
namespace Druidvav\EssentialsBundle\Twig;

use DateTime;
use IntlTimeZone;
use Symfony\Component\HttpKernel\Kernel;
use IntlDateFormatter;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\TwigFunction;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class Basic extends AbstractExtension
{
    protected $translator;
    protected $kernel;

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    public function getKernel(): Kernel
    {
        return $this->kernel;
    }

    public function setKernel(Kernel $kernel)
    {
        $this->kernel = $kernel;
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

    public function getLocale()
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
        if (abs($diff) <= 3600) {
            $diffInt = ceil(abs($diff) / 60);
            $int = $this->getTranslator()->transChoice('general.minutes', $diffInt);
        } elseif (abs($diff) <= 36 * 3600) {
            $diffInt = ceil(abs($diff) / 3600);
            $int = $this->getTranslator()->transChoice('general.hours', $diffInt);
        }
        if (!empty($int) && !empty($diffInt)) {
            return $this->getTranslator()->trans($diff < 0 ? 'general.period_ago' : 'general.period_in', [ '%str%' => $diffInt . ' ' . $int ]);
        }

        $locale = $this->getTranslator()->getLocale();
        $currentYear = date('Y', $date) == date('Y');
        $dateMode = $format == 'long' || $currentYear ? IntlDateFormatter::LONG : IntlDateFormatter::MEDIUM;
        $formatter = IntlDateFormatter::create($locale, $dateMode, IntlDateFormatter::NONE);
        $pattern = $formatter->getPattern();

        if ($currentYear) {
            $pattern = trim(str_replace([ ', y', 'y', '\'г\'.', 'թ.' ], '', $pattern));
        } else {
            $pattern = str_replace([ '\'г\'.', 'թ.' ], '', $pattern);
        }

        $formatter->setPattern($pattern);
        $formatter->setTimeZone(IntlTimeZone::createTimeZone('GMT+0300'));
        return trim($formatter->format($date), " \t\n\r\0\x0B\xC2\xA0\xE2\x80\xAF");
    }

    public function formatDateSmart($date, $format = ''): string
    {
        if (is_object($date) && is_a($date, 'DateTime')) {
            /* @var $date DateTime */
            $date = $date->getTimestamp();
        } elseif (!is_numeric($date)) {
            $date = strtotime($date);
        }

        $locale = $this->getTranslator()->getLocale();
        $currentYear = date('Y', $date) == date('Y');
        $dateMode = $format == 'long' || $currentYear ? IntlDateFormatter::LONG : IntlDateFormatter::MEDIUM;
        $formatter = IntlDateFormatter::create($locale, $dateMode, IntlDateFormatter::NONE);
        $pattern = $formatter->getPattern();

        if ($currentYear) {
            $pattern = trim(str_replace([ ', y', 'y', '\'г\'.' ], '', $pattern));
        } else {
            $pattern = str_replace([ '\'г\'.' ], '', $pattern);
        }

        $formatter->setPattern($pattern);
        $formatter->setTimeZone(IntlTimeZone::createTimeZone('GMT+0300'));
        return trim($formatter->format($date), " \t\n\r\0\x0B\xC2\xA0\xE2\x80\xAF");
    }

    public function formatDatePattern($date, $pattern)
    {
        if (is_object($date) && is_a($date, 'DateTime')) {
            /* @var $date DateTime */
            $date = $date->getTimestamp();
        } elseif (!is_numeric($date)) {
            $date = strtotime($date);
        }

        $locale = $this->getTranslator()->getLocale();
        $formatter = IntlDateFormatter::create($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        $formatter->setPattern($pattern);
        $formatter->setTimeZone(IntlTimeZone::createTimeZone('GMT+0300'));
        return $formatter->format($date);
    }

    public function arrayPrint($string)
    {
        return print_r($string, true);
    }

    public function gruntAsset($string): string
    {
        $assetsFilename = $this->getKernel()->getProjectDir() . '/app/assets.json';
        if (file_exists($assetsFilename)) {
            $data = file_get_contents($assetsFilename);
            if (empty($data)) return '/' . $string;
            $assets = json_decode($data, true);
            if (empty($assets)) return '/' . $string;
            foreach ($assets as $asset) {
                if ($asset['originalPath'] == $string) {
                    return '/' . $asset['versionedPath'];
                }
            }
        }
        return '/' . $string;
    }
}
