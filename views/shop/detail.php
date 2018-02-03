
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>
<script type="text/javascript" src="http://static.ebanhui.com/portal/js/jquery-1.7.2.min.js"></script>
<link type="text/css" href="http://static.ebanhui.com/ebh/js/jquery/jquery-ui/css/default/jquery-ui-1.8.1.custom.css"
      rel="stylesheet"/>
<script type="text/javascript"
        src="http://static.ebanhui.com/ebh/js/jquery/jquery-ui/jquery-ui-1.8.1.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="http://static.ebanhui.com/ebh/tpl/2012/css/basic.css" />
<link rel="stylesheet" type="text/css" href="http://static.ebanhui.com/mall/css/shopmall.css"/>
<style>
.delivery-title{
	padding-left:20px;
	border-bottom:1px solid #cdcdcd;
}
</style>
<body>
<div class="buygoods">
	<div class="buygoodson">
    	<div class="delivery-title">商品审核</div>
        <div class="mallrelease">
        	<div class="mallproperty">
            	<span class="mallspan">属性：</span>
                <input type="radio" disabled <?php if($good['new'] == 1){ echo "checked";} ?>/>
                <label>全新商品</label>
                <input type="radio" disabled <?php if($good['new'] == 2){ echo "checked";} ?> />
                <label class="secondhandgoods">二手商品</label>
            </div>
            <div class="malltitle-1">
            	<span class="mallspan">标题：</span>
                <!-- <input type="text" class="titleinput-1" disabled value="<?php echo $good['gname'] ?>"/> -->
                <span><?php echo $good['gname'] ?></span>
            </div>
            <div class="malltitle-1">
            	<span class="mallspan fl">标签：</span>
                <ul class="labelul-1">
					<li class="mylabel-1">
                        <span class="labeleft-1"></span>
                        <div class="bqnr-2">
                            <?php if(!empty($good['tags'])){?>
                                <?php foreach ($good['tags'] as $tag) { ?>
                                    <a class="labelnode-1" style="display:block;margin-right:10px" href="javascript:void(0)"><?= $tag ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php }?>
                            <?php } ?>
                        </div>
                        <span class="labelright-1"></span>
                    </li>
                </ul>
            </div>
            <div class="malltitle-1">
			<span class="mallspan">价格/积分：</span>
                <input type="radio" <?php if($good['is_real'] ==1 ){ echo "checked";}else{echo "disabled";}?>/>
                <!--不可点击添加class="notclick"-->
                <div class="integralprice <?php if($good['is_real'] ==2 ){ echo "notclick";}?>" >
                    <label>一口价</label>
                    <input type="text" class="priceinput" disabled value="<?php if($good['is_real'] ==1 ){ echo $good['price'];}?>"/>
                    <span>元</span>
                </div>
            </div>
            <div class="malltitle-1">
            	<span class="mallspan"></span>
                <input type="radio" <?php if($good['is_real'] ==2 ){ echo "checked";}else{ echo "disabled";}?>/>
                <!--不可点击添加class="notclick"-->
                <div class="integralprice <?php if($good['is_real'] ==1 ){ echo "notclick";}?>">
                	<label>积分价</label>
                    <input type="text" class="priceinput" disabled value="<?php if($good['is_real'] ==2 ){ echo $good['score'];}?>"/>
                    <span>积分</span>
                </div>
            </div>
            <div class="malltitle-1">
            	<span class="mallspan">库存：</span>
                <input type="text" class="stockinput" disabled value="<?= $good['stock']?>"/>
                <span class="fontsize15">件</span>
            </div>
            <?php if($good['is_real'] ==1 ){ ?>
            <div class="malltitle-1">
            	<span class="mallspan">运送费：</span>
                <input type="text" class="stockinput" disabled value="<?= $good['freight']?>"/>
                <span class="fontsize15">元</span>
            </div>
            <?php }?>
            <div class="malltitle-1">
            	<span class="mallspan">发货地址：</span>
                <span class="fontsize15 sendaddress"><?= $good['fulladdress']?></span>
            </div>
             <div class="malltitle-1">
                <span class="mallspan upimagesfl">商品封面图：</span>
                <div class="upimages-1">
                    <?php if(!empty($good['images'])){?>
                        <?php foreach ($good['images'] as $key => $image) { ?>
                            <?php if($key==0){ ?>
                           <img src="<?= $showpath.$image['path']?>" width="90" height="90" />
                           <?php } ?>
                        <?php }?>
                    <?php } ?>
                </div>
            </div>
            <div class="malltitle-1">
            	<span class="mallspan upimagesfl">商品轮播图：</span>
                <div class="upimages-1">
                    <?php if(!empty($good['images'])){?>
                        <?php foreach ($good['images'] as $key => $image) { ?>
                            <?php if($key!=0){ ?>
                           <img src="<?= $showpath.$image['path']?>" width="90" height="90" />
                           <?php } ?>
                        <?php }?>
                    <?php } ?>
                </div>
            </div>
            <div class="malltitle-1">
            	<span class="mallspan fl">商品详情：</span>
                <div style="float:left;width:820px;height:300px;overflow:auto; padding:6px 10px; border:1px solid #eee;" >
                   <?= $good['descr'] ?>
                </div>
            </div>
            <div class="mallproperty">
                <span class="mallspan">审核：</span>
                <input type="hidden" id="gid" value="<?= $good['gid']?>" />
                <input type="radio" name="checkstatus" id="pass" value="1" onclick="pass()" <?php if($good['audit'] == 1) echo "checked"; ?>/>
                <label>审核通过</label>
                <input type="radio" name="checkstatus" id="back" value="2" onclick="back()" <?php if($good['audit'] == 2) echo "checked"; ?>/>
                <label class="secondhandgoods">审核退回</label>
            </div>
            <div class="mallproperty" style="height:135px;" id="remarkBox" >
                <span class="mallspan">退回意见：</span>
                <textarea rows="5" cols="80" style="margin-top:10px" id="remark" <?php if($good['audit'] == 2) echo 'disabled'; ?>><?php if($good['audit'] == 2) echo $good['remark']; ?></textarea>
            </div>
        </div>
        <?php if($good['audit'] != 2){ ?>
            <a href="javascript:void(0)" class="issued" onclick="submitCheck()">提交</a>
            <a href="javascript:void(0)" class="issued" style="position:relative;top:-47px;left:137px;background-color:#b3bcd0"  onclick="submitClose()">关闭</a>
        <?php }?>
    </div>
</div>
<script type="text/javascript">
    <?php if($good['audit'] == 2){ ?>
            $(function(){
                $("#remarkBox").show();
            });
    <?php }else{ ?>
            $(function(){
                $("#remarkBox").hide();
            });
   <?php } ?>
    
    function back(){
        $("#remarkBox").show();
    }
    function pass(){
        $("#remarkBox").hide();
    }
    function submitCheck(){
        var checkstatus = $("input[name='checkstatus']:checked").val();
        if(checkstatus != 1 && checkstatus != 2){
            alert('请审核商品是否通过!');
            return false;
        }
        var remark = $("#remark").val();
        if(checkstatus == 1){
            remark = '';
        }
        $.post('/shop/update.html',{
            gid:$("#gid").val(),
            status:checkstatus,
            remark:remark
        },function(data){
            if(data.msg){
                top.art.dialog({id: 'Jview'}).close();
            }
            if(remark ==''){
                alert('备注信息必填');
            }
            if(remark.length > 200){
                alert('备注信息不能超过200个字符');
            }
        },'json');
    }
    function submitClose(){

        top.art.dialog({id: 'Jview'}).close();
    }
</script>
</body>
</html>
