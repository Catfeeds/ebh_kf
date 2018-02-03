<?php 
    /*  用于获取城市3级列表
        用法：在view里面输入代码：
        1.不需初始化(没有默认选中):<?php $this->widget('cities_widget') ?>
        2.需要初始化(有默认选中)<?php $this->widget('cities_widget',array('citycode'=>$citycode)) ?>,其中$citycode为默认选中的城市的citycode的值
        3. <?php $this->widget('cities_widget',array('citycode'=>$citycode,'getText'=>1)) ?>获取文本如：浙江 杭州 西湖
    */
    if(isset($data['tag'])){
        $tag = $data['tag'];
    }else{
        $tag = 'address';
    }
    if(isset($data['getText'])){
        $url = '/cities/getAddrText.html';
    }else{
        $url = '/cities/getAddr.html';
    }
    if(!empty($data['citycode'])){?>
        <script type = 'text/javascript'>

             $(function(){
                    $.ajax({
                        type:'post',
                        url:"<?=$url?>",
                        data:{'citycode':'<?php echo $data['citycode'];?>','type':5},
                        dateType:"html",
                        success:function(_html){
                                        $("#<?=$tag?>").html(_html);
                                }
                            });
                        });
        </script>
    <span id="<?=$tag?>" name="<?=$tag?>"> </span>
<?php }else{?>
<span id="<?=$tag?>" name="<?=$tag?>">    
        <select name="address_sheng" id="address_sheng" onchange="select_address(this,1)">
        	<option value="">请选择</option>
            
        </select>
        <select name="address_shi" id="address_shi" onchange="select_address(this,2)">
          	<option value="">请选择</option>
             
        </select>
        <select name="address_qu" id="address_qu">
            <option value="">请选择</option>
            
        </select>
</span>

<script type = 'text/javascript'>
 $(function(){
        $.ajax({
        type:'post',
        url:'/cities/getCities.html',
        data:{'citycode':null,'type':5},
        dateType:"html",
        success:function(_html){
                        $("#address_sheng").html(_html);
                        }
                });
        });
</script>
<?php }?>
<script type = 'text/javascript'>
function select_address(_this,type){
        if($("#address_sheng").val() == ''){
                $("#address_shi").html("<option value=''>请选择</option>");
                $("#address_qu").html("<option value=''>请选择</option>");
                return;
        }
        var citycode = $(_this).val();
        if(type == 1){
                var id = 'address_shi';
                $("#address_shi").html("<option value=''>请选择</option>");
                $("#address_qu").html("<option value=''>请选择</option>");
                         }
                         if(type == 2){
                         var id = 'address_qu';
                         if($(_this).val() == ''){
                                 $("#address_qu").html("<option value=''>请选择</option>");
                                 return false;
                         }
                         }
                         $.ajax({
                                 type:'post',
                                 url:'/cities/getCities.html',
                                 data:{'citycode':citycode,'type':type},
                                 dateType:"html",
                                 success:function(_html){
                            $("#"+id).html(_html);
                                  }
                          });
}
</script>