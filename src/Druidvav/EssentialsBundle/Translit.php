<?php
namespace Druidvav\EssentialsBundle;

class Translit
{
    const TR_NO_SLASHES = 0;
    const TR_ALLOW_SLASHES = 1;
    const TR_ENCODE = 0;
    const TR_DECODE = 1;

    const TRANSLITERATION_MAP = [
        // RUSSIAN
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
        'и' => 'i', 'й' => 'i', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
        'И' => 'I', 'Й' => 'I', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R',
        'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch',
        'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch',
        'ъ' => 'ie', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'iu', 'я' => 'ia',
        'Ъ' => 'Ie', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Iu', 'Я' => 'Ia',
        // UKRAINE
        'є' => 'ie', 'і' => 'i', 'ї' => 'i',  'ґ' => 'g', 'ў' => 'u',
        'Є' => 'Ye', 'I' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G', 'Ў' => 'U',
    ];

    /**
     * Преобразует строку в транслит (URI валидный)
     *
     * @param string $string строка для преобразования
     * @param int $allow_slashes разрешены ли слеши
     * @return string
     */
    public static function url($string, $allow_slashes = self::TR_NO_SLASHES)
    {
        $string = preg_replace("#([^\s]+)\'#usi", '\1', $string);
        $string = preg_replace('#[\s+\-\:\;\'\"]#usi', ' ', $string);

        $slash = "";
        if ($allow_slashes) {
            $slash = '\/';
        }

        static $LettersFrom = 'а б в г д е з и к л м н о п р с т у ф ы э й х ё';
        static $LettersTo   = 'a b v g d e z i k l m n o p r s t u f y e j x e';
        //static $Consonant = 'бвгджзйклмнпрстфхцчшщ';
        static $Vowel = 'аеёиоуыэюя';
        static $BiLetters = array(
            "ж" => "zh", "ц"=>"ts", "ч" => "ch",
            "ш" => "sh", "щ" => "sch", "ю" => "yu", "я" => "ya",
        );

        $string = preg_replace('/[_\s\.,?!\[\](){}]+/', '-', $string);
        $string = preg_replace("/-{2,}/", "--", $string);
        $string = preg_replace("/_-+_/", "--", $string);
        $string = preg_replace('/[_\-]+$/', '', $string);

        $string = mb_strtolower($string, 'UTF-8');

        //here we replace ъ/ь
        $string = preg_replace("/(ь|ъ)([".$Vowel."])/", "j\\2", $string);
        $string = preg_replace("/(ь|ъ)/", "", $string);

        //transliterating
        $string = str_replace(explode(' ', $LettersFrom),  explode(' ', $LettersTo), $string);
        $string = str_replace(array_keys($BiLetters), array_values($BiLetters), $string);

        $string = preg_replace("/j{2,}/", "j", $string);
        $string = preg_replace('/[^' . $slash . '0-9a-z_\-]+/', "-", $string);

        $string = preg_replace('/^[_\-]+/', '', $string);
        $string = preg_replace('/[_\-]+$/', '', $string);
        $string = preg_replace('/[\_\-]+$/', '-', $string);

        return $string;
    }

    public static function text($string)
    {
        $string = strtr($string, self::TRANSLITERATION_MAP);
        $string = iconv('UTF-8' , 'ASCII//TRANSLIT', $string);
        return $string;
    }
}
