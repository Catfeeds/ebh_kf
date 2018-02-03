<?php
if (! defined ( 'IN_EBH' )) {
	exit ( 'Access Denied' );
}
?>
<?php $this->display('default/header')?>
 <div class="box" id="_doc_content">
   <?php $this->display('default/menu')?>
   <div class="open"><a id="_event_sh" href="javascript:;" hidefocus =" hidefocus"><img border="0" src="/static/images/lftopen.png" id="lftopen" /></a></div>
   <!--right-->
   <div class="sideright" id="_doc_right">
    <div class="nav">
		<div class="nav02">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td class="tlft">
					<ul id="_tabs" class="ultabs">
						<li class="on" id="_tab_item_home">
							<b>
								<em>欢迎页</em>
								<!-- <a></a> -->
							</b>
						</li>
					</ul>
				</td>
			  </tr>
			</table>
		</div>
	</div>
    <div class="bgf" id="_contents">
        <iframe id="_content_item_home" src="/default/main.html" marginheight="0" marginwidth="0" frameborder="0" height="300" width="100%"  scrolling="auto"></iframe>
    </div>
   </div>
 </div>

    <script type="text/javascript">
        String.prototype.replaceAll = function(s1,s2) {
            return this.replace(new RegExp(s1,"gm"),s2);
        }
        //初始化变量
        var _doc_right_width = 0,_doc_footer_height,_doc_footer_height=0,_doc_header_top_height=0,_doc_height=0,_doc_content_height=0,_iframe_height=0;
            
        var _menu_current = null,_tab_current = null, _content_current = null;
        var _tab_ids = '',_content_ids = '';
        
        var need_ay_url = false;
        
        $(document).ready(function(){
            //菜单伸缩事件
            $('._menu_ck').bind('click', function(){
                $(this).parent().next('div').toggle();
                var sobj = $(this).prev('span');
                if(sobj.hasClass('iopen')){
                	sobj.addClass("iclose");
                	sobj.removeClass("iopen");
                 }else{
                  	sobj.addClass("iopen");
                	sobj.removeClass("iclose");
                        }
            })

            //页面尺寸
            _doc_right_width = $('#_doc_right').width();
            _doc_footer_height = $('#footer').height();
            _doc_header_height = $('#_doc_header').height();
            _doc_header_top_height = $('#_doc_header_top').height();
            _doc_height = $(window).height();
            _doc_content_height = _doc_height-_doc_header_height-_doc_footer_height - 8;
            $('#_doc_content').height(_doc_content_height);
            _iframe_height = $('#_doc_content').height()-36;
            $('#_content_item_home').height(_iframe_height);
			$(window).resize(function(){
                _doc_right_width = $('#_doc_right').width();
                _doc_footer_height = $('#footer').height();
                _doc_header_height = $('#_doc_header').height();
                _doc_header_top_height = $('#_doc_header_top').height();
                _doc_height = $(window).height();
                _doc_content_height = _doc_height-_doc_header_height-_doc_footer_height - 8;
                $('#_doc_content').height(_doc_content_height);
                _iframe_height = $('#_doc_content').height()-36;
                $('#_contents').find('iframe').height(_iframe_height);
            });
              //菜单隐藏事件
	        $('#_event_sh').click(function(){
	            if($('#_doc_left').width()==0){
	                $('#_doc_left').width(166).show();
	                $('#_doc_right').css('marginLeft','174px');
	                var imgsrc='/static/images/lftopen.png';
	                $(this).children('img').attr('src',imgsrc);
	            }else{
	                $('#_doc_left').width(0).hide();
	                $('#_doc_right').css('marginLeft','10px');
	                var imgsrc='/static/images/lftcolse.png';
	                $(this).children('img').attr('src',imgsrc);
	            }
	        })
	        
            //顶部隐藏事件
            $('#_event_ss').click(function(){
                $(this).attr('title')=='展开'?$(this).attr('title','收缩'):$(this).attr('title','展开');
                if($('#_doc_header_top').css('display') == 'none'){
                    $('#_doc_header_top').show();
                    $('#_doc_content').height(_doc_content_height)
                    $('#_content_item_home').height(_iframe_height);
                    _iframe_height = $('#_doc_content').height()-45;
                    $('#_contents').find('iframe').height($('#_doc_content').height()-45);
                    $(this).find('img').attr('src', '/static/images/i11.png');
                }else{
                    $('#_doc_header_top').hide();
                    $('#_doc_content').height(_doc_content_height + _doc_header_top_height)
                    $('#_content_item_home').height(_iframe_height+_doc_header_top_height);
                    _iframe_height = $('#_doc_content').height()-45;
                    $('#_contents').find('iframe').height($('#_doc_content').height()-45);
                    $(this).find('img').attr('src', '/static/images/itop.png');
                }
            })
            
            _tab_current = $('#_tab_item_home');
            _content_current = $('#_content_item_home');
                
            $('#_doc_left ._menu_item_ck').bind('click',function(){
                $(this).addClass('hover');
                if(null === _menu_current || (_menu_current && $(this) != _menu_current)){
                    null !== _menu_current ? _menu_current.removeClass('hover') : null;
                    _menu_current = $(this);
                    //是否有子菜单
                    if($(this).attr('item') && $(this).attr('item') == '1'){
                       $(this).next('div').toggle();
                       $(this).find('i').attr('class', $(this).find('i').attr('class')=='twoiconopen'?'twoicon':'twoiconopen')
                    }  
                    if(!$(this).attr('link') || $(this).attr('link') == '')
                        return false;
                    //检测标签是否存在
                    var newid = '_tab_item_'+$(this).attr('id').substr(11);
                    //如果是欢迎页 显示欢迎页 禁止移除
                    if(newid=='_tab_item_1'){
                   	 	showTab('_tab_item_home');
                    	return false;
                    }

                    if(checkTab(newid))
                        removeTab(newid);
                    //创建tab
                    var title = $(this).attr('title') ? $(this).attr('title') : $(this).text();
                    createTab(title, newid, $(this).attr('link'));
                    showTab(newid);

                   //tab收缩
                    tabSliderMove();
                }
            })
            
			$('#_doc_left .iclose').live('click',function(){
				$(this).next('a').click();
			})
			
			$('#_doc_left .iopen').live('click',function(){
				$(this).next('a').click();
			})
			
            $('._menu_item_short').click(function(){
                  
					if(!$(this).attr('link'))return false;
                    //检测标签是否存在
                    var newid = '_tab_item_'+$(this).attr('id').substr(11);
                    if(checkTab(newid))
                        removeTab(newid);
                    //创建tab
                    var title = $(this).attr('title') ? $(this).attr('title') : $(this).text();
                    createTab(title, newid, $(this).attr('link'))
                   
                   var isExistMenu = $('#_menu_item_'+$(this).attr('id').substr(11));
                   if(isExistMenu){
                       if(_menu_current)_menu_current.removeClass('hover');
                       _menu_current = isExistMenu;
                       _menu_current.addClass('hover');
                   }
                
            })
            
/*             $('#_tabs').find('em').live('click', function(){
                showTab($(this).parent().parent().attr('id'));
            }) */
            
           //选中tab处理
            $('#_tabs').find('li').live('click', function(){
                showTab($(this).attr('id'));
            })
            //删除tab
            $('#_tabs').find('a').live('click', function(event){
               	// 导航栏只有一个导航菜单时  禁止删除
                var length = $(this).parent().parent().parent().find('li').length;
                if(length<=1){return false;}
                
                removeTab($(this).parent().parent().attr('id'));
                //showTab('_tab_item_1');
                //tab收缩
                tabSliderMove();
                // 阻止事件冒泡到DOM树上
                event.stopPropagation();
            })
            $('#_event_leftmove').click(function(){
                tabLeftMove();
            })
            $('#_event_rightmove').click(function(){
                tabRightMove();
            })
            $('#_event_closeall').click(function(){
                removeAll();
            })
            
            $('#_content_item_home').attr('src', $('#_menu_item_1').attr('link'));
        })
        
        //检测标签是否存在
        function checkTab(id)
        {
            if(_tab_ids.indexOf(id+";") != -1){
                return true;
            }
            return false;
        }
        
        //显示标签
        function showTab(id)
        {
            if(!id)return false;
            _content_current.hide();
            _tab_current.removeClass('on');
            _tab_current = $('#'+id);
            _tab_current.addClass('on');
            _content_current = (id == '_tab_item_home' ? $('#_content_item_home') : $('#'+'_content_item_'+id.substr(10)));
			if(_content_current) _content_current.show();
            if(id != '_tab_item_home' && $('#'+'_menu_item_'+id.substr(10)) && _menu_current){
            	_menu_current.removeClass('hover');
                _menu_current = $('#'+'_menu_item_'+id.substr(10));
                _menu_current.addClass('hover');
            }
            return false;
        }
        
        //创建标签
        function createTab(title, id, url)
        {
            if(checkTab(id))return false;
            _tab_current.removeClass('on');
            var tabstr = '<li id="'+id+'" class="on"><b><em>'+title+'</em><a></a></b></li>';
            $('#_tabs').append(tabstr);
            _tab_ids += id + ';';
             _tab_current = $('#'+id);
            createContent('_content_item_'+id.substr(10), url);
        }
        
        //移除标签
        function removeTab(id)
        {
            var willid = $('#'+id).next('li').attr('id');
            if(!willid)willid = $('#'+id).prev('li').attr('id');
            var willRemoveTab = $('#'+id);
            willRemoveTab.remove();
            willRemoveTab = null;
            _tab_ids = _tab_ids.replaceAll(id+';',' ');
            removeContent('_content_item_'+id.substr(10));
            if(willid)showTab(willid);
        }
        
        //创建内容
        function createContent(id,url)
        {
            _content_current.hide();
            url = need_ay_url ? ayUrl(url) : url;
            var iframestr = '<iframe id="'+id+'" src="'+url+'" marginheight="0" marginwidth="0" frameborder="0" height="'+_iframe_height+'" width="100%"  scrolling="auto"></iframe>';
            $('#_contents').append(iframestr);
            if($.browser.msie){
            	document.frames(id).location.reload();
            }
            _content_ids += id + ';';
            _content_current = $('#'+id);
        }
        
        //移除内容
        function removeContent(id)
        {
            var willRemoveContent = $('#'+id);
            willRemoveContent.remove();
            willRemoveContent = null;
            _content_ids = _content_ids.replaceAll(id+';','');
        }
        
        //解析URL
        function ayUrl(url)
        {
            return url.replaceAll('_','/');
        }
        
        //移除所有
        function removeAll()
        {
            $('#_tabs').find('li').each(function(){
                var removeTab = $(this);
                if(removeTab.attr('id') != '_tab_item_home'){
                    removeTab.remove();
                    removeTab = null;
                }
            })
            $('#_contents').find('iframe').each(function(){
                var removeContent = $(this);
                if(removeContent.attr('id') != '_content_item_home'){
                    removeContent.remove();
                    removeContent = null;
                }
            })
             
            if(null !== _menu_current)_menu_current.removeClass('hover');
            _tab_ids = '';
            _content_ids = '';
            _tab_current = $('#_tab_item_home');
            _content_current = $('#_content_item_home');
            showTab('_tab_item_home');
        }
        
        //左移动
        function tabLeftMove()
        {
            if(_tab_current.attr('id')=='_tab_item_home')return false;
            var prevtab = _tab_current.prev('li');
            showTab(prevtab.attr('id'));
        }
        
        //有移动
        function tabRightMove()
        {
            var nexttab = _tab_current.next('li');
            if(!nexttab)return false;
            showTab(nexttab.attr('id'));
        }

        //收缩处理
        function tabSliderMove()
        {
            var max_tabs = parseInt((_doc_right_width - 255) / 125);
            max_tabs = 8;
            var current_tabs = (_tab_ids.split(';').length > 0) ? _tab_ids.split(';').length : 1;
			var hide_len = 	(current_tabs-max_tabs) >0 ?  current_tabs-max_tabs : 0;
            if(current_tabs > max_tabs){
                //alert('标签数过多，请删除一些再打开！');return false;
				var one_li_len = 119;
                $("#_tabs").animate({"left":(-1)*one_li_len*hide_len+"px"},300);
            }else{
            	$("#_tabs").animate({"left":"0px"},300);
                }
        }
        
    </script>

<?php
$this->display('default/footer');
?>