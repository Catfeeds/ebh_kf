var isandroid = 0;	//是否安卓手机浏览器
var isapple = 0; //判断是否为苹果iPad相关产品
var ebhtitle = "";
var ebhurl = "";
/**
 * 播放相关
 */
function study(source,cwid,title,price,returnurlstr){
	var ready=1;
	if (typeof(window.parent.checkUpdate) != "undefined")
	{
		ready = window.parent.checkUpdate();
	}
	if(!ready)
	{
		return;
	}
	if(price > 0 && typeof(hidecomfirm) == "undefined") 
	{
	
		$.ajax({
			type:"POST",
			url:'/sitecp.php?action=study&op=playagain',
			data:{'cwid':cwid},
			dataType:'json',
			success:function(result){
				if(result.status=='2'){
					showplaydialog(title,price,source,cwid);
				}
				else if(result.status=='1'){
					tostudy(source,cwid,title,price);
				}
				else{
					
					window.location.href='/login.html?returnurl='+returnurlstr;
					
				}
			}
		});
		
	}
	else
	{
		tostudy(source,cwid,title,price,returnurlstr);
	}
	
}

function gotostudy(result)
{
	var ready=1;
	if (typeof(window.parent.checkUpdate) != "undefined")
	{
		ready = window.parent.checkUpdate();
	}
	if(!ready)
	{
		return;
	}
	source=result.data.source;
	cwid=result.data.cwid;
	title=result.data.title;
	price=result.data.price;
	if (source == undefined) 
		source = "http://www.ebanhui.com/";
	var aurl = source + "play.html?t=ajax&cwid=" + cwid + "&price=" + price + "&callback=?";
	$.getJSON(aurl,function(data){
		if (data.status == "1" && data.k != undefined) {
			top.closeplaydialog();
			var playpath = source + "play.html?k=" + encodeURIComponent(data.k) +"&ftype=0"+data.n+ "|" + title;
			play(playpath);
		} else if(data.status == "-1"){
			$("#playdialogmsg").html("您没有权限查看该课件,请重新登录或联系管理员。").css("color","#ff0000");
		}else if(data.status == "-2"){
			$("#playdialogmsg").html("对不起,您未加入该教室,不能学习该教室内的课件。").css("color","#ff0000");
		}else if(data.status == "-3"){
			$("#playdialogmsg").html("对不起,您的余额不足,请您充值后再进行学习。").css("color","#ff0000");
		} else {
			$("#playdialogmsg").html("您的登录已经失效,请重新登录或联系管理员。").css("color","#ff0000");
		}
	});
}

function tostudy(source,cwid,title,price,returnurlstr)
{
	var ready=1;
	if (typeof(window.parent.checkUpdate) != "undefined")
	{
		ready = window.parent.checkUpdate();
	}
	if(!ready)
	{
		return;
	}
	if (source == undefined) 
		source = "http://www.ebanhui.com/";
	var aurl = source + "play.html?t=ajax&cwid=" + cwid + "&price=" + price + "&callback=?";
	$.getJSON(aurl,function(data){
		if (data.status == "1" && data.k != undefined) {
			if (title.length > 50) title = title.substring(0,50);
			var playpath = source + "play.html?k=" + encodeURIComponent(data.k) +"&ftype=0"+data.n+ "|" + title;
			play(playpath);
		} else if(data.status == "-1"){
			//$("#playdialogmsg").html("对不起,您的余额不足，请您充值后再进行学习。").css("color","#ff0000");
		} else {
			//$("#playdialogmsg").html("您的登录已经失效,请重新登录或联系管理员。").css("color","#ff0000");
			window.location.href='/login.html?returnurl='+returnurlstr;
				
		}
	});
}
function freeplay(source,cwid,title,ft,nt,callback)
{
	validapple();
	validandroid();
	var ready=1;
	if (typeof(window.parent.checkUpdate) != "undefined" && !isandroid && !isapple)
	{
		ready = window.parent.checkUpdate();
		if (!ready)
		{
			return;
		}
	}
	var ftype=0;
	if (ft == 1)
	{
		ftype=1;
	}
	var nid = 0;
	if (nt != undefined)
	{
		nid = parseInt(nt);
	}
	if (source == undefined) 
		source = "http://www.ebanhui.com/";
	var aurl = source + "play.html?t=ajax&cwid=" + cwid + "&ftype=" + ftype + "&nid=" + nid + "&callback=?";
	$.getJSON(aurl,function(data){
		if (data.status == "1" && data.k != undefined) {
			var iftype = 0;
			var utype = "";
			if (data.ftype == "1")
			{
				iftype = 1;
			}
			if (data.utype == "T")
			{
				utype = "T";
			}
			if(data.source != undefined && data.source != "") {
				source = data.source;
			}
			if (title.length > 50) title = title.substring(0,50);
			if(isandroid || isapple) {
				var playpath = source + "play.html?k=" + encodeURIComponent(data.k);
				wapplay(playpath,title,iftype,data.n,utype);
			} else {
				var playpath = source + "play.html?k=" + encodeURIComponent(data.k) + "&ftype=" + iftype+data.n+utype + "|" + title;
				play(playpath);
			}
		}else if(data.status==1&&data.isatt==1) {	//课件为附件
			getfile(cwid);
		}else if(data.status=="-1"){
			alert('对不起，此课件不存在或已删除。');
		}else if(data.status=="-2"){
			if (typeof(callback)=="function")
				callback();
			else 
				alert('对不起，您无权查看此课件。\r\n\r\n此课件需要开通本平台服务后才能播放。');
		}else {
			alert('对不起，您未登录或登录信息已经失效，请重新登录。');
			if (typeof(tologin)=="function")
			{
				tologin("/login.html?returnurl=__url__");
			} 
		}
	});
}

