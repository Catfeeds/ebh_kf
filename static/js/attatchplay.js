/**
*播放附件视频
*/
function playattflv(url,attid,title,isfree,num,height,width,hasbtn,callback,key) {
		url = encodeURIComponent(url);
		//console.log(url);
		if(hasbtn == undefined)
			hasbtn = 0;
		var flashvarsVideoNewControls = {
			vcastr_file: url,
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
		swfobject.embedSWF("http://static.ebanhui.com/ebh/flash/newflvplayer.swf", "flvcontrol", width, height, "10.0.0", "/static/flash/expressInstall.swf", flashvarsVideoNewControls, params, attributes);
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