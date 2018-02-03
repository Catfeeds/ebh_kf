<?php
/**
 * 菜单管理
 * Created by PhpStorm.
 * User: ycq
 * Date: 2017/2/9
 * Time: 15:45
 */
class MenuController extends CAdminControl{
    public function __construct() {
        parent::__construct();
        //检查权限
        $menuaccess= $this->model('Menu')->getModuleByName('code','menu');
        $access= $this->model('Menu')->getModuleByName('menuid',$menuaccess['parentid']);
        if (!empty($access)) {
            Ebh::app()->lib('Access')->checkModuleAccess('admin'.$access['code']);
        } else{
            show_message('请为当前模块分配子菜单！', "window.location.href='/default/main.html'");
        }
    }

    function index() {
        $menu_arr = $this->model('Menu')->getMenuList();
        $this->assign('menus', $menu_arr);
        $this->display('menu/menulist');
    }

    /**
     * 删除菜单
     */
    function ajax_remove_menu() {
        $remove_menu = h($this->input->post('remove_menu'));
        if ($remove_menu != '1') {
            echo json_encode(array('status' => 1, 'msg' => '非法操作'));
            exit();
        }
        $menuid = h(intval($this->input->post('menuid')));
        if ($menuid < 1) {
            echo json_encode(array('status' => 2, 'msg' => '参数不完整'));
            exit();
        }
        $menus=$this->model('Menu')->getModuleByName('menuid',$menuid);
        $menucode=$menus['code'];
        if ($menucode=='menu') {
            echo json_encode(array('status' => 3, 'msg' => '菜单管理禁止删除!'));
            exit();
        }
        if ($this->model('Menu')->getModuleByName('parentid',$menuid)) {
            echo json_encode(array('status' => 4, 'msg' => '请先删除子菜单再进行此操作'));
            exit();
        }
        $ret = $this->model('Menu')->remove($menuid);
        if ($ret) {
            echo json_encode(array('status' => 0, 'msg' => '删除成功！'));
            exit;
        }
        echo json_encode(array('status' => 100, 'msg' => '删除失败！'));
        exit;
    }

    /**
     * 修改菜单
     */
    function menuedit() {
        $menuid = h(intval($this->input->get('menuid')));
        $modulenames=$this->model('Menu')->getModuleByName('menuid',$menuid);
        $codeold=$modulenames['code'];
        $codepathold=$modulenames['codepath'];
        if (!$this->input->post()) {
            $menu_arr = $this->model('Menu')->getMenuList();
            $infos = $this->model('Menu')->getModuleByName('menuid',$menuid);
            $this->assign('menus', $menu_arr);
            $this->assign('infos',$infos);
            $this->display('menu/menuedit');
        } else {
            $update_menu = h($this->input->post('update_menu'));
            if ($update_menu != '1') {
                show_message('非法操作！');
                exit();
            }
            $parentid = h(trim($this->input->post('parentid')));
            $title = h(trim($this->input->post('title')));
            $code = h(trim($this->input->post('code')));
            $codepath = h(trim($this->input->post('codepath')));
            $displayorder = h(intval($this->input->post('displayorder')));
            $displayorder = max(0, $displayorder);

            if ($menuid < 1 || empty($title) || empty($codepath) || empty($code)) {
                show_message('参数不完整！');
                exit();
            }
            if ($codepath!=$codepathold) {
                if ($codepath_info = $this->model('Menu')->getModuleByName('codepath', $codepath)){
                    show_message('路径(URL)已存在！');
                    exit();
                }
            }
            if (strlen($codepath) < 3 || strlen($codepath) > 50) {
                show_message('路径(URL)长度为3-50');
                exit();
            }
            if ($code!=$codeold) {
                if ($code_info = $this->model('Menu')->getModuleByName('code', $code)){
                    show_message('CODE标识已存在！');
                    exit();
                }
            }
            if (strlen($code) < 3 || strlen($code) > 20) {
                show_message('CODE标识长度为3-20');
                exit();
            } else {
                $ret = $this->model('Menu')->update($menuid,$parentid, $title, $codepath, $code, $displayorder);
                if ($ret === false) {
                    show_message('修改失败！');
                    exit();
                }
                if ($ret == 0) {
                    show_message('未做修改！');
                    exit();
                }
                echo '修改成功！';
                close_dialog();
                exit();
            }
        }
    }

    /**
     * 添加菜单
     */
    function menuadd() {
        if (!$this->input->post()) {
            $menu_arr = $this->model('Menu')->getMenuList();
            $this->assign('menus', $menu_arr);
            $this->display('menu/menuadd');
        } else {
            $add_menu = h($this->input->post('add_menu'));
            if ($add_menu != '1') {
                show_message('非法操作！');
                exit();
            }
            $parentid = h(intval($this->input->post('parentid')));
            $title = h(trim($this->input->post('title')));
            $code = h(trim($this->input->post('code')));
            $codepath = h(trim($this->input->post('codepath')));
            $displayorder = h(intval($this->input->post('displayorder')));
            $displayorder = max(0, $displayorder);

            if (empty($title) || empty($codepath) || empty($code)) {
                show_message('参数不完整！');
                exit();
            } elseif ($code_info = $this->model('Menu')->getModuleByName('code', $code)) {
                show_message('CODE标识已存在！');
                exit();
            } elseif (strlen($code) < 3 || strlen($code) > 20){
                show_message('CODE标识长度为3-20');
            } elseif ($codepath_info = $this->model('Menu')->getModuleByName('codepath', $codepath)) {
                show_message('路径(URL)已存在！');
                exit();
            } elseif (strlen($codepath) < 3 || strlen($codepath) > 50){
                show_message('路径(URL)长度为3-50');
            }else {
                $ret = $this->model('Menu')->add($title, $codepath, $code, $parentid, $displayorder);
                if ($ret === false) {
                    show_message('添加失败！');
                    exit();
                }
                if ($ret > 0) {
                    echo "添加成功";
                    close_dialog();
                    exit();
                }
                show_message('未知错误！');
                exit();
            }
        }
    }

    /**
     * 菜单列表
     */
    public function menulist(){
    	$menu_arr = $this->model('Menu')->getMenuList();
    	$this->assign('menus', $menu_arr);
    	$this->display('menu/menulist');
    }

    /**
     * 添加或修改菜单时检查字段是否唯一
     * @name string 字段
     * @namevalue string 字段值
     * @nameold string 原字段
     * @nameoldvalue string 原字段值
     * @return boolean 已存在返回false，不存在返回true
     */
    public function isNameExist() {
        $name= h($this->input->post('name'));
        $namevalue= h($this->input->post('namevalue'));
        $nameold= h($this->input->post('nameold'));
        $nameoldvalue= h($this->input->post('nameoldvalue'));
        //修改菜单时先判断当前表格内容是否发生改变，内容没有改变则直接返回true
        if ($namevalue==$nameoldvalue) {
            echo json_encode(array('status'=>1,'info'=>'success'));
            exit;
        }
        $name_info = $this->model('Menu')->getModuleByName($name,$namevalue);
        if (empty($name_info)) {
            echo json_encode(array('status'=>1,'info'=>'success'));exit;
        } else {
            echo json_encode(array('status'=>0,'info'=>'fail'));exit;
        }
    }
}