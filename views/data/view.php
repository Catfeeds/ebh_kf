<?php $this->display('head'); ?>
<script type="text/javascript" src="http://static.ebanhui.com/portal/js/jquery-1.7.2.min.js"></script>
<link type="text/css" href="http://static.ebanhui.com/ebh/js/jquery/jquery-ui/css/default/jquery-ui-1.8.1.custom.css"
      rel="stylesheet"/>
<script type="text/javascript"
        src="http://static.ebanhui.com/ebh/js/jquery/jquery-ui/jquery-ui-1.8.1.custom.min.js"></script>
<body>
<?php $this->display('common/player'); ?>
<div class="tabcones">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php if ($request['cwid'] > 0) { ?>
    <!-- 课件 -->
    <tr>
        <td class="txtlft">课件标题：</td>
        <td><?php echo $info['title'] ?></td>
        <td class="txtlft">所属教师：</td>
        <td><?php echo $info['realname'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">所属分类：</td>
        <td><?php echo $info['name'] ?></td>
        <td class="txtlft">所属年级：</td>
        <td> <?php echo $info['grade'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">课件版本：</td>
        <td> <?php echo $info['edition'] ?></td>
        <td class="txtlft">原作者：</td>
        <td> <?php echo $info['realname'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">定价：</td>
        <td><?php echo $info['verifyprice'] ?></td>
        <td class="txtlft">排序：</td>
        <td><?php echo $info['cdisplayorder'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">封面：</td>
        <td><?php if (!empty($info['logo'])) {
                echo '<img src="' . $info['logo'] . '" />';
            } else {
                echo '无';
            } ?></td>
        <td class="txtlft">课件文件名：</td>
        <td><?php echo $info['cwname'] ?></td>
    </tr>

    <tr>
        <td class="txtlft">标签：</td>
        <td><?php echo $info['tag'] ?></td>
        <td class="txtlft">添加时间：</td>
        <td><?php echo date('Y-m-d H:i:s', $info['dateline']) ?></td>
    </tr>

    <tr>
        <td class="txtlft">课件详情：</td>
        <td colspan="3"><?php echo $info['message'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">课件摘要：</td>
        <td colspan="3"><?php echo $info['summary'] ?></td>
    </tr>

    <?php if($info['islive'] == 1){ ?>
    <tr>
        <td class="txtlft">教师摄像头地址(PC)：</td>
        <td colspan="3"><?php echo $info['teacher_camera_rtmp'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">教师画板地址(PC)：</td>
        <td colspan="3"><?php echo $info['teacher_board_rtmp'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">教师画板地址(WAP)：</td>
        <td colspan="3"><?php echo $info['teacher_board_http'] ?></td>
    </tr>
    <?php } ?>
<?php } elseif ($request['attid'] > 0) { ?>
    <!-- 附件 -->
    <tr>
        <td class="txtlft">附件标题：</td>
        <td><?php echo $info['title'] ?></td>
        <td class="txtlft">原作者：</td>
        <td><?php echo $info['realname'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">附件大小：</td>
        <td><?php echo getSize($info['size']) ?></td>
        <td class="txtlft">添加时间：</td>
        <td><?php echo date("Y-m-d H:i:s", $info['dateline']) ?></td>
    </tr>
    <tr>
        <td class="txtlft">备注信息：</td>
        <td><?php echo $info['message'] ?></td>
        <td class="txtlft">附件格式：</td>
        <td><?php echo $info['suffix'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">关联课件：</td>
        <td><?php echo $info['ctitle'] ?></td>
        <td class="txtlft">所属网校：</td>
        <td><?php echo $info['crname'] ?></td>
    </tr>
<?php } elseif ($request['logid'] > 0) { ?>
    <!-- 评论详情 -->
    <tr>
        <td class="txtlft">评论人：</td>
        <td><?php echo $info['username'] ?></td>
        <td class="txtlft">点评：</td>
        <td><?php echo $info['score'] ?>(分)</td>
    </tr>
    <tr>
        <td class="txtlft">操作IP：</td>
        <td><?php echo $info['fromip'] ?></td>
        <td class="txtlft">评论日期：</td>
        <td><?php echo date("Y-m-d H:i:s", $info['dateline']) ?></td>
    </tr>
    <tr>
        <td class="txtlft">评论对象：</td>
        <td><?php echo $info['type'] ?></td>
        <td class="txtlft">课件名称：</td>
        <td><?php echo $info['title'] ?></td>
    </tr>

    <tr>
        <td class="txtlft">评论内容：</td>
        <td colspan="3" style="height: 120px"><?php echo $info['subject'] ?></td>
    </tr>
<?php } elseif ($request['qid'] > 0) { ?>
    <!-- 答疑详情 -->
    <tr>
        <td class="txtlft">问题：</td>
        <td><?php echo $info['title'] ?></td>
        <td class="txtlft">提问者：</td>
        <td><?php echo $info['realname'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">内容描述：</td>
        <td colspan="3">
            <?php
            $editor = Ebh::app()->lib('UMEditor');
            $editor->simpleEditor('message', '680px', '210px', $info['message']);
            ?></td>
    </tr>
    <tr>
        <td class="txtlft">提问时间：</td>
        <td><?php echo date("Y-m-d H:i:s", $info['dateline']) ?></td>
        <td class="txtlft">所属网校：</td>
        <td><?php echo $info['crname'] ?></td>
    </tr>

    <tr>
        <td class="txtlft">网校域名：</td>
        <td colspan="3"><?php echo $info['domain'] ?>.ebh.net</td>
    </tr>

<?php } elseif ($request['aid'] > 0) { ?>
    <!-- 回答详情 -->
    <tr>
        <td class="txtlft">问题标题：</td>
        <td colspan="3"><?php echo $info['title'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">问题描述：</td>
        <td colspan="3">
            <?php
            $editor = Ebh::app()->lib('UMEditor');
            $editor->simpleEditor('qmessage', '680px', '210px', $info['qmessage']);
            ?></td>
    </tr>
    <tr>
        <td class="txtlft">回答内容：</td>
        <td colspan="3">
            <?php echo $editor->simpleEditor('amessage', '680px', '210px', $info['amessage']); ?></td>
    </tr>
    <tr>
        <td class="txtlft">回答时间：</td>
        <td><?php echo date("Y-m-d H:i:s", $info['dateline']) ?></td>
        <td class="txtlft">回答者：</td>
        <td><?php echo $info['realname'] ?></td>
    </tr>
<?php } elseif ($request['eid'] > 0) { ?>
    <!-- 回答详情 -->
    <tr>
        <td class="txtlft">作业标题：</td>
        <td colspan="3"><?php echo $info['title'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">所属网校：</td>
        <td colspan="3"><?php echo $info['crname'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">发布时间：</td>
        <td><?php echo date("Y-m-d H:i:s", $info['dateline']) ?></td>
        <td class="txtlft">发布教师：</td>
        <td><?php echo $info['realname'] ?></td>
    </tr>
<?php } elseif ($request['reviewid'] > 0) { ?>
    <!-- 主站评论详情 -->
    <tr>
        <td class="txtlft">评论人：</td>
        <td><?php echo $info['username'] ?></td>
        <td class="txtlft">点评文章：</td>
        <td><a href="http://www.ebh.net/news/<?php echo $info['itemid'] ?>.html" target="_blank"
               style="text-decoration: underline"><?php echo $info['isubject'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">操作IP：</td>
        <td><?php echo $info['fromip'] ?></td>
        <td class="txtlft">评论日期：</td>
        <td><?php echo date("Y-m-d H:i:s", $info['dateline']) ?></td>
    </tr>
    <tr>
        <td class="txtlft">评论内容：</td>
        <td colspan="3"><?php echo $info['subject'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">是否审核：</td>
        <td><?php if (empty($info['ischeck'])) {
                echo '未审核';
            } else {
                echo '已审核';
            } ?></td>
        <td class="txtlft">审核状态：</td>
        <td><?php if ($info['status'] == 1) {
                echo '正常';
            } else {
                echo '限制';
            } ?></td>
    </tr>

    <?php exit;
} elseif ($request['fileid'] > 0) { ?>
    <!-- 云盘 -->
    <tr>
        <td class="txtlft">文件名称：</td>
        <td colspan="3"><?php echo $info['title'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">上传账号：</td>
        <td><?php echo $info['username'] ?></td>
        <td class="txtlft">上传人：</td>
        <td><?php echo $info['realname'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">文件大小：</td>
        <td><?php echo $info['size'] ?></td>
        <td class="txtlft">文件类型：</td>
        <td><?php echo $info['suffix'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">上传时间：</td>
        <td><?php echo date("Y-m-d H:i:s", $info['dateline']) ?></td>
        <td class="txtlft">所属网校：</td>
        <td><?php echo $info['crname'] ?></td>
    </tr>
<?php } elseif ($request['crid'] > 0) { ?>
    <!--域名详情-->
    <tr>
        <td class="txtlft">域名全称：</td>
        <td colspan ="3">
        <a href="http://<?=$info['fulldomain'] ?>" target="_blank"><?php echo $info['fulldomain'] ?></a>
        <span style="margin-left: 10px">默认域名:&nbsp;&nbsp;<a href="http://<?=$info['domian'].".ebh.net"?>" target="_blank"><?=$info['domian'].".ebh.net"?></a></span>
        </td>
    </tr>
    <tr>
        <td class="txtlft">备案信息：</td>
        <td colspan ="3"><?php echo $info['icp'] ?></td>
    </tr>
    <tr>
        <td class="txtlft">所属网校：</td>
        <td><?php echo $info['crname'] ?></td>
        <td class="txtlft">提交时间：</td>
        <td><?php echo date("Y-m-d H:i:s", $info['domain_time']) ?></td>

    </tr>
<?php } ?>
<tr>
    <td class="txtlft" style="width:16%;">管理员审核状态：</td>
    <td class="cRed" style="width:20%;"><?php if(empty($info['admin_status'])){ echo '未审核';}elseif($info['admin_status']==1){echo '审核通过';}else{echo '审核未通过';}?></td>
    <td class="txtlft">审核备注：</td>
    <td style="width:34%;"><?php echo $info['admin_remark'] ?></td>
</tr>
<tr>
    <td class="txtlft">管理员IP：</td>
    <td><?php echo $info['admin_ip'] ?></td>
    <td class="txtlft">审核处理时间：</td>
    <td><?php if ($info['admin_dateline']) {
            echo date("Y-m-d H:i:s", $info['admin_dateline']);
        } ?></td>
</tr>
<tr>
    <?php if ($info['admin_status'] == 3) { ?>
        <td class="txtlft">撤销人</td>
        <td><?php echo $info['checkname'] ? $info['checkname'] : '--' ?></td>
    <?php } else { ?>
        <td class="txtlft">审核人</td>
        <td><?php echo $info['checkname'] ? $info['checkname'] : '--' ?></td>
    <?php } ?>
	    <td class="txtlft" rowspan ="2">删除时间：</td>
		<td rowspan ="2"><?php echo empty($info['delline']) ? "无" : date("Y-m-d H:m:s", $info['delline']) ?></td>
</tr>
<!-- 教师审核信息 先隐藏 -->
<!--
     <tr>
    <td class="txtlft">教师审核状态：</td>
    <td class="cRed"><?php echo $statusArr[$info['teach_status']] ?></td>
    <td class="txtlft">审核备注：</td>
    <td><?php echo $info['teach_remark'] ?></td>
  </tr>
     <tr>
    <td class="txtlft">教师IP：</td>
    <td><?php echo $info['teach_ip'] ?></td>
    <td class="txtlft">审核处理时间：</td>
    <td><?php if ($info['teach_dateline']) {
    echo date("Y-m-d H:i:s", $info['teach_dateline']);
} ?></td>
  </tr>  
   -->
<tr>
    <td class="txtlft">删除状态：</td>
    <td class="cRed"><?php echo ($info['del'] == 1) ? "已删除" : "正常" ?></td>
</tr>

<tr>
    <td colspan="4" align="center"><input type="button" value="关闭" class="combtn cbtn_4 form_submit"/></td>
</tr>
</table>
</div>
<script type="text/javascript">
    $(function () {
        $(".form_submit").click(function () {
            top.art.dialog({id: 'Jview'}).close();
        });

    })
</script>
</body>
</html>