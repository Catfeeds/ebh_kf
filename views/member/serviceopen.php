<?php $this->display('head');?>
<body>

<input type="hidden" name='uid' id="uid" value="<?=$userinfo['uid']?>" />
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="tabtit">
    <td colspan="4">&nbsp;&nbsp;登录名:&nbsp;&nbsp;<span style="font-weight:bolder;color:red;font-size:18px;"><?=$userinfo['username']?></span>&nbsp;&nbsp;真实姓名:&nbsp;&nbsp;<span style="font-weight:bolder;color:red;font-size:18px;"><?=$userinfo['realname']?></span>&nbsp;&nbsp;性别 : <span style="font-size:16px;color:red;font-weight:bolder;"><?=$userinfo['sex']==1?'女':'男'?></span>&nbsp;&nbsp;支付合计: <span style="font-size:16px;color:red;font-weight:bolder;" id='totalmoney'>0.00</span>
       </td>
  </tr>
</table>
</div>
    <div style="height:150px;overflow-y:scroll">
        <form class="manopen" tag=0>
        
        </form>
    </div>
    
<div style="text-align:center;margin-top:10px"><input type="submit" value="批量提交" class="combtn cbtn_4 formsbt" onClick="return getSubmitInfo()"/>&nbsp;<input type="submit" value="批量取消" class="combtn cbtn_4 formsbt" onClick="return resetAll()"/></div>


<div style="clear: both;height: 10px;line-height: 10px;visibility: hidden;">
</div>


<!--服务列表 start-->         
<div class="tabcon tablist">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
      <tr class="tabtit">
        <td colspan="5">
        <label for="catid">所属学校</label>
                        <input type="text" style="width:280px; cursor:pointer" readonly value="<?=$crname?>" id="crname" name="crname" class="inp">
                        <button type="button" id="dialog-link" class="ctbtn">选择</button>
                        <button type="button" id="cr_clear" class="ctbtn">清除</button>
                        <input type="hidden" name="crid" id="crid"  value="<?=$crid?>" />
            <select style="width:220px;border:1px solid #a0c5e8;" name="paypackage_list" id="paypackage_list" onChange="changePitemList()">
                <option value="0">选择服务包</option>
            </select>
        
        </td>
      </tr>    
      <tr class="tabtit">
            <td width="10%">选择</td>
            <td width="30%">服务项名称</td>
            <td width="30%">所属服务包</td>
            <td width="10%">价格</td>
            <td width="20%">有效时间</td>
      </tr>
    </tbody>
    <tbody id="payitem_list">
          <tr>
            <td>&nbsp;</td>
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
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
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
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
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
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
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
            <td>&nbsp;</td>
          </tr>
    </tbody>
    </table>
</div>
<!--服务列表 end-->

<!--学校列表 ui-dialog start-->
<div id="dialog" title="选择学校">
    <div style="margin-bottom:6px;">
    	<input type="text" name="classroom_keyword" id="classroom_keyword" class="inp a60" value="请输入网校名称或域名"onblur="if(this.value == ''){this.value = '请输入网校名称或域名';}" onClick="if(this.value == '请输入网校名称或域名'){this.value = '';}else {this.select();}" /> <button type="button" id="classroom_search" class="cbtn_my">搜索</button>
    </div>
    <div class="tabcon tablist">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tbody>
          <tr class="tabtit">
            <td width="10%">选择</td>
            <td width="15%">域名</td>
            <td width="50%">网校名称</td>
            <td width="20%">创建时间</td>
          </tr>
        </tbody>
        <tbody id="moduletbody">
        </tbody>
        </table>
    </div>
</div>
<!--学校列表 ui-dialog end-->
<script>
var hasSubmitAll = false;

