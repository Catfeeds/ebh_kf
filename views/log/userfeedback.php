<?php $this->display('head');?>
<div class="baobiao ft12 threecheck MT_10">
    <ul class="one">
        <li id="one1" onClick="ChangeTab(this,'')" class="<?php echo empty($request['hid'])?"on_box":""?>" >全部</li>
        <li id="one2" onClick="ChangeTab(this,2)" class="<?php if(empty($request['hid'])){echo "";}elseif ($request['hid']=='2'){echo "on_box";}?>" >未处理</li>
        <li id="one3" onClick="ChangeTab(this,1)" class="<?php if(empty($request['hid'])){echo "";}elseif ($request['hid']=='1'){echo "on_box";}?>" >已处理</li>
        <li id="one4" onClick="ChangeTab(this,3)" class="<?= !empty($request['hid'])&&($request['hid'] == '3')? "on_box" : "" ?>">已删除
    </ul>
</div>
<script type="text/javascript">
    function ChangeTab(obj, hid) {
        location.href = '/userfeedback/feedbacklist.html?hid='+hid;
    }
</script>
<form action="/userfeedback/feedbacklist.html" method="get" id="form">
    <input type="hidden" name="cat" value="<?php echo empty($request['cat'])?"0":$request['cat'];?>" />

    <div class="tabconss">
        <span class="wid85">处理状态:</span>
        <select name="hid" id="hid">
            <option value='0' <?php echo empty($request['hid'])?"selected":""?>>全部</option>
            <option value="1" <?php if(empty($request['hid'])){echo "";}elseif($request['hid']==1){ echo "selected";}?>>已处理</option>
            <option value="2" <?php if(empty($request['hid'])){echo "";}elseif($request['hid']==2){ echo "selected";}?>>未处理</option>
            <option value="3" <?php if(empty($request['hid'])){echo "";}elseif($request['hid']==3){ echo "selected";}?>>已删除</option>
        </select>
        <input  name="q" type="text"  class="inp wid140"  placeholder="根据反馈内容查询" value="<?php echo empty($request['q'])?"":$request['q'] ;?>" />
        <input type="submit" value="查询" class="comcheck" />
    </div>
</form>
<div class="tabcon tablist" >
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="tabtit">
            <td>序号</td>
            <td class="leftxt">反馈内容</td>
            <td class="leftxt">来自网校</td>
            <td>来自账号</td>
            <td>来自角色</td>
            <td>邮箱/qq</td>
            <td>状态</td>
            <td>反馈时间</td>
            <td>IP</td>
            <td>操作</td>
        </tr>
        <?php $i = 0;
        if(!empty($feedbacklist)){
            foreach($feedbacklist as $feedback){?>
                <tr <?php $i = ++$i;if($i%2 == 1){echo "class='tabbg'";}?>>
                    <td style="width:50px"><?=$i?></td>
                    <td class="leftxt" style="width:200px" title="<?=$feedback['feedback']?>"><?=$feedback['feedbacklimit']?></td>
                    <td class="leftxt" style="width:120px" title="<?=empty($feedback['schoolname']) ? '-' : $feedback['schoolname']?>"><?=empty($feedback['schoolname']) ? '-' : $feedback['schoolname']?></td>
                    <td style="width:100px" title="<?=$feedback['username']?>"><?=$feedback['username']?></td>
                    <td style="width:100px" title="<?=$feedback['role']?>"><?=$feedback['role']?></td>
                    <td style="width:120px" title="<?=$feedback['email']?>"><?=$feedback['email']?></td>
                    <td style="width:100px" ><?php if(empty($feedback['hid'])){echo "未处理";}elseif($feedback['del']==1){echo "已删除 ";}else{echo "已处理";} ?></td>
                    <td style="width:100px" title="<?=date('Y-m-d H:i', $feedback['dateline'])?>"><?=$feedback['date']?></td>
                    <td style="width:100px" title="<?=$feedback['loginip']?>"><?=$feedback['loginip']?></td>
                    <td style="width:150px" class="tablink">
                    <?php if(!empty($feedback['hid'])){?>
                        <a class="chakan" fbid= "<?=$feedback['fbid']?>" title="处理详情" href="/userfeedback/view.html?fbid=<?=$feedback['fbid']?>" style="color: #f00">详情</a>
                        <span style="margin:0 5px;color:#ccc ">已处理</span>
                        <?php if($feedback['del']==1) {?>
                          <span style="margin:0 5px;color:#ccc">已删除</span>
                        <?php }else{?>
                            <a style="margin:0 5px;" class="del"; fbid="<?=$feedback['fbid']?>">删除</a>
                        <?php }?>
                    <?php }else{?>
                        <a class="chlie" fbid="<?=$feedback['fbid']?>" style="color: #f00">处理</a>
                    <?php }?>
                    </td>
                </tr>
            <?php }
        }?>
    </table>
    <div class="page"><?=$pg?></div>
</div>
<?php
$Dialog = EBH::app()->lib("Dialog");
echo $Dialog::open(".chakan", "chakan", "830", "300", true, false);
?>
<div id="checkdiv" style="display:none"><p><span class="wid85">备注信息:</span><textarea name="remark" id="remark" style="height:140px;width:400px;margin-left:10px"></textarea></p></div>
<style>
    .tabcon td{
        word-break: break-all;
        word-wrap: break-word;
    }
