<form id="ck">
    <table cellspacing="0" cellpadding="0" class="toptable"><tr><td style="padding-top:10px;padding-left:10px;">
    <label>关键字: </label><input type="text" name="q" id="searchkey" value="" size="20" />
    <span>
    <input type="button" onclick="return _search()" value="搜索" class="cbtn_my"/>
    </td></tr>
    </table>
</form>
<script type="text/javascript">
    function _render(_data){
        $(".moduletbody").html('');
        $.each($(_data),function(k,v){
            $(_renderRow(k,v)).appendTo(".moduletbody");
        });
    }

    function _renderRow(k,v){
        var row = ['<tr style="cursor:pointer" id='+v.uid+' onclick="_getTInfo(this,\''+v.username+'\')">'];
        row.push('<td>'+v.username+'</td>');
        row.push('<td>'+(v.realname||'')+'</td>');
        row.push('<td>'+v.mobile+'</td>');
        row.push('<td>'+v.tag+'</td>');
        row.push('</tr>');
        return row.join('');
    }
</script>


<div style="margin-left:auto;margin-right:auto;width:100%">
<table align="center" border="1" cellspacing="0" cellpadding="0" class="listtable">
<tr>
<td>登录名</td>
<td>姓名</td>
<td>手机</td>
<td>标签</td>
</tr>
<tbody class='moduletbody'>
</tbody>
</table>
<div id="pp"></div>
<script type="text/javascript">
$(function(){
    $.post("/school/getListAjax.html",
            {query:'',pageNumber:1,pageSize:10},
            function(message){
                message = JSON.parse(message);
                $('#pp').pagination('refresh',message.shift());
                 _render(message);
                
            }
            );
    return false;
});
function _search(){
   $('#pp').pagination({pageNumber:1});
   $(".pagination-page-list").trigger('change');
   return false;
}
$('#pp').pagination({
    pageSize:10,
    onSelectPage:function(pageNumber, pageSize){
        var query = $('#ck').serialize();
        $.post("/school/getListAjax.html",
            {query:query,pageNumber:pageNumber,pageSize:pageSize},
            function(message){
                message = JSON.parse(message);
                $('#pp').pagination('refresh',message.shift());
                 _render(message);
                
            }
            );
        return false;
    }
});
function _getTInfo(e,username){
    $('#username').val(username);
    $('#mediaid').val($(e).attr('id'));
	$('#username').focus();
    $('.panel-tool-close').trigger('click');
}
</script>