//不处理check，避免事件冒泡引起的问题
function nocheck(itemid){	
	$("input[tag="+itemid+"]").prop('checked', !$("input[tag="+itemid+"]").prop('checked'));
}
function renderForm(itemid,iname,iprice,folderid,crid,imonth,iday){
	if($("input[tag="+itemid+"]").prop('checked')){
		$("input[tag="+itemid+"]").prop('checked', false);
        $("form[tag="+itemid+"]").remove();
        getTotalMoney();
    }else{
		$("input[tag="+itemid+"]").prop('checked', true);
        $("form[tag="+itemid+"]").remove();
        addForm(itemid,iname,iprice,folderid,crid,imonth,iday);
        getTotalMoney();
    }
}
function addForm(itemid,iname,iprice,folderid,crid,imonth,iday){
    var formdata = '<form class="manopen" tag='+itemid+'>';
       formdata+='<input type="hidden" name="itemid" value="'+itemid+'"/>';
       formdata+='<input type="hidden" name="crid" value="'+crid+'"/>';
       formdata+='<input type="hidden" name="folderid" value="'+folderid+'"/>';
       formdata+='<input type="hidden" name="iprice" value="'+iprice+'"/>';
       formdata+='<input type="hidden" name="imonth" value="'+imonth+'"/>';
       formdata+='<input type="hidden" name="iday" value="'+iday+'"/>';
       formdata+='开通服务:&nbsp;&nbsp;<input type="text" value="'+iname+'" style="width:200px;" name="iname" disabled="disabled" class="inp" />';
       formdata+='&nbsp;&nbsp;费用 : <span style="display:inline-block;width:40px;">'+iprice+'</span><select style="display:none" onchange="getMoney(this,\''+iprice+'\')" name="month">';
       formdata+='<option value="1" select=select>1个</option>';
       formdata+='</select>';
       formdata+='&nbsp;&nbsp;支付金额:&nbsp;&nbsp;<input onchange="getTotalMoney();" style="width:60px;" type="text" name="money" >元';
       formdata+='<input type="hidden" name="remoney" >';
       formdata+='&nbsp;&nbsp;<a class="tj" style="cursor:pointer"   onclick="return submitForm(this)">提交</a>';
       formdata+='&nbsp;&nbsp;<a class="qx" style="cursor:pointer"   onclick="return delForm('+itemid+')">取消</a>';
       formdata+='&nbsp;&nbsp;测试:<input type="checkbox" class="cs" style="cursor:pointer"   onclick="cstest(this,0)" />';
       formdata+='<input type="hidden" name="type" value="4">';
       formdata+='</form>';
       var formcname = 'manopen_'+itemid;
       $("form.manopen:last").after(formdata);
       $monthObj = $("form.manopen[tag='"+itemid+"'] select[name=month]");
       getMoney($monthObj,iprice);

}

function delForm(itemid){
    $("form[tag="+itemid+"]").remove();
    $("input[tag="+itemid+"]").prop('checked',false);
    getTotalMoney();
}

function getTotalMoney(){
  var moneyinput = $("form.manopen[tag!=0] input[name=money]");
  var totalmoney = 0;
  moneyinput.each(function(index,obj){
    totalmoney+=$(obj).val()*100;
  });
  $("#totalmoney").text((totalmoney/100).toFixed(2));
}

function getMoney(e,crprice){
    var totalprice = $(e).val()*crprice;
    if(!crprice){
       $(e).siblings('input[name$=money]').val('0.00');
    }else{
       $(e).siblings('input[name$=money]').val(totalprice.toFixed(2));
    }
    getTotalMoney();
}

function cstest(e,crprice){
    var totalprice = $(e).val()*crprice;
    if($(e).prop('checked')){
      if(!crprice){
         $(e).siblings('input[name=money]').val('0.00');
         $(e).siblings('input[name=type]').val(5);
      }else{
         $(e).siblings('input[name=money]').val(totalprice.toFixed(2));
         $(e).siblings('input[name=type]').val(5);
      }
    }else{
        if(!crprice){
         $(e).siblings('input[name=money]').val($(e).siblings('input[name=remoney]').val());
         $(e).siblings('input[name=type]').val(4);
      }else{
         $(e).siblings('input[name=money]').val($(e).siblings('input[name=remoney]').val());
         $(e).siblings('input[name=type]').val(4);
      }
    }
    
    getTotalMoney();
}

function submitForm(e){
   var context = $(e).parent('form');
   var iname = $('input[name=iname]',context).val();
   var itemid = $('input[name=itemid]',context).val();
   var crid = $('input[name=crid]',context).val();
   var money = $('input[name=money]',context).val();
   var remoney = $('input[name=remoney]',context).val();
   var month = $('select[name=month]',context).val();
   var type = $('input[name=type]',context).val();
   var uid = $('#uid').val();
   var folderid = $('input[name=folderid]',context).val();
   var imonth = $('input[name=imonth]',context).val()*month;
   var iday = $('input[name=iday]',context).val()*month;
   var status = check(uid,itemid,money,month,iname);
    if(status!=1){
        alert(status);
        return false;
   }
   if(((money*100 - remoney*100)!=0)&&(type==4)){
    // '服务: '+iname+' 开通金额与所交金额不符,应该为 : '+remoney+'元'
    $.messager.confirm('提示信息','服务:<span style="color:red;font-weight:bolder"> '+iname+' </span><br />开通金额与所交金额不符<br />填写金额为:<strong style="color:red;">'+money+'元</strong><br /><span style="margin-left:42px;">应该为</span> : <span style="color:red;font-weight:bolder">'+remoney+'元</span><br /><span style="margin-left:42px;">您确定要提交吗？</span>',function(r){
        if(r){
           manopensubmit(uid,itemid,money,month,iname,type,folderid,crid,imonth,iday);
        }else{
           return false;
        }
    });
   }else{
     manopensubmit(uid,itemid,money,month,iname,type,folderid,crid,imonth,iday);
   }
   
   return false;
}

