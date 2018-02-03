<?php $this->display('head');?>
<body>
<form action="" method="post" id="form" name="form">
<div class="tabcones">
<input type="hidden" name="dopost" value="add" />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="txtlft">角色名称：</td>
    <td><input type="text" name="rolename" class="inp a40" id="rolename" /><span style="color:red;"> *</span></td>
  </tr>
  <tr>
    <td class="txtlft">选择功能：</td>
    <td>
      <ul class="ctlist clearfix">
      <?php foreach($modulelist as $key => $value) {?>
      	<li><label><input type="checkbox" name="access[]" value="<?=$key?>" /> <?=$value?></label></li>
      <?php }?>
      </ul>
    </td>
  </tr>   
  <tr>
    <td class="txtlft">授权范围：</td>
    <td>
    所有学校:<input type="radio" name="rangetype" value="0" checked="checked" /><br />
    部分学校:<input type="radio" name="rangetype" value="1" /> <button type="button" id="dialog-link" class="ctbtn">添加学校</button>
      <ul id="classroom_range" class="ctlist clearfix" style="margin-top:3px;">
      
      </ul>
    </td>    
  </tr>
  <tr>
    <td class="txtlft">备注：</td>
    <td><textarea name="remark" class="inp a60" style="height:76px;"></textarea></td>    
  </tr>  
 </table>
</div>
<div style="text-align:center;margin-top:50px"><input type="submit" value="添加" class="combtn cbtn_4 formsbt"   /></div>
</form>

<!-- ui-dialog -->
<div id="dialog" title="选择学校">
	<div style="margin-bottom:6px;">
    	<input type="text" name="classroom_keyword" id="classroom_keyword" class="inp a60" value="请输入网校名称或域名"onblur="if(this.value == ''){this.value = '请输入网校名称或域名';}" onClick="if(this.value == '请输入网校名称或域名'){this.value = '';}else {this.select();}" /> <button type="button" id="classroom_search" class="cbtn_my">搜索</button>
    </div>
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
</div>
<script>
$( "#dialog" ).dialog({
	autoOpen: false,
	width: 700,
	height: 550,
	buttons: [
		{
			text: "确定",
			click: function() {
				$("#classroom_range").append( $("#hidden_classroom_list").html() );
				removeTempList();
				//移除重复的元素
				var temp='';
				$("#classroom_range li").each(function(){
					if(temp.indexOf($(this).attr("id")+',')!=-1)
					{
						$(this).remove();
					}
					else
					{
						temp += $(this).attr("id")+',';
					}					
				});
				$( this ).dialog( "close" );
			}
		},
		{
			text: "取消",
			click: function() {
				removeTempList();
				$( this ).dialog( "close" );
			}
		}
	]
});

// Link to open the dialog
$( "#dialog-link" ).click(function( event ) {
	$("input[name=rangetype]:eq(1)").attr("checked",'checked');
	$( "#dialog" ).dialog( "open" );
	event.preventDefault();
	goPage(1);
	$("#classroom_keyword").blur();
});

// Ajax get classroom list
$("#classroom_search").click(function() {
	removeTempList();
	goPage(1);
});
function removeTempList()
{
	$("#moduletbody").html('');//清除列表
	$("#hidden_classroom_list").html('');//清除已勾选的学校
}
function reversecheck(crid)
{
	$("#crid_"+crid).prop("checked", !$("#crid_"+crid).prop("checked"));
}
function checkCrItem(crid, crname)
{
	if ($("#crid_"+crid).prop("checked"))
	{
		$("#crli_"+crid).remove();
	}
	else
	{
		var content = '<li id="crli_'+crid+'"><label><input type="checkbox" name="classroom[]" value="'+crid+'" checked="checked" /> '+crname+'</label></li>';
		$("#hidden_classroom_list").append(content);
	}
	$("#crid_"+crid).prop("checked", !$("#crid_"+crid).prop("checked"));
}
function goPage(page)
{
	var classroom_keyword = $("#classroom_keyword").val();
	if (classroom_keyword == '请输入网校名称或域名') classroom_keyword = '';
	$.post("/classroom/getlist.html", {page: page, keyword: classroom_keyword},
		function(data){
			$("#moduletbody").html(data);
			//处理已勾选的
			$("#hidden_classroom_list li input:checkbox").each(function(){
				var cccrid = $(this).val();
				$("#crid_"+cccrid).attr("checked", true);
			});
		}, "html");
}

$(function(){
	$('.formsbt').click(function(){
		var check = true;
		var rolename = $.trim($("#rolename").val());
		
		if(rolename==''){alert('请输入角色名称');$('#rolename').focus(); check = false;return false;}
		if(check){
			$('#form').submit();
		}
			
	});
})
</script>
</body>
</html>    