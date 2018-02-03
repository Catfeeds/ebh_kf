  <?php show_dialog(".Jview","Jview","500","400",true,false); ?>
  <div class="tabcones">
  <div class="tit">
  <h3>网校详情:</h3>
  </div>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="txtlft">网校名称：</td>
      <td><?php echo $c['crname']?></td>
     </tr>
    <tr>
      <td class="txtlft">电话：</td>
      <td><?php echo $c['crphone']?></td>
     </tr>
       <tr>
      <td class="txtlft">主页：</td>
      <td><?php echo $c['domain']?>.ebh.net</td>
     </tr>
       <tr>
      <td class="txtlft">QQ：</td>
      <td><?php echo $c['crqq']?></td>
       <tr>
      <td class="txtlft">地址：</td>
      <td><?php echo $c['craddress']?></td>
     </tr>
       <tr>
      <td class="txtlft">网校简介：</td>
      <td><?php echo $c['summary']?></td>
     </tr>
       <tr>
      <td class="txtlft">开设课程列表：</td>
      <td>
        <table style="width:100%">
        <?php $grade=array("未设置年级","小学一年级","小学二年级","小学三年级","小学四年级","小学五年级","小学六年级","初中一年级","初中二年级","初中三年级","高中一年级","高中二年级","高中三年级",""=>"未设置年级")?>
          <?php for($i=0;$i<count($folder)/3;$i++){?>
            <tr>
              <?php for($j=$i*3;$j<($i+1)*3;$j++){
                  if($j<count($folder)){
                    ?>
                      <td style="border:solid 0px;width:30%"><?php echo $folder[$j]['foldername']?>&nbsp;（<a class="myedit" href="/school/cchooseTeacher.html?fid=<?=$folder[$j]['folderid']?>&crid=<?=$c['crid']?>" title="课程详情" style="color:blue"><?php echo $grade[$folder[$j]['grade']]?></a>）</td>
                    <?php
                  }else{
                    echo '<td style="border:solid 0px;width:30%"></td>';
                  }
                }
                ?>
            </tr>
            <?php }?>
        </table>
      </td>
     </tr>
       <tr>
      <td class="txtlft">开设班级列表：</td>
      <td>
         <table style="width:100%">
          <?php for($i=0;$i<count($class)/3;$i++){?>
            <tr>
              <?php for($j=$i*3;$j<($i+1)*3;$j++){
                  if($j<count($class)){
                    ?>
                      <td style="border:solid 0px;width:30%"><?php echo $class[$j]['classname']?>&nbsp;（<a class="myedit" href="/school/classChooseTeacher.html?classid=<?=$class[$j]['classid']?>&crid=<?=$c['crid']?>" title="班级详情" style="color:blue" ><?php echo $grade[$class[$j]['grade']]?></a>）</td>
                    <?php
                  }else{
                    echo '<td style="border:solid 0px;width:30%"></td>';
                  }
                }
                ?>
            </tr>
            <?php }?>
        </table> 

      </td>
     </tr>
   </table>
   </div>
   <div style="height:150px">

   </div>
   <script type="text/javascript">
//先判断是否加载artDialog.js
  if(typeof(art)=="undefined"){
    var oHead = document.getElementsByTagName('head').item(0);
    var oScript= document.createElement("script");
    oScript.type = "text/javascript";
    oScript.src="/static/js/artDialog/artDialog.js?skin=blue";
    oHead.appendChild( oScript);
  }
  //弹窗show
  $(function(){
    $('.myedit').click(function(){
      
        var width = '800';
        var height = '360';
        var top = Boolean(1);
        var reload = Boolean();
        var dialogid  = 'Jedit';
        
        var href = $(this).attr('href');
        var title=$(this).attr('title');
        
        var width = width ? width : $(document.body).width()-60;
        var height = height ? height : $(window).height()-75;
        var html = '<iframe scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" width="'+width+'" height="'+height+'" src="'+href+'"></iframe>';
        var artDialog = top == true ? window.top.art.dialog : art.dialog;
         
        artDialog({
            id:dialogid,
            title : title,
            width : width,
            height : height,
            content : html,
            padding : 10,
            resize : false,
            lock : true,
            opacity : 0.2,
            
            close:function(){

            },
            close2:function(){
                showDetail(current_uid);
            }
        });
        
        return false;
    })
   })
        
        
   </script>
