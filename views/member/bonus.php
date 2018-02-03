<?php $this->display('head');?>

<body>
<style type="text/css">
    .bonus {
        background: #ecf0f1;
        border-radius: 3px;
        margin-bottom: 10px;
        margin-top: 10px;
        min-height: 20px;
        padding: 10px;
    }
    .addbtn {
        display: block;
        margin-left: 10px
    }
</style>
<div class="bonus" >
    <a title="奖金发放" class="Bonusissue" href="/member/bonusissue.html"><em class="addbtn">奖金发放</em></a>
</div>
<?php show_dialog(".Bonusissue","Bonusissue","800","600",true,false);
      show_dialog(".Detail","Detail","850","500",true,false);
?>
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="tabtit">
        <td width="20%">标题</td>
        <td width="10%">操作人</td>
        <td>奖金总额</td>
        <td>销售主管</td>
        <td>操作时间</td>
        <td>操作记录</td>
    </tr>
    <?php foreach($recordlist as $key=>$val){?>
        <tr class="<?php echo ($key%2==0)?"tabbg":""?>">
            <td><?php echo $val['title']?></td>
            <td><?php echo $val['operator']?></td>
            <td><?php echo $val['totalmoney']?></td>
            <td><?php echo $val['director']?></td>
            <td><?php echo date("Y-m-d ",$val['dateline'])?></td>
            <td class="tablink"> <a class="Detail" href="/member/recordview.html?bid=<?php echo $val['bid']?>" title="记录详情">查看记录</a></td>
        </tr>
    <?php }?>
</table>
<div class="page"><?php echo $pagestr?></div>
</div>
<script>

</script>
</body>
