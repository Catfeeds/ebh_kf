<?php $this->display('head'); ?>
<body>
<style>
    .tabcones td {
        padding-left: 25px;
        word-break: break-all;
    }
</style>
<div class="tabcones" >
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="15%">客服ID:</td>
            <td width="85%"><?= $info['hid'] ?></td>
        </tr>
        <tr>
            <td>客服姓名:</td>
            <td><?= $info['hname'] ?></td>
        </tr>
        <tr>
            <td>客服IP:</td>
            <td><?= $info['hip'] ?></td>
        </tr>
        <tr>
            <td>处理内容:</td>
            <td title="<?= $info['content'] ?>"><?= $info['hcontent'] ?></td>
        </tr>
        <tr>
            <td>处理时间:</td>
            <td><?= $info['hdateline'] ?></td>
        </tr>
        <tr>
            <td colspan="2" align="center"><input type="button" value="关闭" class="combtn cbtn_4 form_submit"/></td>
        </tr>
    </table>
</div>
</body>
<script type="text/javascript">
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
    })

    $(function () {
        $(".form_submit").click(function () {
            top.art.dialog({id: 'chakan'}).close();
        });
    })
</script>
</html>