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
<style type="text/css">
    .tablink a:first-child{margin-left:0;}
</style>
<body>
<form name="form" id="form" action="/data/identity.html" method="get">
    <input type="hidden" name="crid" id="crid" value="<?php echo $request['crid']?>" />
    <div class="tabconss">
        <input type="button" value="批量审核" class="comcheck audit" data-id="0" />
        <span class="wid85">审核状态:</span>
        <select name="status">
            <option value='-1' <?php echo empty($request['status'])?"selected":""?>>全部</option>
            <option value="0" <?php echo ($request['status']==0)?"selected":""?>>待审核</option>
            <option value="1" <?php echo ($request['status']==1)?"selected":""?>>已通过</option>
            <option value="2" <?php echo ($request['status']==2)?"selected":""?>>未通过</option>
        </select>
        <span class="wid85">所属网校:</span>
        <input value="<?php echo $request["crname"]?>" class="inp wid140" readonly id="crname" name="crname" onClick="$('.Jselect').click()" /><button type="button" id="dialog-link" class="comcheck">选择</button>
        <span class="wid85">申请时间:</span>
        <input type="text" class="inp wid100 Wdate" onClick="WdatePicker()" name="start" value="<?=!empty($request['start']) ? $request['start'] : '' ?>" />
        到
        <input type="text" class="inp wid100 Wdate" onClick="WdatePicker()" name="end" value="<?=!empty($request['end']) ? $request['end'] : '' ?>" />
        <input type="submit" value="查询" class="comcheck"/>
    </div>
</form>
<div class="midnr">
    <div class="tabcon tablist" >
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr class="tabtit">
                <td width="5%">选择</td>
                <td width="5%" class="leftxt">序号</td>
                <td width="10%" class="leftxt">帐号</td>
                <td class="leftxt">所属网校</td>
                <td width="15%" class="leftxt">真实姓名</td>
                <td class="leftxt">手机号码</td>
                <td class="leftxt">申请时间</td>
                <td class="leftxt">审核状态</td>
                <td class="leftxt">审核人</td>
                <td class="leftxt">处理时间</td>
                <td width="15%" class="leftxt">操作</td>
            </tr>
            <?php if (!empty($list)) {
                $status = array(
                    0 => '待处理',
                    1 => '验证成功',
                    2 => '验证失败'
                );
                foreach ($list as $index => $item) { ?>
                    <tr class="<?php if ($index % 2 == 1) { ?>tabbg<?php } ?>">
                        <td><?php if (empty($item['status'])) { ?><input type="checkbox" data-id="<?=$item['aid']?>" /><?php } ?></td>
                        <td class="leftxt"><?=(($request['page'] - 1) * $request['pagesize'] + $index + 1)?></td>
                        <td class="leftxt"><?=$item['username']?></td>
                        <td class="leftxt"><?=$item['crname']?></td>
                        <td class="leftxt"><?=$item['realname']?></td>
                        <td class="leftxt"><?=$item['mobile']?></td>
                        <td class="leftxt"><?=$item['dateline'] > 0 ? date('Y-m-d H:i:s', $item['dateline']) : ''?></td>
                        <td class="leftxt"><?=$status[$item['status']]?></td>
                        <td class="leftxt"><?php if (!empty($item['status'])) { echo !empty($item['urealname']) ? $item['aname'] : $item['ausername']; }?></td>
                        <td class="leftxt"><?php if (!empty($item['status'])) { echo $item['admin_dateline'] > 0 ? date('Y-m-d H:i:s', $item['admin_dateline']) : ''; }?></td>
                        <td class="leftxt tablink">
                            <a href="javascript:;" class="show-info" data-z="<?=$item['idcard_z']?>" data-b="<?=$item['idcard_b']?>">明细</a>
                            <a href="/data/jsauth.html?aid=<?=$item['aid']?>" class="Jview">详情</a>
                        </td>
                    </tr>
                <?php }
            }?>
        </table>
    </div>
    <?php if (!empty($pagestr)) { ?><div class="page"><?php echo $pagestr?></div><?php } ?>
    <div id="checkdiv" style="display:none">
        <p style="height:40px"><span class="wid85">审核状态:</span>
            <label><input type="radio" name="admin_status" value="1" checked="checked" /> 通 过</label>
            <label style="margin-left:20px"><input type="radio" name="admin_status" value="2" /> 未通过</label>
        </p>
        <p><span class="wid85">备注信息:</span><textarea name="remark" id="remark" style="height:140px;width:400px;margin-left:10px"></textarea></p>
    </div>


    <?php
    $Dialog = EBH::app()->lib("Dialog");
    echo $Dialog::open(".Jview","Jview","800","620",true,false);
    echo $Dialog::open(".Jview","Jview","850","670",true,false);
    ?>
