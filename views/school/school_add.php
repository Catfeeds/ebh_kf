<?php
$this->display('school/header');
?>
<body id="main">

 <script>
  $(function() {
  	goPage(1);
    $( "#dialog" ).dialog({
      autoOpen: false,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "explode",
        duration: 1000
      },
    width: 700,
    height: 550,
    });
 
    $( "#dialog-link" ).click(function() {
      $( "#dialog" ).dialog( "open" );
	  $("#classroom_keyword").blur();
    });
    // Ajax get classroom list
	$("#classroom_search").click(function() {
	    
	    goPage(1);  
	});


  });
  	function goPage(page)
	{
	    var classroom_keyword = $("#classroom_keyword").val();
	    if (classroom_keyword == '请输入登录名或姓名') classroom_keyword = '';
	    $.post("/data/getTeacherList.html", {page: page, keyword: classroom_keyword},
	        function(data){
	            $("#moduletbody").html(data);
	        }, "html");
	}
	function checkCrItem(uid, username)
	{
        $('#username').val(username);
        $('#mediaid').val(uid);
    	$('#crid_'+uid).attr('checked',true);//选中单选框
    	$('#dialog').dialog( "close" );
	}
  </script>
<form method="post" action="/school/add.html" onSubmit="return $(this).form('validate')">
<input type="hidden" name="token" value="<?=$token?>" />
<input type="hidden" name="formhash" value="<?=$formhash?>" />
<input type="hidden" name="op" value="add" />
<style type="text/css">
body{line-height:15px;background-color:#FCFDFD;color:#666666;margin-left:50px;margin-right:50px;}
strong{font-size:12px;}
aink{color:#0066CC;}
a:hover{color:#FF6600;}
aisited{color:#003366;}
a:active{color:#9DCC00;}
th{border:solid 1px #D5E3E7;}
.upprogressbox{width:600px}
.upfileinfo{width:600px}
</style>
<div class="tabcon tablist schooltab" >
<table cellspacing="0" cellpadding="0" width="100%" class="maintable"> 
<tr><th style="width:40%">网校名称<em>*</em><p>请输入网校名称，学员可以通过此名称查找到网校。</p></th><td style="width:60%">
<input type="text" maxLength="50" name="crname" class="easyui-validatebox w300" data-options="required:true,validType:'crname',missingMessage:'教室名称不能为空'" /> 
</td></tr>
<tr><th>网校logo</th><td><?php $Upcontrol->upcontrol('cface',1,array(),'pic');?></td></tr>
<tr>
<th>所属教师<em>*</em><p>请选择网校所属的教师。</p>
</th>
<td><div>
<input type="text" readonly value="" id="username" name="username">
<input class="ctbtn" type="button" id="dialog-link" value="选择" />
</div>
<input type="hidden" name="uid" id="mediaid"  value="" />
</td>
</tr>
<tr><th>所属分类<em>*</em><p>请输入教室的行业分类ID。</p></th><td>    
<select name="catid" id="catid"   class="w150">
<option value="0" >教室分类</option>
<?php $this->widget('category_widget',array('where'=>array('position'=>'1'),'tag'=>'catid'));?>
</select></td></tr>

<tr><th>上级网校<p>请选择上级网校。</p></th><td> 
<?php $this->widget('classroom_widget',array('where'=>array('limit'=>'1000'),'tag'=>'upid','selected'=>$crid));?>
</td></tr>

<tr>
	<th>地址<p>请输入地址，请用逗号分割</p></th>
	<td><input type="text" class="w300" maxlength="50" id="craddress" name="craddress" value="" /></td>
</tr>
<tr>
	<th>联系邮箱/主页<p>如果是学校版本填写主页，否则填写邮箱</p></th>
	<td><input type="text" class="w300" maxlength="50" id="cremail" name="cremail" value="" /></td>
</tr>
<tr>
	<th>联系电话</th>
	<td><input type="text" class="w300" maxlength="50" id="crphone" name="crphone" value="" /></td>
</tr>
<tr>
	<th>QQ</th>
	<td><input type="text" class="w300" maxlength="50" id="crqq" name="crqq" value="" /></td>
</tr>
<tr><th>所属城市</th>
<td>
<?php $this->widget('cities_widget');?>
</td></tr><tr><th>平台类型<p>电子网校，同步学堂，培训平台,股票，省市区控制平台，每个类型对应模板选择会不同，其中省市区控制平台只用于控制汇总它所属的子网校信息</p></th><td>
<input type="radio" id="isschoolone" name="isschool" value="0" /><label for="isschoolone">电子教室</label>
<input type="radio" id="isschooltwo" name="isschool" value="1" /><label for="isschooltwo">同步学堂</label>
<input type="radio" id="isschoolthree" name="isschool" value="2" checked /><label for="isschoolthree">云教育网校</label>
<input type="radio" id="isschoolfour" name="isschool" value="3"  /><label for="isschoolfour">学校平台</label>
<input type="radio" id="isschoolfive" name="isschool" value="4"  /><label for="isschoolfive">股票</label>
<input type="radio" id="isschoolsix" name="isschool" value="5"  /><label for="isschoolsix">省市区控制平台</label>
<input type="radio" id="isschoolseven" name="isschool" value="6"  /><label for="isschoolseven">收费学校</label>
<input type="radio" id="isschooleight" name="isschool" value="7"  /><label for="isschooleight">分成收费</label>


</td></td></tr>
<tr>
	<th>学校类型<p>该学校为小学(1)，初中(7)，高中(10)，其他(0)类型学校</p></th>
	<td>
	<input type="radio" id="gradeone" name="grade" value="0" checked /><label for="gradeone">其他</label>
	<input type="radio" id="gradetwo" name="grade" value="1" /><label for="gradetwo">小学</label>
	<input type="radio" id="gradethree" name="grade" value="7" /><label for="gradethree">初中</label>
	<input type="radio" id="gradefour" name="grade" value="10"  /><label for="gradefour">高中</label>
	</td>
</tr>
<tr>
	<th>学校属性<p>默认为0,1表示教学平台,2表示网络学校,3表示企业培训</p></th>
	<td>
	<input type="radio"  name="property" id="property0" value="0" checked /><label for="property0">默认</label>
	<input type="radio"  name="property" id="property1" value="1" /><label for="property1">教学平台</label>
	<input type="radio"  name="property" id="property2" value="2" /><label for="property2">网络学校</label>
	<input type="radio"  name="property" id="property3" value="3"  /><label for="property3">企业培训</label>
	</td>
</tr>
<tr><th>网校域名<em>*</em><p>请输入网校域名。</p></th><td>
<input type="text" class="w300" maxLength="50" name="domain" id="domain" value="" />
.ebh.net</td></tr>
<tr>
	<th>网校模版<em>*</em><p>请选择网校模版。</p></th>
	<td>     
	<select name="template" id="template" class="w150">
	</select>
	</td>
</tr>
<tr>
	<th>标签</th>
	<td><input type="text" class="w300" maxlength="50" name="crlabel" value="" /></td>
</tr>
<tr>
	<th>网校摘要<em>*</em><p>请输入网校的摘要信息。</p></th>
	<td>
		<textarea  class="p98" name="summary"  rows="5" id="summary" style="width:400px"></textarea>
	</td>
</tr>
<tr>
	<th>最大人数<em>*</em><p>请选择此网校最大上课人数。</p></th>
	<td>
		<select id="maxnum" name="maxnum" value="">
		<option value="50" >50</option><option value="100" >100</option><option value="150" >150</option>
	</select>
	</td>
</tr>
<tr>
	<th>网校开始时间<em>*</em></th>
	<td><input type="text" id="begindate" name="begindate" class="w150 easyui-datebox" /></td>
</tr>
<tr>
	<th>有效期限<em>*</em><p>请选择此网校的开通有效期限，以年为单位。</p></th>
	<td>
		<select id="period"  value="" style="width:150px">
			<option value="0">请选择</option>
			<option value="1">1年</option>
			<option value="2">2年</option>
			<option value="3">3年</option>
			<option value="4">4年</option>
			<option value="5">5年</option>
		</select>
	</td>
</tr>
<tr>
	<th>网校结束时间<em>*</em></th>
	<td>
		<input type="text" id="enddate" name="enddate" class="w150 easyui-datebox" />
	</td>
</tr>
<tr>
	<th>开通此电子网校所需金额</th>
	<td><input type="text" class="w150" maxlength="20" name="crprice" id="crprice" value="0.00" />元<em></em></td>
</tr>
<tr>
	<th>网校老师权限<p>电子网校老师非系统模块权限</p></th>
	<td>
		<?php foreach ($tpowerlist as $tv){?>
			<label><input type="checkbox"  name="modulepower[]" value="<?=$tv['catid']?>" /><?=$tv['name']?></label>
		<?php }?>
	</td>
</tr>
<tr>
	<th>网校学生权限<p>电子网校学生非系统模块权限</p></th>
	<td>
		<?php foreach ($stpowerlist as $stv){?>
			<label><input type="checkbox"  name="stumodulepower[]" value="<?=$stv['catid']?>" /><?=$stv['name']?></label>
		<?php }?>
	</td>
</tr>
<tr>
	<th>共享平台分配<p>需要分配哪些平台权限的，就将该平台打钩</p></th>
	<td>
		<?php foreach ($sharelist as $sv){?>
			<label><input type="checkbox"  name="roompermission[]" value="<?=$sv['crid']?>" /><?=$sv['crname']?></label>
		<?php }?>
	</td>
</tr>
<tr>
	<th>分层比例</th>
	<td>
		<span id="ceng_company">
		公司分层
		<input type="text" name="profitratio[company]" value="20" id="profit_company" style="margin-left:84px">
		</span>
		<br style="clear: both;">
		<span id="ceng_agent">
		代理商分层
		<input type="text" name="profitratio[agent]" value="20" id="profit_agent" style="margin-left:70px">
		</span>
		<br style="clear: both;">
		<span id="ceng_teacher">
		内容提供者(教师)分层
		<input type="text" name="profitratio[teacher]" value="60" id="profit_teacher">
		</span>
	</td>
</tr>
<tr>
	<th>公开类型</th><td>
	<input type="radio" id="ispublic" name="ispublic" value="0" />不公开
	<input type="radio" id="ispublic" name="ispublic" value="1" />公开
	<input type="radio" id="ispublic" name="ispublic" value="2" />免费试听
	</td>
</tr>
<tr>
	<th>是否共享平台<p>如果打钩，那么此平台的课件和作业等资源可对其他平台分享</p></th>
	<td><input type="checkbox" id="isshare" name="isshare" value="1" /></td>
</tr>
<tr>
	<th>头部图片
		<p>此处只能上传960px*60px显示在学生和教师 在网校的头部图片。</p>
	</th>
	<td><?php $Upcontrol->upcontrol('banner',1,array(),'pic');?></td>
</tr>
<tr>
	<th>浮动广告
		<p>模板首页浮动广告(告家长书等)</p>
	</th>
	<td><?php $Upcontrol->upcontrol('floatadimg',1,array(),'pic');?></td>
</tr>
<tr>
	<th>浮动广告跳转链接
		<p>模板首页浮动广告跳转链接</p>
	</th>
	<td><input name="floatadurl" style="width:300px" /></td>
</tr>
<tr>
	<th>显示获取用户名链接(one模板)
		<p>one模板是否显示获取用户名链接</p>
	</th>
	<td><input type="checkbox" name="showusername" value="1"/></td>
</tr>
<tr>
	<th>默认密码
		<p>获取用户名显示的默认密码</p>
	</th>
	<td><input name="defaultpass" style="width:300px" /></td>
</tr>
<tr><th>排序</th><td> <input type="text" class="w50" maxlength="50" name="displayorder" id="displayorder" value="" style="width:300px"></td></tr>
</table>
</div>
<div class="buttons">
<input type="submit" name="valuesubmit" value="提交保存" class="combtn cbtn_4 formsbt">
<input type="reset"	 value="重置" class="combtn cbtn_4 formsbt">
 
</div>
</form>
<!-- ui-dialog -->
<div id="dialog" title="选择教师">
    <input type="text" name="classroom_keyword" id="classroom_keyword" value="请输入登录名或姓名"onblur="if(this.value == ''){this.value = '请输入登录名或姓名';}" onClick="if(this.value == '请输入登录名或姓名'){this.value = '';}else {this.select();}" /> <button class="cbtn_my" type="button" id="classroom_search">搜索</button>
    <div class="tabcon tablist">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tbody>
          <tr class="tabtit">
          <td width="10%">选择</td>
            <td width="30%">登录名</td>
            <td width="20%">姓名</td>
            <td width="40%">手机</td>
          </tr>
        </tbody>
        <tbody id='moduletbody'>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </tbody>
        </table>
    </div>
    <div id="hidden_classroom_list" style="display:none"></div>
</div>
<script type='text/javascript'>
$(function(){
$("#ceng_company input").blur(function(){

if(parseInt(this.value)>100){
this.value = 100;
}
$("#ceng_agent input").blur();
}).keyup(function(){
this.value=this.value.replace(/[^\d]/g,'')
});
$("#ceng_agent input").blur(function(){
	var company = parseInt($("#ceng_company input").val());
	if(isNaN(company)){
		company=0;
		$("#ceng_company input").val(0);
	}
	var agent = parseInt(this.value);
	if(company+agent>100){
	this.value = 100-company;
}
$("#ceng_teacher input").blur();
}).keyup(function(){
this.value=this.value.replace(/[^\d]/g,'')
});
$("#ceng_teacher input").blur(function(){
	var company = parseInt($("#ceng_company input").val());
	var agent = parseInt($("#ceng_agent input").val());
	var teacher = parseInt(this.value);
	if(isNaN(company)){
		$("#ceng_company input").val(0);
		company=0;
	}
	if(isNaN(agent)){
		$("#ceng_agent input").val(0);
		agent=0;
	}
	if(isNaN(teacher)){
		$("#ceng_teacher input").val(0);
		teacher=0;
	}
	this.value = 100-company-agent;
	}).keyup(function(){
	this.value=this.value.replace(/[^\d]/g,'')
	});
});
$(function(){
	$.ajax({
		url:'/config/template.json',
		type:'GET',
		dataType:"json",
		success:function(datas){
			
			for(var i=0;i<datas.length;i++){
				$("#template").append("<option value='"+datas[i]['tplname']+"'>"+ datas[i]['tplname']+ "</option>"); 
			}
		},
		error:function(){
			alert();
		}
	});
});
$("#period").change(function(){
	changeperiod();
});
function changeperiod()
 {
	 var begindate = $('#begindate').datebox('getValue');
     if (begindate != "") {
         var datetime = Date.parse(begindate.replace(/\-/g,'/'));
         var pval = parseInt($("#period").val());
		 if (datetime > 0 && pval > 0) { 
			var bdate = new Date();
			bdate.setTime(datetime);
			bdate.setFullYear(bdate.getFullYear()+pval); 
			var month = bdate.getMonth() + 1;
			if (month < 10)
				month = "0" + month;
			var date = bdate.getDate();
			if (date < 10)
				date = "0" + date;
			edatestr = bdate.getFullYear() + "-" + month + "-" + date;
			$('#enddate').datebox('setValue', edatestr);	
		 }
     }
 }
$.extend($.fn.validatebox.defaults.rules, {    
    isprice: {    
        validator: function(value, param){    
              return /^[0-9]{1,}([\.][0-9]{2})?$/.test(value);     
        }     
    },
    isnum: {    
        validator: function(value, param){    
              return /^[0-9]{1,}$/.test(value);     
        }     
    }   
});
$(function(){
     $('#domain').validatebox({    
        required: true,
        validType:'domain',    
        missingMessage:'域名不能为空'
    });  
     $('#summary').validatebox({    
        required: true,    
        validType: 'length[10,1000]',
        missingMessage:'教室摘要不能为空',
        invalidMessage:'教室摘要长度必须为10-1000个字符'
    });
     $('#crprice').validatebox({    
        required: true,
        validType:'isprice',
        missingMessage:'教室金额的格式错误',
        invalidMessage:'教室金额的格式错误,必须为正整数或者两位小数的浮点数'
    }); 
     $('#displayorder').validatebox({    
        required: true,    
        validType: 'isnum',
        missingMessage:'排序不能为空',
        invalidMessage:'排序必须为正整数'
    }); 
	$('#username').validatebox({
		required:true,
		validType: 'text',
		missingMessage:'请选择教师'
	});
	$('#begindate,#enddate').datebox({
		required:true,
		validType:'text',
		missingMessage:'请选择时间'
	});
	$('#profit_company,#profit_agent,#profit_teacher').validatebox({
		required:true,
		validType:'text',
		missingMessage:'请填写分成比例'
	});
	$(".datebox :text").attr("readonly","readonly");
});
</script>
</body>

<?php
$this->display('school/footer');
?>