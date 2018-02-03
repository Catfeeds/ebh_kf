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
    
<div style="text-align:center;margintop:10px 0"><input type="submit" value="批量提交" class="combtn cbtn_4 formsbt" onClick="return submitAll()"/><input type="submit" value="批量取消" class="combtn cbtn_4 formsbt" onClick="return resetAll()"/></div>


<!--学校列表 start-->         
<div class="tabcon tablist">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
      <tr class="tabtit">
        <td colspan="6">
			<input type="text" name="classroom_keyword" id="classroom_keyword" value="请输入网校名称或域名"onblur="if(this.value == ''){this.value = '请输入网校名称或域名';}" onClick="if(this.value == '请输入网校名称或域名'){this.value = '';}else {this.select();}" /> <button type="button" id="classroom_search">搜索</button>
        </td>
      </tr>    
      <tr class="tabtit">
        <td width="10%">选择</td>
        <td width="10%">域名</td>
        <td width="40%">网校名称</td>
        <td width="10%">年费</td>
        <td width="15%">开始时间</td>
        <td width="15%">结束时间</td>
      </tr>
    </tbody>
    <tbody id="moduletbody">
          <tr>
            <td>&nbsp;</td>
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
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
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
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
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
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
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
            <td>&nbsp;</td>
          </tr>
        </tbody>
    </table>
</div>
<!--学校列表 end-->

