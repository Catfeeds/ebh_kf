<?php
$config = array(
    'title'=>'e板会-开启云教学互动时代',
    'keywords'=>'e板会,云教育,教育界的淘宝,开教育店,在线教育,无线互联网教育,在线考试,课后作业,电子教室,网络课堂,同步学堂,补课系统,答疑系统,播放器,课件制作软件,小学,初中,高中,大学,语文,英语,数学,地理,物理,化学,生物,历史,政治,体育,名师讲坛,远程教育,自考,成考,考试辅导,考研,外语,英语,职业技能,资格考试,法律',
    'description'=>'e板会-全球领先的网络在线资源有偿分享增值服务平台,打造教育界的淘宝,让每个人都能开云教育知识店,提供在线教育,无线互联网教育,同步学习,补课系统,答疑系统,小学,初中,高中,大学,语文,英语,数学,地理,物理,化学,生物,历史,政治,体育,名师讲坛,远程教育,自考,成考,考试辅导,考研,外语,英语,职业技能,资格考试,法律等教学',
    'db'=>array(
        'dbtype' => 'mysql',
        'dbdriver' => 'mysqli',
        'tablepre' => 'kf_',
        'pconnect' => false,
        'dbcharset' => 'utf8',
        'autoload' => true,
        'dbhost' => '192.168.0.24',
        'dbuser' => 'root',
        'dbport' => 3306,
        'dbpw' => '123456',
        'dbname' => 'kf',
/*         'slave' => array(
             array(
                 'dbhost' => '192.168.0.28',
                 'dbuser' => 'root',
                 'dbport' => 3306,
                 'dbpw' => '12345699',
                 'dbname' => 'ebh2',
             )
         )*/
    ),
    'ebhdb'=>array(
        'dbtype' => 'mysql',
        'dbdriver' => 'mysqli',
        'tablepre' => 'ebh_',
        'pconnect' => false,
        'dbcharset' => 'utf8',
        'autoload' => true,
        'dbhost' => 'localhost',
        'dbuser' => 'root',
        'dbport' => 3306,
        'dbpw' => '123456',
        'dbname' => 'ebh2'),

    'auto_helper'=>array(
        'common'
    ),
    //路由设置
    'route'=>array(
        'url_mode'=>'QUERY_STRING', //路由模式
        'domain'=>'ebanhui.com',           //网站主域名
        'suffix'=>'.html',           //路径后缀
        'default'=>'default',        //默认控制器
        'directory'=>'portal'        //非www子域名模式下的默认控制器所在文件夹
    ),
    //cookie设置
    'cookie'=>array(
        'prefix'=>'kf_',
        'domain'=>'kf.ebanhui.com',
        'alldomain'=>0, //设置此选项代表当前的主域名，级别高于domain
        'path'=>'/'
    ),
    //log
    'log'=>array(
        'log_path'=>'',                 //日志路径，为空为网站log目录
        'enable'=>true,            //启用日志
        'loglevel'=>1                  //记录日志级别，大于此级别的日志不予记录
    ),
    'cache'=>array(
        'driver'=>'memcache',
        'servers'=>array(
            array('host'=>'127.0.0.1','port'=>11200)
        )
    ),
    //输出编码等设置
    'output'=>array('charset'=>'UTF-8'),
    //安全设置
    'security'=>array('authkey'=>'SFDSEFDSDF'),
    //设置WEB服务器软件类型
    'web'=>array('type'=>'nginx')
);
return $config;