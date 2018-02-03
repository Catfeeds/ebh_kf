<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/static/css/login.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<title>e板会客服系统V1.0</title>
</head>
<body>
<div class="crmtop">
</div>
<div class="dlengt">
<div class="lefet">
<img src="/static/images/ebh/xsllogo.jpg" />
</div>
<form method="post" name="login" id="loginform" >
<input type="hidden" name="dologin" value="yes" />
<div class="ristn">
<h2 class="etert"><img src="/static/images/ebh/titdenbtn.jpg" /></h2>
<div class="drwek">
<span class="resizl">用户名</span>
<input  class="txtket" name="admin_username" id="admin_username" type="text"/>
</div>
<div class="drwek">
<span class="resizl">密　码</span>
<input class="txtket" name="admin_password" id="admin_password" type="password" />
</div>
<div class="drwek">
<span class="resizl">验证码</span>
<input style="width:64px;"  class="txtket" name="seccode" type="text" id="checkCode" />
<a onclick="updatesecode()" class="dzheng"><img style="cursor:pointer;" title="点击刷新"  border="0" id="img_seccode" src="<?php echo geturl('verifycode/getCode')?>"/></a>
</div>
<div class="drwek">
<span class="resizl"></span>
<a href="javascript:void(0)" class="wtrbtn loginsbt">登 录</a>
<a href="javascript:void(0)" class="wtrbtn re">重 置</a>
</div>
</div>
</form>
</div>
<script type="text/javascript">
//判断是否是顶层，不是则将当前页设置为顶层
var siteUrl = "";
if(top.location != self.location){
	top.location=self.location;        
}
$(function(){
	$('.loginsbt').click(function(){
		checkform();
		});
});


//form提交验证
function checkform(){
	if($("#admin_username").val()=='' || $("#admin_username").val()==null)
	{
		alert('账号不能为空！');
		$("#admin_username").focus();
		return false;
	}
	if($("#admin_password").val()=='' || $("#admin_password").val()==null)
	{
		alert('登录密码不能为空！');
		$("#admin_password").focus();
		return false;
	}
	if($("#checkCode").val()=='' || $("#checkCode").val()==null)
	{
		alert('验证码不能为空！');
		$("#checkCode").focus();
		return false;
	}
	$.ajax({
		url:'/default/check.html',
		data:$("#loginform").serialize(),
		type:"POST",
		dataType:"json",
		success:function(json){
			if(json['code'] == 1){
				location.href = json['returnurl'];
			}else{
				updatesecode();
				alert(json['message']);
			}
			return false;
		}
	});
	return false;
};

//鼠标事件监听
 document.onkeydown = function(evt){
	var evt = window.event?window.event:evt;
	if(evt.keyCode==13){
		checkform();
	}
}
 
//更新验证码
function updatesecode(){
	$("#img_seccode").attr('src',"/verifycode/getCode.html"+'?'+Math.random());
	//return ;
}
</script>
</body>
</html>