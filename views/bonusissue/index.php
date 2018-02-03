<?php $this->display('head');?>

<body>
<div class="titr" >
    <a title="奖金发放" class="Bonusissue" href="/bonusissue/addbonus.html"><em class="addbtn">奖金发放</em></a>
</div>
<form action="/bonusissue/index.html" method="get" id="form">
    <div class="tabconss">
        <span class="wid85">检索：</span>
        <input name="q" type="text" class="inp wid140" value="<?php echo $request['q'];?>" />
        <input type="submit" value="查询" class="comcheck"  /><label>按奖金标题查询</label>
    </div>
</form>

<?php show_dialog(".Bonusissue","Bonusissue","850","600",true,true);
      show_dialog(".Detail","Detail","800","500",true,false);
?>
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="tabtit">
        <td  class="leftxt" width="20%">奖金发放标题</td>
        <td width="10%">操作人</td>
        <td width="10%">奖金总额</td>
        <td  class="leftxt" width="10%">销售主管</td>
        <td  width="10%"> 操作时间</td>
        <td  width="10%">操作记录</td>
    </tr>
    <?php if(!empty($recordlist)) {
        foreach($recordlist as $key=>$val){?>
        <tr class="<?php echo ($key%2==0)?"tabbg":""?>">
            <td  class="leftxt"><?php echo $val['title']?></td>
            <td><?php echo $val['operator']?></td>
            <td><?php echo $val['totalmoney']?></td>
            <td class="leftxt"><?php echo $val['director']?></td>
            <td><?php echo date("Y-m-d H:i:s ",$val['dateline'])?></td>
            <td class="tablink"> <a class="Detail" href="/bonusissue/recordview.html?bid=<?php echo $val['bid']?>" title="记录详情">查看记录</a></td>
        </tr>
        <?php } ?>
    <?php }?>
</table>
<div class="page"><?php echo $pagestr?></div>
</div>
<script>

</script>
</body>
