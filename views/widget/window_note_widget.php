<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>51ebh Control Panel</title>
<style type="text/css">
* { word-break: break-all; word-wrap: break-word; }
body { background: #FFF; color: #000; text-align: center; line-height: 1.5em; }
body, h1, h2, h3, h4, h5, p, ul, dl, ol, form, fieldset { margin: 0; padding: 0; }
body, td, input, textarea, select, button { font-size: 12px; font-family: Verdana,Arial,Helvetica,sans-serif; }
ul { list-style: none; }
cite { font-style: normal; }
a { color: #993300; text-decoration: none; }
a:hover { text-decoration: underline; }
a img { border: none; }

/*布局*/
#wrap { margin: 0 auto; padding: 0 2px; width: 98%; text-align: left; }
#header { position: relative; height: 60px; margin-top: 15px; border-bottom: 5px solid #68A3DB; background-color: #E7F1F5; }
#header h2, #topmenu, #menu { position: absolute; }
#header h2 { left: 5px; bottom: 3px; }
#topmenu { right: 1em; bottom: 3.5em; }
#menu { right: 1em; bottom: -5px; line-height: 28px; }
#menu li { float: left; padding: 2px 1em; }
#menu li.active { padding-top: 0; border: solid #68A3DB; border-width: 2px 1px 0; background: #FFF; }
.mainarea { float: right; width: 100%; margin-left: -150px; }
.maininner { margin-left: 170px; }
.side { float: left; width: 150px; }

#content { margin: 1em 0; padding-top: 50px;}
.title { margin-bottom: 10px; padding-bottom: 0.1em; border-bottom: 1px solid #68A3DB;}
.title h1, .title h3 { display: block; height: 35px; line-height: 35px; padding-left: 30px; font-size: 1.17em; background: url(/admin/skins/orange/images/note_icon2.png) no-repeat 2px 5px;}
.footactions { margin: 0 0; padding: 0.5em; background: #FFF; border: 2px solid #68A3DB; border-top-width: 0px; }
/*\*/ * html .footactions { height: 1%; } /**/ * > .footactions { overflow: hidden; }
.footactions .pages { float: right; }
.footactions a { margin-right:12px;}


/*细线边框区域*/
/*-------------*/
.bgrcontent { width: 580px; background-color: #E7F1F5; padding: 10px; margin: 0 auto;}
.bdrcontent { padding: 1em; border: 2px solid #68A3DB; background: #FFF; zoom: 1; border-bottom-width: 0; }

#footer { clear: both; padding: 1em 0; color: #939393; text-align: center; }
#footer p { font-size: 0.83em; }
#footer .menu a { padding: 0 1em; }

</style>
</head>
<body>
<div id="wrap">
    <div id="content">
      <div class="bgrcontent">
        <div class="bdrcontent">
            <div class="title"><h3>操作消息</h3></div>
            <p>
            <?=$data['returnurl']?>
            <a href="<?=$data['returnurl']?>"><?=$data['note']?><script>setTimeout("window.location.href ='<?=$data['returnurl']?>';", 6000);</script><ajaxok></a>
            
            </p>
        </div>
        <div class="footactions">&nbsp; 
<a href="<?=$data['returnurl']?>">确定</a> &nbsp; 
<a href="javascript:history.back();">返回上一页</a> &nbsp; <!-- <a href="/admin.html">首页</a> --></div>

      </div>
    </div>
</div>
</body>
</html>