function getSubmitInfo(){
  if(hasSubmitAll==true){
    return success();
  }
  $forms = $("form.manopen[tag!=0]");
  var dataStroage = new Array();
  var res = null;
  $.each($forms,function(i,n){
    if((res=getSubmitFormInfo(n))!=false){
      dataStroage.push(res);
    }
  });
  $.post('/ibuy/manualnotify_all.html',{dataStroage:dataStroage},function(res){
    eval('res='+res);
    $.each(res,function(i,n){
      if(n>0){
        changeStatus(i,'开通成功');
      }else{
        changeStatus(i,'开通失败');
      }
    });
	goList(1);
  });
  hasSubmitAll = true;
}

//获取表单信息
function getSubmitFormInfo(e){
   var dataStroage = new Object();
   var context = $(e);
   var iname = $('input[name=iname]',context).val();
   var itemid = $('input[name=itemid]',context).val();
   var crid = $('input[name=crid]',context).val();
   var money = $('input[name=money]',context).val();
   var remoney = $('input[name=remoney]',context).val();
   var month = $('select[name=month]',context).val();
   var type = $('input[name=type]',context).val();
   var uid = $('#uid').val();
   var folderid = $('input[name=folderid]',context).val();
   var imonth = $('input[name=imonth]',context).val()*month;
   var iday = $('input[name=iday]',context).val()*month;
   var status = check(uid,itemid,money,month,iname);
    if(status!=1){
        alert(status);
        return false;
   }
   if(((money*100 - remoney*100)!=0)&&(type==4)){
    // '服务: '+iname+' 开通金额与所交金额不符,应该为 : '+remoney+'元'
    $.messager.confirm('提示信息','服务:<span style="color:red;font-weight:bolder"> '+iname+' </span><br />开通金额与所交金额不符<br />填写金额为:<strong style="color:red;">'+money+'元</strong><br /><span style="margin-left:42px;">应该为</span> : <span style="color:red;font-weight:bolder">'+remoney+'元</span><br /><span style="margin-left:42px;">您确定要提交吗？</span>',function(r){
        if(r){
          dataStroage.uid = uid;
          dataStroage.itemid = itemid;
          dataStroage.money = money;
          dataStroage.month = month;
          dataStroage.iname = iname;
          dataStroage.type = type;
          dataStroage.folderid = folderid;
          dataStroage.crid = crid;
          dataStroage.imonth = imonth;
          dataStroage.iday = iday;
          dataStroage.number = 1;
        }
    });
   }else{
        dataStroage.uid = uid;
        dataStroage.itemid = itemid;
        dataStroage.money = money;
        dataStroage.month = month;
        dataStroage.iname = iname;
        dataStroage.type = type;
        dataStroage.folderid = folderid;
        dataStroage.crid = crid;
        dataStroage.imonth = imonth;
        dataStroage.iday = iday;
        dataStroage.number = 1;
   }
   return dataStroage;
}

function submitAll(){
    $("a.tj[status!=1]").trigger('click');
}

function resetAll(){
     $("form.manopen[tag!=0]").remove();
     $(":checkbox").prop('checked',false);
     getTotalMoney();
     hasSubmitAll = false;
     return false;
}

function check(uid,itemid,money,month,iname){
    var errorInfo = new Array();
    var uidstatus = $.isNumeric(uid)&&(uid>0);
    var itemidtatus = $.isNumeric(itemid)&&(itemid>0);
    var moneystatus = $.isNumeric(money)&&(money>=0);
    var monthstatus = $.isNumeric(month)&&(month>0);
    if(!uidstatus){
        errorInfo.push('服务:'+iname+'用户选择有误!');
    }
   
    if(!moneystatus){
        errorInfo.push('服务:'+iname+'支付金额填写有误!');
    }
    if(money>10000000){
       errorInfo.push('服务:'+iname+'支付金额单次不能超过10000000!');
       moneystatus=false;
    }
    if(!monthstatus){
        errorInfo.push('服务:'+iname+'开通时长填写有误!');
    }
    if(!itemidtatus){
        errorInfo.push('服务:'+iname+'服务选择有误!');
    }
    if(uidstatus&&itemidtatus&&monthstatus&&moneystatus){
        return 1;
    }else{
        return errorInfo.join("\r\n");
    }
   
}

