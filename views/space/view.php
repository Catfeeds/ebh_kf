<?php $this->display('head');?>
<body>

<div class="tabcones">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php if($request['fid']>0){?>
<!-- 用户新鲜事 -->
  <tr>
    <td class="txtlft">类型：</td>
    <td colspan="3"><?php echo $info['categoryname']?></td>
  </tr>
  <tr>
    <td width="25%" class="txtlft">日期：</td>
    <td width="25%"><?php echo date('Y-m-d H:i:s', $info['dateline'])?></td>
    <td width="25%" class="txtlft">IP：</td>
    <td width="25%"><?php echo $info['ip']?></td>
  </tr>
  <tr>
    <td class="txtlft">账号：</td>
    <td> <?php echo $info['username']?></td>
    <td class="txtlft">用户：</td>
    <td> <?php echo $info['realname']?></td>
  </tr>
  <tr>
    <td class="txtlft">点赞数：</td>
    <td> <?php echo $info['upcount']?></td>
    <td class="txtlft">评论数：</td>
    <td> <?php echo $info['cmcount']?></td>
  </tr>
  <tr>
    <td class="txtlft">转发数：</td>
    <td><?php echo $info['zhcount']?></td>
    <td class="txtlft">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="txtlft">相关图片：</td>
    <td colspan="3">
    <?php if(!empty($imagelist)) {?>
    	<div class="tabcon tablist" >
	    <table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			 <td width="30%">图片</td>
			 <td width="10%">审核状态</td>
			 <td width="10%">操作</td>
			 <td width="30%">图片</td>
			 <td width="10%">审核状态</td>
			 <td width="10%">操作</td>
		  </tr>
		  <tr class="tabbg">
		  <?php foreach($imagelist as $key=>$image){?>

			  <?php if ($key > 0 && $key % 2 == 0) {?>
		  </tr>
		  <tr class="<?php echo ($key%4==0)?"tabbg":""?>">
			  <?php }?>
			    <td style="word-break:break-all"><a class="Jview2" href="/space/view.html?gid=<?php echo $image['gid']?>" title="图片详情" style="text-decoration:underline;color:#075587" ><img src="<?php echo $showpath.$image['path']?>" height="100" /></a></td>
				<td><?php $astatus=$image['admin_status'];if($astatus==1){echo '已通过';}elseif($astatus==2){ echo '未通过';}else{echo '未审核';}?></td>
			    <td class="tablink">
				<a class="Jview2" href="/space/view.html?gid=<?php echo $image['gid']?>" title="图片详情">详情</a>
				<?php if($astatus==0){?>
				<a class="Jcheck" toid="<?php echo $image['gid']?>">审核</a>
				<?php }?>
				</td>
		  <?php }?>
			    <?php if ($key % 2 == 0) echo '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';?>
		  </tr>
		</table>
		</div>
	<?php } else {echo '无';}?>
    </td>
  </tr>
  <tr>
    <td class="txtlft">详情：</td>
    <td colspan="3">
		<?php if($info['iszhuan']==1){?><!-- 转发动态模板 -->
				<?php if($info['refer_top_delete']==false){?><!-- 转发引用顶级没有被删除 -->
						<?php if($info['category']==1){?><!-- 心情 -->
							<h2 style="border-bottom:solid 1px #eee;padding-bottom:5px;" class="lertsd"><?php echo emotionreplace($info['message']['content'])?></h2>
							<h2 class="lertsd"><span >
							<a  class="kdtjdd" href="http://sns.ebh.net<?php echo geturl($info['message']['referuser']['uid'].'/main')?>"><?php echo $info['message']['referuser']['realname']?></a></span>
							 : <span ><?php echo emotionreplace($info['message']['refer']['content'])?></span></h2>

							<?php echo getimageboxhtml($info['message']['refer']);?>
						<?php }elseif($info['category']==2){?><!-- 日志 -->
							<h2 style="border-bottom:solid 1px #eee;padding-bottom:5px;" class="lertsd">
								<?php echo emotionreplace($info['message']['content'])?>
							</h2>
							<h2 class="lertsd"><span>
							<a  class="kdtjdd" href="http://sns.ebh.net<?php echo geturl($info['message']['referuser']['uid'].'/main')?>"><?php echo $info['message']['referuser']['realname']?></a></span>
							 的日志 <a href="http://sns.ebh.net<?php echo geturl($info['message']['referuser']['uid'].'/blog_view')?>?bid=<?php echo $info['toid']?>" target="_blank" style="color:#2696f0"><?php echo $info['message']['refer']['title']?></a></h2>
							<p class="kgregd"><?php echo $info['message']['refer']['tutor']?></p>
							<?php echo getimageboxhtml($info['message']['refer']);?>
						<?php }elseif($info['category']==4){?><!-- 转载日志 -->
							<h2 style="border-bottom:solid 1px #eee;padding-bottom:5px;" class="lertsd">
								<?php echo emotionreplace($info['message']['content'])?>
							</h2>
							<h2 class="lertsd"><span>
							<a  class="kdtjdd" href="http://sns.ebh.net<?php echo geturl($info['message']['refer']['referuser']['uid'].'/main')?>"><?php echo $info['message']['refer']['referuser']['realname']?></a></span>
							 的日志 <a href="http://sns.ebh.net<?php echo geturl($info['message']['refer']['referuser']['uid'].'/blog_view')?>?bid=<?php echo $info['toid']?>" target="_blank" style="color:#2696f0"><?php echo $info['message']['refer']['title']?></a></h2>
							<p class="kgregd"><?php echo $info['message']['refer']['tutor']?></p>
							<?php echo getimageboxhtml($info['message']['refer']);?>
						<?php } ?>
				<?php }else{?><!-- 转发引用顶级被删除 -->
							<h2 class="lertsd"> <?php echo emotionreplace($info['message']['content'])?></h2>
							<div class="lsidts">
							<div class="ryhdfds">
							<p class="laiwstn">抱歉，此动态已被作者删除。</p>
							</div>
							</div>
				<?php }?>


		<?php }else{?><!-- 非转发动态模板 -->
				<?php if($info['category']==1){?><!-- 心情 -->
					<h2 class="lertsd"><?php echo emotionreplace($info['message']['content'])?></h2>
					<?php echo getimageboxhtml($info['message']);?>
				<?php }elseif($info['category']==2){?><!-- 日志 -->
					<h2 class="lertsd"><a href="http://sns.ebh.net<?php echo geturl($info['fromuid'].'/blog_view')?>?bid=<?php echo $info['toid']?>"  target="_blank" style="color:#2696f0"><?php echo $info['message']['title']?></a></h2>
					<p class="kgregd"><?php echo $info['message']['tutor']?></p>
					<?php echo getimageboxhtml($info['message']);?>
				<?php }elseif($info['category']==4){?><!-- 转载日志 -->
					<div class="f-info">转载了一篇日志</div>
					<h2 class="lertsd"><span>
					<a  class="kdtjdd" href="http://sns.ebh.net<?php echo geturl($info['message']['referuser']['uid'].'/main')?>"><?php echo $info['message']['referuser']['realname']?></a></span>
					 的日志 <a href="http://sns.ebh.net<?php echo geturl($info['message']['referuser']['uid'].'/blog_view')?>?bid=<?php echo $info['message']['referuser']['bid']?>" target="_blank" style="color:#2696f0"><?php echo $info['message']['title']?></a></h2>
					<p class="kgregd"><?php echo $info['message']['tutor']?></p>
					<?php echo getimageboxhtml($info['message']);?>
				<?php } ?>
		<?php }?>


		<!-- 附加学业信息 -->
		<?php if(isset($info['message']['extmsg']) || isset($info['message']['refer']['extmsg'])){ ?>
		<?php
			if(isset($info['message']['extmsg'])){
				$extit = $info['message']['extmsg']['title'];
				$extinfo = $info['message']['extmsg']['contents'];
				$typestr = $info['message']['extmsg']['typestr'];
				$fromuid = $info['message']['extmsg']['fromuid'];
				$name = $info['message']['extmsg']['name'];
			}else{
				$extit = $info['message']['refer']['extmsg']['title'];
				$extinfo = $info['message']['refer']['extmsg']['contents'];
				$typestr = $info['message']['refer']['extmsg']['typestr'];
				$fromuid = $info['message']['refer']['extmsg']['fromuid'];
				$name = $info['message']['refer']['extmsg']['name'];
			}
		?>
		<div class="ksneit">
			<span>转自<a href="http://sns.ebh.net<?php echo geturl($fromuid.'/main')?>" class="kdtjdd"><?php echo $name?></a>的<?php echo $typestr?></span>
			<h2 style="font-weight:bold;" class="lertsd"><?php echo $extit?></h2>
			<p style="margin-top:10px;font-size:12px"><?php echo $extinfo?></p>
		</div>
		<?php } ?>
    </td>
  </tr>
<?php }elseif($request['bid']>0){?>
<!-- 日志 -->
  <tr>
    <td class="txtlft">类型：</td>
    <td colspan="3"><?php echo $info['catename']?></td>
  </tr>
  <tr>
    <td width="25%" class="txtlft">日期：</td>
    <td width="25%"><?php echo date('Y-m-d H:i:s', $info['dateline'])?></td>
    <td width="25%" class="txtlft">IP：</td>
    <td width="25%"><?php echo $info['ip']?></td>
  </tr>
  <tr>
    <td class="txtlft">账号：</td>
    <td> <?php echo $info['username']?></td>
    <td class="txtlft">用户：</td>
    <td> <?php echo $info['realname']?></td>
  </tr>
  <tr>
    <td class="txtlft">点赞数：</td>
    <td> <?php echo $info['upcount']?></td>
    <td class="txtlft">评论数：</td>
    <td> <?php echo $info['cmcount']?></td>
  </tr>
  <tr>
    <td class="txtlft">转发数：</td>
    <td><?php echo $info['zhcount']?></td>
    <td class="txtlft">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="txtlft">标题：</td>
    <td colspan="3"><?php if($info['iszhuan']){ ?> [转] <?php } ?><?php echo $info['title']?></td>
  </tr>
  <tr>
    <td class="txtlft">相关图片：</td>
    <td colspan="3">
    <?php if(!empty($imagelist)) {?>
    	<div class="tabcon tablist" >
	    <table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			 <td width="30%">图片</td>
			 <td width="10%">审核状态</td>
			 <td width="10%">操作</td>
			 <td width="30%">图片</td>
			 <td width="10%">审核状态</td>
			 <td width="10%">操作</td>
		  </tr>
		  <tr class="tabbg">
		  <?php foreach($imagelist as $key=>$image){?>

			  <?php if ($key > 0 && $key % 2 == 0) {?>
		  </tr>
		  <tr class="<?php echo ($key%4==0)?"tabbg":""?>">
			  <?php }?>
			    <td style="word-break:break-all"><a class="Jview2" href="/space/view.html?gid=<?php echo $image['gid']?>" title="图片详情" style="text-decoration:underline;color:#075587" ><img src="<?php echo $showpath.$image['path']?>" height="100" /></a></td>
				<td><?php $astatus=$image['admin_status'];if($astatus==1){echo '已通过';}elseif($astatus==2){ echo '未通过';}else{echo '未审核';}?></td>
			    <td class="tablink">
				<a class="Jview2" href="/space/view.html?gid=<?php echo $image['gid']?>" title="图片详情">详情</a>
				<?php if($astatus==0){?>
				<a class="Jcheck" toid="<?php echo $image['gid']?>">审核</a>
				<?php }?>
				</td>
		  <?php }?>
			    <?php if ($key % 2 == 0) echo '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';?>
		  </tr>
		</table>
		</div>
	<?php } else {echo '无';}?>
    </td>
  </tr>
  <tr>
    <td class="txtlft">详情：</td>
    <td colspan="3"><?php echo $info['content']?></td>
  </tr>
<?php }elseif($request['gid']>0){?>
<!-- 图片详情 -->
  <tr>
    <td class="txtlft">账号：</td>
    <td> <?php echo $info['username']?></td>
    <td class="txtlft">用户：</td>
    <td> <?php echo $info['realname']?></td>
  </tr>
  <tr>
    <td class="txtlft">日期：</td>
    <td><?php echo date('Y-m-d H:i:s', $info['dateline'])?></td>
    <td class="txtlft">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="txtlft">图片：</td>
    <td colspan="3"><img src="<?php echo $showpath.$info['path']?>" /></td>
  </tr>

<?php }?>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <?php $statusArr = array(''=>'未审核','1'=>'审核通过','2'=>'审核未通过','3'=>'审核撤销');?>
    <tr>
    <td width="25%" class="txtlft">管理员审核状态：</td>
    <td width="25%" class="cRed"><?php echo $statusArr[$info['admin_status']]?></td>    
    <td width="25%" class="txtlft">审核备注：</td>
    <td width="25%"><?php echo $info['admin_remark']? $info['admin_remark']:'--'?></td>
  </tr>
  <tr>
    <td class="txtlft">管理员IP：</td>
    <td><?php echo $info['admin_ip']?></td>    
    <td class="txtlft">审核处理时间：</td>
    <td><?php if($info['admin_dateline']){echo date("Y-m-d H:i:s",$info['admin_dateline']);}?></td>    
  </tr>
    <tr>
        <?php if($info['admin_status']==3){?>
            <td class="txtlft">撤销人:</td>
            <td><?php echo $info['checkname']?$info['checkname']:'--'?></td>
        <?php }else{?>
        <td class="txtlft">审核人:</td>
        <td><?php echo $info['checkname']?$info['checkname']:'--'?></td>
        <?php }?>
		    <td class="txtlft" rowspan ="2">删除时间：</td>
		<td rowspan ="2"><?php echo empty($info['delline'])?"无":date("Y-m-d H:m:s",$info['delline'])?></td>
    </tr>
  <!-- 教师审核信息 先隐藏 -->
  <!-- 
     <tr>
    <td class="txtlft">教师审核状态：</td>
    <td class="cRed"><?php echo $statusArr[$info['teach_status']]?></td>    
    <td class="txtlft">审核备注：</td>
    <td><?php echo $info['teach_remark']?></td>    
  </tr>
     <tr>
    <td class="txtlft">教师IP：</td>
    <td><?php echo $info['teach_ip']?></td>    
    <td class="txtlft">审核处理时间：</td>
    <td><?php if($info['teach_dateline']){echo date("Y-m-d H:i:s",$info['teach_dateline']);}?></td>    
  </tr>  
   -->
   <tr>
    <td class="txtlft">删除状态：</td>
    <td class="cRed"><?php echo ($info['del']==1)?"已删除":"正常"?></td>
  </tr>

  <tr><td colspan="4" align="center"><input type="button"  value="关闭" class="combtn cbtn_4 form_submit"   /></td></tr>
 </table>
