<?php $this->display('head');?>
<?php $grade=array("未设置年级","小学二年级","小学三年级","小学四年级","小学五年级","小学六年级","小学七年级","初中一年级","初中二年级","初中三年级","高中一年级","高中二年级","高中三年级",""=>"未设置年级")?>
<body>
<div class="tabcones">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="txtlft" >课程名称：</td>
    <td><?php echo $folder['foldername']?></td>
   </tr>
    <form action="/school/cchoosePost.html" method="post">
     <tr>
    <td class="txtlft">所在年级：</td>
    <td><select id="grade" name="grade">
      <option value="0" selected>未设置年级</option>
      <option value="1"<?=$folder['grade']==1?"selected":""?>>小学一年级</option>
      <option value="2" <?=$folder['grade']==2?"selected":""?>>小学二年级</option>
      <option value="3" <?=$folder['grade']==3?"selected":""?>>小学三年级</option>
      <option value="4" <?=$folder['grade']==4?"selected":""?>>小学四年级</option>
      <option value="5" <?=$folder['grade']==5?"selected":""?>>小学五年级</option>
      <option value="6" <?=$folder['grade']==6?"selected":""?>>小学六年级</option>
      <option value="7" <?=$folder['grade']==7?"selected":""?>>初中一年级</option>
      <option value="8" <?=$folder['grade']==8?"selected":""?>>初中二年级</option>
      <option value="9" <?=$folder['grade']==9?"selected":""?>>初中三年级</option>
      <option value="10" <?=$folder['grade']==10?"selected":""?>>高中一年级</option>
      <option value="11" <?=$folder['grade']==11?"selected":""?>>高中二年级</option>
      <option value="12"<?=$folder['grade']==12?"selected":""?>>高中三年级</option>
    </select></td>
   </tr>

 	<tr>
 		<td class="txtlft">
 		代课老师：
 		</td>
 		<td>
 			  <input type="hidden" name="folderid" value="<?=$folder['folderid']?>">
        <input type="hidden" name="crid" value="<?=$folder['crid']?>">
        <input type="hidden" name="foldername" value="<?=$folder['foldername']?>">
 				
        <ul>
        <?php foreach($teacherList as $teacher){?>
     	  <li style="float:left;width:188px">
          <input name="choose[]"  type="checkbox" style="top:2px;" value="<?=$teacher['uid']?>" <?=in_array($teacher,$folderTeacherList)?'checked':''?>>
          <label id="teachername_381794" style="margin-left:4px;_margin-left:2px;" ><?=$teacher['realname']?>(<?=$teacher['username']?>)</label>
          </li>
        <?php }?>
   		</ul>
 		</td>
 	</tr>
 	 <tr>
    <td class="txtlft"></td>
    <td><input  class="cbtn_my" type="submit" value="修改"></td>

   </tr>
   </form>
 </table>
 </div>
</body>
</html>