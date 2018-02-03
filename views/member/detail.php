<?php show_dialog(".Jorderview","Jorderview","900","600",true,false); ?>
<div class="so">
<div class="solft">
<div class="tit">
<h3>用户详情</h3>
</div>
</div>
</div>
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php if (!empty($roomlist)) { foreach($roomlist as $room){?>
  <tr>
    <td>所在学校：<?php echo $room['crname']?>（<?php echo $room['isschool'] == 7 ? '分成网校' : '学校';?>）</td>
    <td>所在班级：<?php echo $room['classname']?></td>
    <td>加入时间：<?php echo empty($room['cdateline'])?'无':Date('Y-m-d',$room['cdateline'])?></td>
    <td>有效期：<?php echo empty($room['enddate'])?'无限制':Date('Y-m-d',$room['enddate'])?></td>
  </tr>
  <?php }}else{echo '<tr><td class="tablink" style="text-align:center">该用户未在任何网校</td></tr>';}?>
</table>
</div>

<div class="so">
<div class="solft">
<div class="tit">
<h3>已开通课程</h3>
</div>
</div>
</div>
<?php foreach($roomlist as $key => $room){ if(!empty($payfolderlist[$key])){?>
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="tabtit">
    <td colspan="3"><?php echo $room['crname']?></td>
  </tr>
  <tr class="tabtit">
    <td width="40%">课程名称</td>
    <td width="30%">开通时间</td>
    <td width="30%">截止时间</td>
  </tr>
  <?php foreach($payfolderlist[$key] as $folder){?>
  <tr>
    <td><?=$folder['foldername']?></td>
    <td><?=date('Y-m-d H:i:s',$folder['startdate'])?></td>
    <td><?=date('Y-m-d H:i:s',$folder['enddate'])?></td>
  </tr>
  <?php }?>
</table>
</div>
<div style="height:20px"></div>
<?php } }?>

<div class="so">
<div class="solft">
<div class="tit">
<h3>开通记录</h3>
</div>
</div>
<div class="titr">
<a title="开通服务" rel="/member/serviceopen.html?uid=<?=$uid?>" class="Jserviceopen" onclick="serviceopen()"><em class="addbtn">开通服务</em></a>
</div>
</div>
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="tabtit">
    <td>所属网校</td>
    <td>服务包</td>
    <td>订单名称</td>
    <td>支付时间</td>
    <td>开通方式</td>
    <td>金额</td>
    <td>操作</td>
  </tr>
  <?php foreach($orderlist as $value){?>
  <tr>
    <td><?=$value['crname']?></td>
    <td><?=$value['pname']?></td>
    <td><?=$value['ordername']?></td>
    <td><?=date('Y-m-d H:i:s', $value['paytime'])?></td>
    <td><?=$payfrom[$value['payfrom']]?></td>
    <td><?=$value['totalfee']?></td>
    <td class="tablink"><a href="/member/orderview.html?orderid=<?=$value['orderid']?>" title="订单详情" class="Jorderview" id="order_<?=$value['orderid']?>" >订单详情</a></td>
  </tr>
  <?php }?>
</table>
</div>

<div style="height:200px"></div>