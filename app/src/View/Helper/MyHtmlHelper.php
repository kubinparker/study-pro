<?php

namespace App\View\Helper;

use Cake\View\Helper\HtmlHelper;

class MyHtmlHelper extends HtmlHelper {
    
    public function viewListValue($code, $list=array(), $default = '', $options = array()) {

        $options = array_merge(array(
            'after' => '',
            'before' => '',
            ), $options);
        extract($options);

        if (!array_key_exists($code, $list)) {
            return $default;
        }

        return $before.$list[$code].$after;
    }

    // 汎用
    public function view($val, $options = array()) {

    	$options = array_merge(array('before' => '',
                               'after' => '',
                               'default' => '',
                               'empty' => '',
                               'nl2br' => false,
                               'strip_tags' => 'default',
                               'h' => true,
                               'emptyIsZero' => false,
                               'maxLength' => 0
                           ),
                               $options);
    	extract($options);

    	if ($val != "") {
    		return $before.$val.$after;
    	}

    	return $default;
    }
    // 汎用　テキスト整形
    public function viewText($datas, $options=array()) {
    	$options = array_merge(array(
    		'after' => '',
    		'before' => '',
    		'default' => '',
    		'after_append' => null,
    		'separator' => ''), $options);
    	extract($options);

    	if (is_null($datas) || $datas == "") {
            if (is_null($after_append)) {
                return $default;
            } else {
                $txt_arr = array();
                array_push($txt_arr, $after_append);
                return $before . implode($separator, $txt_arr) . $after;
            }
    	}

    	$txt_arr = explode("\n", $datas);
    	if (empty($txt_arr)) {
    		if (is_null($after_append)) {
                return $default;
            } else {
                $txt_arr = array();
                array_push($txt_arr, $after_append);
                return $before . implode($separator, $txt_arr) . $after;
            }
    	}

	    if ($after_append) {
	    	array_push($txt_arr, $after_append);
	    }

	    if (empty($txt_arr)) {
	    	return $default;
	    }
    	return $before . implode($separator, $txt_arr) . $after;
    }

    // セレクト
    public function isSelected($key, $list, $value, $options = array()) {

        $options = array_merge(array(
            'returnText' => null,
            ), $options);
        extract($options);

        $res = false;

        if (!is_array($list)) {
            $res = false;
        }
        if (!array_key_exists($key, $list)) {
            $res = false;
        }
        if ($list[$key] == $value) {
            $res = true;
        }

        if (!is_null($returnText)) {
            if ($res) {
                $res = $returnText.'="'.$returnText.'"';
            } else {
                $res = "";
            }
        }

        return $res;
    }
    public function isCheckedArea($key, $list) {

        if (!is_array($list)) {
            return "";
        }
        if (in_array($key, $list)) {
            return 'checked="checked"';
        }

        return "";
    }
    // 汎用　配列をテキストにする
    public function viewList2Text($lists, $options = array()) {
        $options = array_merge(array(
            'before' => '',
            'after' => '',
            'separator' => '',
            'empty' => '',
            ), $options);
        extract($options);

        if (empty($lists) || !is_array($lists)) {
            return $empty;
        }

        $text = implode($separator, $lists);

        return $before.$text.$after;

    }

    // 画像
    public function viewAttacheImageTag($column, $datas, $size='0', $options = array()) {
        $options = array_merge(
            array(
                'empty' => NO_IMAGE_URL,
                'alt' => ''
            ), $options
            );
        extract($options);

        $img = '';

        $src = $empty;

        if (array_key_exists($column, $datas) && $datas[$column] == "") {

        }
        elseif (array_key_exists('attaches', $datas) && array_key_exists($column, $datas['attaches'])) {
            $src = $datas['attaches'][$column][$size];
        }

        if ($src) {
            $img = '<img src="'.$src.'" alt="'.$alt.'">';
        }

        return $img;

    }

}
