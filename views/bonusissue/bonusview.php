<?php $this->display('head');?>
<body>
<div class="tabcon tablist" >
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="tabtit">
            <td width="10%">帐号</td>
            <td width="10%">姓名</td>
            <td width="5%">名次</td>
            <td width="15%">奖项</td>
            <td width="10%">获得奖金</td>
            <td width="10%">发放前金额</td>
            <td width="10%">发放后金额</td>
            <td width="10%">积分</td>
            <td width="25%">发放时间</td>
        </tr>
       <!--$value[0]->帐号 $val[1]->姓名 $val[2]   -->
        <?php foreach($detaillist as $key=>$value){?>
        <tr class="<?php echo ($key%2==0)?"tabbg":""?>">
            <td><?php echo $value['0'] ?></td>
            <td><?php echo $value['1']?></td>
            <td><?php echo $value['6'] ;?></td>
            <td><?php echo $value['4']?></td>
            <td><?php echo $value['3']?></td>
            <td><?php echo $value['prebalance']?></td>
            <td><?php echo $value['balance']?></td>
            <td><?php echo $value['credit']?></td>
            <td><?php echo date("Y-m-d H:i:s",$value['dateline'])?></td>
        </tr>
        <?php }?>
    </table>
    <div style="display: block;margin-left:370px;margin-top: 25px;"> <input type="button" value="关闭" class="comcheck" id="cancle"/></div>
</div>
<script>
    //关闭弹层
    $("#cancle").click( function () {
        top.art.dialog({id: 'Detail'}).close();
    });

</script>
</body>