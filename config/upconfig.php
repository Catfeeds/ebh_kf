<?php
/*
 * 文件上传相关配置文件
 */
 //课件服务器配置路径
$upconfig['course']['server'][0] = 'http://up.ebh.net/upload.html';
$upconfig['course']['savepath'] = '/data0/uploads/courses/';
$upconfig['course']['showpath'] = '/uploads/courses/';
//上传完成通知地址
$upconfig['course']['notify'] = 'http://up.ebh.net/upnotify.html';	


//图片服务器配置路径
$upconfig['pic']['server'][0] = 'http://up.ebh.net/uploadimage.html';		
$upconfig['pic']['savepath'] = '/data0/htdocs/img/ebh/';
$upconfig['pic']['showpath'] = 'http://img.ebanhui.com/ebh/';

//添加用户头像的图片配置路径
$upconfig['avatar']['server'][0] = 'http://up.ebh.net/avatar.html';		
$upconfig['avatar']['savepath'] = '/data0/htdocs/img/avatar/';
$upconfig['avatar']['showpath'] = 'http://img.ebanhui.com/avatar/';

//答疑相关附件(图片/音频等)服务器配置路径
$upconfig['ask']['server'][0] = 'http://up.ebh.net/uploadimage.html';		
$upconfig['ask']['savepath'] = '/data0/htdocs/img/ask/';
$upconfig['ask']['showpath'] = 'http://img.ebanhui.com/ask/';

//电子教室附件配置
$upconfig['attachment']['server'][0] = 'http://up.ebh.net/upload.html';
$upconfig['attachment']['savepath'] = '/data0/uploads/docs/';
$upconfig['attachment']['showpath'] = '/uploads/docs/';
//flv文件转成m3u8后的保存路径
$upconfig['attachment']['m3u8savepath'] = '/data0/uploads/mu/';
//m3u8对应ts文件的网站前缀
$upconfig['attachment']['m3u8pre'] = '/mu/';	
//上传完成通知地址
$upconfig['attachment']['notify'] = 'http://up.ebh.net/upnotify.html';	


//资源文件配置
$upconfig['rfile']['server'][0] = '/sitecp.php?action=upfile';
$upconfig['rfile']['savepath'] = '/data0/uploads/rfile/';
$upconfig['rfile']['showpath'] = '/uploads/rfile/';

//笔记文件配置
$upconfig['note']['savepath'] = '/data0/uploads/notes/';
$upconfig['note']['showpath'] = '/uploads/notes/';
//笔记附件文件配置
$upconfig['noteatta']['savepath'] = '/data0/htdocs/img/ebh/notes/';
//笔记附件文件显示配置
$upconfig['noteatta']['showpath'] = 'http://img.ebanhui.com/ebh/notes/';

//课程附件上传配置
$upconfig['courseatta']['data']['savepath'] = '/data0/htdocs/file/cuploads/';
//课程附件上传配置
$upconfig['courseatta']['data']['showpath'] = 'http://file.ebanhui.com/cuploads/';

//上传作业配置
$upconfig['stuexam']['server'][0] = '/sitecp.php?action=upatt&type=stuexam';
$upconfig['stuexam']['savepath'] = '/data0/uploads/stuexam/';
$upconfig['stuexam']['path'] = '/exam/';
$upconfig['stuexam']['showpath'] = '/uploads/stuexam/';

//原创空间
//原创作品文件配置
$upconfig['space']['savepath'] = '/data0/uploads/space/';
$upconfig['space']['showpath'] = '/uploads/space/';
$upconfig['space']['imagepath'] = 'http://img.ebanhui.com/space/';

 //作业课件(主观题)上传位置配置
$upconfig['examcourse']['server'][0] = '/sitecp.php?action=examcourse';
$upconfig['examcourse']['savepath'] = '/data0/uploads/examcourse/';
$upconfig['examcourse']['showpath'] = '/uploads/examcourse/';

 //作业课件(主观题)相关图片路径配置
$upconfig['examcoursepic']['server'][0] = '/sitecp.php?action=examcourse';
$upconfig['examcoursepic']['savepath'] = '/data0/htdocs/img/examcourse/';
$upconfig['examcoursepic']['showpath'] = 'http://img.ebanhui.com/examcourse/';


//音频服务器配置路径
$upconfig['audio']['server'][0] = 'http://up.ebh.net/uploadaudio.html';		
$upconfig['audio']['savepath'] = '/data0/htdocs/img/ebh/';
$upconfig['audio']['showpath'] = 'http://img.ebanhui.com/ebh/';

 //临时文件上传目录，如导入学生等xls的临时目录等
$upconfig['temp']['savepath'] = '/data0/uploads/temp/';
$upconfig['temp']['showpath'] = '/uploads/temp/';

//swf
$upconfig['reslibs']['savepath'] = '/data0/uploads/swf/';
$upconfig['reslibs']['showpath'] = '/uploads/swf/';

//电子教案附件配置
$upconfig['tplanatt']['savepath'] = '/data0/uploads/tplanatt1/';
$upconfig['tplanatt']['showpath'] = '/uploads/tplanatt1/';

//im群文件共享保存目录
$upconfig['qunfile']['savepath'] = '/data0/uploads/qun/';
$upconfig['qunfile']['saveserver'] = 'http://www.ebanhui.com/';
$upconfig['qunfile']['showpath'] = '/uploads/qun/';

$upconfig['formula']['posturl'] = 'http://up.ebh.net/formulav2.html';
$upconfig['formula']['savepath'] = '/data0/htdocs/img/formula/';
$upconfig['formula']['showpath'] = 'http://img.ebanhui.com/formula/';

//课件类型为doc/ppt/xls等格式时所提供的预览处理路径
$upconfig['preview']['url'] = 'http://192.168.0.19:887/index.aspx';

//课件笔记配置
$upconfig['fnote']['savepath'] = '/data0/htdocs/img/fnote/';
$upconfig['fnote']['showpath'] = 'http://img.ebanhui.com/fnote/';

//互动课堂服务器配置路径
$upconfig['iroom']['posturl'] = 'http://up.ebh.net/iaclassroom.html';
$upconfig['iroom']['posturl_for_answer'] = 'http://up.ebh.net/iaclassroom.html';
$upconfig['iroom']['savepath'] = '/data0/htdocs/img/iroom/';
$upconfig['iroom']['showpath'] = 'http://img.ebanhui.com/iroom/';

//免费试题资源显示路径
$upconfig['hjexam']['showpath'] = 'http://img.ebanhui.com/';

//资源下载路径配置
$upconfig['resource']['savepath'] = 'E:/data0/uploads/resource/';
$upconfig['resource']['showpath'] = '/uploads/resource/';

?>
