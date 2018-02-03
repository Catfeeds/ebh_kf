<?php $this->display('head');?>
<!--<script type="text/javascript" src="http://static.ebanhui.com/ebh/js/jquery/showmessage/jquery.showmessage.js"></script>-->
<link rel="stylesheet" type="text/css" href="http://static.ebanhui.com/ebh/js/jquery/showmessage/css/default/showmessage.css" media="screen" />
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
            location.href = '/data/homework.html?cat='+cat;
        }
</script>
<form name="form" id="form" action="/data/homework.html" method="get">
<input type="hidden" name="cat" value="<?php echo $request['cat']?>" />
<input type="hidden" name="crid" id="crid" value="<?php echo $request['crid']?>" />
<div class="tabconss">
<?php if(empty($request['cat'])){?>
<input type="button" class="comcheck" value="批量审核" id="docheck">
<?php }?>
<span class="wid85">所属网校:</span>
<input value="<?php echo $request["crname"]?>" class="inp wid140" readonly id="crname" name="crname" onClick="$('.Jselect').click()" /><button type="button" id="dialog-link" class="comcheck">选择</button>
<button type="button" id="clear" class="comcheck" onClick="clearSchool()">清除</button>
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

	 <td class="leftxt" width="28%">作业名称</td>
	 <td>发布教师</td>
	 <td class="leftxt">所属网校</td>
	 <td width="10%">提交日期</td>
      <td>审核/撤销人</td>
	 <td>审核状态</td>
	 <td width="15%">操作</td>
 </tr>
