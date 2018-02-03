<?php
/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2016/9/9
 * Time: 10:35
 */
?>
<?php $this->display('head');?>
<!--<script type="text/javascript" src="http://static.ebanhui.com/ebh/js/jquery/showmessage/jquery.showmessage.js"></script>-->
<link rel="stylesheet" type="text/css" href="http://static.ebanhui.com/ebh/js/jquery/showmessage/css/default/showmessage.css" media="screen" />
<body>
<?php $statusArr = array(''=>'全部','0'=>'待审核','1'=>'已审核','2'=>'已删除');?>
<div class="baobiao ft12 threecheck MT_10">
    <ul class="one">
        <li id="one1" onClick="ChangeTab(this,'')" class="<?php echo ($request['cat']=='')?"on_box":""?>" >全部</li>
        <li id="one2" onClick="ChangeTab(this,0)" class="<?php echo ($request['cat']=='0')?"on_box":""?>" >待审核</li>
        <li id="one3" onClick="ChangeTab(this,1)" class="<?php echo ($request['cat']=='1')?"on_box":""?>" >已审核</li>
    </ul>
</div>
<script type="text/javascript">
    function ChangeTab(obj, cat) {
        location.href = '/data/domain.html?cat='+cat;
    }
</script>
<form name="form" id="form" action="/data/domain.html" method="get">
    <input type="hidden" name="cat" value="<?php echo $request['cat']?>" />
    <input type="hidden" name="crid" id="crid" value="<?php echo $request['crid']?>" />
    <div class="tabconss">
        <span class="wid85">所属网校:</span>
        <input value="<?php echo $request["crname"]?>" class="inp wid140" readonly id="crname" name="crname" onClick="$('.Jselect').click()" /><button type="button" id="dialog-link" class="comcheck">选择</button>
        <button type="button" id="clear" class="comcheck" onClick="clearSchool()">清除</button>
        <span class="wid85">审核状态:</span>
        <select name="admin_status">
            <option value='0' <?php echo empty($request['admin_status'])?"selected":""?>>全部</option>
            <option value="1" <?php echo ($request['admin_status']==1)?"selected":""?>>已通过</option>
            <option value="2" <?php echo ($request['admin_status']==2)?"selected":""?>>未通过</option>
        </select>
        <span class="wid85">关键字:</span>
        <input type="text" class="inp wid140" name="q" value="<?php echo $request['q']?>" />
        <input type="submit" value="查询" class="comcheck"/>
    </div>
