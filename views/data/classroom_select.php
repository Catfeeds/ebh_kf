<?php $this->display('school/header');?>
<form id="ck">
    <table cellspacing="0" cellpadding="0" class="toptable"><tr><td>
    <input type="hidden" name="isschool" value="<?=empty($isschool)?0:$isschool?>">
    <label>关键字: </label><input type="text" name="q" id="searchkey" value="" size="60" />
    <span>
    <input type="button" onclick="return _search()" value="搜索"/>
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
        var row = ['<tr style="cursor:pointer" id="'+v.crid+'" summary="'+v.summary+'" onclick="_getTInfo(this,\''+v.crname+'\')">'];
        row.push('<td>'+v.crname+'</td>');
        row.push('<td>'+v.domain+'</td>');
        row.push('<td>'+v.realname+'</td>');
        row.push('<td>'+getformatdate(v.dateline)+'</td>');
        row.push('</tr>');
        return row.join('');
    }
</script>


<div style="margin-left:auto;margin-right:auto;width:100%">
<table align="center" border="1" cellspacing="0" cellpadding="0" class="listtable">
<tr>
<td>网校名称</td>
<td>域名</td>
<td>所属教师</td>
<td>添加时间</td>
</tr>
<tbody class='moduletbody'>
</tbody>
</table>
<div id="pp"></div>
<script type="text/javascript">
$(function(){
    var query = $('#ck').serialize();
    $.post("/common/getClassroomList.html",
            {query:query,pageNumber:1,pageSize:10},
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
        $.post("/common/getClassroomList.html",
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
function _getTInfo(e,crname){
	var crid = $(e).attr('id');
    $context = $(parent.document);
    $context.find('#crname').val(crname);
    $context.find('#mediaid').val(crid);
	$('#crname').focus();
    $('.panel-tool-close').trigger('click');
    
	try{
		window.parent.getstlist(crid);
	}catch(err){
	}
	try{
		window.parent.getsplist(crid);	
	}catch(err){
	}
	window.parent._search();
    parent.window.closedialog();
}
</script>