function userplay(source,cwid,callback)
{
	validapple();
	validandroid();
	var ready=1;
	if (typeof(window.parent.checkUpdate) != "undefined" && !isandroid && !isapple)
	{
		ready = window.parent.checkUpdate();
		if (!ready)
		{
			return;
		}
	}
	var ftype=2;
	if (source == undefined) 
		source = "http://www.ebanhui.com/";
	var aurl = source + "play.html?t=ajax&cwid=" + cwid + "&ftype=" + 2 + "&callback=?";
	$.getJSON(aurl,function(data){
		if (data.status == "1" && data.k != undefined) {
			var iftype = 2;
			if (data.ftype == "1")
			{
				iftype = 1;
			}
			if(isandroid || isapple) {
				var playpath = source + "play.html?k=" + encodeURIComponent(data.k);
				wapplay(playpath,title,iftype,data.n);
			} else {
				var playpath = source + "play.html?k=" + encodeURIComponent(data.k) + "&ftype=" + iftype+ "|作业解析";
				play(playpath);
			}
		}else if(data.status==1&&data.isatt==1) {	//课件为附件
			getfile2(cwid);
		}else if(data.status==1&&data.isflv==1) {	//课件为FLV
			parent.window.playflv_top('http://www.ebanhui.com/',cwid,'','isfree','num','562','958',0);
		}else if(data.status=="-1"){
			alert('对不起，此课件不存在或已删除。');
		}else if(data.status=="-2"){
			if (typeof(callback)=="function")
				callback();
			else 
				alert('对不起，您无权查看此课件。\r\n\r\n此课件需要开通本平台服务后才能播放。');
		}else {
			alert('对不起，您未登录或登录信息已经失效，请重新登录。');
			if (typeof(tologin)=="function")
			{
				tologin("/login.html?action=login&returnurl=__url__");
			} 
		}
	});
}




/**
*点播
*/
function playdemand(source,cwid,title,ft,nt,callback)
{
	validapple();
	validandroid();
	var ready=1;
	if (typeof(window.parent.checkUpdate) != "undefined" && !isandroid && !isapple)
	{
		ready = window.parent.checkUpdate();
		if (!ready)
		{
			return;
		}
	}
	var ftype=0;
	if (ft == 1)
	{
		ftype=1;
	}
	var nid = 0;
	if (nt != undefined)
	{
		nid = parseInt(nt);
	}
	if (source == undefined) 
		source = "http://www.ebanhui.com/";
	var aurl = source + "play.html?t=ajax&cwid=" + cwid + "&ftype=" + ftype + "&nid=" + nid + "&callback=?";
	$.getJSON(aurl,function(data){
		if (data.status == "1" && data.k != undefined) {
			var iftype = 0;
			var utype = "";
			if (data.ftype == "1")
			{
				iftype = 1;
			}
			if (data.utype == "T")
			{
				utype = "T";
			}
			if (title.length > 50) title = title.substring(0,50);
			if(isandroid || isapple) {
				var playpath = source + "play.html?k=" + encodeURIComponent(data.k);
				wapplay(playpath,title,iftype,data.n,utype);
			} else {
				var playpath = source + "play.html?k=" + encodeURIComponent(data.k) + "&ftype=" + iftype+data.n+utype + "|" + title;
				play(playpath);
			}
		}else if(data.status==1&&data.isatt==1) {	//课件为附件
			getfile(cwid);
		}else if(data.status=="-1"){
			alert('对不起，此课件不存在或已删除。');
		}else if(data.status=="-2"){
			if (typeof(callback)=="function")
				callback();
			else 
				alert('对不起，您无权查看此课件。\r\n\r\n此课件需要开通本平台服务后才能播放。');
		}else if(data.status=="-3"){	//余额不足
			alert('对不起，您的学点不足。\r\n\r\n您可以选择购买学点继续学习。');
		}else {
			alert('对不起，您未登录或登录信息已经失效，请重新登录。');
			if (typeof(tologin)=="function")
			{
				tologin("/login.html?returnurl=__url__");
			} 
		}
	});
}
function getfile(cwid,isfree,source) {
	if(isfree == undefined)
		isfree = 1;
	if(source == undefined)
		source = 'http://www.ebanhui.com/';
	var url = source + "attach.html?cwid="+cwid+"&isfree="+isfree;
	$("#download_form").attr("action",url);
	document.download_form.submit();
}
function play(playpath){
	if (checkplugin())	{
		var player;
		if (navigator.userAgent.toLowerCase().indexOf("msie") > 0 || navigator.userAgent.toLowerCase().indexOf("trident") > 0) {
			player = document.getElementById("ebhplayer");
		}
		else {
			player = document.getElementById("ebhplayer_noie");
		}
		player.__ebhPlay(playpath);
	}
	else {
		if (typeof(window.parent) != "undefined" && typeof(window.parent.checkUpdate) != "undefined"){
			window.parent.checkUpdate();
		}
		else{
			alert("请下载并安装课件播放器再学习，如有问题请联系我们的管理员。");
		}
	}
}

