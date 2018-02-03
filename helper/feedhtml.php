	<?php
/**
 *处理动态feeds格式化后的html
 * @author echo
 */

/**
 * 图片排版
 * 组装图片 生成html
 */
if(!function_exists('getimagehtml')){
	function getimagehtml($images,$size,$showpath){
		$html = '<div class="image_box"> ';
		$style = '';
		foreach($images as $image){
			$showimg = $showpath.''.$image['path'];
			list($width,$height) = explode("_", $size);
			if($width>0 && $height>0 && count($images) >1){
				$style = "style=width:{$width}px;height:{$height}px;text-align:center;vertical-align:middle;display:table-cell;background:#fff;border:0";
			}else{
				$style = "style=text-align:center;vertical-align:middle;display:table-cell;background:#fff;border:0";
			}
			if($size == '0_0'){
				$html.="<div style='float:left;margin-right:4px;margin-bottom:4px'><a href='javascript:;' $style><img src='".$showimg."' layer-img='".$showimg."' /></a></div>";
			}else{
				$html.="<div style='float:left;margin-right:4px;margin-bottom:4px'><a href='javascript:;' $style><img src='".getthumb($showimg,$size)."' layer-img='".$showimg."' /></a></div>";
			}
		}
		return $html."</div>";
	}
}


/**
 * 获取imagebox的html
 */
if(!function_exists('getimageboxhtml')){
	function getimageboxhtml($feedmessage){
		$gidarr = !empty($feedmessage['images']) ? explode(",", $feedmessage['images']) : array();
		$imagecount = count($gidarr);
		$upconfig = Ebh::app()->getConfig()->load('upconfig');
		$showpath = $upconfig['pic']['showpath'];
		$imgmodel = Ebh::app()->model('Image');
		$images = $imgmodel->getimgs($gidarr);
		$imgboxhtml = '';
		if(empty($images)){
			return $imgboxhtml;
		}
		if($imagecount==1){
			//没有650_350尺寸则采用原图
			$imgboxhtml = strpos($images[0]['sizes'], '650_350') !== false ? getimagehtml($images,'650_350',$showpath) : getimagehtml($images,'0_0',$showpath);
		}elseif($imagecount==2||$imagecount==4||$imagecount==8){
			$imgboxhtml = getimagehtml($images,'320_170',$showpath);
		}else{
			$imgboxhtml = getimagehtml($images,'210_110',$showpath);
		}
		return $imgboxhtml;
	}
}

//表情替换为图片
function emotionreplace($content){
	$content   =   str_replace('&#091;','[',$content);
	$content   =   str_replace('&#093;',']',$content);

	$s = preg_replace_callback(
			"/\[(.*)\]/isU",
			function($matchs){
				$emotion = Ebh::app()->getConfig()->load('emotion');
				$ret = '';
				if(!empty($emotion[$matchs[1]])){
					$ret = "<img width=\"24\" height=\"24\" src=\"http://static.ebanhui.com/sns/images/qq/".$emotion[$matchs[1]]."\">";
				}
				return $ret;
			},
			$content
	);


	return $s;
}