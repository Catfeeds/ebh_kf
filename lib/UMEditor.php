<?php

/**
 * 百度UMEditor的封装lib类
 */
class UMEditor {

    public function createEditor($name, $width, $height, $value = NULL) {
        $str = '<link href="http://static.ebanhui.com/um/themes/default/css/umeditor.min.css" type="text/css" rel="stylesheet"></link>';
        $str .= '<script src="/lib/um/umeditor.config.js" type="text/javascript"></script>';
        $str .= '<script src="/lib/um/umeditor.js" type="text/javascript"></script>';
        $str .= '<script type="text/javascript" src="/lib/um/lang/zh-cn/zh-cn.js"></script>';
        $str .= '<script type="text/plain" id="' . $name . '" style="width:' . $width . ';height:' . $height . '"></script>';
        $str .= '<script type="text/javascript">';
        $imagephp = geturl('uploadimage');
        $str .= 'var ue = UM.getEditor("' . $name . '",{textarea:"' . $name . '",imageUrl:"' . $imagephp . '",autoHeightEnabled:false,imagePath:""});';
        if (!empty($value)) {
            $msg = str_replace("\r\n", '', $value);   //替换多余的回车换行
            $msg = str_replace("\n", '', $msg);
            $msg = str_replace("'", "\'", $msg);
            $str .= "ue.setContent('" . $msg . "');";
        }
        $str .= '</script>';
        echo $str;
    }
    public function simpleEditor($name, $width, $height, $value = NULL) {
        $str = '<link href="http://static.ebanhui.com/um/themes/default/css/umeditor.min.css" type="text/css" rel="stylesheet"></link>';
        
        $str .= '<script src="/lib/um/umeditor.config.js" type="text/javascript"></script>';
        $str .= '<script src="/lib/um/umeditor.js" type="text/javascript"></script>';
        $str .= '<script src="http://static.ebanhui.com/ebh/js/formulav2.js" type="text/javascript"></script>';
		$str .= '<link href="http://static.ebanhui.com/ebh/tpl/default/css/public.bak.css" type="text/css" rel="stylesheet"></link>';
        $str .= '<script type="text/javascript" src="/lib/um/lang/zh-cn/zh-cn.js"></script>';
        $str .= '<script type="text/plain" id="' . $name . '" style="width:' . $width . ';height:' . $height . '"></script>';
        $str .= '<script type="text/javascript">';
        $imagephp = geturl('uploadimage');
        $str .= 'var ue = UM.getEditor("' . $name . '",{textarea:"' . $name . '",imageUrl:"' . $imagephp . '",autoHeightEnabled:false,imagePath:"",toolbar:[\'undo redo | bold italic underline strikethrough | superscript subscript | forecolor backcolor | removeformat |\',
            \'insertorderedlist insertunorderedlist | fontsize\' ,
            \'| justifyleft justifycenter justifyright justifyjustify |\',
            \'image formula\'
        ]});';
        if (!empty($value)) {
            $msg = str_replace("\r\n", '', $value);   //替换多余的回车换行
            $msg = str_replace("\n", '', $msg);
            $msg = str_replace("'", "\'", $msg);
            $str .= "ue.setContent('" . $msg . "');";
        }
        $str .= '</script>';
        echo $str;
    }

}
