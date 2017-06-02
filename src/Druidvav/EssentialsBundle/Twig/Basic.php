<?php
namespace Druidvav\EssentialsBundle\Twig;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Extension;
use IntlDateFormatter;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class Basic extends Twig_Extension
{
    protected $translator;
    protected $kernel;

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    public function setKernel(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('format_date_interval', array($this, 'formatDateInterval')),
            new Twig_SimpleFilter('format_date_smart', array($this, 'formatDateSmart')),
            new Twig_SimpleFilter('format_date_pattern', array($this, 'formatDatePattern')),
        );
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('array_print', array($this, 'arrayPrint')),
            new Twig_SimpleFunction('grunt_asset', array($this, 'gruntAsset')),
            new Twig_SimpleFunction('cdn_asset', array($this, 'cdnAsset')),
            new Twig_SimpleFunction('get_locale', array($this, 'getLocale')),
            new Twig_SimpleFunction('is_locale', array($this, 'isLocale')),
        );
    }

    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    public function isLocale($locale)
    {
        return $this->translator->getLocale() == $locale;
    }

    public function formatDateInterval($date, $format = '')
    {
        if (is_object($date) && is_a($date, 'DateTime')) {
            /* @var $date \DateTime */
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
            $pattern = trim(str_replace([ ', y', 'y', '\'г\'.' ], '', $pattern));
        } else {
            $pattern = str_replace([ '\'г\'.' ], '', $pattern);
        }

        $formatter->setPattern($pattern);
        $formatter->setTimeZone(\IntlTimeZone::createTimeZone('GMT+0300'));
        return trim($formatter->format($date), " \t\n\r\0\x0B\xC2\xA0");
    }

    public function formatDateSmart($date, $format = '')
    {
        if (is_object($date) && is_a($date, 'DateTime')) {
            /* @var $date \DateTime */
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
        $formatter->setTimeZone(\IntlTimeZone::createTimeZone('GMT+0300'));
        return trim($formatter->format($date), " \t\n\r\0\x0B\xC2\xA0");
    }

    public function formatDatePattern($date, $pattern)
    {
        if (is_object($date) && is_a($date, 'DateTime')) {
            /* @var $date \DateTime */
            $date = $date->getTimestamp();
        } elseif (!is_numeric($date)) {
            $date = strtotime($date);
        }

        $locale = $this->getTranslator()->getLocale();
        $formatter = IntlDateFormatter::create($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        $formatter->setPattern($pattern);
        $formatter->setTimeZone(\IntlTimeZone::createTimeZone('GMT+0300'));
        return $formatter->format($date);
    }

    public function arrayPrint($string)
    {
        return print_r($string, true);
    }

    public static function gruntAsset($string)
    {
        if (file_exists(WEB_DIRECTORY . '/../app/assets.json')) {
            $data = file_get_contents(WEB_DIRECTORY . '/../app/assets.json');
            if (empty($data)) return '/' . $string;
            $assets = @json_decode($data, true);
            if (empty($assets)) return '/' . $string;
            foreach ($assets as $asset) {
                if ($asset['originalPath'] == $string) {
                    return '/' . $asset['versionedPath'];
                }
            }
        }
        return '/' . $string;
    }

    public function cdnAsset($string)
    {
        if ($string{0} != '/') $string = '/' . $string;

        if ($this->getKernel()->getEnvironment() == 'prod') {
            return 'https://gpcdn.ru' . $string;
        } else {
            return $string;
        }
    }
}