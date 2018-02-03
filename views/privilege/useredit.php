<?php $this->display('head');?>
<body>
<style type="text/css">
.emadui {
    background: url("/static/images/ebh/duiico0221.jpg") no-repeat scroll left center;
    color: #888;
    font-style: normal;
    margin-left: 8px;
    padding-left: 20px;
 }
.emails {
    background: url("/static/images/ebh/ganico0221.jpg") no-repeat scroll left center;
    color: #888;
    font-style: normal;
    margin-left: 8px;
    padding-left: 20px;
}
.emacuo {
    background: url("/static/images/ebh/cuoico0221.jpg") no-repeat scroll left center;
    color: #888;
    font-style: normal;
    margin-left: 8px;
    padding-left: 20px;
}
</style>
<form action="" method="post" id="form" name="form">
<div class="tabcones">
<input type="hidden" name="dopost" value="edit" />
<input type="hidden" name="uid" value="<?=$user['uid']?>" />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
  <td class="txtlft">用户名：</td>
  <td><?=$user['username']?></td>
  </tr>
<tr>
  <td class="txtlft">密码：</td>
  <td><input type="password" name="password" class="inp a40 spassword" value="" onBlur="checkpassword()" /><em id="password_msg"></em></td>    
</tr>
 <tr>
  <td class="txtlft"><span style="color:red;"> *&nbsp;</span>姓名：</td>
  <td><input type="text" name="realname" class="inp a40 srealname" value="<?=$user['realname']?>" onBlur="checkrealname()" /><em id="realname_msg"></em></td>
</tr>   
<tr>
  <td class="txtlft">性别：</td>
  <td><input type="radio" name="sex" value="0" <?php if($user['sex'] == 0) echo 'checked="checked"';?> /> 男 
  	<input type="radio" name="sex" value="1" <?php if($user['sex'] == 1) echo 'checked="checked"';?> /> 女 
  </td>    
</tr>
<tr>
  <td class="txtlft">选择角色：</td>
  <td>
    <ul class="ctlist clearfix">
    <?php foreach($rolearray as $key => $value) {?>
    	<li><label><input type="checkbox" name="roleid[]" value="<?=$key?>" <?php if(strpos(',' . $user['roleid'].',', ',' . $key.',') !== false) echo 'checked="checked" '; ?> /> <?=$value?></label></li>
    <?php }?>
    </ul>
  </td>  
</tr>
<tr>
  <td class="txtlft">手机：</td>
  <td><input type="text" name="mobile" class="inp a40" value="<?=$user['mobile']?>" /> </td>    
</tr>
<tr>
  <td class="txtlft">地址：</td>
  <td><input type="text" name="address" class="inp a40" value="<?=$user['address']?>" /></td>    
</tr>
<tr>
  <td class="txtlft">邮箱：</td>
  <td><input type="text" name="email" class="inp a40" value="<?=$user['email']?>" /></td>    
</tr>
<tr>
  <td class="txtlft">备注：</td>
  <td><textarea name="remark" class="inp a60" style="height:76px;"><?=$user['remark']?></textarea></td>    
</tr>  
</table>
</div>
<div style="text-align:center;margin-top:50px"><input type="button"  value="修改" class="combtn cbtn_4 formsbt" /></div>
</form>
<script type="text/javascript">
var check = true;
function checkpassword()
{
	var spassword = $.trim($(".spassword").val());
	if(spassword==''){
		$("#password_msg").html("");
    $("#password_msg").attr('class','');
	}
	else if(spassword!='' && (spassword.length>12 || spassword.length<6))
	{
		$("#password_msg").html("请输入6-12个字符");
		$("#password_msg").attr('class','emails');
		check = false;
	} else {
		$("#password_msg").html("");
		$("#password_msg").attr('class','emadui');
	}
}
function checkrealname()
{
	var srealname = $.trim($(".srealname").val());
	if(srealname==''){
		$("#realname_msg").html("姓名不能为空");
		$("#realname_msg").attr('class','emacuo');
		check = false;
	} else {
		$("#realname_msg").html("");
		$("#realname_msg").attr('class','emadui');
	}
}
$(function(){
	$('.formsbt').click(function(){
		check = true;
		checkpassword();
		checkrealname();
		if(check){
			$('#form').submit();
		}
			
	});
});
</script>
</body>
</html>    