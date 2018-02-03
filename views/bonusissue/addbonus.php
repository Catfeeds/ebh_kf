<?php $this->display('head');?>
<body>

<div style="float:right;margin-right: 15px;margin-top: 0px;font-size: 15px">
    <p>操作流程:</p>
    <p>1.依次输入数据(奖项为:一等奖，二等奖....)</p>
    <p>2.发放奖金总额等于每个学生发放的奖金数额总和<br>(奖金总额只能输入数字)</p>
    <p>3.点击添加按钮，输入学生账号，添加学生</p>
    <p>4.红色"*"为必填项</p>
</div>
<div class="tabcon tablist" style="margin-top: 0px">
    <input type="hidden" name="dopost" value="add" id="dopost" />
    <p style="margin-bottom: 15px;"><label style="font-size: 18px">奖金发放标题:&nbsp;</label><input class="inp wid350" type="text" id="title" style="width:300px"/><span style="color: red">*</span></p>
    <div style="margin-bottom: 10px;margin-top: 25px;"><label style="font-size: 18px">销售主管姓名:&nbsp;</label><input class="inp wid350" style="width:300px" type="text" name="director" id="director" /><span style="color: red">*</span></div>
    <div style="margin-bottom: 10px;margin-top: 25px;"><label style="font-size: 18px">发放奖金总额:&nbsp;</label><input class="inp wid350" style="width:300px" type="text" name="inputamount" id="inputamount" /><span style="color: red">*</span></div>
    <div style="margin-bottom: 10px;margin-top: 25px;"><label style="font-size: 18px">请先添加学生:&nbsp;</label><button class="comcheck" type="button" id="serach_account">添加</button><span style="color: red">*</span></div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tbody>
        <tr class="tabtit">
            <td width="10%">帐号</td>
            <td width="15%">姓名</td>
            <td width="15%">名次</td>
            <td width="20%">奖项</td>
            <td width="15%">积分</td>
            <td width="15%">奖金数额</td>
            <td width="10%">操作</td>
        </tr>
        </tbody>
        <tbody id='moduletbody'>
        </tbody>
    </table>
    <div style="position: fixed;bottom: 0;display: block;margin-left: 370px;"><input type="button" value="确定" class="comcheck frmsbt"/></div>

</div>


<script>
    //按账户来搜索用户信息
    $( "#serach_account" ).click(function() {

        top.artDialog({
        title: '添加学生',
        content: '<label>学生账号:&nbsp;</label><input class="inp wid180" style="width:165px;height:20px;" name="account" id="account" type="text"/>',
        width:370,
        okValue: '确定',
        ok: function () {
            var account = parent.$("#account").val();
            $.post("/bonusissue/getaccount.html", { account: account},
            function(data){
                if(data.data==false){
                    $.showmessage(
                        {
                            img: 'error',
                            message: '请输入正确帐号',
                            title: '消息通知'
                        }
                    );
                    return false;
                }
                var html = '<tr class="data"><td width="10%" class="username" name="username">'+data.data['username']+'</td>'+
                    '<td width="15%" class="realname" name="realname">'+data.data['realname']+'</td>' +
                    '<td width="15%"><input style="width:100px" type="text" name="rank"  class="rank" value=""/><span style="color: red">&nbsp;&nbsp;*</span></td>'+
                    '<td width="20%"><input style="width:100px" type="text" name="awards" class="awards" value=""/><span style="color: red">&nbsp;&nbsp;*</span></td>' +
                    '<td width="15%">'+data.data['credit']+'</td>' +
                    '<td width="15%"><input style="width:100px" type="text" name="bonusamount"  class="bonusamount" value=""/><span style="color: red">&nbsp;&nbsp;*</span></td>'+
                    '<td width="10%"><a class="del" id="del">删除</a></td>'+
                    '<td style="display:none"><input style="width:100px" type="hidden" class="uid" name="uid" value="'+data.data['uid']+'"/></td></tr>';

                if($('.data').text()==''){
                    $("#moduletbody").append( html);
                }else{
                    var aa = new Array();
                    $('.data').children('.username').each(function(){
                        aa.push($(this).text());
                    });
                    if($.inArray(data.data['username'], aa)== -1){
                        $("#moduletbody").append( html);
                    }
                }
            }, "json");
        },
        cancelValue: '取消',
            cancel: function () {}
        });
    });
        
    $(document).on("click","#del",function (){
        $(this).parent('td').parent('tr').remove();
    });


