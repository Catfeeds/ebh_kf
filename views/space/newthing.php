<?php $this->display('head');?>
<!--<script type="text/javascript" src="http://static.ebanhui.com/ebh/js/jquery/showmessage/jquery.showmessage.js"></script>-->
<!--<link rel="stylesheet" type="text/css" href="http://static.ebanhui.com/ebh/js/jquery/showmessage/css/default/showmessage.css" media="screen" />-->
<body>
<?php $statusArr = array(''=>'全部','0'=>'待审核','1'=>'已审核','2'=>'已删除');?>
<div class="baobiao ft12 threecheck MT_10">
  <ul class="one">
   <li id="one1" onClick="ChangeTab(this,'')" class="<?php echo ($request['cat']=='')?"on_box":""?>" >全部</li>
   <li id="one2" onClick="ChangeTab(this,0)" class="<?php echo ($request['cat']=='0')?"on_box":""?>" >待审核</li>
   <li id="one3" onClick="ChangeTab(this,1)" class="<?php echo ($request['cat']=='1')?"on_box":""?>" >已审核</li>
  </ul>
</div>
<script type="text/javascript">
        function ChangeTab(obj, cat) {
            location.href = '/space/newthing.html?cat='+cat;
        }
</script>
<form name="form" id="form" action="/space/newthing.html" method="get">
<input type="hidden" name="cat" value="<?php echo $request['cat']?>" />
<div class="tabconss">
<?php if(empty($request['cat'])){?>
<input type="button" class="comcheck" value="批量审核" id="docheck">
<?php }?>
<span class="wid85">审核状态:</span>
<select name="admin_status">
<option value='0' <?php echo empty($request['admin_status'])?"selected":""?>>全部</option>
<option value="1" <?php echo ($request['admin_status']==1)?"selected":""?>>已通过</option>
<option value="2" <?php echo ($request['admin_status']==2)?"selected":""?>>未通过</option>
</select>
<span class="wid85">关键字:</span>
<input type="text" class="inp wid140" name="q" value="<?php echo $request['q']?>" />
<input type="submit" value="查询" class="comcheck"/>
</div>
</form>
<div class="midnr">
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="tabtit">
	 <td width="5%"><input type="checkbox"  id="selall"/>全选</td>

	 <td class="leftxt" width="30%">标题/内容</td>
	 <td width="10%">账号</td>
	 <td>用户</td>
	 <td>类型</td>
	 <td>日期</td>
	 <td>IP</td>
      <td width="5%">审核/撤销人</td>
	 <td width="7%">审核状态</td>
	 <td width="15%">操作</td>
 </tr>
 <?php if(!empty($feedslist)){ ?>
<?php foreach($feedslist as $key=>$feeds){?>
    <tr class="<?php echo ($key%2==0)?"tabbg":""?>">
    <td>
    <?php if(empty($feeds['admin_status'])||$feeds['admin_status']==3){?>
    	<input type="checkbox" value="<?php echo $feeds['fid']?>" name="sel" />
	<?php }?>
	<?php echo max(0,($request['page']-1))*$request['pagesize']+$key+1?></td>

    <td class="leftxt" style="word-break:break-all"><a class="Jview" href="/space/view.html?fid=<?php echo $feeds['fid']?>" title="新鲜事详情" style="text-decoration:underline;color:#075587" ><?php $message =$feeds['message']; if ($feeds['category'] == 2) echo empty($message['refer'])?$message['title']:(empty($message['content'])?'（无文字内容）':$message['content']); else echo  empty($message['content'])?'（无文字内容）':$message['content'];?></a></td>
    <td><?php echo $users[$feeds['fromuid']]['username']?></td>
    <td><?php echo $users[$feeds['fromuid']]['realname']?></td>
	<td><?php echo $category[$feeds['category']]?></td>
	<td><?php echo date("Y-m-d H:i:s",$feeds['dateline'])?></td>
	<td><?php echo $feeds['ip']?></td>
        <td><?php echo $feeds['checkname']?$feeds['checkname']:'--'?></td>
	<td><?php $astatus=$feeds['admin_status'];if($astatus==1){echo '已通过';}elseif($astatus==2){ echo '未通过';}else{echo '未审核';}?></td>
    <td class="tablink">
	<a class="Jview" href="/space/view.html?fid=<?php echo $feeds['fid']?>" title="新鲜事详情">详情</a>
	<?php if($feeds['del']){?>
		<span>已删除</span>
	<?php }else{?>
	<?php if($astatus==1){?>
            <a class = revoke toid = "<?php echo $feeds['fid']?>" status = "<?php echo $astatus?>">撤销</a>
	<?php }elseif($astatus==2){?>
	<a class="Jdel" toid="<?php echo $feeds['fid']?>" >删除</a>
            <a class = revoke toid = "<?php echo $feeds['fid']?>" status =" <?php echo $astatus?>">撤销</a>
	<?php }else{?>
	<a class="Jcheck" toid="<?php echo $feeds['fid']?>">审核</a>
	<?php }}?>
	</td>
</tr>
 <?php }?>
 <?php }?>
