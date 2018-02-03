<?php $this->display('head');?>
<body>

<div>
    <input class="inp wid180" name="account" id="account"type="text" placeholder="请输入学生帐号"/> <button class="comcheck" type="button" id="serach_account">添加</button>
</div>
<div class="tabcon tablist" style="margin-top: 40px">
    <input type="hidden" name="dopost" value="add" id="dopost" />
    <p style="margin-bottom: 15px;"><label style="font-size: 18px">标题:</label><input class="inp wid350" type="text" id="title"/></p>
    <div style="margin-bottom: 10px;margin-top: 25px;"><label style="color: red">销售主管姓名:</label><input style="width:100px" type="text" name="director" id="director" /></div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tbody>
        <tr class="tabtit">
            <td width="10%">帐号</td>
            <td width="15%">姓名</td>
            <td width="15%">名次</td>
            <td width="20%">奖项</td>
            <td width="15%">积分</td>
            <td width="15%">奖金数额</td>
            <td width="15%">操作</td>
       </tr>
        </tbody>
        <tbody id='moduletbody'>
        </tbody>
    </table>
        <div style="display: block;margin-left: 230px;margin-top: 10px;"><input type="button" value="确定" class="comcheck frmsbt"/> <input type="button" value="取消" class="comcheck" id="cancle"/></div>

</div>


<script>
    //按账户来搜索用户信息
    $( "#serach_account" ).click(function() {
        var account = $("#account").val();
        $.post("/member/getaccount.html", { account: account},
            function(data){
            if(data.data==false){
                return false;
            }
            var html = '<tr class="data"><td width="10%" class="username" name="username">'+data.data['username']+'</td><td width="15%" class="realname" name="realname">'+data.data['realname']+'</td>' +
                    '<td width="15%"><input style="width:100px" type="text" name="rank"  class="rank" value=""/></td>'+
                    '<td width="25%"><input style="width:100px" type="text" name="awards" class="awards" value=""/></td>' +
                    '<td width="15%"><input style="width:100px" type="text" name="integral" class="integral" value=""/></td>' +
                    '<td width="15%"><input style="width:100px" type="text" name="bonusamount"  class="bonusamount" value=""/></td>'+
                    '<td  width="15%"><a class="del" id="del">删除</a><td>'+
                    '<td><input style="width:100px" type="hidden"  class="uid" name="uid" value="'+data.data['uid']+'"/><td></tr>'

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



    });
    $(document).on("click","#del",function (){
        $(this).parent('td').parent('tr').remove();
    });

    //关闭弹层
    $("#cancle").click( function () {
        top.art.dialog({id: 'Bonusissue'}).close();
    });

    $(".frmsbt").click( function () {
        var data = new Array();
        var data1 = new Array();
        $('.data').each(function () {
            data.push($(this).find('.username').text());//0 帐号
            data.push($(this).find('.realname').text());//1  真实姓名
            data.push($(this).find('.integral').val());//2   积分
            data.push($(this).find('.bonusamount').val());//3 奖金数额
            data.push($(this).find('.awards').val());//4 奖项
            data.push($(this).find('.uid').val());//5 uid
            data.push($(this).find('.rank').val());//6 名次
            data1.push(data);
            data=[];
        });
        var title=$('#title').val();
        var director=$('#director').val();
        var dopost=$('#dopost').val();
        var totalmoney=0;
        //计算发放奖金总额
        for (var i=0;i<data1.length;i++){
            totalmoney+=parseInt(data1[i][3]);
        }
        for( var i=0;i<data1.length;i++){
            var bonusamount=data1[i][3];
            var rank=data1[i][6];
            var integral=data1[i][2];
            var awards=data1[i][4];
            //alert(bonusamount);
        };
        //alert(bonusamount);
        if($('#account').val()==''){
            $.showmessage(
                {
                    img: 'error',
                    message: '请输入帐号',
                    title: '消息通知'
                }
            );
            return false;
        }
        if(title==''){
            $.showmessage(
                {
                    img: 'error',
                    message: '请输入标题',
                    title: '消息通知'
                }
            );
            return false;
        }
        if(director==''){
            $.showmessage(
                {
                    img: 'error',
                    message: '请输入销售主管的姓名',
                    title: '消息通知'
                }
            );
            return false;
        }
        if(isNaN(rank)|| rank==''){
            $.showmessage(
                {
                    img: 'error',
                    message: '请输入正确的名次',
                    title: '消息通知'
                }
            );
            return false;
        }
        if(awards=='' ){
            $.showmessage(
                {
                    img: 'error',
                    message: '请填写奖项',
                    title: '消息通知'
                }
            );
            return false;
        }
        if(isNaN(integral)|| integral==''){
            $.showmessage(
                {
                    img: 'error',
                    message: '请输入正确的积分',
                    title: '消息通知'
                }
            );
            return false;
        }
        if(isNaN(bonusamount)|| bonusamount==''){
            $.showmessage(
                {
                    img: 'error',
                    message: '请输入正确的奖金数额',
                    title: '消息通知'
                }
            );
            return false;
        }

        //console.log(totalmoney);
        $.post('/member/bonusissue.html',{jsonstr:data1,director:director,dopost:dopost,title:title,totalmoney:totalmoney},
            function(){
                top.art.dialog({id: 'Bonusissue'}).close();
                location.href='/member/bonus.html';

        })


    })
</script>
</body>
