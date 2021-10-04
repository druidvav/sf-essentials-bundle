<?php
namespace Druidvav\EssentialsBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Autolink extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            'autolink' => new TwigFilter('autolink', [$this, 'autoLink'], [ 'pre_escape' => 'html', 'is_safe' => [ 'html' ] ]),
        ];
    }

    /**
     * Replace URLs in message with HTML link elements.
     * @param string $string The original message.
     * @return string The message with URLs replaced with anchors.
     */
    public function autoLink(string $string): string
    {
        $regexp = '/(https?)(:\/\/)?(\w+\.)?(\w+)\.([\w\/\-_.~&=?]+)/i';
        $anchor = '<a href="%s" target="_blank">%s</a>';
        preg_match_all($regexp, $string, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $replace = sprintf($anchor, $match[0], $match[0]);
            $string = str_replace($match[0], $replace, $string);
        }
        return $string;
    }
}