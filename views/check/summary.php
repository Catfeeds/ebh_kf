<?php $this->display('head');?>
<div class="baobiao ft12 threecheck MT_10">
    <ul class="one">
        <li id="one1" onClick="ChangeTab(this,'')" class="<?php echo ($request['cat']=='')?"on_box":""?>" >数据审核</li>
        <li id="one2" onClick="ChangeTab(this,1)" class="<?php echo ($request['cat']=='1')?"on_box":""?>" >用户空间审核</li>
        <li id="one3" onClick="ChangeTab(this,2)" class="<?php echo ($request['cat']=='2')?"on_box":""?>" >云盘审核</li>
    </ul>
</div>
<script type="text/javascript">
    function ChangeTab(obj, cat) {
        location.href = '/check/summary.html?cat='+cat;
    }
</script>
<body>
<form action="/check/summary.html" method="get" id="form">
    <input type="hidden" name="cat" value="<?php echo $request['cat']?>">
    <div class="tabconss">
        <span>审核类型: </span>
        <select name="ctype" size="">
            <option value="">全部</option>
            <?php foreach($type as $k =>$val){?>
                <option value="<?php echo $k?>" <?php echo $request['ctype'] == $k ? 'selected' : ''?>><?php echo $val?></option>
            <?php }?>
        </select>
        <span>审核人：</span>
        <select id='sel' name="admin_uid" onchange="this.nextSibling.value= $('#sel option:selected').attr('checkname')">
            <option></option>
            <?php foreach($userlist as $v){?>
                <option value="<?php echo $v['uid']?>" checkname="<?php echo $v['realname']?>" <?php echo $request['admin_uid']==$v['uid']?'selected':''?>><?php echo $v['realname']?></option>
            <?php }?>
        </select><input name="checkname" class="inp wid140" style="width:144px;display:none;height:21px;position:absolute;left:270px;" value="<?php echo $_REQUEST['checkname']?>">
        <span class="wid85">日期从&nbsp;</span><input name="startdate" type="text"   class="inp" onClick="WdatePicker()" value="<?php echo $request['startdate']?>" /> 到 <input name="enddate" type="text"   class="inp" onClick="WdatePicker()" value="<?php echo $request['enddate']?>" />
        <input type="submit" name="input" value="查询" class="comcheck"/>
        <input type="submit" name="reflash" value="刷新" class="comcheck">
    </div>
</form>
<div class="tabcon tablist" >
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="tabtit">
            <td width="%">审核人</td>
            <td>审核类型</td>
            <td>审核通过次数</td>
            <td>审核未通过次数</td>
            <td width="%">审核次数</td>
            <td>撤销次数</td>
<!--            <td width="%">一周次数</td>-->
        </tr>
        <?php if(!empty($summary)){?>
        <?php foreach($summary as $key=>$value){?>
            <tr class='<?php echo ($key%2==1)?"tabbg":""?>'>
                <td><?php echo $value['realname']? $value['realname'] :($value['username']?$value['username']:'--')?></td>
                <td><?php $t = $request['ctype'];echo $t?$type[$t]:'全部'?></td>
                <td><?php echo $value['count']-$value['uncheck']-$value['revoke']?></td>
                <td><?php echo $value['uncheck']?></td>
                <td><?php echo $value['count']-$value['revoke']?></td>
                <td><?php echo $value['revoke']?></td>
<!--                <td>--><?php //echo $value['weekcount']?><!--</td>-->
            </tr>
        <?php }?>
        <?php }else{?>
            <tr><td colspan="6" class="tablink" style="text-align:center;color: #ff0000">未找到相关信息</td></tr>
        <?php }?>
    </table>
</div>
<div class="page"><?php echo $pagestr?></div>
<div id="detail" name="detail"></div>
<script>

</script>

</body>
</html>