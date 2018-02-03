<?php $this->display('head');?>
<body>
<?php show_dialog(".Jview","Jview","730","600",true,true); ?>
<form action="/log/index.html" method="get" id="form">
<div class="tabconss">
<span class="wid85">检索：</span>
<input title = "用户名" name="q" type="text"   class="inp" value="<?=empty($q) ? '请输入用户名' : $q?>" onBlur="if(this.value == ''){this.value = '请输入用户名';}" onClick="if(this.value == '请输入用户名'){this.value = '';}else {this.select();}" />
    <span class="wid85">模块：</span>
	<select name="module" class="wid140">
    	<option value="0">全部</option>
        <option value="系统登录"<?php if($module == '系统登录') echo ' selected="selected"';?>>系统登录</option>
        <option value="权限设置"<?php if($module == '权限设置') echo ' selected="selected"';?>>权限设置</option>
        <option value="用户管理"<?php if($module == '用户管理') echo ' selected="selected"';?>>用户管理</option>
        <option value="网校管理"<?php if($module == '网校管理') echo ' selected="selected"';?>>网校管理</option>
        <option value="数据审核"<?php if($module == '数据审核') echo ' selected="selected"';?>>数据审核</option>
        <option value="用户空间审核"<?php if($module == '用户空间审核') echo ' selected="selected"';?>>用户空间审核</option>
    </select>

<span class="wid85">日期从&nbsp;</span> <input name="startdate" type="text"   class="inp" onClick="WdatePicker()" value="<?=$startdate?>" /> 到 <input name="enddate" type="text"   class="inp" onClick="WdatePicker()" value="<?=$enddate?>" />
    <input type="submit" value="查询" class="comcheck"/>
</div>
</form>
<div class="ctip cmt"><b>查询描述：</b>用户名: <?=$q?> 模块：<?=$module?>  日期 从 <?=$startdate?> 到 <?=$enddate?></div>
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="tabtit">
	 <td>序号</td>
	 <td>客服用户名（姓名）</td>
	 <td>模块</td>
	 <td>操作</td>
	 <td>操作对象</td>
	 <td>ID</td>
	 <td class="leftxt">信息</td>
	 <td>IP</td>
	 <td>时间</td>
 </tr>
 <?php foreach($loglist as $key=>$log){?>
   <tr class="<?=($key%2==0)?"tabbg":""?>">
	 <td width="5%"><?=$numberstart+$key+1?></td>
	 <td><?=$log['username']?><?php if(!empty($log['realname'])) echo '(' . $log['realname'] . ')';?></td>     
	 <td><?=$log['module']?></td>
	 <td><?=$log['operation']?></td>
	 <td><?=$log['objectname']?></td>
     <td><?php if ($log['objectid']) echo $log['objectid'];?></td>
	 <td class="leftxt"><?=$log['info']?></td>
	 <td><?=$log['ip']?></td>     
	 <td><?=empty($log['dateline']) ? '' : date('Y-m-d H:i:s', $log['dateline'])?></td>
 </tr> 
  <?php }?>
  </table>
  </div>
  
  <div class="page"><?=$pagestr?></div>

</body>
</html>