<?php $this->display('head');?>
<script type="text/javascript" src="/static/artDialog/artDialog.js?skin=blue"></script>
<style>
    tr td:first-child{text-align:right;width:200px;}
    td.remark{height:200px;vertical-align:top;}
    div.close{margin-top:20px;text-align:center;}
</style>
<body>
<div class="tabcones">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>所属网校：</td><td><?=$jsauth['crname']?></td>
        </tr>
        <tr><td>处理时间：</td><td><?=!empty($jsauth['admin_dateline']) ? date('Y-H-d H:i:s', $jsauth['admin_dateline']) : '---'?></td><tr>
        <tr><td class="remark">申请备注：</td><td class="remark"><?=$jsauth['kfnotes']?></td><tr>
        <tr><td class="remark">身份审核备注：</td><td class="remark"><?=!empty($jsauth['admin_remark']) ? $jsauth['admin_remark'] : '---'?></td><tr>
    </table>
    <div class="close"><input type="button"  value="关闭" class="combtn cbtn_4 form_submit"   /></div>
</div>
</body>
<script type="text/javascript">
    $(function(){
        $(".form_submit").click(function(){
            top.art.dialog({id: 'Jview'}).close();
        });
    })
</script>
</html>