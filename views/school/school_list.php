<?php $this->display('head');?>
<body>
    <div class="titr">
      <a title="添加网校" class="Jadd" href="/school/add.html"><em class="addbtn">添加网校</em></a>
    </div>
  <?php show_dialog(".Jadd","Jadd","1000","720",true,false); ?>
  <?php show_dialog(".Jedit","Jedit","1000","720",true,false); ?>
  <?php show_dialog(".Jview","Jview","730","600",true,false); ?>
  <form action="/school/index.html" method="get" id="form">
    <div class="tabconss">
        <span class="wid110">网校名称或域名：</span>
        <input title = "网校名称" name="q" type="text"   class="inp wid140" value="<?=$q?>"/>
        <span>所属分类：</span>
        <select name="catid" id="catid">
        	<option value="0">所有分类</option>
        	<?php if (is_array($categorylist)){
        		foreach($categorylist as $v)
        		{
					if ($catid == $v['catid']){
					{
						echo '<option value='.$v['catid'] .' selected="selected">'.$v['name'].'</option>';
					}

					}else{
						echo '<option value='.$v['catid'] .'>'.$v['name'].'</option>';
					}
        		}
        	}?>
        </select>
        <span>是否存在电视版：</span>
		<select name="hastv">
		    <option value="-1">全部</option>
		    <option value="1" <?php if ($hastv == 1) echo 'selected="selected"';?>>存在</option>
		    <option value="0" <?php if ($hastv == 0) echo 'selected="selected"';?>>不存在</option>
		</select>
		<span>学校类型：</span>
		<select name="ctype">
		    <option value="-1">全部</option>
		    <option value="0" <?php if ($ctype == 0) echo 'selected="selected"';?>>默认网校</option>
		    <option value="1" <?php if ($ctype == 1) echo 'selected="selected"';?>>新注册的普通网校</option>
		    <option value="2" <?php if ($ctype == 2) echo 'selected="selected"';?>>新注册的分成网校</option>
		</select>
		<input type="submit" name="input" value="查询" class="comcheck"/>
    </div>
  </form>
  <div class="ctip cmt"><b>查询描述：</b>
	网校名称或域名: <?=$q?>
	所属分类：<?=$catname?>
	是否存在电视版：<?php switch($hastv){
		case -1:
			echo '全部';
			break;
		case 0:
			echo '不存在';
			break;
		case 1:
			echo '存在';
			break;
		};?>
	学校类型：<?php switch($ctype){
		case -1:
			echo '全部';
			break;
		case 0:
			echo '默认网校';
			break;
		case 1:
			echo '新注册的普通网校';
			break;
		case 1:
			echo '新注册的分成网校';
			break;
		};?>
  </div>
  <div class="tabcon tablist" >
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr class="tabtit">
        <td class="leftxt" width="25%">网校名称</td>
        <td width="10%">网校域名</td>
        <td width="10%">网校crid</td>
        <td width="5%">学生数</td>
        <td class="leftxt" width="20%">管理员</td>
        <td width="15%">手机</td>
        <td width="10%">建立时间</td>
        <td width="5%">状态</td>
        <td width="10%">操作</td>
      </tr>

      <?php foreach($classroomList as $key=>$cr){?>
      <tr id="item<?=$cr['crid']?>" onclick="showDetail(<?php echo $cr['crid']?>)" class="<?=($key%2==1)?"tabbg":""?>">
        <td class="pointer leftxt"><?php echo $cr['crname']?></td>
        <td class="pointer"><?php echo $cr['domain']?></td>
        <td class="pointer"><?php echo $cr['crid']?></td>
        <td class="pointer"><?php echo $cr['stunum']?></td>
        <td class="pointer leftxt"><?php if (!empty($cr['username'])) echo $cr['username'];
        	if (!empty($cr['realname'])) echo '('. $cr['realname'] .')';?>
        </td>
        <td class="pointer"><?=$cr['mobile'];?></td>
        <td class="pointer"><?php echo date('Y-m-d',$cr['begindate'])?></td>
        <td class="pointer"><?php if ($cr['status'] == 1) echo '<span>正常</span>'; else echo '<span>锁定</span>';?></td>
        <td><a class="Jadd" href="/school/edit.html?crid=<?=$cr['crid']?>" title="修改" style="color:#F00;text-decoration:none;">修改</a>&nbsp;<a  href="http://<?php echo $cr['domain']?>.ebh.net" title="首页" target="_blank" style="color:#F00;text-decoration:none;">首页</a></td>
      </tr>
      <?php }?>

    </table>
  </div>
  
  <div class="page"><?=$pagestr?></div>
  <div id="detail" name="detail"></div>
  <script>

    var current_uid='';
    function showDetail(uid)
    {
      if (current_uid != '')
      {
    // $("#item"+current_uid).removeClass("current_user");
    $("#item"+current_uid+" td").css('background-color','');
    $("#item"+current_uid).mouseover(function(){
      $(this).find('th,td').css('background-color', '#EAEAEA');
    });
    $("#item"+current_uid).mouseout(function(){
      $(this).find('th,td').css('background-color', '');
    });
  }
  current_uid = uid;
  // $("#item"+uid).addClass("current_user");
  $("#item"+uid+" td").css("background-color", "#0CF");
  $("#item"+uid).mouseover(function(){
    $(this).find('th,td').css('background-color', '#0CF');
  });
  $("#item"+uid).mouseout(function(){
    $(this).find('th,td').css('background-color', '#0CF');
  });

  $.post("<?php echo geturl('school/detail');?>",{'crid':uid},function(data){
   $("#detail").html(data);
 },"html");
}
$(function(){
  current_uid = '<?=!empty($classroomList)?$classroomList[0]['crid']:""?>';
  if (current_uid != '') showDetail(current_uid); 
});
</script>

</body>
</html>