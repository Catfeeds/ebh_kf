<?php

/**
 * flash上传控件类
 */
class UpcontrolLib {
    
    /**
     * upcontrol 生成上传控件函数
     * 此函数可上传图片、课件等文件到分布式服务器中
     * 通过flash的方式上传
     *
     * @param string $name     控件的名称
     * @param string $type     上传的类型，默认值0表示上传课件，1表示上传图片，2更新图片，3上传课件包括其他格式，4上传附件，6资源
     * @param string $valueparam     原有控件值
     */
    function upcontrol($name = "up", $type = 0, $valueparam = array(), $uptype = 'courseware', $flashparam = array() , $multifile = false) {
        $constr = $this->getcontrolhtml($name, $type, $valueparam, $uptype, $flashparam, $multifile);
        echo $constr;
    }

    function getcontrolhtml($name = "up", $type = 0, $valueparam = array(), $uptype = 'courseware', $flashparam = array() , $multifile = false) {
        $flashparam['button_image_url'] = empty($flashparam['button_image_url']) ? 'http://static.ebanhui.com/ebh/images/TestImageNoText_65x29.png' : $flashparam['button_image_url'];
        $flashparam['button_width'] = empty($flashparam['button_width']) ? '65' : $flashparam['button_width'];
        $flashparam['button_height'] = empty($flashparam['button_height']) ? '29' : $flashparam['button_height'];
        $flashparam['button_text'] = empty($flashparam['button_text']) ? '上传' : $flashparam['button_text'];
        $flashparam['button_text_left_padding'] = empty($flashparam['button_text_left_padding']) ? '12' : $flashparam['button_text_left_padding'];


        $uploadurl = $this->getpostpath($type);
        if ($type == 0) {
            $filetyps = '*.ebhp';
            $filedes = 'e板会文件';
        } else if ($type == 4) {
            $filetyps = '*.ppt;*.excel;*.xls;*.wps;*.pdf;*.doc;*.docx;*.mp3;*.swf;*.avi;*.mpg;*.flv;*.zip;*.rar;*.7z;*.pptx;*.xlsx;*.rmvb;*.mp4';
            $filedes = '文件格式';
        } elseif ($type == 3) {
            $filetyps = '*.ppt;*.excel;*.xls;*.wps;*.pdf;*.doc;*.docx;*.ebhp;*.avi;*.mpg;*.mp3;*.flv;*.zip;*.rar;*.7z;*.pptx;*.xlsx;*.rmvb;*.mp4';
            $filedes = '文件格式';
        } elseif ($type == 6) {
            $filetyps = '*.ppt;*.xls;*.wps;*.pdf;*.doc;*.docx;*.txt;*.jpg;*.jpeg;*.png;*.gif;*.pptx;*.xlsx';
            $filedes = '文件格式';
        } elseif ($type == 7) {
            $filetyps = '*.ppt;*.excel;*.xls;*.wps;*.doc;*.jpg;*.jpeg;*.png;*.gif;*.docx;*.ebh;*.avi;*.mpg;*.flv;*.pptx;*.xlsx';
            $filedes = '文件格式';
        } elseif ($type == 8) {
            $filetyps = '*.ebhp;*.jpg;*.jpeg;*.bmp;*.gif';
            $filedes = 'e板会文件或图片';
        } elseif ($type == 9) {
            $filetyps = '*.wav;*.mp3';
            $filedes = '音频文件';
        }elseif ($type == 10) {
            $filetyps = '*.ebhp;*.flv;*.ppt;*.pptx;*.doc;*.docx';
            $filedes = '解析课件';
        } elseif ($type == 11) {
            $filetyps = '*.flv;*.ppt;*.pptx;*.doc;*.docx;*.mp4';
            $filedes = '答疑上传附件';
        } else {
            $filedes = '图片文件';
            $filetyps = '*.jpg;*.jpeg;*.png;*.gif';
        }
        $constr = '<script type="text/javascript">';
        $constr .= 'var ' . $name . '_swfu;';
        $constr .= '$(document).ready(function(){';
        $constr .= 'var ' . $name . '_settings = {';
        $constr .= 'flash_url : "http://static.ebanhui.com/ebh/flash/upload.swf",';
        if ($type == 0) {
            $constr .= 'upload_url: "' . $uploadurl . '",';
        } else if ($type == 3) {
            $constr .= 'upload_url: "' . $uploadurl . '",';
        } else {
            $constr .= 'upload_url: "' . $uploadurl . '?uptype=' . $uptype . '",';
            // $constr .= 'upload_url: "' . $uploadurl . '",';
        }
        //在更新缩略图的时候需要附带更新的尺寸大小和原图片地址
        if (!empty($valueparam['size'])) {
            $constr .= 'post_params: {"PHPSESSID" : "","updatesize":"' . $valueparam['size'] . '","old_path":"' . $valueparam['old_path'] . '"},';
        } else {
            $constr .= 'post_params: {"PHPSESSID" : ""},';
        }
        $constr .= 'file_size_limit : "300 MB",';
        $constr .= 'file_types : "' . $filetyps . '",';
        $constr .= 'file_types_description : "' . $filedes . '",';
        $constr .= 'file_upload_limit : 100,';
        $constr .= 'file_queue_limit : 10,';
        $constr .= 'custom_settings : {';
        $constr .= 'progressTarget : "' . $name . '_upprogressbox",';
        $constr .= 'cancelButtonId : "' . $name . '_btnCancel"';
        $constr .= '},';
        $constr .= 'debug: false,';
        $constr .= 'button_image_url: "' . $flashparam['button_image_url'] . '",';
        $constr .= 'button_width: "' . $flashparam['button_width'] . '",';
        $constr .= 'button_height: "' . $flashparam['button_height'] . '",';
        $constr .= 'button_placeholder_id: "' . $name . '_spanuploadbutton",';
        $constr .= 'button_text: \'<span class="theFont">' . $flashparam['button_text'] . '</span>\',';
        $constr .= 'button_text_style: ".theFont { font-size: 14; }",';
        $constr .= 'button_text_left_padding: 12,';
        $constr .= 'button_text_top_padding: 3,';
		if($multifile)
			$constr .= 'button_action : SWFUpload.BUTTON_ACTION.SELECT_FILES,';
		
        $constr .= 'file_queued_handler : fileQueued,';
        $constr .= 'file_queue_error_handler : fileQueueError,';
        $constr .= 'file_dialog_complete_handler : fileDialogComplete,';
        $constr .= 'upload_start_handler : uploadStart,';
        $constr .= 'upload_progress_handler : uploadProgress,';
        $constr .= 'upload_error_handler : uploadError,';
        $constr .= 'upload_success_handler : uploadSuccess,';
        $constr .= 'upload_complete_handler : uploadComplete,';
        $constr .= 'queue_complete_handler : queueComplete';
        $constr .= '};';

        $constr .= $name . '_swfu = new SWFUpload(' . $name . '_settings);';
//        $constr .= 'if($.isFunction(top.resetmain)) top.resetmain();';

        $constr .= '});;';
        $constr .= '</script>';
        $filenameid = $name . '[upfilename]';
        $filepathid = $name . '[upfilepath]';
        $filesizeid = $name . '[upfilesize]';
        $filenamevalue = empty($valueparam['upfilename']) ? '' : $valueparam['upfilename'];
        $filepathvalue = empty($valueparam['upfilepath']) ? '' : $valueparam['upfilepath'];
        $filesizevalue = empty($valueparam['upfilesize']) ? '' : $valueparam['upfilesize'];
		if(empty($multifile)){
        if (empty($valueparam['size'])) {
            if (empty($filepathvalue)) {
                $constr .= '<div id="' . $name . '_upprogressbox" class="upprogressbox" >';
            } else {
                $constr .= '<div id="' . $name . '_upprogressbox"  class="upprogressbox" style="display:block;">';
            }
//	}
            $constr .= '<div class="upfileinfo">';
            $constr .= '<span class="upstatusinfo"><img src="http://static.ebanhui.com/ebh/images/upload.gif"/></span>';
            $constr .= '<span id="' . $name . '_spanUpfilename" class="spanUpfilename">';
            $percent = "0%";
            if (!empty($filepathvalue)) {
                $percent = "100%";
                if ($type == 0) {
                    $constr .= $filenamevalue;
                    $constr .= '</span>';
                    $constr .= '<span id="' . $name . '_spanUppercent" style="width:100%">&nbsp;100%</span>';
                } elseif ($type == 1 || $type == 9 || $type == 3) {
                    $constr .= empty($filenamevalue) ? $filepathvalue : $filenamevalue;
                    $constr .= '</span>';
                    $constr .= '<span id="' . $name . '_spanUppercent" style="width:100%">&nbsp;100%</span><span id="' . $name . '_spanShowButton"><a href="' . $filepathvalue . '" target="_blank">&nbsp;查看</a></span>';
                }
            } else {
                $constr .= '</span>';
                $constr .= '<span id="' . $name . '_spanUppercent"></span>';
                if ($type == 1) {
                    $constr .= '<span id="' . $name . '_spanShowButton"><a href="' . $filepathvalue . '" target="_blank">&nbsp;查看</a></span>';
                }
            }
            $constr .= '<span><a href="javascript:" onclick="deleteUpload(\'' . $name . '\')">&nbsp;删除</a></span>';
            $constr .= '</div>';
            $constr .= '<div class="upprogressbar">';
            $constr .= '<span class="upprogressstext">上传总进度：</span>';
            $constr .= '<span id="' . $name . '_spanUppercentBox" class="spanUppercentBox">';
            if (!empty($filepathvalue)) {
                $constr .= '<span id="' . $name . '_spanUpShowPercent" class="spanUpShowPercent" style="width:100%"></span>';
            } else {
                $constr .= '<span id="' . $name . '_spanUpShowPercent" class="spanUpShowPercent"></span>';
            }
            $constr .= '</span>';

            $constr .= '<span id="' . $name . '_spanUppercentinfo"  class="spanUppercentinfo">' . $percent . '</span>';
            $constr .= '<span id="' . $name . '_spanUpInfo" class="spanUpInfo"></span>';
            $constr .= '</div>';
        }
        $constr .= '</div>';
		}
        if (isset($valueparam['size'])) {
            $constr .= '<div style="clear:both"><span id="' . $name . '_spanuploadbutton"></span></div>';
        } else {
            $constr .= '<div style="clear:both"><span id="' . $name . '_spanuploadbutton"></span></div>';
        }
		if(empty($multifile)){
        $constr .= '<input type="hidden" id="' . $filenameid . '" name="' . $filenameid . '" value="' . $filenamevalue . '"/>';
        $constr .= '<input type="hidden" id="' . $filepathid . '" name="' . $filepathid . '" value="' . $filepathvalue . '"/>';
        $constr .= '<input type="hidden" id="' . $filesizeid . '" name="' . $filesizeid . '" value="' . $filesizevalue . '"/>';
		}
        return $constr;
    }

