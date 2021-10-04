<?php
namespace Druidvav\EssentialsBundle;

use SimpleXMLElement;

class XmlArrayTools
{
    private static function parseXmlElement(SimpleXMLElement $xmlElement): array|string
    {
        $out = [];
        if ($xmlElement->attributes()) {
            $out['@attributes'] = [];
            foreach ($xmlElement->attributes() as $key => $value) $out['@attributes'][$key] = $value->__toString();
        }
        $multiTagMap = [];
        foreach ($xmlElement->children() as $child) {
            if (isset($out[$child->getName()])) {
                if (!isset($multiTagMap[$child->getName()])) {
                    $out[$child->getName()] = [
                        $out[$child->getName()],
                        self::parseXmlElement($child),
                    ];
                    $multiTagMap[$child->getName()] = true;
                } else $out[$child->getName()][] = self::parseXmlElement($child);
            } else {
                $out[$child->getName()] = self::parseXmlElement($child);
            }
        }
        if (count($out) == 1 && isset($out['@attributes'])) {
            $body = $xmlElement->__toString();
            if ($body) $out[] = $body;
        }
        if (count($out) == 0) return $xmlElement->__toString();
        return $out;
    }

    public static function xmlToArray($xml): array|string
    {
        if (!$xml instanceof SimpleXMLElement) {
            $xml = str_replace(["\r\n", "\n", "\t"], '', $xml);
            $xml = simplexml_load_string($xml);
        }
        if ($xml) return self::parseXmlElement($xml);
        else return [];
    }

    public static function arrayToXml($array): string
    {
        $xmlResult = '';
        foreach ($array as $key => $value) {
            if ($key === '@attributes' || $key === 'tag') continue;
            $xmlStruct = array();
            if (isset($value['tag'])) {
                $xmlStruct['tag'] = $value['tag'];
            } else {
                $xmlStruct['tag'] = $key;
            }

            if (is_array($value)) {
                if (isset($value['@attributes'])) {
                    $xmlStruct['@attributes'] = $value['@attributes'];
                }
                if (isset($value['body'])) {
                    if (is_array($value['body'])) {
                        $xmlStruct['body'] = self::arrayToXml($value['body']);
                    } else {
                        $xmlStruct['body'] = htmlspecialchars($value['body'], ENT_NOQUOTES);
                    }
                } else {
                    $xmlStruct['body'] = self::arrayToXml($value);
                }
            } else {
                $xmlStruct['body'] = htmlspecialchars($value, ENT_NOQUOTES);
            }

            $propertiesString = '';
            if (isset($xmlStruct['@attributes']) && !empty($xmlStruct['@attributes'])) {
                foreach ($xmlStruct['@attributes'] as $attributeName => $attributeValue) {
                    $propertiesString .= ' ' . $attributeName . '="' . htmlspecialchars($attributeValue, ENT_QUOTES|ENT_HTML5) . '" ';
                }
            }
            $xmlResult .= '<' . $xmlStruct['tag'] . $propertiesString . ((!empty($xmlStruct['body']) || $xmlStruct['body'] === '0') ? '>' . $xmlStruct['body'] . '</' . $xmlStruct['tag'] . '>' : '/>');
        }
        return $xmlResult;
    }
}