//数据提交
    $(".frmsbt").click( function () {
        var data = new Array();
        var data1 = new Array();
        $('.data').each(function () {
            data.push($(this).find('.username').text());//0 帐号
            data.push($(this).find('.realname').text());//1  真实姓名
            data.push($(this).find('.integral').text());//2   积分
            data.push($(this).find('.bonusamount').val());//3 奖金数额
            data.push($(this).find('.awards').val());//4 奖项
            data.push($(this).find('.uid').val());//5 uid
            data.push($(this).find('.rank').val());//6 名次
            data1.push(data);
            data = [];
        });
        var title = $('#title').val();
        var director = $('#director').val();
        var dopost = $('#dopost').val();
        var totalmoney = 0;
        //计算发放奖金总额
        for (var i = 0; i < data1.length; i++) {
            totalmoney += parseFloat(data1[i][3]);
        }
        for (var i = 0; i < data1.length; i++) {
            var bonusamount = data1[i][3];
            var rank = data1[i][6];
            var awards = data1[i][4];
            //alert(bonusamount);
        }
        if($('.username').text()==''){
            $.showmessage(
                {
                    img: 'error',
                    message: '请添加学生',
                    title: '消息通知'
                }
            );
            return false;
        }
        if (title == '') {
            $.showmessage(
                {
                    img: 'error',
                    message: '请输入标题',
                    title: '消息通知'
                }
            );
            return false;
        }
        if (director == '') {
            $.showmessage(
                {
                    img: 'error',
                    message: '请输入销售主管的姓名',
                    title: '消息通知'
                }
            );
            return false;
        }
        if ($('#inputamount').val() == '') {
            $.showmessage(
                {
                    img: 'error',
                    message: '请输入奖金总额',
                    title: '消息通知'
                }
            );
            return false;
        }

        if (isNaN(rank) || rank == '') {
            $.showmessage(
                {
                    img: 'error',
                    message: '请输入正确的名次',
                    title: '消息通知'
                }
            );
            return false;
        }
        if (awards == '') {
            $.showmessage(
                {
                    img: 'error',
                    message: '请填写奖项',
                    title: '消息通知'
                }
            );
            return false;
        }
        if (isNaN(bonusamount) || bonusamount == '') {
            $.showmessage(
                {
                    img: 'error',
                    message: '请输入正确的奖金数额',
                    title: '消息通知'
                }
            );
            return false;
        }

        if ($('#inputamount').val() != totalmoney) {
            $.showmessage(
                {
                    img: 'error',
                    message: '总的奖金数额和你输入的不一致！',
                    title: '消息通知'
                }
            );
            return false;

        }
        if (confirm("请确认输入的数据无误！")) {
            $.post(
                '/bonusissue/addbonus.html',
                {
                    jsonstr: data1,
                    director: director,
                    dopost: dopost,
                    title: title,
                    inputamount: $('#inputamount').val(),
                    totalmoney: totalmoney
                },
                function (data) {
                    if(data.code==0){
                       // window.parent.document.getElementById('_content_item_30').src = '/bonusissue/index.html';
                        top.art.dialog({id: 'Bonusissue'}).close();
                    }else{
                        $.showmessage(
                            {
                                img: 'error',
                                message: '添加失败',
                                title: '消息通知'
                            }
                        );
                        return false;
                    }

                },'json');
        }

    });
    
    //奖金总额限制输入数字
    $('#inputamount').keydown(function () 
    {
        if(!(event.keyCode==46)&&!(event.keyCode==8)&&!(event.keyCode==37)&&!(event.keyCode==39)){
            if(!((event.keyCode>=48&&event.keyCode<=57)||(event.keyCode>=96&&event.keyCode<=105))){
                event.returnValue=false;
            }
        }
    });
</script>
</body>
