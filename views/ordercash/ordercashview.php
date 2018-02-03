<?php $this->display('head');?>
<script type="text/javascript" src="http://static.ebanhui.com/portal/js/jquery-1.7.2.min.js"></script>
<link type="text/css" href="http://static.ebanhui.com/ebh/js/jquery/jquery-ui/css/default/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="http://static.ebanhui.com/ebh/js/jquery/jquery-ui/jquery-ui-1.8.1.custom.min.js"></script>
<body>
<div class="tabcones">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
  	<td class="txtlft">汇款单：</td>
  	<td colspan="3"><img src="<?php echo $prepath.$detail['imgpath']?>" /></td>
  </tr>
  <tr>
    <td class="txtlft">账号：</td>
    <td><?php echo $detail['username']?></td>    
    <td class="txtlft">用户：</td>
    <td><?php echo !empty($detail['realname']) ? $detail['realname'] : '无'?></td>    
  </tr>
  <tr>
    <td class="txtlft">所属网校：</td>
    <td><?php echo $detail['crname']?></td>
    <td class="txtlft">来源IP：</td>
    <td><?php echo $detail['ip']?></td>
  </tr>
  <tr>
  	<td class="txtlft">所选服务项：</td>
  	<td colspan="3"><?php echo implode('<br><br>', $serlist)?></td>
  </tr>
  <tr>
    <td class="txtlft">汇款金额：</td>
    <td> <?php echo $detail['remit']?></td>
    <td class="txtlft">联系方式：</td>
    <td> <?php echo $detail['contact']?></td>
  </tr>
  <tr>
    <td class="txtlft">其他说明：</td>
    <td><?php echo $detail['remark']?></td>    
    <td class="txtlft">提交时间：</td>
    <td><?php echo date('Y-m-d H:i:s',$detail['dateline'])?></td>    
  </tr>
  <tr>
    <td class="txtlft">管理员审核状态：</td>
    <td class="cRed"><?php if($detail['admin_status']==1){echo '已通过';}elseif($detail['admin_status']==2){ echo '未通过';}else{echo '未审核';}?></td>    
    <td class="txtlft">审核备注：</td>
    <td><?php echo $detail['admin_remark']?></td>    
  </tr>
  <tr>
    <td class="txtlft">管理员IP：</td>
    <td><?php echo $detail['admin_ip']?></td>    
    <td class="txtlft">审核处理时间：</td>
    <td><?php echo !empty($detail['admin_dateline']) ? date('Y-m-d H:i:s',$detail['admin_dateline']) : ''?></td>    
  </tr>
    <tr>
        <td class = "txtlft">审核人</td>
        <td><?php echo $detail['checkname'] ? $detail['checkname'] : '--'?></td>
    </tr>
  <tr><td colspan="4" align="center"><input type="button"  value="关闭" class="combtn cbtn_4 form_submit"/></td></tr>
 </table>
</div>
<script type="text/javascript">
$(function(){
	$(".form_submit").click(function(){
		top.art.dialog({id: 'Jview'}).close();
	});
	
})
</script>
 </body>
</html>