<?php $this->display('head');?>
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
            location.href = '/space/photo.html?cat='+cat;
        }
</script>
<form name="form" id="form" action="/space/photo.html" method="get">
<input type="hidden" name="cat" value="<?php echo $request['cat']?>" />
<div class="tabconss">
<?php if(empty($request['cat'])){?>
<input type="button" class="comcheck" value="批量审核" id="docheck">
<?php }?>
<span class="wid85">审核状态:</span>
<select name="admin_status">
<option value='0' <?php empty($request['admin_status'])?"selected":""?>>全部</option>
<option value="1" <?php ($request['admin_status']==1)?"selected":""?>>已通过</option>
<option value="2" <?php ($request['admin_status']==2)?"selected":""?>>未通过</option>
</select>
<span class="wid85">日期从&nbsp;</span> <input name="begindate" type="text"   class="inp" onClick="WdatePicker()" value="<?php echo $request['begindate']?>" />到&nbsp;<input name="enddate" type="text"   class="inp" onClick="WdatePicker()" value="<?php echo $request['enddate']?>" />
<input type="submit" value="查询" class="comcheck"/>
</div>
</form>
<div class="midnr">
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="tabtit">
	 <td width="5%"><input type="checkbox"  id="selall"/>全选</td>

	 <td class="leftxt" width="30%">图片</td>
	 <td width="10%">账号</td>
	 <td>用户</td>
	 <td>日期</td>
      <td>审核/撤销人</td>
	 <td width="7%">审核状态</td>
	 <td width="15%">操作</td>
 </tr>
 <?php if(!empty($imagelist)){ ?>
<?php foreach($imagelist as $key=>$image){?>
    <tr class="<?php echo ($key%2==0)?"tabbg":""?>">
    <td>
    <?php if(empty($image['admin_status'])||$image['admin_status']==3){?>
    	<input type="checkbox" value="<?php echo $image['gid']?>" name="sel" />
	<?php }?>
	<?php echo max(0,($request['page']-1))*$request['pagesize']+$key+1?>
    </td>

    <td class="leftxt" style="word-break:break-all"><div style="width:360px;overflow: hidden;"><a class="Jview2" href="/space/view.html?gid=<?php echo $image['gid']?>" title="图片详情" style="text-decoration:underline;color:#075587" ><img src="<?php echo $showpath.$image['path']?>" height="100" /></a></div></td>
    <td><?php echo $users[$image['uid']]['username']?></td>
    <td><?php echo $users[$image['uid']]['realname']?></td>
	<td><?php echo date("Y-m-d H:i:s",$image['dateline'])?></td>
    <td><?php echo $image['checkname']?$image['checkname']:'--'?></td>
	<td><?php $astatus=$image['admin_status'];if($astatus==1){echo '已通过';}elseif($astatus==2){ echo '未通过';}else{echo '未审核';}?></td>
    <td class="tablink">
	<a class="Jview2" href="/space/view.html?gid=<?php echo $image['gid']?>" title="图片详情">详情</a>
	<?php if($astatus==0||$astatus==3){?>
	<a class="Jcheck" toid="<?php echo $image['gid']?>">审核</a>
	<?php }else{?>
        <a class="revoke" toid="<?php echo $image['gid']?>" status=<?php echo $astatus?>>撤销</a>
        <?php }?>
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
	echo $Dialog::open(".Jview2","Jview2","1100","620",true,false);
?>
</div>

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
                            img:'success',
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
                    setTimeout(function(){
                        location.reload();
                    },1000)
		        	dialog.close();
			        },'json');
	            return false;
	        }},
	        {name: '关闭'}]);
	});

    //撤销审核
    $('.revoke').click(function(){
        if(!window.confirm('是否撤销审核')){
            return false;
        }
        var toid = $(this).attr('toid');
        var status = $(this).attr('status');
        $.post('/space/revoke.html',{toid:toid,status:status,type:10},function(data){
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
		if(confirm("确定要删除该图片吗?")){
			$.post('/space/delprocess.html',{toid:toid,type:10},function(){
					window.location.reload();
				});
			}
		});

	//批量选择
	$("#selall").click(function(){
		$("input[name='sel']").attr("checked", $("#selall").prop('checked'));
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
		    title: '图片批量审核',
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
		        $.post('/space/multcheckprocess.html',{ids:idarr.join(","),admin_status:admin_status,admin_remark:remark,type:10},function(data){
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