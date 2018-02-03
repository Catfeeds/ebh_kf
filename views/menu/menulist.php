<?php $this->display('head');?>
<body>
<?php show_dialog(".Medit","Medit","730","310",true,false); ?>
<?php show_dialog(".Madd","Madd","730","310",true,false); ?>
<div style="height: 40px;background-color: #ECF0F1; margin-top: 10px">
    <div class="titr" style="float: left;margin-top: 5px;">
        <a title="添加菜单" class="Madd" href="/menu/menuadd.html"><em class="addbtn">添加菜单</em></a>
    </div>
</div>
<div class="tabcon tablist" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr class="tabtit">
	 <td style="padding-left: 10px;">ID</td>
	 <td style="width: 200px;">名称</td>
	 <td>上级ID</td>
     <td>CODE标识</td>
	 <td style="width: 200px;">路径(URL)</td>
	 <td>排序号</td>
	 <td colspan="3">操作</td>
 </tr>
<?php if(!empty($menus)){?>
    <?php foreach ($menus as $menu){?>
		<tr class="">
            <td style="padding:12px 20px;"><?=$menu['menuid']?></td>
			<td class="leftxt" style="width: 200px;">
                <img class="menuimg" src="/static/images/ebh/menu/menu_<?=$menu['code']?>.png" onerror="this.onerror=null; this.src='/static/images/ebh/menu/menu.png'" /><?=$menu['title']?>
            </td>
			<td><?=$menu['parentid']?></td>
            <td><?=$menu['code']?></td>
			<td style="width: 200px;"><?=$menu['codepath']?></td>
			<td class="center"><?=$menu['displayorder']?></td>
            <td><a title="添加菜单" class="Madd" href="/menu/menuadd.html?menuid=<?=$menu['menuid']?>" style="color: red">添加</a></td>
			<td><a title="编辑菜单" class="Medit" href="/menu/menuedit.html?menuid=<?=$menu['menuid']?>" style="color: red">编辑</a></td>
			<td><a class="delete" href="javascript:;" data-id=<?=$menu['menuid']?> onclick="delmenu(<?=$menu['menuid']?>)" style="color: red">删除</a></td>
		</tr>
        <?php if(!empty($menu['child'])){?>
            <?php
                $children = $menu['child'];
                foreach($children as $item){
            ?>
            <tr class="">
                <td style="padding:0 20px;""><?=$item['menuid']?></td>
                <td  class="leftxt" style="width: 200px;">
                    <span><?=str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;|-----',1)?></span>
                    <img class="menuimg"  src="/static/images/ebh/menu/menu_<?=$item['code']?>.png" onerror="this.onerror=null; this.src='/static/images/ebh/menu/menu.png'" /><?=$item['title']?>
                </td>
                <td><?=$item['parentid']?></td>
                <td><?=$item['code']?></td>
                <td style="width: 200px;"><?=$item['codepath']?></td>
                <td class="center"><?=$item['displayorder']?></td>
                <td><span title="添加菜单" style="color: grey">添加</span></td>
                <td><a title="编辑菜单" class="Medit" href="/menu/menuedit.html?menuid=<?=$item['menuid']?>" style="color: red">编辑</a></td>
                <?php
                    if($item['code']=='menu') {
                        echo "<td><span title='删除' style='color: grey'>删除</span></td>";
                    } else {
                        echo "<td><a class='delete' href='javascript:;' style='color: red' data-id=".$item['menuid']." onclick=delmenu(".$item['menuid'].")>删除</a></td>";
                    }
                ?>
            </tr>
            <?php }?>
        <?php }?>
    <?php }?>
<?php }?>
  </table>
  </div>
<script type="text/javascript">
    function delmenu(menuid) {
        if (confirm('您确定要删除这个菜单吗?')) {
            $.ajax({
             type: "POST",
             url: "/menu/ajax_remove_menu.html",
             data: {'menuid':menuid,'remove_menu':1},
             dataType: "json",
             success: function(data) {
                if (data.status==0) {
                    $.showmessage({
                        img : 'success',
                        message:data.msg,
                        title:'消息提醒',
                        callback: function(){location.reload();}
                    });
                } else {
                    $.showmessage({
                        img : 'error',
                        message:data.msg,
                        title:'消息提醒',
                    });
                }
             }
           });
        }
    }
</script>
</body>
</html>