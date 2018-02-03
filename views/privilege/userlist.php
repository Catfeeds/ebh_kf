<?php $this->display('head');?>
<body>
<div class="titr">
<a title="添加客服" class="Jadd" href="/privilege/useradd.html"><em class="addbtn">添加客服</em></a>
</div>
<?php show_dialog(".Jadd","Jadd","730","600",true,false); ?>
<?php show_dialog(".Jedit","Jedit","730","600",true,false); ?>
<form action="/privilege/userlist.html" method="get" id="form">
	<div class="tabconss">
		<span class="wid85">检索：</span>
		<input title = "账号或姓名" name="q" type="text"   class="inp wid140" value="<?=empty($q) ? '请输入账号或姓名' : $q?>" onBlur="if(this.value == ''){this.value = '请输入账号或姓名';}" onClick="if(this.value == '请输入账号或姓名'){this.value = '';}else {this.select();}" />
		<span class="wid85">所属角色：</span>
		<select name="roleid">
			<option value="0">全部</option>
		<?php foreach ($role_array as $key => $value) {?>
			<option value="<?=$key?>"<?php if($roleid == $key) echo ' selected="selected"';?>><?=$value?></option>	        
		<?php }?>
		</select>
		<input type="submit" value="查询" class="comcheck"/>
	</div>
</form>
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="tabtit">
	 <td>序号</td>
	 <td>登录账号</td>
	 <td>姓名</td>
	 <td>登录次数</td>
	 <td>最后登录IP</td>
	 <td>最后登录时间</td>
	 <td>所属角色</td>
	 <td>操作</td>
 </tr>
 <?php foreach($userlist as $key=>$user){?>
   <tr class="<?=($key%2==0)?"tabbg":""?>">
	 <td width="5%"><?=$numberstart+$key+1?></td>
	 <td><?=$user['username']?></td>     
	 <td><?=$user['realname']?></td>
	 <td><?=$user['logincount']?></td>
	 <td><?=$user['lastloginip']?></td>
	 <td><?=empty($user['lastlogintime']) ? '' : date('Y-m-d H:i:s', $user['lastlogintime'])?></td>
	 <td style="word-break:break-all;word-wrap:break-word;"><?=$user['role']?></td>
	 <td class="tablink"> 
     <?php if($user['uid'] >1) {
		if($user['status'] == 1)  {
		?>
		<a href="/privilege/userlock.html?uid=<?=$user['uid']?>" title="客服锁定"  class="Jlock">锁定</a>
        <?php
		}
		else {
		?>
        <a href="/privilege/userunlock.html?uid=<?=$user['uid']?>" title="客服解锁"  class="Junlock">解锁</a>
        <?php
		}
		?>
        <a href="/privilege/useredit.html?uid=<?=$user['uid']?>" title="客服编辑"  class="Jedit">编辑</a>
        <a href="/privilege/userdelete.html?uid=<?=$user['uid']?>" title="客服删除"  class="Jdel">删除</a>
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
		if(!confirm("确定要删除该客服吗?")){ return false;}		
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
	//ajax锁定
	$(".Jlock").click(function(){
		if(!confirm("确定要锁定该客服吗?")){ return false;}		
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
	//ajax解锁
	$(".Junlock").click(function(){
		if(!confirm("确定要解锁该客服吗?")){ return false;}		
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