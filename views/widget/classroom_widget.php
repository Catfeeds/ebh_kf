<?php
	if(!empty($data['tag'])){
		$crTag = $data['tag'];
	}else{
		$crTag='crid';
	}
	if(!empty($data['where'])){
		$where = $data['where'];
	}else{
		$where= array();
	}
	if(!empty($data['selected'])){
		$selected = $data['selected'];
	}else{
		$selected= '';
	}
?>
<select name="<?=$crTag?>" id="<?=$crTag?>">
       <option value="0">请选择</option>
</select>
<script type="text/javascript">
	$(function(){
		var _html = $("#<?=$crTag?>").html();
		$.post('/school/getCrList.html',
				{where:"<?php json_encode($where);?>",checked:"<?=$selected?>"},
				function(message){
				$("#<?=$crTag?>").html(_html+message);	
				}
			);
	});
</script>