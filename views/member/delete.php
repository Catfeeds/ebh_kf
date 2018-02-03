<?php $this->display('head');?>
<body>

<form action="/member/delete.html" method="get" id="form">
<div class="tabconss">
	<span class="wid85">检索：</span>
	<input id="qtel" name="qtel" type="text" class="inp wid140" value="<?=empty($qtel) ? '' : $qtel?>" placeholder="请输入手机号" />
	<input id="q" name="q" type="text" class="inp wid140" value="<?=($q == '') ? '' : $q?>" placeholder="请输入用户名"/>
	<input type="submit" value="查询" class="comcheck" onClick="($('#qtel').val()!='')?checkmobile():submit();return false;" /><label>（<span id="mobileonly">查询手机号用户，11位数字,或者查询用户账号、姓名、昵称。</span>）</label>
</div>
</form>
<div class="ctip cmt"><b>查询描述： <?php if (!empty($qtel)) {echo $qtel.'(精准查询)'; }else{ echo '(模糊查询)';}?></div>
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr class="tabtit">
		<td>序号</td>
		<td>登录账号</td>
		<td>姓名</td>
		<td>注册时间</td>
		<td>最后登录IP</td>
		<td>最后登录时间</td>
		<td>登录次数</td>
		<td>操作</td>
	</tr>
<?php if (!empty($showtip)) 
		echo '<tr><td colspan="8" class="tablink" style="text-align:center">'.$showtip.'</td>'; 
	else foreach($memberlist as $key=>$member){?>
	<tr id="useritem_<?=$member['uid']?>" onClick="showDetail('<?=$member['uid']?>')" class="<?=($key%2==0)?"tabbg":""?>">
		<td style="cursor:pointer" width="5%"><?=$numberstart+$key+1?></td>
		<td style="cursor:pointer"><?=$member['username']?></td>     
		<td style="cursor:pointer"><?=$member['realname']?></td>
		<td style="cursor:pointer"><?=date('Y-m-d H:i:s', $member['dateline'])?></td>
		<td style="cursor:pointer"><?=$member['lastloginip']?></td>
		<td style="cursor:pointer"><?=empty($member['lastlogintime']) ? '' : date('Y-m-d H:i:s', $member['lastlogintime'])?></td>
		<td style="cursor:pointer"><?=$member['logincount']?></td>
		<td class="tablink">
			<?php if(!empty($member['status'])){?>
			<a href="javascript:void(0);" onclick="deletem(<?=$member['uid']?>,'delete')" title="删除用户"  class="Jedit">删除</a>
			<?php }else{?>
				<span title="删除用户" style="color: grey">已删除</span>
			<?php }?>
			<?php if(!empty($member['is_mobile'])){?>
			<a href="javascript:void(0);" onclick="deletem(<?=$member['uid']?>,'mobile')" title="解绑手机"  class="Jedit">解绑手机</a>
			<?php }else{?>
				<span title="解绑手机" style="color: grey">解绑手机</span>
			<?php }?>
			<?php if(!empty($member['is_email'])){?>	
			<a href="javascript:void(0);" onclick="deletem(<?=$member['uid']?>,'email')" title="解绑邮箱"  class="Jedit">解绑邮箱</a>
			<?php }else{?>
				<span title="解绑邮箱" style="color: grey">解绑邮箱</span>
			<?php }?>
			<?php if(!empty($member['is_wx'])){?>		
			<a href="javascript:void(0);" onclick="deletem(<?=$member['uid']?>,'wx')" title="解绑微信" class="Jedit">解绑微信</a>
			<?php }else{?>
				<span title="解绑微信" style="color: grey">解绑微信</span>
			<?php }?>
			<?php if(!empty($member['is_qq'])){?>		
				<a href="javascript:void(0);" onclick="deletem(<?=$member['uid']?>,'qq')" title="解绑QQ"  class="Jedit">解绑QQ</a>
			<?php }else{?>
				<span title="解绑QQ" style="color: grey">解绑QQ</span>
			<?php }?>
		</td>
	</tr> 
<?php }?>
	</table>
</div>
  
<div class="page"><?=$pagestr?></div>
  
<div id="member_detail">




</div>
<style>
.fontred{color:red}
</style>
<script type="text/javascript">
var current_uid='';
function showDetail(uid)
{
	if (current_uid != '')
	{
		$("#useritem_"+current_uid).removeClass("current_user");
		$("#useritem_"+current_uid).mouseover(function(){
			$(this).find('th,td').css('background-color', '#EAEAEA');
		});
	}
	current_uid = uid;
	$("#useritem_"+uid).addClass("current_user");
	$("#useritem_"+uid+" td").css("background-color", "#0CF");
	$("#useritem_"+uid).mouseover(function(){
		$(this).find('th,td').css('background-color', '#0CF');
	});

	$.post("/member/detail.html", {uid: uid}, function(data){
		$("#member_detail").html(data);
	},"html");
}
$(function(){
	current_uid = '<?=$firstuid?>';
	if (current_uid != '') showDetail(current_uid);
});
function checkmobile(){
	var patrn=/^1[3-8]{1}\d{9}$/;
	var mobile = $.trim($('#qtel').val());
	$('#mobileonly,#qtel').removeClass('fontred');
	if (!patrn.exec(mobile)){
		playnotice();
		return false;
	}
	$('#form').submit();
}
function playnotice(){
	var times = 0;
	var itv = setInterval(function(){
		if(times<7){
			$('#mobileonly,#qtel').toggleClass('fontred');
			times++;
		}else{
			clearInterval(itv);
		}
	},200);
	$('#qtel').focus();
}
$('#qtel').keydown(function(){
	$(this).removeClass('fontred');
}).click(function(){
	$(this).removeClass('fontred');
})
function deletem(uid,unbindtype){
	var r = (unbindtype=='delete')?confirm('确认要删除该用户吗？'):confirm('确认要解绑该用户吗？');
	if(r){
		$.ajax({
			url:'/member/dounbind.html',
			type:'post',
			data:{'uid':uid,'unbindtype':unbindtype},
			datatype:'json',
			success:function(data){
				data = $.parseJSON(data);
				alert(data.msg);
				if(data.status == 1)
					location.reload();
			}
		});
	}
	
}
</script>
</body>
</html>