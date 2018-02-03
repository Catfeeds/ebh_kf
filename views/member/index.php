<?php $this->display('head');?>
<body>
<div class="titr">
<a title="添加用户" class="Jadd" href="/member/add.html"><em class="addbtn">添加用户</em></a>
</div>
<?php show_dialog(".Jadd","Jadd","730","600",true,false); ?>
<?php show_dialog(".Jedit","Jedit","730","600",true,false); ?>
<script>
function serviceopen(){
	var width = '900';
	var height = '600';
	var top = true;
	var dialogid  = 'Jserviceopen';
	
	var href = $(".Jserviceopen").attr('rel');
	var title = $(".Jserviceopen").attr('title');
	
	var width = width ? width : $(document.body).width()-60;
	var height = height ? height : $(window).height()-75;
	var html = '<iframe scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" width="'+width+'" height="'+height+'" src="'+href+'"></iframe>';
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
				showDetail(current_uid);
			}
	});
	
	return false;
}
</script>
<form action="/member/index.html" method="get" id="form">
<div class="tabconss">
	<span class="wid85">检索：</span>
	<input name="q" type="text" class="inp wid140" value="<?=$q?>" />
    <input type="hidden" name="aq" id="aq" value="1" />
	<input type="submit" value="精准查询" class="comcheck" onClick="$('#aq').val(1)" /><input type="submit" style="margin-right:10px;" value="模糊查询" class="comcheck" onClick="$('#aq').val(0)" /><label>
关键字（精准查询仅包括:登录名 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;模糊查询包括:登录名、真实姓名、昵称。）</label>
</div>
</form>
<div class="ctip cmt"><b>查询描述：</b><?=$q?> <?php if ($aq == 1 && $q) echo '(精准查询)'; elseif ($q) echo '(模糊查询)';?></div>
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
 <?php if (!empty($showtip)) echo '<tr><td colspan="8" class="tablink" style="text-align:center">'.$showtip.'</td>'; else foreach($memberlist as $key=>$member){?>
   <tr id="useritem_<?=$member['uid']?>" onClick="showDetail('<?=$member['uid']?>')" class="<?=($key%2==0)?"tabbg":""?>">
	 <td style="cursor:pointer" width="5%"><?=$numberstart+$key+1?></td>
	 <td style="cursor:pointer"><?=$member['username']?></td>     
	 <td style="cursor:pointer"><?=$member['realname']?></td>
	 <td style="cursor:pointer"><?=date('Y-m-d H:i:s', $member['dateline'])?></td>
	 <td style="cursor:pointer"><?=$member['lastloginip']?></td>
	 <td style="cursor:pointer"><?=empty($member['lastlogintime']) ? '' : date('Y-m-d H:i:s', $member['lastlogintime'])?></td>
	 <td style="cursor:pointer"><?=$member['logincount']?></td>
	 <td class="tablink">
        <a href="/member/edit.html?uid=<?=$member['uid']?>" title="用户编辑"  class="Jedit">编辑</a>
	 </td>
 </tr> 
  <?php }?>
  </table>
</div>
  
<div class="page"><?=$pagestr?></div>
  
<div id="member_detail">




</div>
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
</script>
</body>
</html>