function checkplugin()
{
	var hasPlugin = true;
	if (navigator.userAgent.toLowerCase().indexOf("msie") > 0  || navigator.userAgent.toLowerCase().indexOf("trident") > 0) {
		var player = document.getElementById("ebhplayer");
		if (player == null || typeof(player.__ebhPlay)=="undefined") {
			hasPlugin = false;
		}
	}
	else {
		var mimetype = navigator.mimeTypes["application/ebhplay"];
		if(mimetype)
		{
			hasPlugin = true;
		}
		else
		{
			hasPlugin = false;
		}
	}
    
	return hasPlugin;
}
function wapplay(fname,title,isfree,hasnote,utype) {
	if(utype == undefined)
		utype = "";
	if (isandroid)
	{
		ebhurl = fname + "&ftype=" + isfree + hasnote+utype;
		EbhPayer.toPlay(ebhurl,title);
	} else {	//苹果调用
		var _parentWindow = window.parent;
		if (_parentWindow != window && _parentWindow && _parentWindow.wapplay)	//父级表示在框架内，目前不支持框架内调用，只能调用父级页面的相关方法
		{
			_parentWindow.isapple = isapple;
			_parentWindow.wapplay(fname,title,isfree,hasnote);
		} else {
			ebhtitle = title;
			ebhurl = fname + "&ftype=" + isfree + hasnote;
			window.location.href = "objc:ebhplayer";
		}
	}
	
}
function playspace(id,title) {
	if (typeof(window.parent.checkUpdate) != "undefined")
	{
		ready = window.parent.checkUpdate();
	}
	var aurl = "http://api.ebanhui.com/valid.php"+ "?callback=?";
	$.getJSON(aurl,function(result){
		if (result.status == 1) {
			var playurl = result.k.replace('upload','play');
			playurl += '&id='+id;
			playfile(playurl,title);
		}
	});
}
function playfile(url,title)
{
	play(url+'|'+title);
}
/*
直接调用播放器，不播放任何代码
*/
function openplay()
{
	if (typeof(window.parent.checkUpdate) != "undefined")
	{
		ready = window.parent.checkUpdate();
	}
	play('|');
}
/*
打开直播课程
*/
function playonline(key,crid,id,rflag) {
	if (typeof(window.parent.checkUpdate) != "undefined")
	{
		ready = window.parent.checkUpdate();
		if (!ready)
		{
			return;
		}
	}
	var rflagstr = "|f=0";
	if (rflag == 1)
	{
		rflagstr = "|f=1";
	}
	//var playurl = "line://k="+key+"|r="+crid+"|c="+id+rflagstr;
	var playurl = "line://r="+crid+"|c="+id+rflagstr + "|k=" + key;
	play(playurl);
}
function getversion() {
	var version = "";
	if (checkplugin())	{
		var player;
		if (navigator.userAgent.toLowerCase().indexOf("msie") > 0  || navigator.userAgent.toLowerCase().indexOf("trident") > 0) {
			player = document.getElementById("ebhplayer");
		}
		else {
			player = document.getElementById("ebhplayer_noie");
		}
		version = player.__GetebhVersion;
	}
	else {
		if (typeof(window.parent) != "undefined" && typeof(window.parent.checkUpdate) != "undefined"){
			window.parent.checkUpdate();
		}
		else{
			alert("请下载并安装课件播放器再学习，如有问题请联系我们的管理员。");
		}
	}
	return version;
}
//判断是否苹果产品
function validapple(){
	var result = 0;
	var userAgent = navigator.userAgent.toLowerCase(); 
	if (userAgent.indexOf("ipad") != -1 || userAgent.indexOf("iphone") != -1)
	{
		result = 1;
	}
	isapple = result;
	return result;
}
//判断是否安卓产品
function validandroid(){
	var result = 0;
	var userAgent = navigator.userAgent.toLowerCase(); 
	if ( userAgent.indexOf("android") != -1)
	{
		result = 1;
	}
	isandroid = result;
	return result;
}
function getEbhTitle(){
	return ebhtitle;
}