    /**
     * getpostpath 获取上传路径请求处理路径
     *
     * @param string $type     上传的类型，默认值0表示上传课件，1表示上传图片
     * @return string      	     返回上传路径请求处理路径
     */
    function getpostpath($type = 0) {
        include S_ROOT . '/config/upconfig.php';
        $_UP = Ebh::app()->getConfig()->load('upconfig');
        $uploadurl = '';
        if ($type == 0 || $type == 3 || $type == 10 || $type == 11) {
            $scount = count($_UP['course']['server']);
            $spos = rand(0, $scount - 1);
            $uploadurl = $_UP['course']['server'][$spos];
        } elseif ($type == 4) {
            $scount = count($_UP['attachment']['server']);
            $spos = rand(0, $scount - 1);
            $uploadurl = $_UP['attachment']['server'][$spos];
        } elseif ($type == 5) {
            $scount = count($_UP['room']['server']);
            $spos = rand(0, $scount - 1);
            $uploadurl = $_UP['room']['server'][$spos];
        } elseif ($type == 6) {
            $scount = count($_UP['rfile']['server']);
            $spos = rand(0, $scount - 1);
            $uploadurl = $_UP['rfile']['server'][$spos];
        } elseif ($type == 7) {
            $scount = count($_UP['stuexam']['server']);
            $spos = rand(0, $scount - 1);
            $uploadurl = $_UP['stuexam']['server'][$spos];
        } elseif ($type == 8) {//作业课件
            $scount = count($_UP['examcourse']['server']);
            $spos = rand(0, $scount - 1);
            $uploadurl = $_UP['examcourse']['server'][$spos];
        } elseif ($type == 9) {//音频文件，用在答疑
            $scount = count($_UP['audio']['server']);
            $spos = rand(0, $scount - 1);
            $uploadurl = $_UP['audio']['server'][$spos];
        } else {
            $scount = count($_UP['pic']['server']);
            $spos = rand(0, $scount - 1);
            $uploadurl = $_UP['pic']['server'][$spos];
        }
        return $uploadurl;
    }

}
