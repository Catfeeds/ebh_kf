<?php $this->display('head');?>
<body>
<style type="text/css">
    div.tablist{padding-top:10px;padding-bottom:10px;}
    dl.menu{border-bottom:1px dashed #f0a30a;margin:10px 0;padding-bottom:5px;}
    dl.menu dt,dl.menu dd{width:1400px;}
    dl.menu dt{margin:10px 20px;height:30px;line-height:30px;vertical-align:middle;}
    dl.menu dt *{vertical-align:middle;}
    dl.menu dd div{margin:5px 30px;height:30px;line-height:30px;vertical-align:middle;}
    dl.menu dd div *{vertical-align:middle;}
    dl.menu label.oper{display:none;}
    dl.menu:hover dt label.oper{display:inline;}
    dl.menu dd div:hover label{display:inline;}

    dl.menu label.edit{border:1px solid #000;vertical-align:middle;font-size:14px;padding:3px 5px 5px 3px;}
    dl.menu label.edit input{border:0 none;vertical-align:baseline;margin:0;padding:0 auto;font-size:14px;}
    dl.menu span.code input{width:100px;}
    dl.menu span.title input{width:200px;}
    dl.menu span.codepath input{width:400px;}
    dl.menu span.displayorder input{width:100px;}
    button{margin-right:5px;padding:0 10px;}
    dl.menu span{margin-right:5px;}
</style>
<div class="tabcon tablist" id="menulist">
    <div>
        <label><button type="button" class="create top">添加菜单</button></label>
    </div>
    <div class="menu-view">
<?php if (!empty($menus)) {
    foreach ($menus as $pid => $menu) { ?>
    <dl class="menu">
        <dt>
            <span class="displayorder" mid="<?=$pid?>"><?=htmlspecialchars($menu['displayorder'], ENT_NOQUOTES)?></span>
            <span class="code" data-v="<?=htmlspecialchars($menu['code'], ENT_COMPAT)?>" data-prefix="menu_"><img src="/static/images/ebh/menu_<?=htmlspecialchars($menu['code'], ENT_COMPAT)?>.png"/></span>
            <span class="title"><?=htmlspecialchars($menu['title'], ENT_NOQUOTES)?></span>
            <span class="codepath"><?=htmlspecialchars($menu['codepath'], ENT_NOQUOTES)?></span>
            <label class="oper">
                <button type="button" class="edit init">编辑</button>
                <button type="button" class="create-sub init">添加子菜单</button>
                <button type="button" class="del-all init">删除</button>
                <input type="hidden" value="<?=$pid?>" />
            </label>
        </dt>

        <?php if (!empty($menu['child'])) { ?>
            <dd>
            <?php foreach ($menu['child'] as $mid => $child) { ?>
                <div>
                    <span class="displayorder" mid="<?=$mid?>"><?=htmlspecialchars($child['displayorder'], ENT_NOQUOTES)?></span>
                    <span class="code" data-v="<?=htmlspecialchars($child['code'], ENT_COMPAT)?>" data-prefix="icon_"><img src="/static/images/ebh/icon_<?=htmlspecialchars($child['code'], ENT_COMPAT)?>.png"/></span>
                    <span class="title"><?=htmlspecialchars($child['title'], ENT_NOQUOTES)?></span>
                    <span class="codepath"><?=htmlspecialchars($child['codepath'], ENT_NOQUOTES)?></span>
                    <label class="oper">
                        <button type="button" class="edit init">编辑</button>
                        <button type="button" class="del init">删除</button>
                        <input type="hidden" value="<?=$mid?>" />
                    </label>
                </div>
            <?php } ?>
            </dd>
        <?php } ?>
    </dl>
    <?php }
} ?>
    </div>
    <div>
        <label><button type="button" class="create">添加菜单</button></label>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        $.extend({
            'note': function(msg, callback) {
                var dia = window.top.art.dialog({
                    id: 'note',
                    content: msg,
                    lock: true,
                    fixed: true,
                    okValue: '确定',
                    ok: function() {

                    }
                });
                dia.show();
                setTimeout(function () {
                    dia.close();
                    if (typeof(callback) == 'function') {
                        callback();
                    }
                }, 2000);
            },
            'dia': function(msg, ok, cancel) {
                var dia = window.top.art.dialog({
                    id: 'note',
                    content: msg,
                    lock: true,
                    fixed: true,
                    okValue: '确定',
                    ok: ok || function() {},
                    cancelValue: '取消',
                    cancel: cancel || function() {}
                });
                dia.show();
            },
            'request': function(url, postData, success) {
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    data: postData,
                    success: function(ret) {
                        if (ret.errno > 0) {
                            $.note(ret.msg);
                            return;
                        }
                        success(ret.data);
                    }
                });
            },
            'sort': function(item) {
                var siblings = null;
                var sub = true;
                if (item[0].nodeName.toLowerCase() == 'div') {
                    siblings = item.parent().children();
                } else {
                    item = item.parents('dl');
                    siblings = item.parent().children();
                    sub = false;
                }

                if (siblings.size() == 0) {
                    return;
                }
                var len = siblings.size();
                var selfIndex = siblings.index(item);

                var o = [];
                for(var i = 0; i < len; i++) {
                    var menu = null;
                    if (sub) {
                        menu = $(siblings.get(i)).find('span.displayorder');
                    } else {
                        menu = $(siblings.get(i)).find('dt span.displayorder');
                    }
                    o.push({'i': parseInt(menu.html()), 'id': menu.attr('mid'), 's': i == selfIndex ? 'm' : 'o'});
                }
                o.sort(function(a, b) {
                    if (parseInt(a.i) < parseInt(b.i)) {
                        return 1;
                    }
                    if (a.i == b.i) {
                        if(parseInt(a.id) > parseInt(b.id)) {
                            return 1;
                        }
                        if(parseInt(a.id) < parseInt(b.id)) {
                            return -1;
                        }
                        return 0;
                    }
                    return -1;
                });
                var index = 0;
                for(var i = 0; i < len; i++) {
                    if (o[i].s == 'm') {
                        index = i;
                        break;
                    }
                }
                if (selfIndex == index) {
                    return;
                }
                if (index < selfIndex) {
                    //move up
                    item.insertBefore(siblings[index]);
                } else {
                    //move down
                    item.insertAfter(siblings[index]);
                }
            }
        });
        $.fn.extend({
            'menuEdit': function() {
                $(this).bind('click', function(e) {
                    var t = $(e.target);
                    var nodeType = e.target.nodeName.toLowerCase();
                    if (nodeType != 'button') {
                        return false;
                    }
                    if (t.hasClass('create')) {
                        var html = '<dl class="menu">' +
                            '<dt>' +
                            '<span class="displayorder"><label class="edit">排序号：<input min="0" maxlength="13" value="0" type="number"></label></span>' +
                            '<span class="code" data-v="20" data-prefix="menu"><label class="edit">图标代码：<input maxlength="15" value="" type="text"></label></span>' +
                            '<span class="title"><label class="edit">名称：<input maxlength="20" value="" type="text"></label></span>' +
                            '<span class="codepath"><label class="edit">路径代码：<input maxlength="50" value="" type="text"></label></span>' +
                            '<label class="create-p">' +
                            '<button type="button" class="cancel editing">取消</button><button type="button" class="add-menu editing">保存</button></label>' +
                            '</dt>' +
                            '<dd></dd>' +
                            '</dl>';
                        if (t.hasClass('top')) {
                            t.parent('label').parent('div').after(html);
                        } else {
                            t.parent('label').parent('div').before(html);
                        }
                        return false;
                    }
                    if (t.hasClass('edit')) {
                        var oper = t.parent('label');
                        var editbox = oper.prevAll('span');
                        if (editbox.size() == 0) {
                            return false;
                        }
                        oper.removeClass('oper');
                        oper.find('button.init').hide();
                        oper.append('<button type="button" class="cancel editing">取消</button>');
                        oper.append('<button type="button" class="update editing">保存</button>');
                        editbox.each(function() {
                            var that = $(this);
                            if (that.hasClass('code')) {
                                var v = that.data('v') || '';
                                that.html('<label class="edit">图标代码：<input type="text" maxlength="15" d="' + v + '" value="' + v + '" /></label>');
                                return;
                            }
                            if (that.hasClass('title')) {
                                that.html('<label class="edit">名称：<input type="text" maxlength="20" d="' + that.html() + '"  value="' + that.html() + '" /></label>');
                                return;
                            }
                            if (that.hasClass('codepath')) {
                                that.html('<label class="edit">路径代码：<input type="text" maxlength="50" d="' + that.html() + '"  value="' + that.html() + '" /></label>');
                                return;
                            }
                            if (that.hasClass('displayorder')) {
                                that.html('<label class="edit">排序号：<input type="number" min="0" maxlength="13" d="' + that.html() + '"  value="' + that.html() + '" /></label>');
                                return;
                            }
                        });
                        return false;
                    }
                    if (t.hasClass('del')) {
                        $.dia('确定要删除吗？', function() {
                            var id = $(t.nextAll('input')[0]).val();
                            if (id === undefined) {
                                return false;
                            }
                            $.request('/menu/ajax_remove_menu.html', { 'menuid': id, 'remove_menu': 1 }, function() {
                                t.parent('label').parent('div').remove();
                            });
                        });
                        return false;
                    }
                    if (t.hasClass('del-all')) {
                        $.dia('确定要删除吗？', function() {
                            var id = $(t.nextAll('input')[0]).val();
                            if (id === undefined) {
                                return false;
                            }
                            $.request('/menu/ajax_remove_menu.html', { 'menuid': id, 'remove_menu': 1 }, function() {
                                t.parents('dl').remove();
                            });
                        });
                        return false;
                    }
                    if (t.hasClass('create-sub')) {
                        var subbox = t.parents('dl').find('dd');
                        var id = $(t.nextAll('input')[0]).val();
                        if (id === undefined) {
                            return false;
                        }
                        subbox.append('<div>' +
                            '<span class="displayorder"><label class="edit">排序号：<input min="0" maxlength="13" value="0" type="number"></label></span>' +
                            '<span class="code" data-v="" data-prefix="icon_"><label class="edit">图标代码：<input maxlength="15" value="" type="text"></label></span>' +
                            '<span class="title"><label class="edit">名称：<input maxlength="20" value="" type="text"></label></span>' +
                            '<span class="codepath"><label class="edit">路径代码：<input maxlength="50" value="" type="text"></label></span>' +
                            '<label class="create-s"><button type="button" class="cancel editing">取消</button><button type="button" class="add-sub editing">保存</button><input type="hidden" value="' + id + '" /></label></div>');
                        return false;
                    }
                    if (t.hasClass('cancel')) {
                        var oper = t.parent('label');
                        if (oper.hasClass('create-p')) {
                            oper.parents('dl').remove();
                            return true;
                        }
                        if (oper.hasClass('create-s')) {
                            oper.parent('div').remove();
                            return true;
                        }
                        var editbox = oper.prevAll('span');
                        editbox.each(function() {
                            var that = $(this);
                            if (that.hasClass('code')) {
                                var input = $(that.find('input')[0]);
                                that.data('v', input.val());
                                that.html('<img src="/static/images/ebh/'+ that.data('prefix') + input.attr('d') + '.png" />');
                                input = null;
                                return;
                            }
                            if (that.hasClass('title') || that.hasClass('codepath') || that.hasClass('displayorder')) {
                                var input = $(that.find('input')[0]);
                                that.html(input.attr('d'));
                                input = null;
                                return;
                            }
                        });
                        oper.find('button.init').show();
                        oper.find('button.editing').remove();
                        oper.addClass('oper');
                        return false;
                    }
                    if (t.hasClass('update')) {
                        var oper = t.parent('label');
                        var id = $(t.siblings('input')[0]).val();
                        if (id === undefined) {
                            return false;
                        }
                        var postData = {'update_menu':'1'};
                        postData.menuid = id;
                        var editbox = oper.prevAll('span');
                        editbox.each(function() {
                            var that = $(this);
                            var input = $(that.find('input')[0]);
                            if (that.hasClass('code')) {
                                postData.code = $.trim(input.val());
                                return;
                            }
                            if (that.hasClass('title')) {
                                postData.title = $.trim(input.val());
                                return;
                            }
                            if (that.hasClass('codepath')) {
                                postData.codepath = $.trim(input.val());
                                return;
                            }
                            if (that.hasClass('displayorder')) {
                                postData.displayorder = $.trim(input.val());
                                return;
                            }
                        });
                        $.request('/menu/ajax_update_menu.html', postData, function() {
                            editbox.each(function() {
                                var that = $(this);
                                var input = $(that.find('input')[0]);
                                if (that.hasClass('code')) {
                                    that.data('v', $.trim(input.val()));
                                    that.html('<img src="/static/images/ebh/'+ that.data('prefix') + $.trim(input.val()) + '.png" />');
                                    input = null;
                                    return;
                                }
                                that.html($.trim(input.val()));
                                input = null;
                            });
                            oper.find('button.editing').remove();
                            oper.find('button.init').show();
                            oper.addClass('oper');
                            $.sort(oper.parent());
                        });
                        return false;
                    }
                    if (t.hasClass('add-sub')) {
                        var oper = t.parent('label');
                        var postData = {'add_menu':'1'};
                        var id = $(t.nextAll('input')[0]).val();
                        if (id !== undefined) {
                            postData.parentid = id;
                        }
                        var editbox = oper.prevAll('span');
                        editbox.each(function() {
                            var that = $(this);
                            var input = $(that.find('input')[0]);
                            if (that.hasClass('code')) {
                                postData.code = $.trim(input.val());
                                return;
                            }
                            if (that.hasClass('title')) {
                                postData.title = $.trim(input.val());
                                return;
                            }
                            if (that.hasClass('codepath')) {
                                postData.codepath = $.trim(input.val());
                                return;
                            }
                            if (that.hasClass('displayorder')) {
                                postData.displayorder = $.trim(input.val());
                                return;
                            }
                        });
                        $.request('/menu/ajax_add_menu.html', postData, function(ret) {
                            editbox.each(function() {
                                var that = $(this);
                                var input = $(that.find('input')[0]);
                                if (that.hasClass('code')) {
                                    that.data('v', $.trim(input.val()));
                                    that.html('<img src="/static/images/ebh/'+ that.data('prefix') + $.trim(input.val()) + '.png" />');
                                    input = null;
                                    return;
                                }
                                that.html($.trim(input.val()));
                                input = null;
                            });
                            oper.find('button.editing').remove();
                            oper.append('<button type="button" class="edit init">编辑</button>');
                            oper.append('<button type="button" class="del init">删除</button>');
                            oper.append('<input type="hidden" value="' + ret.newid + '" />')
                            oper.addClass('oper');
                            $.sort(oper.parent());
                        });
                        return false;
                    }
                    if (t.hasClass('add-menu')) {
                        var oper = t.parent('label');
                        var postData = {'add_menu':'1'};
                        var editbox = oper.prevAll('span');
                        editbox.each(function() {
                            var that = $(this);
                            var input = $(that.find('input')[0]);
                            if (that.hasClass('code')) {
                                postData.code = $.trim(input.val());
                                return;
                            }
                            if (that.hasClass('title')) {
                                postData.title = $.trim(input.val());
                                return;
                            }
                            if (that.hasClass('codepath')) {
                                postData.codepath = $.trim(input.val());
                                return;
                            }
                            if (that.hasClass('displayorder')) {
                                postData.displayorder = $.trim(input.val());
                                return;
                            }
                        });
                        $.request('/menu/ajax_add_menu.html', postData, function(ret) {
                            editbox.each(function() {
                                var that = $(this);
                                var input = $(that.find('input')[0]);
                                if (that.hasClass('code')) {
                                    that.data('v', $.trim(input.val()));
                                    that.html('<img src="/static/images/ebh/'+ that.data('prefix') + $.trim(input.val()) + '.png" />');
                                    input = null;
                                    return;
                                }
                                that.html($.trim(input.val()));
                                input = null;
                            });
                            oper.find('button.editing').remove();
                            oper.append('<button type="button" class="edit init">编辑</button>');
                            oper.append('<button type="button" class="create-sub init">添加子菜单</button>');
                            oper.append('<button type="button" class="del-all init">删除</button>');
                            oper.append('<input type="hidden" value="' + ret.newid + '" />')
                            oper.addClass('oper');
                            $.sort(oper.parent());
                        });
                        return false;
                    }
                });
            }
        });
        $("#menulist").menuEdit();
    })(jQuery);
</script>
</body>
</html>