function getEbhURL() {
	return ebhurl;
}

/**
*解答问题
*/
function playask(qid)
{
	var ready=1;
	if (typeof(window.parent.checkUpdate) != "undefined" && !isandroid && !isapple)
	{
		ready = window.parent.checkUpdate();
		if (!ready)
		{
			return;
		}
	}
	source = "http://www.ebanhui.com/";
	var aurl = source + "ask.php?t=ajax&qid=" + qid + "&callback=?";
	$.getJSON(aurl,function(data){
		var playsource = "ask1://www.ebanhui.com/";
		if (data.status == "1" && data.k != undefined) {
			var title = "ask";
			var playpath = playsource + "ask.php?k=" + encodeURIComponent(data.k)+"|" + title;
			play(playpath);
		}else if(data.status=="-1"){
			alert('对不起，此问题不存在或已删除。');
		}else if(data.status=="-2"){
			alert('对不起，您无权解答此问题。\r\n\r\n此问题需要开通本平台服务后才能解答。');
		}else {
			alert('对不起，您未登录或登录信息已经失效，请重新登录。');
			if (typeof(tologin)=="function")
			{
				tologin("/login.html?returnurl=__url__","");
			} 
		}
	});
}
/**
*播放器提问
*/
function addquestion(key,rid) {
	var ready=1;
	if (typeof(window.parent.checkUpdate) != "undefined" && !isandroid && !isapple)
	{
		ready = window.parent.checkUpdate();
		if (!ready)
		{
			return;
		}
	}
	var playpath = "ask2:";
	if(rid == undefined)
		rid = "";
	if(key != undefined)
		var playpath = "ask2:" + rid + ":" + key;

	play(playpath);
}
/**
*下载课件文件
*/
function downfile(cwid,callback) {
	var url = '/attach.html?cwid='+cwid+"&inajax=1";
	$.ajax({
		type: "POST",
		url: url,
		dataType: "json",
		success: function(result){
			if(result != null && typeof(result) != undefined){
				if(result.status != undefined) {
					if(result.status == 1) {
						var source = 'http://www.ebanhui.com/';
						if(result.source != undefined) {
							source = result.source;
						}
						getfile(cwid,0,source);
					} else if(result.status == -1) {
						alert('对不起，您未登录或登录信息已经失效，请重新登录。');
						if (typeof(tologin)=="function")
						{
							tologin("/login.html?returnurl=__url__","");
						} 
					} else if(result.status == 0) {
						if(callback)
							callback();
						else
							alert("对不起，此课件需要开通本平台服务后才能播放。");
					}
				} else {
				}
			}
		}
	}); 
}
/**
*播放视频
*/
function playflv(source,cwid,title,isfree,num,height,width,hasbtn,callback,key) {
		$("#flvwrap").dialog("open");
		var url = source+"jsattach.html?cwid="+cwid+"&k="+key;
		// console.log(url);
		url = encodeURIComponent(url);
		if(hasbtn == undefined)
			hasbtn = 0;
		var flashvarsVideoNewControls = {
			source: url,
			type: "video",
			streamtype: "file",
			server: "",
			duration: "52",
			poster: "",
			autostart: "false",
			logo: "",
			logoposition: "top left",
			logoalpha: "30",
			logowidth: "130",
			logolink: "http://www.ebanhui.com",
			hardwarescaling: "false",
			darkcolor: "000000",
			brightcolor: "4c4c4c",
			controlcolor: "FFFFFF",
			hovercolor: "67A8C1",
			controltype: 1,
			classover: hasbtn
		};

		var params = {
			menu: "false",
			scale: "noScale",
			allowFullscreen: "true",
			allowScriptAccess: "always",
			bgcolor: "#000000",
			quality: "high",
			wmode: num=='1'?"Opaque":isfree=='-2'?"Opaque":"Window"
		};

		var attributes = {
			id:"flvcontrol"
		};
		if (width==undefined)
		{
			width="900px"
		}
		if (height==undefined)
		{
			width="562px"
		}
		swfobject.embedSWF("http://static.ebanhui.com/ebh/flash/videoFlvPlayer.swf?idd="+Math.random(), "flvcontrol", width, height, "10.0.0", "/static/flash/expressInstall.swf", flashvarsVideoNewControls, params, attributes);
}
/**
*播放视频
*/
function playebh2(source,cwid,title,isfree,num,height,width,hasbtn,callback,key) {
		$("#flvwrap").dialog("open");
		url=source;
		console.log(url);
		url = encodeURIComponent(url);
		if(hasbtn == undefined)
			hasbtn = 0;
		var flashvarsVideoNewControls = {
			source: url,
			type: "video",
			streamtype: "file",
			server: "",
			duration: "52",
			poster: "",
			autostart: "false",
			logo: "",
			logoposition: "top left",
			logoalpha: "30",
			logowidth: "130",
			logolink: "http://www.ebanhui.com",
			hardwarescaling: "false",
			darkcolor: "000000",
			brightcolor: "4c4c4c",
			controlcolor: "FFFFFF",
			hovercolor: "67A8C1",
			controltype: 1,
			classover: hasbtn
		};

		var params = {
			menu: "false",
			scale: "noScale",
			allowFullscreen: "true",
			allowScriptAccess: "always",
			bgcolor: "#000000",
			quality: "high",
			wmode: num=='1'?"Opaque":isfree=='-2'?"Opaque":"Window"
		};

		var attributes = {
			id:"flvcontrol"
		};
		if (width==undefined)
		{
			width="900px"
		}
		if (height==undefined)
		{
			width="562px"
		}
		swfobject.embedSWF("/static/flash/player.swf", "flvcontrol", width, height, "10.0.0", "/static/flash/expressInstall.swf", flashvarsVideoNewControls, params, attributes);
}

