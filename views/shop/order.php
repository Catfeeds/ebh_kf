<?php $this->display('head');?>
<body>
    <div class="titr">
      <!-- <a title="添加网校" class="Jadd" href="/school/add.html"><em class="addbtn">添加网校</em></a> -->
    </div>
  <?php show_dialog(".Jadd","Jadd","1000","720",true,false); ?>
  <?php show_dialog(".Jedit","Jedit","1000","720",true,false); ?>
  <?php show_dialog(".Jview","Jview","730","600",true,false); ?>
  <?php
    $Dialog = EBH::app()->lib("Dialog");
    echo $Dialog::open(".Jselect", "Jselect", "800", "620", false, false);
    echo $Dialog::open(".Jview", "Jview", "800", "620", true, false);
    echo $Dialog::open(".Jplaymp3", "Jplay", "950", "400", true, false);
    echo $Dialog::open(".Jplay", "Jplay", "950", "600", true, false);
  ?>
  <form action="/shop/order.html" method="get" id="form">
    <div class="tabconss">
        <span class="wid110">订单编号</span>
        <input title = "订单编号" name="oid" type="text"   class="inp wid140" value="<?= !empty($paginate['params']['oid']) ? $paginate['params']['oid']: ''?>"/>
        <span class="wid110">所属网校商城</span>
        <input value="<?= !empty($paginate['params']['cname']) ? $paginate['params']['cname']: '' ?>" class="inp wid140" readonly id="cname" name="cname"
               onClick="$('.Jselect').click()"/>
        <span class="wid110">卖家</span>
        <input title = "卖家" name="seller" type="text"   class="inp wid140" value="<?= !empty($paginate['params']['seller']) ? $paginate['params']['seller']: '' ?>"/>
        <span class="wid110">买家</span>
        <input title = "买家" name="customer" type="text"   class="inp wid140" value="<?= !empty($paginate['params']['customer']) ? $paginate['params']['customer']: '' ?>"/>
        <span class="wid85">支付方式:</span>
        <select name="pay_way" class='pay_way'>
          <!-- 1余额，2微信，3支付宝，4银联，5积分，6其他 -->
            <option value='0' <?php echo !isset($paginate['params']['pay_way']) ? "selected" : "" ?>>全部</option>
            <option value="3" <?php echo ($paginate['params']['pay_way'] == 3) ? "selected" : "" ?>>支付宝</option>
            <option value="8" <?php echo ($paginate['params']['pay_way'] == 8) ? "selected" : "" ?>>余额支付</option>
            <option value="9" <?php echo ($paginate['params']['pay_way'] == 9) ? "selected" : "" ?>>微信支付</option>
            <option value="10" <?php echo ($paginate['params']['pay_way'] == 10) ? "selected" : "" ?>>积分兑换</option>
        </select>
        <span class="wid85">订单状态:</span>
        <select name="order_status" class='order_status'>
          <!-- '订单状态：1待发货，2待确认，3交易成功，4退款中，5退款已确认，6交易关闭',', -->
            <option value='0' <?php echo empty($paginate['params']['order_status']) ? "selected" : "" ?>>全部</option>
            <option value='1' <?php echo ($paginate['params']['order_status'] == 1) ? "selected" : "" ?>>待付款</option>
            <option value="2" <?php echo ($paginate['params']['order_status'] == 2) ? "selected" : "" ?>>待发货</option>
            <option value="3" <?php echo ($paginate['params']['order_status'] == 3) ? "selected" : "" ?>>待确认</option>
            <option value="4" <?php echo ($paginate['params']['order_status'] == 4) ? "selected" : "" ?>>交易成功</option>
            <option value="5" <?php echo ($paginate['params']['order_status'] == 5) ? "selected" : "" ?>>退款中</option>
            <option value="6" <?php echo ($paginate['params']['order_status'] == 6) ? "selected" : "" ?>>退款已确认</option>
            <option value="7" <?php echo ($paginate['params']['order_status'] == 7) ? "selected" : "" ?>>交易关闭</option>
        </select>
        <br>
        <span class="wid85">下单时间</span> <input name="orderstart" type="text"   class="inp wid100 Wdate" onClick="WdatePicker()" value="<?=$paginate['params']['orderstart']?>" />到<input name="orderend" type="text"   class="inp wid100 Wdate" onClick="WdatePicker()" value="<?=$paginate['params']['orderend']?>" />
        <span class="wid85">支付时间</span> <input name="paystart" type="text"   class="inp wid100 Wdate" onClick="WdatePicker()" value="<?=$paginate['params']['paystart']?>" />到<input name="payend" type="text"   class="inp wid100 Wdate" onClick="WdatePicker()" value="<?=$paginate['params']['payend']?>" />
		    <input type="submit" name="input" value="查询" class="comcheck"/>
    </div>
  </form>
  <div class="tabcon tablist" >
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr class="tabtit">
        <td width="5%">序号</td>
        <td width="5%">订单编号</td>
        <td width="5%">所属网校商城</td>
        <td width="4%">卖家</td>
        <td width="4%">买家</td>
        <td width="6%">下单时间</td>
        <td width="6%">支付时间</td>
        <td width="5%">支付方式</td>
        <td width="5%">交易金额（元）</td>
        <td width="5%">实收金额（元）</td>
        <td width="5%">订单状态</td>
        <td width="5%">操作</td>
      </tr>
      <?php
        function getPay($code){
          // 1 年卡, 2 快钱 ,3 支付宝 ,4人工开通 ,5内部测试 ,6 农行支付 ,7银联支付 ,8余额支付 ,9微信支付, 10积分兑换',
          switch($code){
            case 1:
              return '年卡';break;
            case 2:
              return '快钱';break;
            case 3:
              return '支付宝';break;
            case 4:
              return '人工开通';break;
            case 5:
              return '内部测试';break;  
            case 6:
              return '农行支付';break;
            case 7:
              return '银联支付';break;  
            case 8:
              return '余额支付';break;  
            case 9:
              return '微信支付';break;  
            case 10:
              return '积分兑换';break;    
          }
        }
        function getStatus($code){
          // 0待付款，1待发货，2待确认，3交易成功，4退款中，5退款已确认，6交易关闭',
          switch($code){
            case 0:
              return '待付款';break;
            case 1:
              return '待发货';break;
            case 2:
              return '待确认';break;
            case 3:
              return '交易成功';break;
            case 4:
              return '退款中';break;
            case 5:
              return '退款已确认';break;
            case 6:
              return '交易关闭';break;
          }
        }
       ?>
      <?php 
      $tradeMoney = 0;
      $avaliMoney = 0; 
      if(!empty($paginate['list'])){
      foreach($paginate['list'] as $key=>$order){?>
      <tr class="<?=($key%2==1)?"tabbg":""?>">
        <td class="pointer"><?= $order['orderid']?></td>
        <td class="pointer"><?= $order['ordernum']?></td>
        <td class="pointer"><?= $order['classroom']?></td>
        <td class="pointer"><?= $order['seller']?></td>
        <td class="pointer"><?= $order['customer']?></td>
        <td class="pointer"><?= date("Y-m-d H:i:s",$order['dateline'])?></td>
        <td class="pointer"><?php if(!empty($order['paytime'])){ echo date("Y-m-d H:i:s",$order['paytime']);} ?></td>
        <td class="pointer"><?= getPay($order['payfrom']) ?></td>
        <?php if(!$order['iscore']){ ?>
        <td class="pointer"><?= $order['money'] ?></td>
        <td class="pointer"><?= $order['totalfee'] ?></td>
        <?php $tradeMoney += $order['totalfee'];
          $avaliMoney += $order['money'];}else{ ?>
        <td class="pointer"><?= $order['totaliscore'] ?></td>
        <td class="pointer"><?= $order['totaliscore'] ?></td>
        <?php }?>
        <td class="pointer"><?= getStatus($order['type']) ?></td>
        
        <td><a class="Jview" href="/shop/orderdetail.html?orderid=<?= $order['orderid'].'&buyer_uid='.$order['buyer_uid'] ?>" title="订单详情" >订单详情</a></td>
      </tr>
      <?php }}?>
      <tr><td colspan="8" style="text-align: left; padding-left: 30px;">本页合计交易金额: <b style="color:red"><?= round($avaliMoney,2) ?></b> 元. 本页合计实收金额: <b style="color:red"><?= round($tradeMoney, 2) ?></b> 元.</td></tr>
    </table>
  </div>
  
  <div class="page"><?=$paginate['html']?></div>
  <div id="detail" name="detail"></div>
  <!-- ui-dialog -->
  <div id="dialog" title="选择学校">
      <input class="inp a60" type="text" name="classroom_keyword" id="classroom_keyword" value="请输入网校名称或域名"
             onblur="if(this.value == ''){this.value = '请输入网校名称或域名';}"
             onClick="if(this.value == '请输入网校名称或域名'){this.value = '';}else {this.select();}"/>
      <button class="cbtn_my" type="button" id="classroom_search">搜索</button>
      <div class="tabcon tablist">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
              <tr class="tabtit">
                  <td width="10%">选择</td>
                  <td width="15%">域名</td>
                  <td width="50%">网校名称</td>
                  <td width="20%">创建时间</td>
              </tr>
              </tbody>
              <tbody id='moduletbody'>
              </tbody>
          </table>
      </div>
      <div id="hidden_classroom_list" style="display:none"></div>
  </div>
  <script>
    $("#dialog").dialog({
        autoOpen: false,
        width: 700,
        height: 550,
        buttons: [
            {
                text: "取消",
                click: function () {
                    $(this).dialog("close");
                }
            }
        ]
    });
    $("#cname").click(function (event) {
        $("#dialog").dialog("open");
        event.preventDefault();
        goPage(1);
        $("#classroom_keyword").blur();
    });
    function goPage(page) {
        var classroom_keyword = $("#classroom_keyword").val();
        if (classroom_keyword == '请输入网校名称或域名') classroom_keyword = '';
        $.post("/data/getlist.html", {page: page, keyword: classroom_keyword},
            function (data) {
                $("#moduletbody").html(data);
            }, "html");
    }
    $("#classroom_search").click(function () {

        goPage(1);
    });
    function checkCrItem(crid, crname) {
        $('#cname').val(crname);
        $('#crid').val(crid);
        $('#crid_' + crid).attr('checked', true);//选中单选框
        $('#dialog').dialog("close");

    }
    function clearSchool() {
        $("#cname").val("");
        $('#crid').val("");
    }
    $('.pay_way').change(function(){
      $(".comcheck").trigger('click');
    });
     $('.order_status').change(function(){
      $(".comcheck").trigger('click');
    });
  </script>
</body>
</html>