<?php foreach($homeworks as $key=>$h){?>
    <tr class="<?php echo ($key%2==0)?"tabbg":""?>">
    <td>
	<?php if(empty($h['admin_status'])||$h['admin_status']==3){?>
    	<input type="checkbox" value="<?php echo $h['eid']?>" name="sel" />
	<?php }?>
	<?php echo max(0,($request['page']-1))*$request['pagesize']+$key+1?></td>
    <td class="leftxt"><a class="Jview" href="/data/view.html?eid=<?php echo $h['eid']?>" title="作业详情" style="text-decoration:underline;color:#075587" ><?php echo $h['title']?></a></td>
    <td><?php echo $h['realname']?></td>
	<td class="leftxt"><?php echo $h['crname']?></td>
    <td><?php echo date("Y-m-d H:i:s",$h['dateline'])?></td>
    <td><?php echo $h['checkname']?$h['checkname']:'--'?></td>
	<td><?php if($h['admin_status']==1){echo '已通过';}elseif($h['admin_status']==2){ echo '未通过';}else{echo '未审核';}?></td>
    <td class="tablink">
	<a class="Jview" href="/data/view.html?eid=<?php echo $h['eid']?>" title="作业详情">详情</a>
	<?php if($h['admin_status']==0||$h['admin_status']==3){?>
	<a class="Jcheck" toid="<?php echo $h['eid']?>">审核</a>
    <?php }else{?>
        <a class="revoke" toid="<?php echo $h['eid']?>" status="<?php echo $h['admin_status']?>" >撤销</a>
    <?php }?>
	</td>

</tr>
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
	echo $Dialog::open(".Jview","Jview","800","620",true,false);
	//echo $Dialog::open(".Jselect","Jselect","800","620",false,false);
?>
</div>
<!-- ui-dialog -->
<!-- <div id="dialog" title="选择学校">
    <input class="inp a60" type="text" name="classroom_keyword" id="classroom_keyword" value="请输入网校名称或域名"onblur="if(this.value == ''){this.value = '请输入网校名称或域名';}" onClick="if(this.value == '请输入网校名称或域名'){this.value = '';}else {this.select();}" /><button class="cbtn_my" type="button" id="classroom_search">搜索</button>
    <div class="tabcon tablist">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tbody>
          <tr class="tabtit">
            <td width="10%">选择</td>
            <td width="15%">域名</td>
            <td width="50%">网校名称</td>
            <td width="20%">创建时间</td>
          </tr>
        </tbody>
        <tbody id='moduletbody'>
        </tbody>
        </table>
    </div>
    <div id="hidden_classroom_list" style="display:none"></div>
</div> -->
<script type="text/javascript">
$(function(){
    //审核操作
    $(document).on("click", ".Jcheck", function () {
        var obj = $(this);
        var toid = obj.attr('toid');
        var astatus = obj.attr('astatus');
        var cat = <?php echo $cat;?>;
        var dialog = window.top.art.dialog({
            title: '课件审核',
            content: document.getElementById("checkdiv"),
            width: '600px',
            lock: true,
            opacity: 0.2
            //close:function(){window.location.reload();}
        });

        dialog.button([{
            name: '确定',
            focus: true,
            callback: function () {
                var admin_status = window.top.$("input[name='admin_status']:checked").val();
                var remark = window.top.$("#remark").val();
                //alert(selectrole); return false;
                $.post('/data/checkprocess.html', {
                    toid: toid,
                    admin_status: admin_status,
                    admin_remark: remark,
                    type: 7
                }, function (data) {
                    var revoke = "<a class=\"revoke\" toid=" + toid + " status=" + admin_status + ">撤销</a>";
                    var delt = "<a class=\"Jdel\" toid=" + toid + ">删除</a>";
                    if (data.msg) {
                        //alert(data.msg);
                        $.showmessage({
                            img: 'success',
                            message: data.msg,
                            title: '消息通知',
                            timeoutspeed: 1500
                        });
                    }
                    if(cat == -1){
                        if (data.code == 0) {//审核成功
                            obj.parent().prev().html('已通过');
                            obj.parent().siblings("td:first").find("input").remove();
                            obj.parent().append(revoke);
                            obj.parent().prev().prev().html(data.checker);
                            obj.remove();
                        }
                        if(data.code == 2){//审核不通过
                            obj.parent().prev().html('未通过');
                            obj.parent().siblings("td:first").find("input").remove();
                            obj.parent().append(revoke);
                            obj.parent().prev().prev().html(data.checker);
                            obj.remove();
                        }
                        if(data.code == 1){//审核失败
                            obj.parent().prev().html('未通过');
                            obj.parent().siblings("td:first").find("input").remove();
                            obj.parent().append(revoke);
                            obj.parent().prev().prev().html(data.checker);
                            obj.remove();
                        }  
                    }else{
                        obj.parent().parent().remove();
                    }
                    dialog.close();
                    $('#remark').val('');
//                    setTimeout(function(){
//                        location.reload();
//                    },2000)
                }, 'json');
                return false;
            }
        },
            {name: '关闭'}]);
    });

    //撤销审核
    $(document).on("click", ".revoke", function () {

        if (!window.confirm('是否撤销审核')) {
            return false;
        }
        var obj = $(this);
        var toid = $(this).attr('toid');
        var status = $(this).attr('status');
        var num = $(this).parent().parent().find('td').first().html();
        var cat = <?php echo $cat;?>;
        $.post('/data/revoke.html', {toid: toid, status: status, type: 7}, function (data) {
            if(cat == -1){
              if (data.code == 0) {
                    $.showmessage({
                        img: 'success',
                        message: '撤销成功',
                        title: '消息通知'
                    });
                    var html = "<a class='Jcheck' toid="+toid+">审核</a>";
                    var check = "<input value="+toid+" name='sel' type='checkbox'>"+(parseInt(num));
                    $(obj).parent().prev().html('未审核');
                    $(obj).parent().append(html);
                    $(obj).parent().parent().find('td').first().html(check);
                    $(obj).remove();
                } else {
                    $.showmessage({
                        img: 'error',
                        message: '撤销失败',
                        title: '消息通知'
                    });
                }  
            }else{
                if (data.code == 0) {
                    $.showmessage({
                        img: 'success',
                        message: '撤销成功',
                        title: '消息通知'
                    });
                    obj.parent().parent().remove();
                } else {
                    $.showmessage({
                        img: 'error',
                        message: '撤销失败',
                        title: '消息通知'
                    });
                }
            }
            
        }, 'json');
        return false;
    })

    //删除操作
    $(document).on("click", ".Jdel", function () {
        var toid = $(this).attr('toid');
        if (confirm("确定要删除该课件吗?")) {
            $.post('/data/delprocess.html', {toid: toid, type: 1}, function () {
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
		    title: '作业批量审核',
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
		        $.post('/data/multcheckprocess.html',{ids:idarr.join(","),admin_status:admin_status,admin_remark:remark,type:7},function(data){
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
	        {name: '关闭'}]);	
	});
});
// Link to open the dialog
$( "#dialog-link,#crname" ).click(function( event ) {
   /* $( "#dialog" ).dialog( "open" );*/
   var content_dialog = '<div id="dialog" title="选择学校" style="width:600px"><input class="inp a60" type="text" name="classroom_keyword" id="classroom_keyword" value="请输入网校名称或域名"onblur="if(this.value == \'\'){this.value = \'请输入网校名称或域名\';}" onClick="if(this.value == \'请输入网校名称或域名\'){this.value = \'\';}else {this.select();}" /><button class="cbtn_my" type="button" id="classroom_search">搜索</button><div class="tabcon tablist"  style="margin-top:5px" ><table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr class="tabtit"><td width="10%">选择</td><td width="15%">域名</td><td width="50%">网校名称</td><td width="20%">创建时间</td></tr></tbody><tbody id=\'moduletbody\'></tbody></table></div><div id="hidden_classroom_list" style="display:none"></div></div>';
   dialog_choose_crid = window.art.dialog({
        title: '网校筛选',
        content: content_dialog,
        width: '600px',
        lock:true,
        opacity : 0.2
       //   close:function(){window.location.reload();}
    });
    dialog_choose_crid.button([
        {name: '关闭'}
    ]);
    $("#classroom_search").click(function() {
        goPage(1);  
    });
    event.preventDefault();
    goPage(1);
    $("#classroom_keyword").blur();
});

// Ajax get classroom list
/*$("#classroom_search").click(function() {
    
    goPage(1);  
});*/
function checkCrItem(crid, crname)
{
    $('#crname').val(crname);
    $('#crid').val(crid);
    $('#crid_'+crid).attr('checked',true);//选中单选框
    //$('#dialog').dialog( "close" );
    dialog_choose_crid.close();
}
function goPage(page)
{
    var classroom_keyword = $("#classroom_keyword").val();
    if (classroom_keyword == '请输入网校名称或域名') classroom_keyword = '';
    $.post("/data/getlist.html", {page: page, keyword: classroom_keyword},
        function(data){
            $("#moduletbody").html(data);
        }, "html");
}
function clearSchool(){
  $("#crname").val("");
   $('#crid').val("");
}
</script>
</body>
</html>   