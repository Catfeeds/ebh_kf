<?php $this->display('head');?>
<body>
<div class="tabcones">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td colspan="4" style="color:#075587;text-align:center;" class="tabtit">订单支付信息</td></tr>
  <tr>
    <td class="txtlft">订单名称：</td>
    <td><?=$order['ordername']?></td>
    <td class="txtlft">订单卡号：</td>
    <td><?=$order['ordernumber']?></td>    
  </tr>
  <tr>
    <td class="txtlft">交易时间：</td>
    <td><?=date("Y-m-d H:i:s",$order['dateline'])?></td>    
    <td class="txtlft">账号：</td>
    <td><?=$order['username']?></td>    
  </tr>
 <tr>
    <td class="txtlft">真实姓名：</td>
    <td><?=$order['realname']?></td>    
    <td class="txtlft">支付方式：</td>
    <td><?=$payfrom[$order['payfrom']]?></td>    
  </tr>
  <tr>
    <td class="txtlft">金额(元)：</td>
    <td><?=$order['totalfee']?></td>    
    <td class="txtlft">所属网校：</td>
    <td><?=$order['crname']?></td>    
  </tr>
  <tr>
    <td class="txtlft">下单ip：</td>
    <td><?=$order['ip']?></td>    
    <td class="txtlft">支付ip：</td>
    <td><?=$order['payip']?></td>    
  </tr> 
  <tr>
    <td class="txtlft">购买编号：</td>
    <td><?=$order['buyer_id']?></td>    
    <td class="txtlft">购买人信息：</td>
    <td><?=$order['buyer_info']?></td>    
  </tr>  
  <tr>
    <td class="txtlft">备注：</td>
    <td colspan="3"><?=$order['remark']?></td>    
  </tr> 
  <tr>
  <!-- <td class="txtlft">订单明细：</td> -->
  <td colspan="4"><div class="tablist"><table  class="selectcontainer" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr><td colspan="8" >订单明细</td></tr>
  <tr><td>序号</td><td>明细名称</td><td>套餐名称</td><td>产品名称</td><td>课程名称</td><td>费用</td><td>时长</td></tr>
    <?php if($order['detaillist']){foreach($order['detaillist'] as $key=> $detail){?>
    <tr>
  	<td><?=($key+1)?></td>
  	<td><?=$detail['oname']?></td>
  	<td><?=$detail['iname']?></td>
  	<td><?=$detail['pname']?></td>
  	<td><?=$detail['foldername']?></td>
  	<td><?=$detail['fee']?></td>	
  	<td><?php if($detail['omonth']){echo $detail['omonth']." 月";}elseif($detail['oday']){echo $detail['oday']." 天";}?></td>	
  	</tr>
  	<?php }}?>
    </table></div></td>
  </tr>
	<tr><td colspan="4" align="center"><input type="button"  value="关闭" class="combtn cbtn_4 form_submit"   /></td></tr>		 
 </table>
 

</div>


<script type="text/javascript">
$(function(){
	$(".form_submit").click(function(){
		top.art.dialog({id: 'Jorderview'}).close();
	});
	
})
</script>
</body>
</html>    