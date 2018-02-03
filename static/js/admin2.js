
$.extend($.fn.validatebox.defaults.rules, {
		
	        equalPwd: {
		        validator: function(value, param){
		        	return value == $(param[0]).val();
		        },
				message:"密码不匹配"
	        },
			username :{
				validator :function (value){
					if(value.length>=6 && value.length<=16)
					{
						$.ajax({
							url:'/admin/common/exists.html',
							data:{'username':value},
							type:'GET',
							async:false,
							success:function(data){
								if(data==1){
									$.fn.validatebox.defaults.rules.username.message = '用户名已经存在';
									exists = 1;
								}
								else{
									exists = 0;
								}		
							},
							error:function(){
								alert();
							}
							
						});
						return !exists;
					}
					else{
						$.fn.validatebox.defaults.rules.username.message = '用户名长度必须在6-12';
						return false;
					}
				},
				message:""
			},
			domain:{
				validator :function (value){
					if(value.length>=2 && value.length<=12)
					{
						$.ajax({
							url:'/school/exists_domain.html',
							data:{'domain':value},
							type:'GET',
							async:false,
							success:function(data){
								if(data==1){
									$.fn.validatebox.defaults.rules.domain.message = '域名已经存在';
									exists = 1;
								}
								else{
									exists = 0;
								}		
							},
							error:function(){
								alert();
							}
							
						});
						return !exists;
					}
					else{
						$.fn.validatebox.defaults.rules.domain.message = '域名的长度在2-12位之间';
						return false;
					}
				}
			},
			crname:{
				validator :function (value){
				
					if(value.length>=2 && value.length<=50)
					{
						$.ajax({
							url:'/school/exists_crname.html',
							data:{'crname':value},
							type:'GET',
							async:false,
							success:function(data){
								if(data==1){
									$.fn.validatebox.defaults.rules.crname.message = '网校名已经存在';
									exists = 1;
								}
								else{
									exists = 0;
								}		
							},
							error:function(){
								alert();
							}
							
						});
						return !exists;
					}
					else{
						$.fn.validatebox.defaults.rules.crname.message = '网校名长度在2-50位之间';
						return false;
					}
				}
			}
	    });	

function getformatdate(timestamp)
{
	var time = new Date(parseInt(timestamp) * 1000);
	var timestr = time.getFullYear()+"-"+
				(frontzero(time.getMonth()+1))+"-"+
				frontzero(time.getDate())+" "+
				frontzero(time.getHours())+":"+
				frontzero(time.getMinutes())+":"+
				frontzero(time.getSeconds());
	return timestr;
}
function frontzero(str)
{
	str = str.toString();
	str.length==1?str="0"+str:str;
	return str;
}
function presstosearch(event){
			if(event.keyCode==13)
			{
				_search();
			}
		}
var gwhere;
function getsearchdata(data){
				var datas = eval('('+data+')'); 
					
					var pagination = datas.splice(datas.length-1,1);
					
					gwhere = pagination[0];
					$('#pp').pagination({
					pageNumber:1,
					pageSize:pagination[0]['pagesize'],
					total:pagination[0]['total'],
					afterPageText: '页 共'+pagination[0]['pages']+'页',
					displayMsg : '共 '+pagination[0]['total']+ ' 项　　　　　　　　'
					});
					formatdata(datas);
					$('#dg').datagrid('loadData',datas);
}
function selectsp(crid){
		$('#dialog').dialog({    
	    title: '选择服务包',    
	    width:700,
		height:450, 
	    closed: false,    
	    cache: false,    
	    href: '/admin/servicepack/servicepack_search.html?crid='+crid,    
	    modal: true   
		});
}
function selectcr(){
		$('#dialog').dialog({    
	    title: '选择学校',    
	    width:700,
		height:450, 
	    closed: false,    
	    cache: false,    
	    href: '/admin/common/classroom_search.html',    
	    modal: true   
		});   
}
function selectterm(){
		$('#dialog').dialog({    
	    title: '选择服务期',    
	    width:700,
		height:450, 
	    closed: false,    
	    cache: false,    
	    href: '/admin/spterm/search.html',    
	    modal: true   
		});
		$("#ck").trigger('click');    
}