</div>
<script type="text/javascript">
   // Link to open the dialog
   $( "#dialog-link,#crname" ).click(function( event ) {
       /* $( "#dialog" ).dialog( "open" );*/
       var content_dialog = '<div id="dialog" title="选择学校" style="width:600px"><input class="inp a60" type="text" name="classroom_keyword" id="classroom_keyword" value="请输入网校名称或域名"onblur="if(this.value == \'\'){this.value = \'请输入网校名称或域名\';}" onClick="if(this.value == \'请输入网校名称或域名\'){this.value = \'\';}else {this.select();}" /><button class="cbtn_my" type="button" id="classroom_search">搜索</button>&nbsp;&nbsp;<button type="button" onclick="clearSchool()">清空</button><div class="tabcon tablist"  style="margin-top:5px" ><table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr class="tabtit"><td width="10%">选择</td><td width="15%">域名</td><td width="50%">网校名称</td><td width="20%">创建时间</td></tr></tbody><tbody id=\'moduletbody\'></tbody></table></div><div id="hidden_classroom_list" style="display:none"></div></div>';
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
   function checkCrItem(crid, crname)
   {
       $('#crname').val(crname);
       $('#crid').val(crid);
       $('#crid_'+crid).attr('checked',true);//选中单选框
       dialog_choose_crid.close();
       $('#form').submit();
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
       $('#form').submit();
   }
   //查看详情
   $(".show-info").bind('click', function() {
       var imgs = [];
       var that = $(this);
       if (that.data('z')) {
           imgs.push('<img src="'+that.data('z')+'" style="max-width:80%" />');
       }
       if (that.data('b')) {
           imgs.push('<img src="'+that.data('b')+'" style="max-width:80%" />');
       }

       var dialog =  window.top.art.dialog({
           title: '查看详情',
           content: imgs.join('<br /><br />'),
           width: '600px',
           lock:true,
           opacity : 0.2
       });
       dialog.button([{name: '关闭'}]);
   });
   //审核
   $(".audit").bind('click', function() {
       if ($(".tabcon input[type='checkbox']:checked").size() == 0) {
           $.showmessage({
               img : 'success',
               message:'请选择操作项',
               title:'消息通知'
           });
           return false;
       }
       var dialog =  window.top.art.dialog({
           title: '审核',
           content: document.getElementById("checkdiv"),
           width: '600px',
           lock:true,
           opacity : 0.2
       });
       dialog.button([{
           name: '确定',
           focus: true,
           callback: function () {
               var formdata = {};
               formdata.aids = [];
               $(".tabcon input[type='checkbox']:checked").each(function() {
                   formdata.aids.push($(this).data('id'));
               });
               formdata.status = window.top.$("input[name='admin_status']:checked").val();
               formdata.remark = window.top.$("#remark").val();
               $.ajax({
                   url: '/data/ajax_identity_audit.html',
                   type: 'post',
                   data: formdata,
                   dataType: 'json',
                   success: function(ret) {
                       if (ret.errno > 0) {
                           $.showmessage({
                               img : 'success',
                               message: ret.msg,
                               title:'消息通知',
                               callback: function() {
                                   location.reload();
                               }
                           });
                           return false;
                       }
                       $.showmessage({
                           img : 'success',
                           message: '处理成功',
                           title:'消息通知',
                           callback: function() {
                               location.reload();
                           }
                       });
                       return false;
                   }
               });
               dialog.close();
               return false;
           }},{name: '关闭'}]);
   });
   $("select[name='status']").bind('change', function() {
      $("#form").trigger('submit');
   });
</script>
</body>
</html>
