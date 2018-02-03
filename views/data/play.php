<?php $this->display('head');?>
<link type="text/css" href="http://static.ebanhui.com/ebh/js/jquery/jquery-ui/css/default/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="http://static.ebanhui.com/ebh/js/jquery/jquery-ui/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="http://static.ebanhui.com/ebh/js/play.js?version=2016101301"></script>
<script src="http://static.ebanhui.com/ebh/js/swfobject.js" type="text/javascript"></script>
<script type="text/javascript" src="/static/js/attatchplay.js?version=2016101301"></script>
<script type="text/javascript">
<!--
	//flv播放
			$(function (){
			<?php if($course){//课件播放?>
			
			var cwid = <?= $course['cwid']?>;
			var isfree = 1;
			var num = 1;//教室内
			var lastsuffix = 'flv';
			<?php if(!empty($types)){?>
				lastsuffix = '<?= $types ?>';
			<?php } ?>
			if(lastsuffix == 'flv'){
				//flv
				<?php 
					if(!empty($course['m3u8url'])) {
				?>
					playmu('<?= $course['m3u8url'] ?>',cwid,'',isfree,num,'562','938',0,'<?= $course['thumb'] ?>',<?= $course['cwsize']?>);
				<?php 
					}else{
				?>
					playflv('<?= $course['cwsource'] ?>',cwid,'',isfree,num,'562','938',0,'','<?=$k?>');
				<?php } ?>

			}else if (lastsuffix == 'mp3'){
				//ebh
				playaudio('<?= $course['cwsource'] ?>',cwid,'',isfree,num,'400','938',0,'','<?=$k?>');
			}else if (lastsuffix == 'ebh' || lastsuffix == 'ebhp'){
				//ebh
				freeebh('<?= $course['cwsource'] ?>',cwid,isfree,'','','','<?=$k?>');
			}else if(lastsuffix == 'swf'){
                playswf('<?= $course['cwsource'] ?>',cwid,'',isfree,num,'562','980',1,'<?=$k?>');
            }
			<?php }else if($attach){//附件播放?>
				var lastsuffix = 'flv';
				var isfree = 1;
				var num = 1;//教室内
				var attid=<?=$attach['attid']?>;
				<?php if(!empty($types)){?>
				lastsuffix = '<?= $types ?>';
				<?php } ?>
				if(lastsuffix=='flv'){
					playattflv('<?= $url ?>',attid,'',isfree,num,'562','938',0,'','<?=$k?>');
				}else if (lastsuffix == 'mp3'){
				//ebh
					playattaudio('<?= $url ?>',attid,'',isfree,num,'400','938',0,'','<?=$k?>');
				}else if(lastsuffix=='swf'){
					playswf('<?= $attach['source'] ?>',attid,'',isfree,num,'562','900',0,'','<?=$k?>');
				}
			<?php }?>
	});
//-->
</script>
    <body>
	    	<?php if(preg_match("/.*(\.ebhp)$/",$course['cwurl'])){?>
					<div style=" position: relative;height:600px;margin-left:10px;z-index:601;float:left;">
					<div id="playcontrol" style="width:400px;height:328px;"></div>
					</div>
			<?php }else if($types == 'mp3') {?>
            <div style=" float:left;position: relative;margin-left:10px;z-index:601;">
			<?php } else { ?>
			<div style=" float:left;position: relative;height:600px;margin-left:10px;z-index:601;">
			<?php } ?>
                <div id="flvcontrol" style="width:928px;height:600px;"></div>
            </div>
    </body>
</html>





