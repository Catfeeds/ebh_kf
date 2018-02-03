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
<input type="hidden" name="dopost" value="add" />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="txtlft"><span style="color:red;"> *&nbsp;</span>用户名：</td>
    <td><input type="text" name="username" id="username" class="inp a40 susername"  onblur="checkusername()" /><em id="username_msg" class="emails">6~16个字符，包括字母、数字、下划线，字母开头</em></td>
    </tr>
  <tr>
    <td class="txtlft"><span style="color:red;"> *&nbsp;</span>密码：</td>
    <td><input type="password" name="password" class="inp a40 spassword" value="" onBlur="checkpassword()" /><em id="password_msg" class="emails">请输入6-12位密码</em></td>
  </tr>
  <tr>
    <td class="txtlft"><span style="color:red;"> *&nbsp;</span>确认密码：</td>
    <td><input type="password" name="confirm" class="inp a40 sconfirm" value="" onBlur="checkconfirm()" /><em id="confirm_msg" class="emails">请输入6-12位密码</em></td></td>    
  </tr>
   <tr>
    <td class="txtlft">真实姓名：</td>
    <td><input type="text" name="realname" class="inp a40 srealname" /></td>
  </tr>
  <tr>
    <td  class="txtlft">用户昵称：</td>
    <td><input type="text" class="inp a40" name="nickname"   maxlength="25" /></td>
  </tr>
  <tr>
    <td class="txtlft">性　　别：</td>
    <td>
        <input type="radio" name="sex" checked="checked" value="0">男
        <input type="radio" name="sex" value="1" >女
    </td>    
  </tr>
  <tr>
    <td class="txtlft">出生日期：</td>
    <td><input type="text"  id="birthdate" name="birthdate" class="inp a40" onClick="WdatePicker()" value="<?php $memberdetail['birthdate']=empty($memberdetail['birthdate'])?time():$memberdetail['birthdate']; echo date("Y-m-d",$memberdetail['birthdate']) ?>">
    </td>
  </tr>
<tr><td class="txtlft">电话号码：</td><td><input type="text" name="phone" class="inp a40" ></td></tr>
<tr><td class="txtlft">手机号码：</td><td><input type="text" name="mobile" class="inp a40" maxlength="11"></td></tr>
<tr><td class="txtlft">电子邮箱：</td><td><input type="text" name="email" class="inp a40" ></td></tr>
<tr><td class="txtlft">ＱＱ号码：</td><td><input type="text" name="qq" class="inp a40" ></td></tr>
<tr><td class="txtlft">M  S  N：</td><td><input type="text" name="msn" class="inp a40" ></td></tr>
<tr><td class="txtlft">出生地：</td><td><input type="text" name="native" class="inp a40" ></td></tr>
<tr>
    <td class="txtlft">居住城市：</td>
    <td><?php $this->widget('cities_widget')?></td>
</tr>
<tr>
    <td class="txtlft">详细地址：</td>
    <td><input type="text" name="address" class="inp a40" ></td>
</tr>
<tr>
    <td class="txtlft">自我介绍：</td>
    <td><textarea cols="55" rows="3" name="profile" class="inp a60" style="height:76px;"></textarea></td>
</tr> 
 </table>
</div>
<div style="text-align:center;margin-top:50px">
<input type="button"  value="添加" class="combtn cbtn_4 formsbt" />
<input type="reset"  name="valuereset" value="重置" class="combtn cbtn_4">
</div>
</form>
<script type="text/javascript">
var check = true;
function checkusername()
{
	var susername = $.trim($(".susername").val());
	if(susername==''){
		$("#username_msg").html("用户名不能为空");
		$("#username_msg").attr('class','emacuo');
		check = false;
	}
	else if((!susername.match(/^[a-zA-Z][a-z0-9A-Z_]{5,15}$/)))
	{
		$("#username_msg").html("6~16个字符，包括字母、数字、下划线，字母开头");
		$("#username_msg").attr('class','emails');
		check = false;
	}
	else
	{
		$.ajax({
			type:"post",
			url:"<?=geturl('member/checkIsUserExist')?>",
			dataType:'json',
			data:{'username':susername},
			async:false,
			success:function(data){
				if(data == 0){
					$("#username_msg").html("用户名已存在");
					$("#username_msg").attr('class','emacuo');
					check = false;
				}
				else
				{
					$("#username_msg").html("");
					$("#username_msg").attr('class','emadui');
				}
			}
		});
	}
}
function checkpassword()
{
	var spassword = $.trim($(".spassword").val());
	var sconfirm = $.trim($(".sconfirm").val());
	if(spassword==''){
		$("#password_msg").html("密码不能为空");
		$("#password_msg").attr('class','emacuo');
		check = false;
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

	if (spassword == sconfirm && sconfirm != ''){
		$("#confirm_msg").html("");
		$("#confirm_msg").attr('class','emadui');
	}
	else if (spassword != sconfirm && sconfirm != ''){
		$("#confirm_msg").html("两次密码输入不一致");
		$("#confirm_msg").attr('class','emails');
		check = false;
	}
}
function checkconfirm()
{
	var spassword = $.trim($(".spassword").val());
	var sconfirm = $.trim($(".sconfirm").val());
	if(sconfirm==''){
		$("#confirm_msg").html("确认密码不能为空");
		$("#confirm_msg").attr('class','emacuo');
		check = false;
	}
	else if(sconfirm.length>12 || sconfirm.length<6){
		$("#confirm_msg").html("请输入6-12位密码");
		$("#confirm_msg").attr('class','emails');
		check = false;
	} 
	else if (spassword != sconfirm){
		$("#confirm_msg").html("两次密码输入不一致");
		$("#confirm_msg").attr('class','emails');
		check = false;
	}
	else {
		$("#confirm_msg").html("");
		$("#confirm_msg").attr('class','emadui');
	}
}
$(function(){
	$('.formsbt').click(function(){
		check = true;
		checkusername();
		checkpassword();
		checkconfirm();
		if(check){
			$('#form').submit();
		}
			
	});
});
</script>
</body>
</html>    