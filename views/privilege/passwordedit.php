<?php $this->display('head');?>
<body>

<form action="/privilege/passwordedit.html" method="post" id="form" name="form">
<div class="tabcones">
<input type="hidden" name="dopost" value="edit" />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="txtlft">原始密码：</td>
    <td><input type="password" name="old_password" id="old_password" class="inp" value="" /><span style="color:red;">*</span></td>
    </tr>
  <tr>
    <td class="txtlft">新密码：</td>
    <td><input type="password" name="new_password" id="new_password"  class="inp" value="" /><span style="color:red;"> *</span></td>    
  </tr>
   <tr>
    <td class="txtlft">确认新密码：</td>
    <td><input type="password" name="new_password_repeat" id="new_password_repeat"  class="inp" value=""  /><span style="color:red;"> *</span></td>
  </tr>
</table>
</div>
 <div style="text-align:center;margin-top:50px"><input type="button"  value="修改" class="comcheck" id ="formsbt"  /></div>
</form>
<script type="text/javascript">
$(function(){
	$('#formsbt').click(function(){
		var check = true;
		if($("#old_password").val() == '') {alert('请输入原始密码');$("#old_password").focus();check = false;return false;}
		if($("#new_password").val() == '') {alert('请输入新密码');$("#new_password").focus();check = false;return false;}
		if($("#new_password_repeat").val() == '') {alert('请确认新密码');$("#new_password_repeat").focus();check = false;return false;}
		
		if($("#new_password").val() != $("#new_password_repeat").val() ) {alert('两次密码输入不一致');$("#new_password").focus();check = false;return false;}

		if(check){
			$('#form').submit();
		}
			
	});
})
</script>
</body>
</html>    