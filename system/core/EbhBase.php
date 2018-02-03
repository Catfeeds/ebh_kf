<?php

/*
 * EbhBase 类文件
 */
//定义项目请求开始时间
defined('EBH_BEGIN_TIME') or define('EBH_BEGIN_TIME', microtime(true));
//定义项目当前时间
defined('SYSTIME') or define('SYSTIME', time());
//系统类路径
define('SYS_PATH', S_ROOT . 'system/');
//组件类路径
define('COMPONENT_PATH', S_ROOT . 'components/');
//配置文件路径
define('CONFIG_PATH', S_ROOT . 'config/');
//控制器类路径
define('CONTROL_PATH', S_ROOT . 'controllers/');
//helper文件路径
define('HELPER_PATH', S_ROOT . 'helper/');
//视图文件路径
define('VIEW_PATH', S_ROOT . 'views/');
//model类路径
define('MODEL_PATH', S_ROOT . 'models/');
//第三方类库路径
define('LIB_PATH', S_ROOT . 'lib/');
//缓存文件夹路径
define('CACHE_PATH', S_ROOT . 'cache/');

class EbhBase {

    private static $_app;   //应用程序引用变量
    private static $_logger;    //本地日志类引用变量

    /**
     * 创建对象实例
     * @param string $classname创建的对象类名称
     * @param string $config配置文件路径
     * @return object 新创建的对象实例引用
     */

    public static function createApplication($classname, $config) {
        return new $classname($config);
    }

    /**
     * 创建网页Application类
     * @param string $config 配置文件路径
     * @return object Application实例引用
     */
    public static function createWebApplication($config) {
        return self::createApplication('CWebApplication', $config);
    }

	/**
     * 创建非网页Application类
     * @param string $config 配置文件路径
     * @return object Application实例引用
     */
    public static function createIndexApplication($config) {
        return self::createApplication('CIndexApplication', $config);
    }

    /**
     * 设置当前实例
     * @param object $app 当前实例引用
     */
    public static function setApplication($app) {
        self::$_app = $app;
    }

    /**
     * 设置本地日志类
     * @param object $logger 日志类
     */
    public static function setLogger($logger) {
        self::$_logger = $logger;
    }

    /**
     * 返回当前应用实例
     * @return object 当前应用实例
     */
    public static function app() {
        return self::$_app;
    }

    /**
     * 自动加载类方法
     * @param string $classname类名
     */
    public static function autoload($classname) {
        if (isset(self::$_coreClasses[$classname])) {
            include SYS_PATH . self::$_coreClasses[$classname];
        } else {
            if (IS_DEBUG) {  //类文件不存在
            }
        }
    }

    /**
     * @var array 核心类路径对应表 
     */
    private static $_coreClasses = array(
		'CWebApplication' => 'core/CWebApplication.php',
        'CApplication' => 'core/CApplication.php',
        'CComponent' => 'core/CComponent.php',
        'CRouter' => 'core/CRouter.php',
        'CUri' => 'core/CUri.php',
        'CControl' => 'core/CControl.php',
        'CModel' => 'core/CModel.php',
        'CDb' => 'db/CDb.php',
        'CResult' => 'db/CResult.php',
        'CLog' => 'core/CLog.php',
        'CCache' => 'cache/CCache.php',
        'CInput' => 'core/CInput.php',
        'CConfig' => 'core/CConfig.php',
		'CAdminControl' => 'core/CAdminControl.php',
        'CEbhModel' => 'core/CEbhModel.php',
        'CSnsModel' => 'core/CSnsModel.php',
        'NewexamModel' => 'core/NewexamModel.php',
    );
}

//注册类加载方法
spl_autoload_register(array('EbhBase', 'autoload'));