function manopensubmit(uid,itemid,money,month,iname,type,folderid,crid,imonth,iday){

    //字段校验判断。。。。。。
    var status = check(uid,itemid,money,month,iname);
    if(status!=1){
        alert(status);
        return false;
    }
    $.post('/ibuy/manualnotify.html',
            {uid:uid,number:month,itemid:itemid,money:money,type:type,folderid:folderid,crid:crid,omonth:imonth,oday:iday},
            function(d){
                if(d==1){
                    changeStatus(itemid,"开通成功");
                    goList(1);
                }else{
                    changeStatus(itemid,"开通失败");
                    goList(1);
                }
            }
        );
    // $('.panel-tool-close').trigger('click');
    return false;
}

function changeStatus(crid,info){
   var context = $("form[tag="+crid+"]");
   $("a.tj",context).attr("onclick","success()").html(info);
   $("a.tj",context).attr("status","1");
   $("a.qx",context).remove();
   $("input[tag="+crid+"]").prop('checked',false);
}
function success(){
    alert('操作已成功!请勿重复提交!');
    return false;
}

//form submit end

function changePitemList()
{
	goList(1);
}
function goList(page)
{
	var uid = $("#uid").val();
	var pid = $("#paypackage_list").val();
	$.post("/payitem/getlist.html", {page: page, pid: pid, uid: uid},
		function(data){
			$("#payitem_list").html(data);
		}, "html");
}


<!--学校列表 开始-->
$( "#dialog" ).dialog({
	autoOpen: false,
	width: 700,
	height: 550,
	buttons: [
		{
			text: "取消",
			click: function() {
				$("#moduletbody").html('');//清除列表
				$( this ).dialog( "close" );
			}
		}
	]
});

// Link to open the dialog
$( "#dialog-link" ).click(function( event ) {
	$( "#dialog" ).dialog( "open" );
	event.preventDefault();
	goPage(1);
	$("#classroom_keyword").blur();
});
$( "#crname" ).click(function( event ) {
	$( "#dialog" ).dialog( "open" );
	event.preventDefault();
	goPage(1);
	$("#classroom_keyword").blur();
});

$("#cr_clear").click(function(){
	$("#crname").val('');
	$("#crid").val('');
	$("#payitem_list").html('');
	$("#paypackage_list").html('<option value="0">选择服务包</option>');
});

// Ajax get classroom list
$("#classroom_search").click(function() {
	goPage(1);
});

function goPage(page)
{
	var classroom_keyword = $("#classroom_keyword").val();
	if (classroom_keyword == '请输入网校名称或域名') classroom_keyword = '';
	$.post("/classroom/getlist.html", {page: page, keyword: classroom_keyword, isschool: 7, checkaccess: 1},
		function(data){
			$("#moduletbody").html(data);
		}, "html");
}
function reversecheck(crid)
{
	$("#crid_"+crid).prop("checked", !$("#crid_"+crid).prop("checked"));
}
function checkCrItem(crid, crname)
{
	//取出crid,crname
	$("#crname").val(crname);
	$("#crid").val(crid);
	//设置服务包下拉菜单
	getPackageOption(crid);

	$("#payitem_list").html('');//清除服务项列表
	$("#moduletbody").html('');//清除服务包列表
	$( "#dialog" ).dialog( "close" );
}

function getPackageOption(crid)
{
	$.post("/paypackage/getlist.html", {crid: crid}, function(data){
		$("#paypackage_list").html('<option value="0">选择服务包</option>');	
		$.each(data, function(i, n){
			$("#paypackage_list").append('<option value="'+i+'">'+n+'</option>'); 
		});
		if ($("#paypackage_list option").size()>1)
		{
			$("#paypackage_list").get(0).selectedIndex=1;
			changePitemList();
		}
	}, "json");
}
<!--学校列表 结束-->
$(function(){
	var crid = $("#crid").val();
	if(crid != '') getPackageOption(crid);
});
</script>
</body>
</html>    