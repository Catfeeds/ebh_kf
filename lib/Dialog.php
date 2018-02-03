<?php
/**
 * 弹窗类
 * @author hfs
 *
 */
class Dialog{
	
	/**
	 * 弹出框
	 * @param string $classid 点击按钮或者链接id或者class
	 * @param striing $dialogid 弹窗标识id
	 * @param int $width 弹出框宽度
	 * @param int $height 弹出框高度
	 * @param bool top 是否弹出iframe
	 * @param bool reload 关闭窗口 是否刷新
	 * @method title,href是从<a>或者input标签中取
	 */
	public static function open($classid,$dialogid,$width,$height,$top=false,$reload=false){
		?>
		<script type="text/javascript">
			//先判断是否加载artDialog.js
			if(typeof(art)=="undefined" && typeof(hasAdd)=="undefined"){
				hasAdd = 1;
			    var oHead = document.getElementsByTagName('head').item(0);
			    var oScript= document.createElement("script");
			    oScript.type = "text/javascript";
			    oScript.src="/static/js/artDialog/artDialog.js?skin=blue";
			    oHead.appendChild( oScript);
			}
			//弹窗show
			$(function(){
                $(document).on('click', '<?php echo $classid ?>', function(){

						var width = '<?php echo $width;?>';
						var height = '<?php echo $height;?>';
						var top = Boolean(<?php echo $top;?>);
						var reload = Boolean(<?php echo $reload;?>);
						var dialogid  = '<?php echo $dialogid;?>';

                        var href = $(this).attr('href');
						var title=$(this).attr('title');

                        var width = width ? width : $(document.body).width()-60;
                        var height = height ? height : $(window).height()-75;
                        var html = '<iframe scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" width="'+<?php echo $width ?>+'" height="'+<?php echo $height ?>+'" src="'+href+'"></iframe>';
                        var artDialog = top == true ? window.top.art.dialog : art.dialog;

                        artDialog({
                            	id:dialogid,
                                title : title,
                                width : width,
                                height : height,
                                content : html,
                                padding : 10,
                                resize : false,
                                lock : true,
                                opacity : 0.2,

                                close:function(){
                                    if(reload == true){
                                        window.location.reload();
                                    }
                                }
                        });

                        return false;
                })
       		 })

	       </script>
	<?php
	    }

	/**
	 * 弹窗关闭
	 * @param string $dialogid 弹窗标识id
	 *
	 */
	public static function close($dialogid = null){
		$script = "
		<script type='text/javascript'>
			var artDialog = window.top.art ? window.top.art.dialog : art.dialog;
			var dialogid = '".$dialogid."';
			if(dialogid){
				artDialog({id:dialogid}).close() 
			}else{
				dialoglist=artDialog.list;
				
				for (var i in dialoglist){
					dialoglist[i].close()
				}
			}
		</script>";
		
		echo $script;
	
	}
		    
}