<script>
function check(uid,crid,money,month,crname){
    var errorInfo = new Array();
    var uidstatus = $.isNumeric(uid)&&(uid>0);
    var cridtatus = $.isNumeric(crid)&&(crid>0);
    var moneystatus = $.isNumeric(money)&&(money>=0);
    var monthstatus = $.isNumeric(month)&&(month>0);
    if(!uidstatus){
        errorInfo.push('网校:'+crname+'用户选择有误!');
    }
   
    if(!moneystatus){
        errorInfo.push('网校:'+crname+'支付金额填写有误!');
    }
    if(money>10000000){
       errorInfo.push('网校:'+crname+'支付金额单次不能超过10000000!');
       moneystatus=false;
    }
    if(!monthstatus){
        errorInfo.push('网校:'+crname+'开通时长填写有误!');
    }
    if(!cridtatus){
        errorInfo.push('网校:'+crname+'网校选择有误!');
    }
    if(uidstatus&&cridtatus&&monthstatus&&moneystatus){
        return 1;
    }else{
        return errorInfo.join("\r\n");
    }
   
}
function manopensubmit(uid,crid,money,month,crname,type){

    //字段校验判断。。。。。。
    var status = check(uid,crid,money,month,crname);
    if(status!=1){
        alert(status);
        return false;
    }
    $.post('/classactive/manualnotify.html',
            {uid:uid,month:month,crid:crid,money:money,type:type},
            function(d){
                if(d==1){
                    changeStatus(crid,"开通成功");
                }else{
                    changeStatus(crid,"开通失败");
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
function renderForm(crid,crname,crprice){
   if($("input[tag="+crid+"]").prop('checked')){
        $("form[tag="+crid+"]").remove();
        addForm(crid,crname,crprice);
        getTotalMoney();
    }else{
        $("form[tag="+crid+"]").remove();
        getTotalMoney();
    }
     
}


function addForm(crid,crname,crprice){
    var formdata = '<form class="manopen" tag='+crid+'>';
       formdata+='<input type="hidden" name="crid" value="'+crid+'"/>';
       formdata+='<input type="hidden" value="'+crprice+'"/>';
       formdata+='开通网校:&nbsp;&nbsp;<input type="text" value="'+crname+'" style="width:200px;"  name="crname"  disabled=disabled />';
       formdata+='&nbsp;&nbsp;年费 : <span style="display:inline-block;width:40px;">'+crprice+'</span>&nbsp;&nbsp;开通时长:&nbsp;&nbsp;<select onchange="getMoney(this,\''+crprice+'\')" name="month">';
       // formdata+='<span>年费:'+crprice+'</span>';
       formdata+='<option value=0>请选择</option>';
       formdata+='<?php for ($i=1; $i <= 12; $i++) {?>';
       formdata+='<option value="<?=$i?>"><?=$i?>个月</option>';
       formdata+='<?php }?>';
       formdata+='</select>';
       formdata+='&nbsp;&nbsp;支付金额:&nbsp;&nbsp;<input onchange="getTotalMoney();" style="width:60px;" type="text" name="money" >&nbsp;&nbsp;元';
       formdata+='<input type="hidden" name="remoney" >';
       formdata+='&nbsp;&nbsp;<a class="tj" style="cursor:pointer"   onclick="return submitForm(this)">提交</a>';
       formdata+='&nbsp;&nbsp;<a class="qx" style="cursor:pointer"   onclick="return delForm('+crid+')">取消</a>';
       formdata+='&nbsp;&nbsp;测试:<input type="checkbox" class="cs" style="cursor:pointer"   onclick="cstest(this,0)" />';
       formdata+='<input type="hidden" name="type" value="4">';
       formdata+='</form>';
       var formcname = 'manopen_'+crid;
       $("form.manopen:last").after(formdata);
}

function delForm(crid){
    $("form[tag="+crid+"]").remove();
    $("input[tag="+crid+"]").prop('checked',false);
    getTotalMoney();
}


function getMoney(e,crprice){
    var totalprice = $(e).val()*crprice/12;
    if(!crprice){
       $(e).siblings('input[name$=money]').val('0.00');
    }else{
       $(e).siblings('input[name$=money]').val(totalprice.toFixed(2));
    }
    getTotalMoney();
}

function cstest(e,crprice){
    var totalprice = $(e).val()*crprice/12;
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
   var crname = $('input[name=crname]',context).val();
   var crid = $('input[name=crid]',context).val();
   var money = $('input[name=money]',context).val();
   var remoney = $('input[name=remoney]',context).val();
   var month = $('select[name=month]',context).val();
   var type = $('input[name=type]',context).val();
   var uid = $('#uid').val();
   var status = check(uid,crid,money,month,crname);
    if(status!=1){
        alert(status);
        return false;
   }
   if(((money*100 - remoney*100)!=0)&&(type==4)){
    // '网校: '+crname+' 开通金额与所交金额不符,应该为 : '+remoney+'元'
    $.messager.confirm('提示信息','网校:<span style="color:red;font-weight:bolder"> '+crname+' </span><br />开通金额与所交金额不符<br />填写金额为:<strong style="color:red;">'+money+'元</strong><br /><span style="margin-left:42px;">应该为</span> : <span style="color:red;font-weight:bolder">'+remoney+'元</span><br /><span style="margin-left:42px;">您确定要提交吗？</span>',function(r){
        if(r){
           manopensubmit(uid,crid,money,month,crname,type);
        }else{
           return false;
        }
    });
   }else{
     manopensubmit(uid,crid,money,month,crname,type);
   }
   
   return false;
}

function submitAll(){
    $("a.tj[status!=1]").trigger('click');
}

function resetAll(){
     $("form.manopen[tag!=0]").remove();
     $(":checkbox").prop('checked',false);
     getTotalMoney();
     return false;
}

function getTotalMoney(){
  var moneyinput = $("form.manopen[tag!=0] input[name=money]");
  var totalmoney = 0;
  moneyinput.each(function(index,obj){
    totalmoney+=$(obj).val()*100;
  });
  $("#totalmoney").text((totalmoney/100).toFixed(2));
}

function cs(crid){
  getMoney(this,crid,0);
}

//form submit end

<!--学校列表 开始-->
// Ajax get classroom list
$("#classroom_search").click(function() {
	goPage(1);	
});

function goPage(page)
{
	var classroom_keyword = $("#classroom_keyword").val();
	if (classroom_keyword == '请输入网校名称或域名') classroom_keyword = '';
	$.post("/classroom/getlistajax.html", {page: page, keyword: classroom_keyword},
		function(data){
			$("#moduletbody").html(data);
		}, "html");
}
<!--学校列表 结束-->

</script>
</body>
</html>    