<?php $this->display('head');?>
<body>
<div class="titr">
<a title="添加角色" class="Jadd" href="/privilege/roleadd.html"><em class="addbtn">添加角色</em></a>
</div>
<?php show_dialog(".Jadd","Jadd","730","600",true,false); ?>
<?php show_dialog(".Jedit","Jedit","730","600",true,false); ?>
<form action="/privilege/rolelist.html" method="get" id="form">
<div class="tabconss">
	<span class="wid85">角色名称：</span>
	<input title = "角色名称" name="q" type="text"   class="inp wid140" value="<?=$q?>"/>
    <input type="submit" value="查询" class="comcheck"/>
</div>
</form>
<div class="ctip cmt"><b>查询描述：</b>角色名称: <?=$q?></div>
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="tabtit">
	 <td>序号</td>
	 <td class="leftxt">角色名称</td>
	 <td class="leftxt">功能模块</td>
	 <td class="leftxt">授权范围</td>
	 <td>操作</td>
 </tr>
 <?php foreach($rolelist as $key=>$value){?>
   <tr class="<?=($key%2==0)?"tabbg":""?>">
	 <td style="width:50px"><?=$numberstart+$key+1?></td>
	 <td class="leftxt" class="leftxt" style="width:100px"><?=$value['rolename']?></td>
	 <td class="leftxt" style="width:300px;word-break:break-all;word-wrap:break-word;"><?=$value['access']?></td>
	 <td class="leftxt" style="width:400px;word-break:break-all;word-wrap:break-word;"><?=$value['classroom']?></td>
	 <td style="width:100px" class="tablink">
     <?php if ($value['roleid'] > 1) {?>
     	<a title="编辑角色" class="Jedit" href="/privilege/roleedit.html?roleid=<?=$value['roleid']?>">编辑</a>
        <a class="Jdel" href="/privilege/roledelete.html?roleid=<?=$value['roleid']?>" >删除</a>
     <?php }?>
	 </td>
 </tr> 
  <?php }?>
  </table>
  </div>
  
  <div class="page"><?=$pagestr?></div>

<script type="text/javascript">
$(document).ready(function(){
	//ajax删除
	$(".Jdel").click(function(){
		if(!confirm("确定要删除该角色吗?")){ return false;}		
		var url = $(this).attr("href");
		$.get(url,function(data){
			if(data.status){
				$.showmessage({
					img : 'success',
					message:data.msg,
					title:'消息提醒',
					callback: function(){location.reload();}
				});				
			}else{
				$.showmessage({
					img : 'error',
					message:data.msg,
					title:'消息提醒'
				});	
			}
		},'json');
	return false;
	});
});
</script>
</body>
</html>