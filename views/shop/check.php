<?php $this->display('head');?>

<body>
    <div class="titr">
      <!-- <a title="添加网校" class="Jadd" href="/school/add.html"><em class="addbtn">添加网校</em></a> -->
    </div>
  <?php show_dialog(".Jadd","Jadd","1000","720",true,false); ?>
  <?php show_dialog(".Jedit","Jedit","1000","720",true,false); ?>
  <?php show_dialog(".Jview","Jview","1100","600",true,true); ?>
  <form action="/shop/check.html" method="get" id="form">
    <div class="tabconss">
        <span class="wid110">商品名称</span>
        <input title = "网校名称" name="q" type="text"   class="inp wid140" value="<?=$paginate['params']['q']?>"/>
        <span class="wid85">审核状态:</span>
        <select name="status" id="selectStatus">
            <option value='0' <?php echo empty($paginate['params']['status']) ? "selected" : "" ?>>全部</option>
            <option value="1" <?php echo ($paginate['params']['status'] == 1) ? "selected" : "" ?>>等待审核</option>
            <option value="2" <?php echo ($paginate['params']['status'] == 2) ? "selected" : "" ?>>通过</option>
            <option value="3" <?php echo ($paginate['params']['status'] == 3) ? "selected" : "" ?>>已退回</option>
        </select>
		    <input type="submit" name="input" value="查询" class="comcheck"/>
    </div>
  </form>
  <div class="tabcon tablist" >
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr class="tabtit">
        <td width="5%">序号</td>
        <td width="40%">商品名称</td>
        <td width="5%">发布人</td>
        <td width="20%">发布时间</td>
        <td width="10%">状态</td>
        <td width="20%">操作</td>
      </tr>
      <?php
      function getStatus($code){
      	switch ($code) {
      		case '0':
      			return "等待审核";
      			break;
      		case '1':
      			return "已通过";
      			break;
      		case '2':
      			return "已退回";
      			break;
      		default:
      			# code...
      			break;
      	}
      }
      function getAction($item){
        $linkDetail = '<a class="Jview" href="/shop/detail.html?gid='.$item['gid'].'" title="查看详情" >查看详情</a>&nbsp;'; 
        $linkPass   = '<a class="pass" href="javascript:void(0)" onclick="update(this, 1)" title="通过" gid="'.$item['gid'].'" style="color:red">通过</a>&nbsp;';
        $linkFail  = '<a class="fail" href="javascript:void(0)" onclick="update(this, 2)" title="退回" gid="'.$item['gid'].'" style="color:red">退回</a>&nbsp;';
        $linkBack  = '<a class="back" href="javascript:void(0)" onclick="update(this, 0)" title="撤销" gid="'.$item['gid'].'" style="color:red">撤销</a>&nbsp;';
        $space      = '&nbsp;';
        $waitAction = $linkDetail.$space.$linkPass.$space.$linkFail;
        $passAction = $linkDetail.$space.$linkFail;
      	$backAction = $linkDetail.$space.$linkBack;
      	switch ($item['audit']) {
      		case '0':
      			return $waitAction;
      			break;
      		case '1':
      			return $passAction;
      			break;
      		case '2':
      			return $backAction;
      			break;
      		default:
      			# code...
      			break;
      	}
      }
      function getgoodsurl($crid,$gid){
        $goodurl = '';
        if(empty($crid) || empty($gid) || !is_numeric($crid) || !is_numeric($gid)){
            return $goodurl;
        }
        $route = EBH::app()->route;
        $shopconfig = Ebh::app()->getConfig()->load('shopconfig');
        $url = $shopconfig['showurl'];
        return "$url/$crid/item-$gid".$route['suffix']; 
      }
       ?>
      <?php foreach($paginate['list'] as $key=>$item){?>
      <tr id="item<?= $item['gid'] ?>"  class="<?=($key%2==1)?"tabbg":""?>">
        <td class="pointer"><?= $item['gid']?></td>
        <td class="pointer" style=""><a  target="_blank" href="<?= getgoodsurl($item['crid'],$item['gid']) ?>"><?= $item['gname']?></a></td>
        <td class="pointer"><?= $item['username']?></td>
        <td class="pointer">
        	<?= date("Y-m-d H:i:s",$item['dateline'])?>
        </td>
        <td class="pointer"><?= getStatus($item['audit'])?></td>
        <td><?= getAction($item) ?></td>
      </tr>
      <?php }?>

    </table>
  </div>
  
  <div class="page"><?=$paginate['html']?></div>
  <div id="detail" name="detail"></div>
  <div id="checkdiv" style="display:none">
        <p style="height:40px" >
          <!-- <span class="wid85">商品退回信息</span> -->
            <!-- <label><input type="radio" name="status" value="1" checked="checked"/> 通 过</label>
            <label style="margin-left:20px"><input type="radio" name="status" value="2"/>退回</label> -->
        </p>

        <p><span class="wid85">备注信息:</span><textarea name="remark" id="remark"
                                                     style="height:140px;width:400px;margin-left:10px"></textarea></p>
  </div>
  <script>
    function update(obj,status){
      var obj = $(obj);
      var gid = obj.attr('gid');
      if(status == 0){//撤销
        $.post('/shop/update.html',{
          gid:gid,
          status:status
        },function(data){
          if(data.status){//成功
              var pass = "<a style=\"color:red\" class=\"pass\" onclick=\"update(this,1)\" gid=" + gid +">通过</a>";;//通过
              var back = "<a style=\"color:red\" class=\"fail\" onclick=\"update(this,2)\" gid=" + gid +">退回</a>";;//退回
              obj.parent().prev().html('等待审核');
              obj.siblings("a[class='pass']").remove();
              obj.parent().append(pass);
              obj.parent().append(back);
              obj.remove();
          }
          else{
            alert('撤销失败');
          }
            
        },'json');
      }
      if(status == 1){//通过
        $.post('/shop/update.html',{
          gid:gid,
          status:status
        },function(data){
          if(data.status){//成功
            if(data.code == 1){//商品审核通过
              obj.parent().prev().html('已通过');
              obj.remove();
            }
            if(data.code == 2){//商品已经退回
              obj.parent().prev().html('已退回');
              obj.remove();
            }
          }
          else{
            alert('审核失败，请重新审核');
          }
            
        },'json');
    }
    if(status == 2){//退回
      var dialog = window.top.art.dialog({
          title: '退回信息',
          content: document.getElementById("checkdiv"),
          width: '600px',
          lock: true,
          opacity: 0.2
        });
        dialog.button([{
          name: '确定',
          focus: true,
          callback: function () {
              var status = 2;
              var remark = window.top.$("#remark").val();
              // if(remark ==''){
              //   alert('备注信息必填');
              //   return ;
              // }
              // if(remark.length > 200){
              //   alert('备注信息不能超过200个字符');
              //   return ;
              // }
              $.post('/shop/update.html', {
                  gid: gid,
                  remark: remark,
                  status: status
              }, function (data) {
                  if (data.msg) {
                      $.showmessage({
                          img: 'success',
                          message: data.msg,
                          title: '消息通知',
                          timeoutspeed: 1500
                      });
                      var revoke = "<a style=\"color:red\" onclick=\"update(this,0)\" gid=" + gid +">撤销</a>";
                      obj.prev(".pass").remove();
                      obj.parent().prev().html('已退回');
                      obj.parent().append(revoke);
                      obj.remove();
                      window.top.$('#remark').val('');
                       dialog.close();
                  }
                  if(remark ==''){
                    alert('备注信息必填');
                  }
                  if(remark.length > 200){
                    alert('备注信息不能超过200个字符');
                  }
              }, 'json');
              return false;
          }
      },{name: '关闭'}]);
    }
  }
   
  </script>
  <script type="text/javascript">
    $("#selectStatus").change(function(){
      $(".comcheck").trigger('click');
    });
  </script>
</body>
</html>