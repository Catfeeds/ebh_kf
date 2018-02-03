<?php $this->display('head'); ?>
<script type="text/javascript" src="http://static.ebanhui.com/portal/js/jquery-1.7.2.min.js"></script>
<link type="text/css" href="http://static.ebanhui.com/ebh/js/jquery/jquery-ui/css/default/jquery-ui-1.8.1.custom.css"
      rel="stylesheet"/>
<script type="text/javascript"
        src="http://static.ebanhui.com/ebh/js/jquery/jquery-ui/jquery-ui-1.8.1.custom.min.js"></script>
<body>
<?php $this->display('common/player'); ?>
<div class="tabcones">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="txtlft">商品名称</td>
        <td class="txtlft" width="60">数量</td>
        <td class="txtlft">金额</td>
    </tr>
    <?php $total = 0; ?>
    <?php foreach ($orderdetails['orders'] as $orderdetail) {?>
    <tr>
        <td class="txtlft"><?= $orderdetail['gname'] ?></td>
        <td class="txtlft"><?= $orderdetail['quantity'] ?></td>
        <?php if(empty($orderdetail['totaliscore'])){ ?>
        <td class="txtlft"><?= $orderdetail['totalprice']  ?></td>
        <?php }else{ ?>
        <td class="txtlft"><?= $orderdetail['totaliscore']  ?></td>
        <?php } ?>
        <?php $total += $orderdetail['totalprice']; ?>
    </tr>
    <?php }?>
     <tr>
        <td class="txtlft" colspan="2">总计</td>
        <?php if(!empty($orderdetails['orders'][0]['totaliscore'])){  ?>
        <td class="txtlft"><?= $orderdetails['orders'][0]['totaliscore'] ?></td>
        <?php }else{ ?>
        <td class="txtlft"><?= $total ?></td>
        <?php } ?>
    </tr>
</table>
<br >
<hr style="height:1px;border:none;border-top:1px dashed #0066CC;" />
<style type="text/css">
    .txt{
        color: rgb(7, 85, 135);
        text-align:center;
    }
</style>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="8" class="txt" style="font-size:150%;">卖家信息</td>
    </tr>
    <tr>
        <td class="txt" width="10%">用户名</td>
        <td class="txt" width="15%"><?= $orderdetails['seller']['username'] ?></td>
        <td class="txt" width="10%">真实姓名</td>
        <td class="txt" width="15%"><?= $orderdetails['seller']['realname'] ?></td>
        <td class="txt" width="9%">IP地址</td>
        <td class="txt" width="16%"><?= $orderdetails['seller']['lastloginip'] ?></td>
        <td class="txt" width="10%">IP归属地</td>
        <td class="txt" width="15%"><?= $orderdetails['seller']['lastloginzone'] ?></td>
    </tr>
    <tr>
        <td class="txt">卖家地址</td>
        <td class="txt" colspan="3"><?= $orderdetails['seller']['address'] ?></td>
        <td class="txt">卖家电话</td>
        <td class="txt" colspan="3"><?= $orderdetails['seller']['mobile'] ?></td>
    </tr>
</table>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="8" class="txt" style="font-size:150%;">买家信息</td>
    </tr>
    <tr>
        <td class="txt" width="10%">用户名</td>
        <td class="txt" width="15%"><?= $orderdetails['customer']['username'] ?></td>
        <td class="txt" width="10%">真实姓名</td>
        <td class="txt" width="15%"><?= $orderdetails['customer']['realname'] ?></td>
        <td class="txt" width="9%">IP地址</td>
        <td class="txt" width="16%"><?= $orderdetails['customer']['lastloginip'] ?></td>
        <td class="txt" width="10%">IP归属地</td>
        <td class="txt" width="15%"><?= $orderdetails['customer']['lastloginzone'] ?></td>
    </tr>
    <tr>
        <td class="txt">买家地址</td>
        <td class="txt" colspan="3"><?= $orderdetails['customer']['address'] ?></td>
        <td class="txt">买家电话</td>
        <td class="txt" colspan="3"><?= $orderdetails['customer']['mobile'] ?></td>
    </tr>
    <tr>
    <td colspan="8" align="center"><input type="button" value="关闭" class="combtn cbtn_4 form_submit"/></td>
    </tr>
</table>
</div>
<script type="text/javascript">
    $(function () {
        $(".form_submit").click(function () {
            top.art.dialog({id: 'Jview'}).close();
        });

    })
</script>
</body>
</html>