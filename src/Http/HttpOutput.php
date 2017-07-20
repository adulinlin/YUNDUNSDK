<?php

namespace YunDunSdk\Http;

class HttpOutput
{
    private static $outputtype  = 'JSON';
    private static $supporttype = array('JSON', 'XML', 'JSONP');


    /**
     * @param array $data  数组或者字符串
     * @node_name 输出
     * @link
     * @desc
     */
    public static function output($data = [])
    {
        switch (strtoupper(self::$outputtype)) {
            case 'JSON':
                header('Content-Type:application/json; charset=utf-8');
                if (is_array($data)) {
                    echo json_encode($data);
                } else {
                    echo $data;
                }
                exit;
                break;
            case 'XML':
                header('Content-Type:text/xml; charset=utf-8');
                if (is_array($data)) {
                    echo self::xml_encode($data);
                } else {
                    echo $data;
                }
                exit;
                break;
            case 'JSONP':
                header('Content-Type:application/json; charset=utf-8');
                $handler = strtolower('callback');
                if (is_array($data)) {
                    echo $handler . '(' . json_encode($data) . ');';
                } else {
                    echo $handler . '(' . $data . ');';
                }
                exit;
                break;
        }
    }


    /**
     * @param $type
     * @node_name set output type
     * @link
     * @desc
     */
    public static function setType($type)
    {
        if (in_array(strtoupper($type), self::$supporttype)) {
            self::$outputtype = strtoupper($type);
        }
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $encoding 数据编码
     * @param string $root 根节点名
     * @return string
     */
    private static function xml_encode($data, $encoding = 'utf-8', $root = 'YunDun')
    {
        $xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
        $xml .= '<' . $root . '>';
        $xml .= self::data_to_xml($data);
        $xml .= '</' . $root . '>';
        return $xml;
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    private static function data_to_xml($data)
    {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml .= "<$key>";
            $xml .= (is_array($val) || is_object($val)) ? self::data_to_xml($val) : $val;
            list($key,) = explode(' ', $key);
            $xml .= "</$key>";
        }
        return $xml;
    }


    /**
     * @param $xml
     * @param bool $recursive
     * @return array
     * @node_name xml->array
     * @link
     * @desc
     */
    public static function XML2Array($xml, $recursive = false)
    {
        if (!$recursive) {
            $array = simplexml_load_string($xml);
        } else {
            $array = $xml;
        }

        $newArray = array();
        $array    = ( array )$array;
        foreach ($array as $key => $value) {
            $value = ( array )$value;
            if (isset ($value [0])) {
                $newArray [$key] = trim($value [0]);
            } else {
                $newArray [$key] = self::XML2Array($value, true);
            }
        }
        return $newArray;
    }


}