</table>
</div>
<div class="page"><?php echo $pagestr?></div>
<div id="checkdiv" style="display:none">
<p style="height:40px"><span class="wid85">审核状态:</span>
<label><input type="radio" name="admin_status" value="1" checked="checked" /> 通 过</label>
<label style="margin-left:20px"><input type="radio" name="admin_status" value="2" /> 未通过</label>
</p>
<p><span class="wid85">备注信息:</span><textarea name="remark" id="remark" style="height:140px;width:400px;margin-left:10px"></textarea></p>
</div>

<div id="multcheckdiv" style="display:none">
<p style="height:40px"><span class="wid85">审核状态:</span>
<label><input type="radio" name="admin_status" value="1" /> 通 过</label>
<label style="margin-left:20px"><input type="radio" name="admin_status" value="2" /> 未通过</label>
</p>
<p><span class="wid85">备注信息:</span><textarea name="remark" id="multremark" style="height:140px;width:400px;margin-left:10px"></textarea></p>
</div>
<?php 
	$Dialog = EBH::app()->lib("Dialog");
	echo $Dialog::open(".Jview","Jview","1100","620",true,false);
?>
</div>

<script type="text/javascript">
$(function(){
	//审核操作
	$('.Jcheck').click(function(){
		var obj = $(this);
		var toid = obj.attr('toid');

		var dialog =  window.top.art.dialog({
		    title: '新鲜事审核',
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
		        $.post('/space/checkprocess.html',{toid:toid,admin_status:admin_status,admin_remark:remark,type:8},function(data){
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
				    	obj.parent().siblings("td:first").find("input").remove();
				    	obj.remove();
					}
		        	dialog.close();
                    setTimeout(function(){
                        location.reload();
                    },1000)
			        },'json');
	            return false;
	        }},
	        {name: '关闭'}]);
	});

    //撤销操作
    $('.revoke').click(function(){
        if(!window.confirm('是否撤销审核')){
            return false;
        }
        var toid = $(this).attr('toid');
        var status = $(this).attr('status');
        $.post('/space/revoke.html',{toid:toid,status:status,type:8},function(data){
            if(data.code == 0){
                $.showmessage({
                    img : 'success',
                    message:'撤销成功',
                    title:'消息通知'
                });
                setTimeout(function(){
                    location.reload();
                },1000)
            }else{
                $.showmessage({
                    img : 'error',
                    message:'撤销失败',
                    title:'消息通知'
                });
            }
        },'json');
        return false;
    })
	//删除操作
	$(".Jdel").click(function(){
		var toid = $(this).attr('toid');
		if(confirm("确定要删除该新鲜事吗?")){
			$.post('/space/delprocess.html',{toid:toid,type:8},function(){
					window.location.reload();
				});
			}
		});

	//批量选择
	$("#selall").click(function(){
		$("input[name='sel']").attr("checked", this.checked);
	});
	//批量审核
	$("#docheck").click(function(){
		var idarr = new Array();
		$("input[name='sel']").each(function(){
			if($(this).prop("checked")==true){
				idarr.push($(this).val());
				}
			});
		if(idarr.length==0){
			alert("请选择要审核的记录");
			return false;
			}
		//console.log(idarr);


		//弹窗处理
		var dialog =  window.top.art.dialog({
		    title: '新鲜事批量审核',
		    content: document.getElementById("multcheckdiv"),
		    width: '600px',
		    lock:true,
		    opacity : 0.2
           //	close:function(){window.location.reload();}
		}); 
		window.top.$("input[name='admin_status']:even").attr("checked",true);
		dialog.button([{
	    	name: '确定',
	    	focus: true,
	        callback: function () {
	        	var admin_status = window.top.$("input[name='admin_status']:checked").val();
	        	var remark = window.top.$("#multremark").val();
		        //alert(selectrole); return false;
		        $.post('/space/multcheckprocess.html',{ids:idarr.join(","),admin_status:admin_status,admin_remark:remark,type:8},function(data){
			        if(data.msg){
				        //alert(data.msg);
                        $.showmessage({
                            img : 'success',
                            message:data.msg,
                            title:'消息通知'
                       });
				     }
				     if(!data.code){//审核成功
				    	dialog.close();
				    	$("input[name=sel]").each(function(){
					    	$(this).attr("checked",false);
					    	});
						location.reload();
					 }

			        },'json');
	            return false;
	        }},
	        {name: '关闭'}]);	});
});
</script>
</body>
</html>