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
<input type="hidden" name="uid" value="<?=$memberdetail['uid']?>"/>
<input type="hidden" name="token" value="<?=$token?>"/>
<input type="hidden" name="formhash" value="<?=$formhash?>"/>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr>
    <td class="txtlft">修改密码：</td>
    <td><input type="password" name="password" maxlength="50" class="inp a40 spassword" value="" onBlur="checkpassword()" /><em id="password_msg"></em></td>
  </tr>
   <tr>
    <td class="txtlft">真实姓名：</td>
    <td><input type="text" name="realname" maxlength="50" class="inp a40" value="<?=$memberdetail['realname']?>" /></td>
  </tr>
  <tr>
    <td  class="txtlft">用户昵称：</td>
    <td><input type="text" class="inp a40" name="nickname"   maxlength="25" value="<?=$memberdetail['nickname']?>" /></td>
  </tr>
  <tr>
    <td class="txtlft">性　　别：</td>
    <td>
        <input type="radio" name="sex"  <?php if($memberdetail['sex']==0){echo 'checked=checked';}?> value="0">男
        <input type="radio" name="sex"  <?php if($memberdetail['sex']==1){echo 'checked=checked';}?> value="1" >女
    </td>    
  </tr>
  <tr>
    <td class="txtlft">出生日期：</td>
    <td><input type="text"  id="birthdate" name="birthdate" class="inp a40" onClick="WdatePicker()" value="<?php echo $memberdetail['birthdate']=empty($memberdetail['birthdate'])? '': date("Y-m-d",$memberdetail['birthdate']);?>">
    </td>
  </tr>
<tr><td class="txtlft">电话号码：</td><td><input type="text" name="phone" class="inp a40" value="<?=$memberdetail['phone']?>" /></td></tr>
<tr><td class="txtlft">手机号码：</td><td><input type="text" name="mobile" class="inp a40" maxlength="11" value="<?=$memberdetail['mobile']?>" /></td></tr>
<tr><td class="txtlft">电子邮箱：</td><td><input type="text" name="email" class="inp a40" value="<?=$memberdetail['email']?>" /></td></tr>
<tr><td class="txtlft">ＱＱ号码：</td><td><input type="text" name="qq" class="inp a40" value="<?=$memberdetail['qq']?>" /></td></tr>
<tr><td class="txtlft">M  S  N：</td><td><input type="text" name="msn" class="inp a40" value="<?=$memberdetail['msn']?>" /></td></tr>
<tr><td class="txtlft">出生地：</td><td><input type="text" name="native" class="inp a40" value="<?=$memberdetail['native']?>" /></td></tr>
<tr>
    <td class="txtlft">居住城市：</td>
    <td><?php $this->widget('cities_widget',array('citycode'=>$memberdetail['citycode']));?></td>
</tr>
<tr>
    <td class="txtlft">详细地址：</td>
    <td><input type="text" name="address" class="inp a40" value="<?=$memberdetail['address']?>" /></td>
</tr>
<tr>
    <td class="txtlft">自我介绍：</td>
    <td><textarea cols="55" rows="3" name="profile" class="inp a60" style="height:76px;"><?=$memberdetail['profile']?></textarea></td>
</tr> 
 </table>
</div>
<div style="text-align:center;margin-top:50px">
<input type="button" name="valuesubmit" value="提交保存" class="combtn cbtn_4 formsbt">
<input type="reset"  name="valuereset" value="重置" class="combtn cbtn_4">
</div>
</form>
<script type="text/javascript">
var check = true;
function checkpassword()
{
	var spassword = $.trim($(".spassword").val());
	var sconfirm = $.trim($(".sconfirm").val());
	if(spassword==''){
		$("#password_msg").html("");
    	$("#password_msg").attr('class','');
	}
	else if(spassword.length>12 || spassword.length<6){
		$("#password_msg").html("请输入6-12位密码");
		$("#password_msg").attr('class','emails');
		check = false;
	}
	else {
		$("#password_msg").html("");
		$("#password_msg").attr('class','emadui');
	}
}
$(function(){
	$('.formsbt').click(function(){
		check = true;
		checkpassword();
		if(check){
			$('#form').submit();
		}
			
	});
});
</script>
</body>
</html>    