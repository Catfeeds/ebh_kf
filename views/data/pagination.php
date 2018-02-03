<div class="easyui-pagination" style="border:1px solid #ccc;" id="pp"
        data-options='
            total: <?php echo $pagination['total'];?>,
            pageSize: <?php echo $pagination['pagesize'];?>,
			pageNumber: <?php echo $pagination['pagenumber'];?>,
			beforePageText: "第",
			afterPageText: "页 共<?php echo $pagination['pages'];?>页",
			displayMsg : "共 <?php echo $pagination['total'];?> 项　　　　　　　　",
            onSelectPage: function(pageNumber, pageSize){
				getdata(pageNumber, pageSize,gwhere);
            }
			'
			>
	</div>
	<?php //echo str_replace('"','\"',htmlspecialchars(json_encode($where),ENT_QUOTES));?>
	<script>
	
	function getdata(pageNumber, pageSize,where){
	
		if(where==null)
			where = new Object();

		where.pagenumber = pageNumber;
		where.pagesize = pageSize;

			var url = '<?php echo geturl("$ctrl/getlist")?>';
			$.ajax({
				url:url,
				data:{'param':where},
				type:'GET',
				success:function(data){
				
					var datas = eval('('+data+')'); 
					
					var pagination = datas.splice(datas.length-1,1);
				
					$('#pp').pagination({
					total:pagination[0]['total'],
					afterPageText: '页 共'+pagination[0]['pages']+'页',
					displayMsg : '共 '+pagination[0]['total']+ ' 项　　　　　　　　'　　　
					});
					formatdata(datas);
					$('#dg').datagrid('loadData',datas);
					// _render(datas);
						
				},
				error:function(){
					alert();
				}
			});
		}
	</script>
	