/**
*播放附件视频
*/
function playattflv(source,attid,title,isfree,num,height,width,hasbtn,callback,key) {
		$("#flvwrap").dialog("open");
		var url = source+"attach.html?attid="+attid+"&k="+key;
		url = encodeURIComponent(url);
		console.log(url);
		if(hasbtn == undefined)
			hasbtn = 0;
		var flashvarsVideoNewControls = {
			source: url,
			type: "video",
			streamtype: "file",
			server: "",
			duration: "52",
			poster: "",
			autostart: "false",
			logo: "",
			logoposition: "top left",
			logoalpha: "30",
			logowidth: "130",
			logolink: "http://www.ebanhui.com",
			hardwarescaling: "false",
			darkcolor: "000000",
			brightcolor: "4c4c4c",
			controlcolor: "FFFFFF",
			hovercolor: "67A8C1",
			controltype: 1,
			classover: hasbtn
		};

		var params = {
			menu: "false",
			scale: "noScale",
			allowFullscreen: "true",
			allowScriptAccess: "always",
			bgcolor: "#000000",
			quality: "high",
			wmode: num=='1'?"Opaque":isfree=='-2'?"Opaque":"Window"
		};

		var attributes = {
			id:"flvcontrol"
		};
		if (width==undefined)
		{
			width="900px"
		}
		if (height==undefined)
		{
			width="562px"
		}
		swfobject.embedSWF("http://static.ebanhui.com/ebh/flash/videoFlvPlayer.swf", "flvcontrol", width, height, "10.0.0", "/static/flash/expressInstall.swf", flashvarsVideoNewControls, params, attributes);
}
/**
*播放视频
*/
function playswf(source,cwid,title,isfree,num,height,width,hasbtn,key,callback) {
		var url = source+"jsattach.html?cwid="+cwid+"&k="+key;
		url = encodeURIComponent(url);
		if(hasbtn == undefined)
			hasbtn = 0;
		var flashvarsVideoNewControls = {
			swfurl: url,
			type: "video",
			streamtype: "file",
			server: "",
			duration: "52",
			poster: "",
			autostart: "false",
			logo: "",
			logoposition: "top left",
			logoalpha: "30",
			logowidth: "130",
			logolink: "http://www.ebanhui.com",
			hardwarescaling: "false",
			darkcolor: "000000",
			brightcolor: "4c4c4c",
			controlcolor: "FFFFFF",
			hovercolor: "67A8C1",
			controltype: 1,
			classover: hasbtn
		};

		var params = {
			menu: "false",
			scale: "noScale",
			allowFullscreen: "true",
			allowScriptAccess: "always",
			bgcolor: "#000000",
			quality: "high",
			wmode: num=='1'?"Opaque":isfree=='-2'?"Opaque":"Window"
		};

		var attributes = {
			id:"flvcontrol"
		};
		if (width==undefined)
		{
			width="900px"
		}
		if (height==undefined)
		{
			width="562px"
		}
		swfobject.embedSWF("http://static.ebanhui.com/ebh/flash/SwfBox_2.swf", "flvcontrol", width, height, "10.0.0", "/static/flash/expressInstall.swf", flashvarsVideoNewControls, params, attributes);
}
/**
*播放视频
*/
function playrtmp(source,cwid,title,isfree,num,height,width,hasbtn,pic,callback) {
		var url = source;
		url = encodeURIComponent(url);
		if(hasbtn == undefined)
			hasbtn = 0;
		var flashvarsVideoNewControls = {
			source: url,
			type: "video",
			streamtype: "file",
			server: "",
			duration: "52",
			poster: "",
			autostart: "false",
			logo: "",
			logoposition: "top left",
			logoalpha: "30",
			logowidth: "130",
			logolink: "http://www.ebanhui.com",
			hardwarescaling: "false",
			darkcolor: "000000",
			brightcolor: "4c4c4c",
			controlcolor: "FFFFFF",
			hovercolor: "67A8C1",
			controltype: 1,
			picurl:pic,
			classover: hasbtn
		};

		var params = {
			menu: "false",
			scale: "noScale",
			allowFullscreen: "true",
			allowScriptAccess: "always",
			bgcolor: "#000000",
			quality: "high",
			wmode: num=='1'?"Opaque":isfree=='-2'?"Opaque":"Window"
		};

		var attributes = {
			id:"flvcontrol"
		};
		if (width==undefined)
		{
			width="900px"
		}
		if (height==undefined)
		{
			width="562px"
		}
		swfobject.embedSWF("http://static.ebanhui.com/ebh/flash/flvplayer.swf", "flvcontrol", width, height, "10.0.0", "/static/flash/expressInstall.swf", flashvarsVideoNewControls, params, attributes);
}
/**
*播放视频
*/
function playmu(source,cwid,title,isfree,num,height,width,hasbtn,pic,size,callback,pageControl,mode,seek) {
		var url = source;
		url = encodeURIComponent(url);
		if(hasbtn == undefined)
			hasbtn = 0;
		if(seek == undefined)
			seek = -1;
		if(mode == undefined)
			mode = 0;
		if(pageControl == undefined)
			pageControl = 0;
		var flashvarsVideoNewControls = {
			source: url,
			type: "video",
			streamtype: "file",
			server: "",
			duration: "52",
			poster: "",
			autostart: "false",
			logo: "",
			logoposition: "top left",
			logoalpha: "30",
			logowidth: "130",
			logolink: "http://www.ebanhui.com",
			hardwarescaling: "false",
			darkcolor: "000000",
			brightcolor: "4c4c4c",
			controlcolor: "FFFFFF",
			hovercolor: "67A8C1",
			controltype: 1,
			picurl:pic,
			size:size,
			classover: hasbtn,
			pageControl: pageControl,
			seek: seek,
			mode: mode
		};

		var params = {
			menu: "false",
			scale: "noScale",
			allowFullscreen: "true",
			allowScriptAccess: "always",
			bgcolor: "#000000",
			quality: "high",
			wmode: num=='1'?"Opaque":isfree=='-2'?"Opaque":"Window"
		};

		var attributes = {
			id:"flvcontrol"
		};
		if (width==undefined)
		{
			width="900px"
		}
		if (height==undefined)
		{
			width="562px"
		}
		swfobject.embedSWF("http://static.ebanhui.com/ebh/flash/playerv3.swf", "flvcontrol", width, height, "10.0.0", "/static/flash/expressInstall.swf", flashvarsVideoNewControls, params, attributes);
}
/**
*播放音频
*/
/**
*播放视频
*/
function playaudio(source,cwid,title,isfree,num,height,width,hasbtn,callback,key) {
		$("#flvwrap").dialog("open");
		var url = source+"attach.html?cwid="+cwid+"&.mp3&k="+key;
		url = encodeURIComponent(url);
		if(hasbtn == undefined)
			hasbtn = 0;//无右下角提交时间,则不显示自动提交时间
		var flashvarsVideoNewControls = {
			source: url,
			type: "video",
			streamtype: "file",
			server: "",
			duration: "52",
			poster: "",
			autostart: "false",
			logo: "",
			logoposition: "top left",
			logoalpha: "30",
			logowidth: "130",
			logolink: "http://www.ebanhui.com",
			hardwarescaling: "false",
			darkcolor: "000000",
			brightcolor: "4c4c4c",
			controlcolor: "FFFFFF",
			hovercolor: "67A8C1",
			controltype: 1,
			classover: hasbtn
		};

		var params = {
			menu: "false",
			scale: "noScale",
			allowFullscreen: "true",
			allowScriptAccess: "always",
			bgcolor: "#000000",
			quality: "high",
			wmode: num=='1'?"Opaque":isfree=='-2'?"Opaque":"Window"
		};

		var attributes = {
			id:"flvcontrol"
		};
		if (width==undefined)
		{
			width="900px"
		}
		if (height==undefined)
		{
			width="400px"
		}
		swfobject.embedSWF("http://static.ebanhui.com/ebh/flash/aplayer.swf", "flvcontrol", width, height, "10.0.0", "/static/flash/expressInstall.swf", flashvarsVideoNewControls, params, attributes);
}
function playattaudio(source,attid,title,isfree,num,height,width,hasbtn,callback,key) {
		$("#flvwrap").dialog("open");
		var url = source+"attach.html?attid="+attid+"&.mp3&k="+key;
		url = encodeURIComponent(url);
		if(hasbtn == undefined)
			hasbtn = 0;//无右下角提交时间,则不显示自动提交时间
		var flashvarsVideoNewControls = {
			source: url,
			type: "video",
			streamtype: "file",
			server: "",
			duration: "52",
			poster: "",
			autostart: "false",
			logo: "",
			logoposition: "top left",
			logoalpha: "30",
			logowidth: "130",
			logolink: "http://www.ebanhui.com",
			hardwarescaling: "false",
			darkcolor: "000000",
			brightcolor: "4c4c4c",
			controlcolor: "FFFFFF",
			hovercolor: "67A8C1",
			controltype: 1,
			classover: hasbtn
		};

		var params = {
			menu: "false",
			scale: "noScale",
			allowFullscreen: "true",
			allowScriptAccess: "always",
			bgcolor: "#000000",
			quality: "high",
			wmode: num=='1'?"Opaque":isfree=='-2'?"Opaque":"Window"
		};

		var attributes = {
			id:"flvcontrol"
		};
		if (width==undefined)
		{
			width="900px"
		}
		if (height==undefined)
		{
			width="400px"
		}
		swfobject.embedSWF("http://static.ebanhui.com/ebh/flash/aplayer.swf", "flvcontrol", width, height, "10.0.0", "/static/flash/expressInstall.swf", flashvarsVideoNewControls, params, attributes);
}
//播放ebhp文件
var playpath;
function freeebh(source,cwid,isfree,title,ft,nt,key,callback)
{
				playpath = source+ "attach.html?cwid="+cwid+"&k=" +key;
				playebh(playpath,cwid,title,isfree,1,'600','900',1);


}
/**
*ebh，ebhp播放视频
*/
function playebh(url,cwid,title,isfree,num,height,width,hasbtn,callback) {
	//	var url = source+"attach.html?cwid="+cwid;
		url = encodeURIComponent(url);

		if(hasbtn == undefined)
			hasbtn = 0;
		var flashvarsVideoNewControls = {
			source: url,
			type: "video",
			streamtype: "file",
			server: "",
			duration: "52",
			poster: "",
			autostart: "false",
			logo: "",
			logoposition: "top left",
			logoalpha: "30",
			logowidth: "130",
			logolink: "http://www.ebanhui.com",
			hardwarescaling: "false",
			darkcolor: "000000",
			brightcolor: "4c4c4c",
			controlcolor: "FFFFFF",
			hovercolor: "67A8C1",
			controltype: 1,
			classover: hasbtn
		};

		var params = {
			menu: "false",
			scale: "noScale",
			allowFullscreen: "true",
			allowScriptAccess: "always",
			bgcolor: "FFFFFF",
			quality: "high",
			wmode: num=='1'?"Opaque":isfree=='-2'?"Opaque":"Window"
		};

		var attributes = {
			id:"playcontrol"
		};
		if (width==undefined)
		{
			width="900px"
		}
		if (height==undefined)
		{
			width="562px"
		}
		swfobject.embedSWF("/static/flash/player.swf", "playcontrol", width, height, "10.0.0", "/static/flash/expressInstall.swf", flashvarsVideoNewControls, params, attributes);
}
	
