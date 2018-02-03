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
<input type="hidden" name="add_menu" value="1" />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="txtlft" style="width:25%"><span style="color:red;"> *&nbsp;</span>父级菜单：</td>
            <td>
                <select name="parentid" style="width:220px">
                    <option value="0">顶级菜单</option>
                <?php foreach ($menus as $item){?>
                    <option value="<?=$item['menuid']?>"><?=$item['title']?><?=$item['codepath']?></option>
                <?php }?>
                </select>
            </td>
        </tr>
            <td class="txtlft"><span style="color:red;"> *&nbsp;</span>排序号：</td>
            <td><input type="text" value="0" name="displayorder" class="inp a40 displayorder" onblur="checkdisplayorder()" /><em id="displayorder_msg"></em></td>
        </tr>
        <tr>
            <td class="txtlft"><span style="color:red;"> *&nbsp;</span>CODE标识：</td>
            <td><input type="text" name="code" class="inp a40 code" onblur="checkcode()" /><em id="code_msg"></em></td>
        </tr>
        <tr>
            <td class="txtlft"><span style="color:red;"> *&nbsp;</span>路径(URL)：</td>
            <td><input type="text" name="codepath" class="inp a40 codepath" onblur="checkcodepath()" /><em id="codepath_msg"></em></td>
        </tr>
        <tr>
            <td class="txtlft"><span style="color:red;"> *&nbsp;</span>菜单名称：</td>
            <td><input type="text" name="title" class="inp a40 title" onblur="checktitle()" /><em id="title_msg"></em></td>
        </tr>
    </table>
</div>
<div style="text-align:center;margin-top:10px"><input type="button"  value="添加" class="combtn cbtn_4 formsbt" /></div>
</form>
<script type="text/javascript">
    var check = true;
    /**
     * 验证CODE标识是否存在且唯一
     */
    function checkcode() {
        var code = $.trim($(".code").val());
        if(code==''){
            $("#code_msg").html("CODE标识不能为空");
            $("#code_msg").attr('class','emacuo');
            check = false;
        } else if((!code.match(/^[a-zA-Z][a-z0-9A-Z]{2,19}$/))) {
            $("#code_msg").html("3~20个以字母开头的字母或数字");
            $("#code_msg").attr('class','emails');
            check = false;
        } else {
            $.ajax({
                type:"post",
                url:"<?=geturl('menu/isnameexist')?>",
                dataType:'json',
                data:{name:'code',namevalue:code},
                async:false,
                success:function(data){
                    console.log(data);
                    if(data.status === 0){
                        $("#code_msg").html("CODE标识已存在");
                        $("#code_msg").attr('class','emacuo');
                        check = false;
                    } else {
                        $("#code_msg").html("");
                        $("#code_msg").attr('class','emadui');
                    }
                }
            });
        }
    }
    /**
     * 验证路径(URL)是否存在且唯一
     */
    function checkcodepath() {
        var codepath = $.trim($(".codepath").val());
        if(codepath==''){
            $("#codepath_msg").html("路径(URL)不能为空");
            $("#codepath_msg").attr('class','emacuo');
            check = false;
        } else if((!codepath.match(/^[a-zA-Z][a-z0-9A-Z/]{2,49}$/))) {
            $("#codepath_msg").html("3~50个字母开头的字母、数字或斜杠字符");
            $("#codepath_msg").attr('class','emails');
            check = false;
        } else {
            $.ajax({
                type:"post",
                url:"<?=geturl('menu/isnameexist')?>",
                dataType:'json',
                data:{name:'codepath',namevalue:codepath},
                async:false,
                success:function(data){
                    console.log(data);
                    if(data.status === 0){
                        $("#codepath_msg").html("路径(URL)已存在");
                        $("#codepath_msg").attr('class','emacuo');
                        check = false;
                    } else {
                        $("#codepath_msg").html("");
                        $("#codepath_msg").attr('class','emadui');
                    }
                }
            });
        }
    }
    /**
     * 验证排序号是否存在
     */
    function checkdisplayorder() {
        var displayorder = $.trim($(".displayorder").val());
        if(displayorder==''){
            $("#displayorder_msg").html("排序号不能为空");
            $("#displayorder_msg").attr('class','emacuo');
            check = false;
        } else if(!displayorder.match(/^\d+$/)) {
            $("#displayorder_msg").html("请输入0或正整数");
            $("#displayorder_msg").attr('class','emails');
            check = false;
        } else {
            $("#displayorder_msg").html("");
            $("#displayorder_msg").attr('class','emadui');
        }
    }
    /**
     * 验证菜单名称是否存在且符合规范
     */
    function checktitle() {
        var title = $.trim($(".title").val());
        if(title==''){
            $("#title_msg").html("菜单名称不能为空");
            $("#title_msg").attr('class','emacuo');
            check = false;
        }else if(!title.match(/^[0-9A-Za-z\u4e00-\u9fa5]{2,20}$/)) {
            $("#title_msg").html("2~20个中英文字符或数字");
            $("#title_msg").attr('class','emails');
            check = false;
        } else {
            $("#title_msg").html("");
            $("#title_msg").attr('class','emadui');
        }
    }
    $(function(){
        $('.formsbt').click(function(){
            check = true;
            checktitle();
            checkdisplayorder();
            checkcode();
            checkcodepath();
            if(check){
                $('#form').submit();
            }
        });
    });
</script>
</body>
</html>    