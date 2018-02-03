<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link type="text/css" rel="stylesheet" href="/static/css/content.css" />
<script type="text/javascript" src="/static/js/jquery.js" ></script>
<style>
    *{ margin: 0; padding: 0;}
    .content_box{ margin-left: 0px;}
    h1 { height:30px;line-height:30px;font-size:14px;padding-left:15px;background:#EEE;border-bottom:1px solid #ddd;border-right:1px solid #ddd;overflow:hidden;zoom:1;margin-bottom:10px;}
    h1 b {color:#3865B8;}
    h1 span {color:#ccc;font-size:10px;margin-left:10px;}


    #Profile{ width:48%; height:191px; float:left;margin:5px 15px 0 0;}
    #system {width:48%;float:left;margin:5px 15px 0 0;}
    #yourphpnews {width:48%;float:left;margin:5px 15px 0 0;}
    .list ul{ border:1px #ddd solid; border-bottom:none; font:12px "宋体";}
    .list ul li{ border-bottom:1px #ddd solid; height:26px; line-height:26px; color:#777; list-style: none; padding-left: 5px; }
    .list ul li span{ display:block; float:left; color:#777;}

	.notread { background:#d18eca}
    #sitestats {width:48%; height:191px; float:left;margin:5px 0  0 0;overflow:hidden;}
    #sitestats div {_width:99.5%;border:1px solid #ddd;overflow:hidden;zoom:1;}
    #sitestats ul {width:102%;padding:2px 0 0 2px;_padding:1px 0 0 1px;height:132px; font:12px "宋体"}
    #sitestats ul li {float:left;height:44px; float:left; width:16.1%;_width:16.3%;text-align:center;border-right:1px solid #fff;border-bottom:none; list-style: none;}
    #sitestats ul li b {float:left;width:100%;height:21px;line-height:22px;  background:#EFEFEF;color:#777;font-weight:normal;}
    #sitestats ul li span {float:left;width:100%;color:#3865B8;background:#F8F8F8;height:21px;line-height:21px;overflow:hidden;zoom:1;}

    #roomlist{width:48%;float:left;}
    #roomlist table{
	   border-collapse:collapse;
        }
    #roomlist table,th, td,tr
    {
        border: 1px solid #ddd ;
    	text-align:center;
    	height:30px;
    	line-height:30px;
    }
    #roomlist table th{
        height: 30px;
        line-height: 30px;
        font-size: 14px;
        padding-left: 15px;
        background: #EEE;
        border-bottom: 1px solid #ddd;
        border-right: 1px solid #ddd;
        overflow: hidden;
        margin-bottom: 10px;
    	font-weight:900;
    }
    #roomlist table,th, td{
	    color:#777;
    }
    
    .danger,.danger td{
    	color:#fff;
	   background-color:#da4f49
    }
    .warning,.warning td{
    	color:#fff;
	    background-color: #faa732;
    }
    .info,.info td{
    	color:#fff;
	   background-color: #49afcd;
    }
    
    
.btn {
	min-height:30px;
	min-width:50px;
    display: inline-block;
    *display: inline;
    padding: 4px 12px;
    margin-bottom: 0;
    *margin-left: .3em;
    font-size: 14px;
    line-height: 20px;
    color: #333;
    text-align: center;
    text-shadow: 0 1px 1px rgba(255,255,255,0.75);
    vertical-align: middle;
    cursor: pointer;
    background-color: #f5f5f5;
    *background-color: #e6e6e6;
    background-image: -moz-linear-gradient(top,#fff,#e6e6e6);
    background-image: -webkit-gradient(linear,0 0,0 100%,from(#fff),to(#e6e6e6));
    background-image: -webkit-linear-gradient(top,#fff,#e6e6e6);
    background-image: -o-linear-gradient(top,#fff,#e6e6e6);
    background-image: linear-gradient(to bottom,#fff,#e6e6e6);
    background-repeat: repeat-x;
    border: 1px solid #ccc;
    *border: 0;
    border-color: #e6e6e6 #e6e6e6 #bfbfbf;
    border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
    border-bottom-color: #b3b3b3;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff',endColorstr='#ffe6e6e6',GradientType=0);
    filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
    *zoom:1;-webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
    -moz-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05)
}  
.btn-danger {
    color: #fff;
    text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
    background-color: #da4f49;
    *background-color: #bd362f;
    background-image: -moz-linear-gradient(top,#ee5f5b,#bd362f);
    background-image: -webkit-gradient(linear,0 0,0 100%,from(#ee5f5b),to(#bd362f));
    background-image: -webkit-linear-gradient(top,#ee5f5b,#bd362f);
    background-image: -o-linear-gradient(top,#ee5f5b,#bd362f);
    background-image: linear-gradient(to bottom,#ee5f5b,#bd362f);
    background-repeat: repeat-x;
    border-color: #bd362f #bd362f #802420;
    border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffee5f5b',endColorstr='#ffbd362f',GradientType=0);
    filter: progid:DXImageTransform.Microsoft.gradient(enabled=false)
}
.btn-info {
    color: #fff;
    text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
    background-color: #49afcd;
    *background-color: #2f96b4;
    background-image: -moz-linear-gradient(top,#5bc0de,#2f96b4);
    background-image: -webkit-gradient(linear,0 0,0 100%,from(#5bc0de),to(#2f96b4));
    background-image: -webkit-linear-gradient(top,#5bc0de,#2f96b4);
    background-image: -o-linear-gradient(top,#5bc0de,#2f96b4);
    background-image: linear-gradient(to bottom,#5bc0de,#2f96b4);
    background-repeat: repeat-x;
    border-color: #2f96b4 #2f96b4 #1f6377;
    border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff5bc0de',endColorstr='#ff2f96b4',GradientType=0);
    filter: progid:DXImageTransform.Microsoft.gradient(enabled=false)
}
.btn-warning {
    color: #fff;
    text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
    background-color: #faa732;
    *background-color: #f89406;
    background-image: -moz-linear-gradient(top,#fbb450,#f89406);
    background-image: -webkit-gradient(linear,0 0,0 100%,from(#fbb450),to(#f89406));
    background-image: -webkit-linear-gradient(top,#fbb450,#f89406);
    background-image: -o-linear-gradient(top,#fbb450,#f89406);
    background-image: linear-gradient(to bottom,#fbb450,#f89406);
    background-repeat: repeat-x;
    border-color: #f89406 #f89406 #ad6704;
    border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fffbb450',endColorstr='#fff89406',GradientType=0);
    filter: progid:DXImageTransform.Microsoft.gradient(enabled=false)
}

.pages {
    float: right;
    padding-right: 20px;
}
.listPage a {
    background: none repeat scroll 0 0 #f9f9f9;
    border: 1px solid #f9f9f9;
    color: #767676 !important;
    display: block;
    float: left;
    font-weight: bold;
    height: 26px;
    line-height: 26px;
    margin: 0 2px;
    text-align: center;
    text-decoration: none;
    width: 30px;
}
.listPage a:visited {
    background: none repeat scroll 0 0 #f9f9f9;
    border: 1px solid #f9f9f9;
    color: #323232;
    display: block;
    float: left;
    height: 26px;
    line-height: 26px;
    margin: 0 2px;
    text-align: center;
    text-decoration: none;
    width: 30px;
}
.listPage a:hover {
    border: 1px solid #0ca6df;
    text-decoration: none;
}
.listPage .none {
    background: none repeat scroll 0 0 #23a1f2;
    border: 1px solid #23a1f2;
    color: #ffffff !important;
    font-weight: bold;
}
#next {
    height: 26px;
    width: 66px;
}
#gopage {
    border: 1px solid #cccccc;
    float: left;
    font-size: 12px;
    padding: 3px 2px;
    text-align: center;
    width: 26px;
}
#page_go {
    height: 20px;
    width: 45px;
}

.export{
    float: right;
    margin-top: -38px;
    margin-right: 10px;
    height: 16px;
    line-height: 16px;
    width: 30px;
    min-height: 16px;
    color: #777;
    background: #faa732 no-repeat fixed top;
	font-size: 16px;
	font-weight:600;
}

.page{background:#ECF4F9;line-height:30px;height:30px;margin-top:15px;border-top:1px solid #d5e3e7}
.pglft{float:left;padding-left:15px;display:inline}
.pglft span{margin:0 5px;color:#f00}
.pgrgt{float:right;display:inline;padding-right:10px;text-align:right}
.pgrgt a{display:inline-block;margin:0 3px}
.pgrgt em input{width:35px;height:15px;line-height:15px\9;border:1px solid #d5e3e7;margin:0 3px;text-align:center;color:#075587;vertical-align:1px}
.pgrgt a:link,.pgrgt a:active,.pgrgt a:visited{color:#075587;text-decoration:none}
.pgrgt a:hover{color:#075587;text-decoration:underline}
.page a{width:20px;text-align:center}
.current_page {background:#ccc;color:red;}
.pgrgt .first_page {width:35px}
a.alft,a.argt,a.zhuan{background:url(/static/images/btnicon.png) no-repeat}
a.alft{background-position:0 -64px;width:12px;height:12px;vertical-align:middle}
a.argt{background-position:0 -81px;width:12px;height:12px;vertical-align:middle;margin-right:5px}
a.zhuan{background-position:0 -97px;width:16px;height:16px;vertical-align:-3px;*vertical-align:middle;margin-right:5px}
</style>
<script>
$(function(){
	$('ul li').mouseover(function(){
	    $(this).css('background-color', '#EAEAEA');
	}).mouseout(function(){
	    $(this).css('background-color', '');
	});
})
</script>
</head>
<body>
<div class="content_box">
<div id="Profile" class="list">
    <h1><b>个人信息</b><span>Profile&nbsp; Info</span></h1>
    <ul>
        <li><span>账号：</span><?php echo $user['username']?></li>
        <li><span>姓名：</span><?php echo $user['realname']?></li>
        <li><span>所属角色：</span><?php echo $user['role']?></li>
        <li><span>最后登录时间：</span><?php echo $user['lastlogintime']?></li>
        <li><span>最后登录IP：</span><?php echo $user['lastloginip']?></li>
    </ul>
</div>
<div style="clear: both">&nbsp;</div>
<div id="roomlist" class="list">
    <h1><b>网校服务期(最近一个月)</b><span>Term&nbsp;Service</span></h1>
    <a href="/default/exportexpiredschool.html"  target="_blank"><span class="btn export">导出</span></a>
    <table width="100%">
    <tr>
    <th width="25%">网校名称</th>
    <th width="10%">网校域名</th>
    <th width="20%">开始日期</th>
    <th width="20%">结束日期</th>
    <th width="10%">结束倒计时</th>
    </tr>
    <?php if(!empty($roomlist)){?>
    <?php foreach ($roomlist as $room){?>
    <?php $dtdate =round((($room['enddate'])-strtotime(date("Y-m-d",SYSTIME)))/3600/24);
    if($dtdate<=0){
        $bgclasss ='danger';
    }elseif($dtdate>0 && $dtdate<=3){
        $bgclasss ='warning';
    }elseif($dtdate>=4 && $dtdate<=10){
        $bgclasss ='info';
    }else{
        $bgclasss='';
    }
    
    ?>
    
    <tr class="<?=$bgclasss?>">
    <td style="text-align:left;padding-left:5px">
    <a href="http://<?=$room['domain']?>.ebh.net" target="_blank" style="color:#08c"><?=$room['crname']?></a></td>
    <td><?=$room['domain']?></td>
    <td><?=date("Y-m-d",$room['begindate'])?></td>
    <td><?=date("Y-m-d",$room['enddate'])?></td>
    <td><?=$dtdate?> 天</td>
    </tr>
    <?php }?>
    <?php }else{?>
    <tr><td colspan="5">30天内暂无即将到期网校数据</td></tr>
    <?php }?>
 </table>
</div>
<div style="clear:both;float:left;margin-top:20px;width:48%;">
备注说明:
<button type="button" class="btn btn-danger"></button> 已经过期的网校 &nbsp;
<button type="button" class="btn btn-warning"></button> 即将3天内过期的网校 &nbsp;
<button type="button" class="btn btn-info"></button>即将10天内过期的网校 &nbsp;
</div>
<div style="clear:both;float:left;margin-top:20px">
    <?php if(!empty($pagestr)){?>
    <?=$pagestr?>
    <?php }?>
</div>
<div style="clear:both">&nbsp;</div>
</div>
</body>
</html>