</form>
<div class="midnr">
    <div class="tabcon tablist" >
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr class="tabtit">
                <td width="5%">排序</td>
                <td width="10%">域名名称</td>
                <td class="leftxt">所属网校</td>
                <td width="15%">提交时间</td>
                <td>审核人</td>
                <td>审核状态</td>
                <td width="15%">操作</td>
            </tr>
            <?php if(!empty($domainList)){ ?>
            <?php foreach($domainList as $key=>$domain){?>
                <tr class="<?php echo ($key%2==0)?"tabbg":""?>">
                    <td><?php echo max(0,($request['page']-1))*$request['pagesize']+$key+1?></td>
                    <td><a class="Jview" href="/data/view.html?crid=<?php echo $domain['crid']?>" title="域名详情" style="text-decoration:underline;color:#075587" ><?php echo $domain['fulldomain']?></a></td>
                    <td class="leftxt"><?php echo $domain['crname']?></td>
                    <td><?php if(empty($domain['domain_time'])){ }else{ echo date("Y-m-d",$domain['domain_time']);} ?></td>
                    <td><?php echo $domain['checkname']?$domain['checkname']:'--'?></td>
                    <td><?php if($domain['admin_status']==1){echo '已通过';}elseif($domain['admin_status']==2){ echo '未通过';}else{echo '未审核';}?></td>
                    <td class="tablink">
                        <a class="Jview" href="/data/view.html?crid=<?php echo $domain['crid']?>" title="域名详情">详情</a>
                        <?php if($domain['admin_status']==0||$domain['admin_status']==3){?>
                            <a class="Jcheck1" toid="<?php echo $domain['crid']?>" data-domain="<?=$domain['fulldomain']?>">审核</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php }?>
          <?php  } ?>
        </table>
    </div>
    <div class="page"><?php echo $pagestr?></div>
    <div id="checkdiv" style="display:none">
        <div style="height:50px;">
            <span class='wid85' style="display:inline-block">绑定域名:</span>
            <span style="text-decoration:underline;font:italic small-caps bold 18px/1.5em arial,verdana;"><a class="new_domain" href="http://icp.chinaz.com/?type=host&s=www.ebh.net" target="_blank"></a></span>
            <span style="color:red;font-size: 14px;padding-left:5px;">点击查询备案信息</span>
            
        </div>
    
        <div style='height:40px'><span class='wid85'>审核状态:</span>
            <label><input type='radio' name='admin_status' value='1' checked='checked' /> 通 过</label>
            <label style='margin-left:20px'><input type='radio' name='admin_status' class="checkunpass" value='2' /> 未通过</label>
        </div>
        <div style='margin-bottom: 20px;height: 100px;'>
        <span class='wid85' style='display:inline-block;height:100px;line-height:100px;'>备注信息:</span>
        <textarea name='remark' id='remark' class="inp" style='height:100px;width:400px;resize: none'></textarea>
        </div>
        
        <div style="height:50px;">
        <span class='wid85' style="display:inline-block">备案信息:</span>
        <input  class='inp wid140' type='text' name='icp'  id='icp'  style='width:398px;height:22px' />
        <p class='eMsg'  style='display:none;color:red;font-size: 14px;padding-left:50px;'>请填写备案信息!</p>
        </div>
        
        <div style="margin-bottom: 20px;">
        <span style='color:red;'>备案信息输入如下所示:</span>
        <span >浙ICP备xxxxxx号 Copyright © 2011-2016 www.xxx.cn All Rights Reserved </span>
        </div>
    </div>


    <?php
    $Dialog = EBH::app()->lib("Dialog");
    echo $Dialog::open(".Jview","Jview","800","620",true,false);
    //echo $Dialog::open(".Jselect","Jselect","800","620",false,false);
    ?>
</div>
<!-- ui-dialog -->
<!-- <div id="dialog" title="选择学校">
    <input class="inp a60" type="text" name="classroom_keyword" id="classroom_keyword" value="请输入网校名称"onblur="if(this.value == ''){this.value = '请输入网校名称';}" onClick="if(this.value == '请输入网校名称'){this.value = '';}else {this.select();}" /><button class="cbtn_my" type="button" id="classroom_search">搜索</button>
    <div class="tabcon tablist">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr class="tabtit">
                <td width="10%">选择</td>
                <td width="15%">域名</td>
                <td width="50%">网校名称</td>
                <td width="20%">域名提交时间</td>
            </tr>
            </tbody>
            <tbody id='moduletbody'>
            </tbody>
        </table>
    </div>
    <div id="hidden_classroom_list" style="display:none"></div>
</div> -->
<script type="text/javascript">
    $(function() {

        $('.checkunpass').on("click",function(){
            parent.$(".eMsg").hide();
        });
        //审核操作
        $('.Jcheck1').click(function(){
            //设置domain
            var domian = $(this).data('domain');
            var seach_api_url = 'http://icp.chinaz.com/?type=host&s=';
            $(".new_domain").html(domian);
            $(".new_domain").attr("href",seach_api_url+encodeURIComponent(domian));
            
            var obj = $(this);
            var toid = obj.attr('toid');
            var dialog =  window.top.art.dialog({
                title: '域名审核',
                content: document.getElementById("checkdiv"),
                width: '600px',
                lock:true,
                opacity : 0.2
            });
            dialog.button([{
                name: '确定',
                focus: true,
                callback: function () {
                    var admin_status = window.top.$("input[name='admin_status']:checked").val();
                    var remark = window.top.$("#remark").val();
                    var icp =window.top. $("input[name='icp']").val();
                    //alert(icp); return false;
                    //alert(selectrole); return false;
                    $.post('/data/checkprocess.html',{toid:toid,admin_status:admin_status,admin_remark:remark,type:13,icp:icp},function(data){
                        if(data.msg){
                            if(data.msg=="请填写备案信息"){
                                parent.$(".eMsg").show();
                                return;
                            }else{
                                if(!data.code){//审核成功
                                    obj.parent().prev().html('已审核');
                                    obj.parent().siblings("td:first").find("input").remove();
                                    obj.remove();
                                }
                                $.showmessage({
                                    img : 'success',
                                    message:data.msg,
                                    title:'消息通知',
                                });
                                dialog.close();
                                setTimeout(function(){
                                    location.reload();
                                },2000)
                            }
                        }
                    },'json');
                    return false;
                }},
                {name: '关闭'}]);
        });



    });

   // Link to open the dialog
$( "#dialog-link,#crname" ).click(function( event ) {
   /* $( "#dialog" ).dialog( "open" );*/
   var content_dialog = '<div id="dialog" title="选择学校" style="width:600px"><input class="inp a60" type="text" name="classroom_keyword" id="classroom_keyword" value="请输入网校名称或域名"onblur="if(this.value == \'\'){this.value = \'请输入网校名称或域名\';}" onClick="if(this.value == \'请输入网校名称或域名\'){this.value = \'\';}else {this.select();}" /><button class="cbtn_my" type="button" id="classroom_search">搜索</button><div class="tabcon tablist"  style="margin-top:5px" ><table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr class="tabtit"><td width="10%">选择</td><td width="15%">域名</td><td width="50%">网校名称</td><td width="20%">创建时间</td></tr></tbody><tbody id=\'moduletbody\'></tbody></table></div><div id="hidden_classroom_list" style="display:none"></div></div>';
   dialog_choose_crid = window.art.dialog({
        title: '网校筛选',
        content: content_dialog,
        width: '600px',
        lock:true,
        opacity : 0.2
       //   close:function(){window.location.reload();}
    });
    dialog_choose_crid.button([
        {name: '关闭'}
    ]);
    $("#classroom_search").click(function() {
        goPage(1);  
    });
    event.preventDefault();
    goPage(1);
    $("#classroom_keyword").blur();
});

// Ajax get classroom list
/*$("#classroom_search").click(function() {
    
    goPage(1);  
});*/
function checkCrItem(crid, crname)
{
    $('#crname').val(crname);
    $('#crid').val(crid);
    $('#crid_'+crid).attr('checked',true);//选中单选框
    //$('#dialog').dialog( "close" );
    dialog_choose_crid.close();
}
function goPage(page)
{
    var classroom_keyword = $("#classroom_keyword").val();
    if (classroom_keyword == '请输入网校名称或域名') classroom_keyword = '';
    $.post("/data/getlist.html", {page: page, keyword: classroom_keyword},
        function(data){
            $("#moduletbody").html(data);
        }, "html");
}
function clearSchool(){
  $("#crname").val("");
   $('#crid').val("");
}
</script>
</body>
</html>