</style>
<script type="text/javascript">

    // 删除左右两端的空格
    function trim(str){
        return str.replace(/(^\s*)|(\s*$)/g, "");
    }

    $(function() {
        // 用户反馈处理限制五百字
        $(document).on("keyup","#textts",function(e){
            if($(this).val().length >= 200){
                var str = $(this).val().substr(0, 200);
                $(this).val(str);
            }
            if(e.keyCode==13){
                e.keyCode=9;
            }
        });

        // 反馈框获取焦点,清除默认字符
        $(document).on("focus","#textts",function(){
            if(trim($(this).val())=='请填写用户反馈处理信息,该信息直接以邮件形式反馈给用户预留的QQ邮箱或者其他邮箱,请认真填写!!!' || trim($(this).val()) == '反馈内容不能为空') {
                $(this).val('');
                $(this).css('color','#333');
            }
        });

        // 失去焦点时显示提示字
        $(document).on("blur","#textts",function(){
            if($(this).val()=='') {
                $(this).val('请填写用户反馈处理信息,该信息直接以邮件形式反馈给用户预留的QQ邮箱或者其他邮箱,请认真填写!!!');
                $(this).css('color','#999');
            }
        });

        // 用户反馈处理
        $(document).on("click", ".chlie", function () {
            var innerhtml ='<div id="checkdiv"><p><textarea id="textts" style="height:140px;width:400px;margin-left:10px;color:#999;background:#fff;border:1px solid #ccc;">请填写用户反馈处理信息,该信息直接以邮件形式反馈给用户预留的QQ邮箱或者其他邮箱,请认真填写!!!</textarea></p></div>';
            var fbid = $(this).attr("fbid");
            var obj = $(this);
            // 用户反馈处理弹出框
            dialog({
                id: "unicfb",
                title: "反馈信息处理",
                content: innerhtml,
                cancel: function () {
                },
                cancelValue: "取消",
                ok: function () {
                    if(trim($('#textts').val()) == '' || trim($('#textts').val()) == '请填写用户反馈处理信息,该信息直接以邮件形式反馈给用户预留的QQ邮箱或者其他邮箱,请认真填写!!!' || trim($('#textts').val()) == '反馈内容不能为空'){
                        $('#textts').val('反馈内容不能为空');
                        $('#textts').css('color', 'red');
                        return false;
                    }
                    if($('#textts').val().length > 200){
                        return false;
                    }
                    var content = $('#textts').val();
                    $.ajax({
                        url: '/userfeedback/addprocess.html',
                        data: {"content":content, "fbid":fbid},
                        type: "post",
                        dataType: "json",
                        success: function(data){
                            if(data.ins == 'done'){
                                var chakan = "<span style=\"margin:0;color:#ccc\">已处理</span>";
                                var xiqin = "<a class=\"chakan\" fbid= \"" + fbid + "\" title=\"处理详情\" href=\"/userfeedback/view.html?fbid=" + fbid + "\" style=\"color: #f00\">详情</a>";
                                var del = "<a class=\"del\" fbid=" + fbid + ">删除</a>";
                                obj.parent().append(xiqin);
                                obj.parent().append(chakan);
                                obj.parent().append(del);
                                obj.parent().prev().prev().prev().html('已处理');
                                obj.remove();
                            }
                        }

                    })
                },
                okValue: "确定"
            }).showModal();
        });
    });

    /*
     *解决火狐下title显示不完全问题
     */
    function firefoxTitle() {
        var userAgent = navigator.userAgent;
        if (userAgent.indexOf("Firefox") > -1) {
            var obj = $(document).find("*[title]");
            var x,y,ox,oy;
            for (var o = 0, len = obj.length; o < len; o++) {
                var t = $(obj[o]).attr("title");
                var txt = $(obj[o]).text();
                if(txt='详情')continue;
                $(obj[o]).removeAttr("title");
                $(obj[o]).attr("my-title", t);
                obj[o].flag=false;
                $(obj[o]).on({
                    mouseenter:function () {
                        var that = this;
                        setTimeout(function() {
                            if($(that)[0].flag){
                                var cont = $(that).attr("my-title").replace(/</g,"＜").replace(/>/g,"＞");
                                var innerhtml = $("<span class='my-title' style='position: absolute;display: block;max-width: 500px;padding: 2px 5px;font-size: 13px;font: 12px/1.2 \"Microsoft YaHei\", sinsun;color: #575757;background: linear-gradient(#fff, #f1f2f7, #e3e5f0);border: 1px solid #7a7a7a;border-radius: 2px;word-break: break-all;word-wrap: break-word;z-index:10'>" + cont + "</span>");
                                innerhtml.css({
                                    top: y + 18 + "px",
                                    left: x + 5 + "px"
                                });
                                $("body").append(innerhtml);
                                return ;
                            }
                        }, 50);
                    },
                    mouseleave:function () {
                        $(this)[0].flag=false;
                        $("body").find(".my-title").remove();
                    },
                    mousemove:function (e) {
                        $(this)[0].flag=true;
                        x=e.pageX;
                        y=e.pageY;
                    }
                })
            }
        }
    }
    $(function () {
        firefoxTitle();
    });

    //删除操作
    $(document).on("click", ".del", function () {
        var fbid = $(this).attr('fbid');
        var obj = $(this);
        if (confirm("确定要删除该反馈吗?")) {
            $.post('/userfeedback/delFeedBack.html', {fbid: fbid}, function () {
                var del = "<span style=\"margin:0;color:#ccc\">已删除</span>";
                obj.parent().append(del);
                obj.parent().prev().prev().prev().html('已删除');
                obj.remove();
                location.reload();

            });
        }
    });


</script>
