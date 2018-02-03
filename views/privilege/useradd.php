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
    <td><input type="text" name="username" class="inp a40 susername" onblur="checkusername()" /><em id="username_msg" class="emails">6~16个字符，包括字母、数字、下划线，字母开头</em></td>
    </tr>
  <tr>
    <td class="txtlft"><span style="color:red;"> *&nbsp;</span>密码：</td>
    <td><input type="password" name="password" class="inp a40 spassword" value="123456" onblur="checkpassword()" /><em id="password_msg" class="emails">初始默认密码：123456</em></td>    
  </tr>
   <tr>
    <td class="txtlft"><span style="color:red;"> *&nbsp;</span>姓名：</td>
    <td><input type="text" name="realname" class="inp a40 srealname" onblur="checkrealname()" /><em id="realname_msg"></em></td>
  </tr>   
  <tr>
    <td class="txtlft">性别：</td>
    <td><input type="radio" name="sex" value="0"  checked="checked"/> 男 
    	<input type="radio" name="sex" value="1" /> 女 
    </td>    
  </tr>
  <tr>
    <td class="txtlft">选择角色：</td>
    <td>
      <ul class="ctlist clearfix">
      <?php foreach($rolearray as $key => $value) {?>
      	<li><label><input type="checkbox" name="roleid[]" value="<?=$key?>" /> <?=$value?></label></li>
      <?php }?>
      </ul>
    </td>  
  </tr>
  <tr>
    <td class="txtlft">手机：</td>
    <td><input type="text" name="mobile" value="" class="inp a40"   /> </td>    
  </tr>
  <tr>
    <td class="txtlft">地址：</td>
    <td><input type="text" name="address" value="" class="inp a40"/></td>    
  </tr>
  <tr>
    <td class="txtlft">邮箱：</td>
    <td><input type="text" name="email" value="" class="inp a40" /></td>    
  </tr>
  <tr>
    <td class="txtlft">备注：</td>
    <td><textarea name="remark" class="inp a60" style="height:76px;"></textarea></td>    
  </tr>  
 </table>
</div>
 <div style="text-align:center;margin-top:50px"><input type="button"  value="添加" class="combtn cbtn_4 formsbt" /></div>
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
			url:"<?=geturl('privilege/checkIsUserExist')?>",
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
	if(spassword==''){
		$("#password_msg").html("密码不能为空");
		$("#password_msg").attr('class','emacuo');
		check = false;
	}
	else if(spassword.length>12 || spassword.length<6)
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
		checkusername();
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