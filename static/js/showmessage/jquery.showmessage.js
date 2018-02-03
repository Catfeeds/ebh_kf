/**
 * jQuery showmessage 1.0
 *
 * 创建时间 2010-8-13
 * 作    者 李凯
 *
 * 参数列表
 * message      :必填                  输出消息体
 * title        :可选 默认为 提示信息  输出标题
 * animatespeed :可选 默认为 2000毫秒  显示和隐藏的动作时间
 * timeoutspeed :可选 默认为 1000毫秒  消息停留时间
 * callback     :可选                  成功后的回调函数
 * 
 * example : $.showmessage({message:'这是信息'});
 **/

(function($){	
	$.showmessage = function(callerSettings){
		//初始化数据
		var settings = $.extend({
			title           : '提示信息',
			message         :         '',
			animatespeed    :          0,
			timeoutspeed    :       1000,
			img				:		null,
			callback        :       null
			}, callerSettings||{}
		);
		settings.width        = 300;
		settings.height       = 100;

		if(settings.img=='success'){
			settings.contentclass=' success';
			settings.flag = true;
		}else if(settings.img=='error'){
			settings.contentclass = ' error';
			settings.flag = true;
		}else{
			settings.flag = false;
			settings.contentclass='';			
		}
		settings.timeoutspeed = settings.timeoutspeed + settings.animatespeed;
		//设置样式
		// var scrollTop = window.parent.document.documentElement.scrollTop || window.parent.document.body.scrollTop;
		settings.css  = 'position:absolute;';
		try{
			settings.css += 'left:' + ($(window.document).scrollLeft() + ($(document).width() - settings.width )/2 ) + 'px;';
			settings.css += 'top:' + ($(window.document).scrollTop() + 240 ) + 'px;';
		}catch(e){
			settings.css += 'left:240px;';
			settings.css += 'top:240px;';	
		}	

		// alert($(window.parent.document).scrollTop());

		// settings.css += 'left:0px;';
		// settings.css += 'top:' + ($(window.parent.document).scrollTop() + ($(window).height() - settings.height )/2) + 'px;';
		// settings.css += 'top:0px;';
		// settings.css += 'top:' + $(document).height() - settings.height )/2 + 'px;';
		
		settings.css += ' width:' + settings.width + 'px;';
		settings.css += ' height:' + settings.height + 'px;';

		settings.strongcss =   ' width:' + (settings.width-40 - (settings.flag?60:0)) + 'px;';
		//定义显示信息的id
		settings.currentid = 'jquerymessage' + $.showmessage.showindex;
		//计算HTML		
		settings.html   =	'<div class="jquerymessage" id="' + settings.currentid + '" style="' + settings.css + '">';
		settings.html  +=	'<table cellspacing="0" cellpadding="0" border="0" class="pop_message_table" style="width: 100%; height: 100%;">';
		settings.html  +=	'<tbody>';
		settings.html  +=	'<tr>';
		settings.html  +=	'<td class="pop_topleft"></td>';
		settings.html  +=	'<td class="pop_border"></td>';
		settings.html  +=	'<td class="pop_topright"></td>';
		settings.html  +=	'</tr>';
		settings.html  +=	'<tr>';
		settings.html  +=	'<td class="pop_border"></td>';
		settings.html  +=	'<td class="pop_content">';

		settings.html  +=	'<h2><span>' + settings.title + '</span></h2>';
		settings.html  +=	'<div class="message_content'+settings.contentclass+'">';
		settings.html  +=	'<div class="message_body" align="center">';
		settings.html  +=	'<p><strong style="' + settings.strongcss + '">' + settings.message + '</strong></p></div>';
		settings.html  +=	'</div>';

		settings.html  +=	'</td>';
		settings.html  +=	'<td class="pop_border"></td>';
		settings.html  +=	'</tr>';
		settings.html  +=	'<tr>';
		settings.html  +=	'<td class="pop_bottomleft"></td>';
		settings.html  +=	'<td class="pop_border"></td>';
		settings.html  +=	'<td class="pop_bottomright"></td>';
		settings.html  +=	'</tr>';
		settings.html  +=	'</tbody>';
		settings.html  +=	'</table>';
		settings.html  +=	'</div>';

		//计数器++
		$.showmessage.showindex++;
		//移除所有其他信息
		$('.jquerymessage').remove();
		//输出html
		$(document.body).append(settings.html);
		//定时器显隐
		if(settings.animatespeed!=0){
			$("#"+settings.currentid).fadeIn(settings.animatespeed);
		}else{
			$("#"+settings.currentid).show();
		}
		setTimeout(function(){
			if(settings.animatespeed!=0){
				$("#"+settings.currentid).fadeOut(settings.animatespeed,function(){
					$("#"+settings.currentid).remove();
					if(settings.callback!=null && $.isFunction(settings.callback)){
						settings.callback();
					}
				});
			}else{
				$("#"+settings.currentid).remove();
				if(settings.callback!=null && $.isFunction(settings.callback)){
					settings.callback();
				}
			}			
		},settings.timeoutspeed);
	};
	$.showmessage.showindex = 0;
})(jQuery);