</div>
<script type="text/javascript">
$(function(){
	$(".form_submit").click(function(){
		top.art.dialog({id: 'Jview<?php if ($request['gid']>0) echo '2';?>'}).close();
	});
})
</script>

<!--审核弹框-->
<div id="checkdiv" style="display:none">
<p style="height:40px"><span class="wid85">审核状态:</span>
<label><input type="radio" name="admin_status" value="1" checked="checked" /> 通 过</label>
<label style="margin-left:20px"><input type="radio" name="admin_status" value="2" /> 未通过</label>
</p>
<p><span class="wid85">备注信息:</span><textarea name="remark" id="remark" style="height:140px;width:400px;margin-left:10px"></textarea></p>
</div>

<?php
	$Dialog = EBH::app()->lib("Dialog");
	echo $Dialog::open(".Jview2","Jview2","1050","620",true,false);
?>
<script type="text/javascript">
$(function(){
	//审核操作
	$('.Jcheck').click(function(){
		var obj = $(this);
		var toid = obj.attr('toid');

		var dialog =  window.top.art.dialog({
		    title: '图片审核',
		    content: document.getElementById("checkdiv"),
		    width: '600px',
		    lock:true,
		    opacity : 0.2
           //	close:function(){window.location.reload();}
		}); 
		dialog.button([{
	    	name: '确定',
	    	focus: true,
	        callback: function () {
	        	var admin_status = window.top.$("input[name='admin_status']:checked").val();
	        	var remark = window.top.$("#remark").val();
		        //alert(selectrole); return false;
		        $.post('/space/checkprocess.html',{toid:toid,admin_status:admin_status,admin_remark:remark,type:10},function(data){
			        if(data.msg){
				        //alert(data.msg);
                        $.showmessage({
                            img : 'success',
                            message:data.msg,
                            title:'消息通知'
                        });
				    }
					if(!data.code){//审核成功
						if (admin_status == 2)
							obj.parent().prev().html('未通过');
						else if (admin_status == 1)
							obj.parent().prev().html('已通过');
						obj.remove();
					}
		        	dialog.close();
			        },'json');
	            return false;
	        }},
	        {name: '关闭'}]);
	});

	//删除操作
	$(".Jdel").click(function(){
		var toid = $(this).attr('toid');
		if(confirm("确定要删除该图片吗?")){
			$.post('/space/delprocess.html',{toid:toid,type:10},function(){
					window.location.reload();
				});
			}
		});

});
</script>
 </body>
</html>