//听课完成
function studyfinish(cwid,ctime,ltime,finished,logid) {
	var lid = 0;
	var url = '/member/studyfinish.html';
	$.ajax({
		type: "POST",
		url: url,
		async:false,
		data:{'cwid':cwid,'ctime':ctime,'ltime':ltime,'finished':finished,'logid':logid},
		success: function(result){
			var json = eval('('+result+')');
			if(json.status == 0) {
				lid = 0;	
			} else {
				lid = json.status;
			}
		}
	}); 
	return lid;
}

 //ebh，ebhp播放(flash调用)
	function ToStart() {
			var fname = playpath;
			var play = document.getElementById("playcontrol");
			if(play != null && play.toPlay != undefined ) {
				play.toPlay(fname);
				window.document.title = title;
			} 
	}

/*
*课件详情里面共用的js代码
*/
//附件弹出框
$(function(){
        $('#atsrc').dialog({
			autoOpen: false,
            resizable:false,
			draggable:false,
            type:'post',
            zIndex:"400",
			modal: false//模式对话框
         });
         $(".atfalsh").click(function(){
			var attid = $(this).attr('aid');
			var source = $(this).attr('source');
			var suffix = $(this).attr('suffix');
			var title = $(this).attr('title');
			playatt(source,attid,suffix,title);
		});
	});
	function playatt(source,attid,suffix,title) {
		var width = 600;
		var height = 540;
		if (suffix == 'swf'){
			$("#atsrc").html(playtype(source,'swf', attid));
        } else if (suffix == 'flv') {
			$("#atsrc").html(playtype(source,'flv', attid,width, height));
        } else {
			width = 250;
			height = 65;
			$("#atsrc").html(playtype(source,'mp3', attid));
        }

		$('#atsrc').dialog("option", "title", title);
        if (suffix == 'flv'){
			$('#atsrc').dialog("option", "width", width + 10);
			$('#atsrc').dialog("option", "height", height + 50);
        } else {
			var isMobile = false;
            if (isMobile){
				$('#atsrc').dialog("option", "width", 310);
				$('#atsrc').dialog("option", "height", 81);
            } else{
				$('#atsrc').dialog("option", "width", width);
                $('#atsrc').dialog("option", "height", height);
            }
		}

        $("#atsrc").dialog({
			close: function() {
				$("#atsrc").html('');
            }
        });
        $('#atsrc').dialog("open");
			return;
	}
	function playtype(source,type, attid, width, height){
		var url = source + "attach.html?attid=" + attid;
		if (type == 'swf'){
			var objhtml = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%" id="Main">'
			objhtml += '<param name="movie" value="' + url + '" />'
			objhtml += '<param name="quality" value="high" />'
			objhtml += '<param name="bgcolor" value="#869ca7" />'
			objhtml += '<param name="allowScriptAccess" value="sameDomain" />'
			objhtml += '<param name="allowFullScreen" value="true" />'
			objhtml += '<!--[if !IE]>-->'
			objhtml += '<object type="application/x-shockwave-flash" data="' + url + '" width="100%" height="100%">'
			objhtml += '<param name="quality" value="high" />'
			objhtml += '<param name="bgcolor" value="#869ca7" />'
			objhtml += '<param name="allowScriptAccess" value="sameDomain" />'
			objhtml += '<param name="allowFullScreen" value="true" />'
			objhtml += '<!--<![endif]-->'
			objhtml += '<!--[if gte IE 6]>-->'
			objhtml += '<p>'
			objhtml += 'Either scripts and active content are not permitted to run or Adobe Flash Player version'
			objhtml += '10.0.0 or greater is not installed.'
			objhtml += '</p>'
			objhtml += '<!--<![endif]-->'
			objhtml += '<a href="http://www.adobe.com/go/getflashplayer">'
			objhtml += '<img src="/static/images/get_flash_player.gif" alt="Get Adobe Flash Player" />'
			objhtml += '</a>'
			objhtml += '<!--[if !IE]>-->'
			objhtml += '</object>'
			objhtml += '<!--<![endif]-->'
			objhtml += '</object>'
		} else if (type == 'flv'){
			var objhtml = ''
			objhtml += '<object id=flvcontrol style="VISIBILITY: visible" height=' + height + ' width=' + width + ' classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000>'
			objhtml += '<param name="FlashVars" value="source=' + encodeURIComponent(url) + '&classover=0">'
			objhtml += '<param name="movie" value="http://static.ebanhui.com/ebh/flash/videoFlvPlayer.swf">'
			objhtml += '<param name="src" value="http://static.ebanhui.com/ebh/flash/videoFlvPlayer.swf">'
			objhtml += '<param name="quality" value="high">'
			objhtml += '<param name="allowScriptAccess" value="always" />'
			objhtml += '<param name="allowFullScreen" value="true" />'
			objhtml += '<!--[if !IE]>-->'
			objhtml += '<object id="blog_index_flash_ff" data="http://static.ebanhui.com/ebh/flash/videoFlvPlayer.swf" style="visibility: visible;" width="' + width + 'px" height="' + height + 'px">'
			objhtml += '<param name="movie" value="http://static.ebanhui.com/ebh/flash/videoFlvPlayer.swf">'
			objhtml += '<param name="quality" value="high">'
			objhtml += '<param name="allowFullScreen" value="true" />'
			objhtml += '<param name="FlashVars" value="source=' + encodeURIComponent(url) + '&classover=0"/>'
			objhtml += '<param name="allowScriptAccess" value="sameDomain" />'
			objhtml += '<param name="allowFullScreen" value="true" />'
			objhtml += '<!--<![endif]-->'
			objhtml += '</object>'
		} else{
			var objhtml ='<object type="application/x-shockwave-flash" data="http://static.ebanhui.com/ebh/flash/rect.swf?mp3='+encodeURIComponent(url)+'&autostart=1&autoplay=1" width="240" height="20" id="dewplayer-rect">'
				objhtml +='<param name="wmode" value="transparent" />'
				objhtml +='<param name="movie" value="http://static.ebanhui.com/ebh/flash/rect.swf?mp3='+encodeURIComponent(url)+'" />'
				objhtml +='</object>'
		}
		return objhtml;
    }

function closeLightFun(opens){	//开关灯方法
	
	$("#showdiv").css("height", $(document).height());
		if(opens==1){
			$("#showdiv").toggle();  
		}
		else if((opens==2)){
			$("#showdiv").toggle(); 
		}
}

function getfile2(cwid) {
	var url = "http://www.ebanhui.com/attach.html?examcwid="+cwid;
	$("#download_form").attr("action",url);
	document.download_form.submit();
	return false;
}