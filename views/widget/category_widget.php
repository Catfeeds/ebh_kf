<script>
	$(function(){

		$.post('/common/getGategoriesList.html',
				{where:'<?php echo @json_encode($data['where']) ?>',checked:'<?php echo @$data['selected'] ?>',isad:'<?php echo @$data['isad']?>'},
				function(message){
				 $("#<?=@$data['tag']?>").append(message);	
				}
			);
	});
</script>